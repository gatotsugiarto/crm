<?php

use yii\db\Migration;

/**
 * Stops quotation approval from creating every Sales Order item twice.
 *
 * Two triggers copied quotation_item -> sales_order_item for the same SO:
 * `trg_quotation_to_sales_order` (quotation AFTER UPDATE, inserts the SO and then
 * copies the items) and `trg_so_copy_items` (sales_order AFTER INSERT, fires on
 * that insert and copies them again). The copy is removed from the quotation
 * trigger and kept in `trg_so_copy_items`, because that one also serves Sales
 * Orders created by hand from the SO form with a quotation selected.
 *
 * Plain CREATE TRIGGER (no DELIMITER, no DEFINER) so it runs through PDO and on
 * production's MySQL 5.5. The DB user needs the TRIGGER privilege.
 */
class m260928_120000_fix_duplicate_sales_order_items extends Migration
{
    public function up()
    {
        $this->execute('DROP TRIGGER IF EXISTS `trg_quotation_to_sales_order`');
        $this->execute(<<<'SQL'
CREATE TRIGGER `trg_quotation_to_sales_order` AFTER UPDATE ON `quotation` FOR EACH ROW BEGIN
    IF NEW.status = 'Approved' AND OLD.status <> 'Approved' THEN
        UPDATE opportunity
        SET
            stage = 'Closed Won',
            probability = 100,
            amount = NEW.total_amount,
            updated_at = NOW()
        WHERE id = NEW.opportunity_id;

        -- Items are copied by trg_so_copy_items (sales_order AFTER INSERT).
        IF NOT EXISTS (
            SELECT 1 FROM sales_order WHERE quotation_id = NEW.id
        ) THEN
            INSERT INTO sales_order (
                order_number, account_id, quotation_id, order_date, total_amount,
                status, status_id, created_at, created_by, updated_at, updated_by
            )
            VALUES (
                CONCAT(
                    'SO/',
                    DATE_FORMAT(NOW(), '%Y%m%d'),
                    '/',
                    LPAD(
                        IFNULL((
                            SELECT MAX(CAST(SUBSTRING_INDEX(order_number, '/', -1) AS UNSIGNED))
                            FROM sales_order
                            WHERE DATE(created_at) = CURDATE()
                        ), 0) + 1,
                        4,
                        '0'
                    )
                ),
                NEW.account_id, NEW.id, CURDATE(), NEW.total_amount,
                'Draft', 1, NOW(), NEW.updated_by, NOW(), NEW.updated_by
            );
        END IF;
    END IF;
END
SQL
        );
    }

    public function down()
    {
        // Restores the original trigger, including its (duplicating) item copy.
        $this->execute('DROP TRIGGER IF EXISTS `trg_quotation_to_sales_order`');
        $this->execute(<<<'SQL'
CREATE TRIGGER `trg_quotation_to_sales_order` AFTER UPDATE ON `quotation` FOR EACH ROW BEGIN
    DECLARE v_so_id INT;
    IF NEW.status = 'Approved' AND OLD.status <> 'Approved' THEN
        UPDATE opportunity
        SET
            stage = 'Closed Won',
            probability = 100,
            amount = NEW.total_amount,
            updated_at = NOW()
        WHERE id = NEW.opportunity_id;

        IF NOT EXISTS (
            SELECT 1 FROM sales_order WHERE quotation_id = NEW.id
        ) THEN
            INSERT INTO sales_order (
                order_number, account_id, quotation_id, order_date, total_amount,
                status, status_id, created_at, created_by, updated_at, updated_by
            )
            VALUES (
                CONCAT(
                    'SO/',
                    DATE_FORMAT(NOW(), '%Y%m%d'),
                    '/',
                    LPAD(
                        IFNULL((
                            SELECT MAX(CAST(SUBSTRING_INDEX(order_number, '/', -1) AS UNSIGNED))
                            FROM sales_order
                            WHERE DATE(created_at) = CURDATE()
                        ), 0) + 1,
                        4,
                        '0'
                    )
                ),
                NEW.account_id, NEW.id, CURDATE(), NEW.total_amount,
                'Draft', 1, NOW(), NEW.updated_by, NOW(), NEW.updated_by
            );

            SET v_so_id = LAST_INSERT_ID();

            INSERT INTO sales_order_item (
                sales_order_id, product_id, qty, price, discount, total,
                status_id, created_at, created_by, updated_at, updated_by
            )
            SELECT
                v_so_id, qi.product_id, qi.qty, qi.price, qi.discount, qi.total,
                1, NOW(), NEW.updated_by, NOW(), NEW.updated_by
            FROM quotation_item qi
            WHERE qi.quotation_id = NEW.id;
        END IF;
    END IF;
END
SQL
        );
    }
}
