<?php

use yii\db\Migration;

/**
 * Business line (NHS, NXG, IPTV, ...) and SPH numbering per line.
 *
 * - product.business_line: the product's line, set in Product & Pricing.
 * - quotation.business_line: the line a quotation belongs to. A quotation holds
 *   products of one line only; Create Quotation splits an opportunity with mixed
 *   lines into one quotation per line. Existing SPH-numbered quotations get it from
 *   their number (SLS-XXX).
 * - quotation_layout.number_code "SPH/SLS-NHS/EXT" becomes "SPH/SLS-{LINE}/EXT";
 *   numbers look like 001/SPH/SLS-NHS/EXT/IX/2026, running per line per year.
 * - trg_quotation_to_sales_order: the opportunity is closed only when none of its
 *   quotations is still Draft/Sent (Closed Won with the approved total, or Closed
 *   Lost when all were rejected), instead of Closed Won on the first approval.
 *   Sales Order creation is unchanged. down() restores the m260928_120000 trigger.
 */
class m260930_090000_business_line extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}', 'business_line', $this->string(20)->null()->after('revenue_model'));
        $this->createIndex('idx_product_business_line', '{{%product}}', 'business_line');
        $this->addColumn('{{%quotation}}', 'business_line', $this->string(20)->null()->after('quotation_number'));
        $this->createIndex('idx_quotation_business_line', '{{%quotation}}', 'business_line');

        $this->execute("UPDATE quotation SET business_line = SUBSTRING_INDEX(SUBSTRING_INDEX(quotation_number, '/SLS-', -1), '/', 1)
                        WHERE quotation_number REGEXP '^[0-9]{3,}/SPH/SLS-[A-Z0-9]+/'");
        $this->update('{{%quotation_layout}}', ['number_code' => 'SPH/SLS-{LINE}/EXT'], ['number_code' => 'SPH/SLS-NHS/EXT']);

        $this->execute('DROP TRIGGER IF EXISTS `trg_quotation_to_sales_order`');
        $this->execute(<<<'SQL'
CREATE TRIGGER `trg_quotation_to_sales_order` AFTER UPDATE ON `quotation` FOR EACH ROW BEGIN
    -- Close the opportunity once none of its quotations is still Draft/Sent: Closed Won
    -- with the approved quotations' total if any was approved, else Closed Lost. An
    -- opportunity can have one quotation per business line (and revisions), so a
    -- single approval no longer closes it on its own.
    IF NEW.status IN ('Approved', 'Rejected') AND OLD.status <> NEW.status AND NEW.opportunity_id IS NOT NULL THEN
        IF NOT EXISTS (
            SELECT 1 FROM quotation
            WHERE opportunity_id = NEW.opportunity_id AND id <> NEW.id AND status IN ('Draft', 'Sent')
        ) THEN
            IF EXISTS (
                SELECT 1 FROM quotation WHERE opportunity_id = NEW.opportunity_id AND status = 'Approved'
            ) THEN
                UPDATE opportunity
                SET stage = 'Closed Won',
                    probability = 100,
                    amount = (SELECT IFNULL(SUM(total_amount), 0) FROM quotation
                              WHERE opportunity_id = NEW.opportunity_id AND status = 'Approved'),
                    updated_at = NOW()
                WHERE id = NEW.opportunity_id;
            ELSE
                UPDATE opportunity
                SET stage = 'Closed Lost', probability = 0, updated_at = NOW()
                WHERE id = NEW.opportunity_id;
            END IF;
        END IF;
    END IF;

    IF NEW.status = 'Approved' AND OLD.status <> 'Approved' THEN

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
        $this->update('{{%quotation_layout}}', ['number_code' => 'SPH/SLS-NHS/EXT'], ['number_code' => 'SPH/SLS-{LINE}/EXT']);
        $this->dropIndex('idx_quotation_business_line', '{{%quotation}}');
        $this->dropColumn('{{%quotation}}', 'business_line');
        $this->dropIndex('idx_product_business_line', '{{%product}}');
        $this->dropColumn('{{%product}}', 'business_line');
    }
}
