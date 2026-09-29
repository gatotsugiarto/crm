<?php

use yii\db\Migration;

/**
 * Lead conversion copies the lead's description into the new opportunity's
 * description (it used to be left empty). Lead description is now required in
 * the Lead model, so converted opportunities always carry it.
 *
 * Recreates sp_convert_lead_to_customer from the m260928_150000 version with
 * only that change; down() restores that version. Needs CREATE/ALTER ROUTINE.
 */
class m260929_090000_convert_copies_lead_description extends Migration
{
    public function up()
    {
        $this->execute('DROP PROCEDURE IF EXISTS `sp_convert_lead_to_customer`');
        $this->execute(<<<'SQL'
CREATE PROCEDURE `sp_convert_lead_to_customer`(IN p_lead_id INT,
    IN p_user_id INT,
    OUT p_account_id INT,
    OUT p_contact_id INT,
    OUT p_opportunity_id INT)
BEGIN
    -- Variabel
    DECLARE v_company_name VARCHAR(255);
    DECLARE v_address TEXT;
    DECLARE v_contact_name VARCHAR(200);
    DECLARE v_email VARCHAR(150);
    DECLARE v_phone VARCHAR(50);
    DECLARE v_industry VARCHAR(100);
    DECLARE v_customer_segment VARCHAR(50);
    DECLARE v_description VARCHAR(255);
    DECLARE v_city_id INT;
    DECLARE v_province_id INT;
    DECLARE v_country_id INT;
    DECLARE v_postal_id INT;
    DECLARE v_owner_user_id INT;

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- 1. Ambil & LOCK data lead
    SELECT 
        company_name, contact_name, email, phone, industry, customer_segment, description, address,
        city_id, province_id, country_id, postal_code_id, owner_user_id
    INTO 
        v_company_name, v_contact_name, v_email, v_phone, v_industry, v_customer_segment, v_description, v_address,
        v_city_id, v_province_id, v_country_id, v_postal_id, v_owner_user_id
    FROM `lead`
    WHERE id = p_lead_id AND is_converted = 0
    FOR UPDATE;

    -- Validasi
    IF v_company_name IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Lead not found or already converted';
    END IF;

    -- fallback owner
    IF v_owner_user_id IS NULL THEN
        SET v_owner_user_id = p_user_id;
    END IF;

    -- 2. Insert ACCOUNT
        INSERT INTO `account` (
        name, account_type, customer_segment, industry, phone, email, address,
        city_id, province_id, country_id, postal_code_id,
        status_id, created_at, created_by, updated_at, updated_by, owner_user_id
    ) VALUES (
        v_company_name, 'Prospect', v_customer_segment, v_industry, v_phone, v_email, v_address,
        v_city_id, v_province_id, v_country_id, v_postal_id,
        1, NOW(), p_user_id, NOW(), p_user_id, v_owner_user_id
    );

    SET p_account_id = LAST_INSERT_ID();

    -- 2b. Lead address also becomes the account's Billing, Shipping and Office
    --     addresses (edited later where they differ)
    IF v_address IS NOT NULL AND TRIM(v_address) <> '' THEN
        INSERT INTO `account_address` (
            account_id, address_type, address,
            city_id, province_id, country_id, postal_code_id,
            status_id, created_at, created_by, updated_at, updated_by
        ) VALUES
            (p_account_id, 'Billing', v_address, v_city_id, v_province_id, v_country_id, v_postal_id, 1, NOW(), p_user_id, NOW(), p_user_id),
            (p_account_id, 'Shipping', v_address, v_city_id, v_province_id, v_country_id, v_postal_id, 1, NOW(), p_user_id, NOW(), p_user_id),
            (p_account_id, 'Office', v_address, v_city_id, v_province_id, v_country_id, v_postal_id, 1, NOW(), p_user_id, NOW(), p_user_id);
    END IF;
        
        -- Karena is_primary reset semua by account_id
        UPDATE contact 
        SET is_primary = 0 
        WHERE account_id = p_account_id;

    -- 3. Insert CONTACT
    INSERT INTO `contact` (
        account_id, fullname, email, phone, is_primary,
        status_id, created_at, created_by, updated_at, updated_by
    ) VALUES (
        p_account_id, v_contact_name, v_email, v_phone, 1,
        1, NOW(), p_user_id, NOW(), p_user_id
    );

    SET p_contact_id = LAST_INSERT_ID();

    -- 4. (🔥 BEST PRACTICE) Auto create OPPORTUNITY
    INSERT INTO `opportunity` (
        account_id, contact_id, name, description, stage,
        amount, probability, close_date,
        created_at, created_by, updated_at, updated_by, owner_user_id
    ) VALUES (
        p_account_id,
        p_contact_id,
        CONCAT('Opportunity - ', v_company_name),
        v_description,
        'Prospecting',
        0,
        10,
        DATE_ADD(CURDATE(), INTERVAL 30 DAY),
        NOW(),
        p_user_id,
                NOW(),
        p_user_id,
        v_owner_user_id
    );

    SET p_opportunity_id = LAST_INSERT_ID();

    -- 5. Update LEAD
    UPDATE `lead`
    SET 
        is_converted = 1,
        converted_account_id = p_account_id,
        converted_contact_id = p_contact_id,
        updated_at = NOW(),
        updated_by = p_user_id
    WHERE id = p_lead_id;

    COMMIT;

END
SQL
        );
    }

    public function down()
    {
        $this->execute('DROP PROCEDURE IF EXISTS `sp_convert_lead_to_customer`');
        $this->execute(<<<'SQL'
CREATE PROCEDURE `sp_convert_lead_to_customer`(IN p_lead_id INT,
    IN p_user_id INT,
    OUT p_account_id INT,
    OUT p_contact_id INT,
    OUT p_opportunity_id INT)
BEGIN
    -- Variabel
    DECLARE v_company_name VARCHAR(255);
    DECLARE v_address TEXT;
    DECLARE v_contact_name VARCHAR(200);
    DECLARE v_email VARCHAR(150);
    DECLARE v_phone VARCHAR(50);
    DECLARE v_industry VARCHAR(100);
    DECLARE v_customer_segment VARCHAR(50);
    DECLARE v_city_id INT;
    DECLARE v_province_id INT;
    DECLARE v_country_id INT;
    DECLARE v_postal_id INT;
    DECLARE v_owner_user_id INT;

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- 1. Ambil & LOCK data lead
    SELECT 
        company_name, contact_name, email, phone, industry, customer_segment, address,
        city_id, province_id, country_id, postal_code_id, owner_user_id
    INTO 
        v_company_name, v_contact_name, v_email, v_phone, v_industry, v_customer_segment, v_address,
        v_city_id, v_province_id, v_country_id, v_postal_id, v_owner_user_id
    FROM `lead`
    WHERE id = p_lead_id AND is_converted = 0
    FOR UPDATE;

    -- Validasi
    IF v_company_name IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Lead not found or already converted';
    END IF;

    -- fallback owner
    IF v_owner_user_id IS NULL THEN
        SET v_owner_user_id = p_user_id;
    END IF;

    -- 2. Insert ACCOUNT
        INSERT INTO `account` (
        name, account_type, customer_segment, industry, phone, email, address,
        city_id, province_id, country_id, postal_code_id,
        status_id, created_at, created_by, updated_at, updated_by, owner_user_id
    ) VALUES (
        v_company_name, 'Prospect', v_customer_segment, v_industry, v_phone, v_email, v_address,
        v_city_id, v_province_id, v_country_id, v_postal_id,
        1, NOW(), p_user_id, NOW(), p_user_id, v_owner_user_id
    );

    SET p_account_id = LAST_INSERT_ID();

    -- 2b. Lead address also becomes the account's Billing, Shipping and Office
    --     addresses (edited later where they differ)
    IF v_address IS NOT NULL AND TRIM(v_address) <> '' THEN
        INSERT INTO `account_address` (
            account_id, address_type, address,
            city_id, province_id, country_id, postal_code_id,
            status_id, created_at, created_by, updated_at, updated_by
        ) VALUES
            (p_account_id, 'Billing', v_address, v_city_id, v_province_id, v_country_id, v_postal_id, 1, NOW(), p_user_id, NOW(), p_user_id),
            (p_account_id, 'Shipping', v_address, v_city_id, v_province_id, v_country_id, v_postal_id, 1, NOW(), p_user_id, NOW(), p_user_id),
            (p_account_id, 'Office', v_address, v_city_id, v_province_id, v_country_id, v_postal_id, 1, NOW(), p_user_id, NOW(), p_user_id);
    END IF;
        
        -- Karena is_primary reset semua by account_id
        UPDATE contact 
        SET is_primary = 0 
        WHERE account_id = p_account_id;

    -- 3. Insert CONTACT
    INSERT INTO `contact` (
        account_id, fullname, email, phone, is_primary,
        status_id, created_at, created_by, updated_at, updated_by
    ) VALUES (
        p_account_id, v_contact_name, v_email, v_phone, 1,
        1, NOW(), p_user_id, NOW(), p_user_id
    );

    SET p_contact_id = LAST_INSERT_ID();

    -- 4. (🔥 BEST PRACTICE) Auto create OPPORTUNITY
    INSERT INTO `opportunity` (
        account_id, contact_id, name, stage,
        amount, probability, close_date,
        created_at, created_by, updated_at, updated_by, owner_user_id
    ) VALUES (
        p_account_id,
        p_contact_id,
        CONCAT('Opportunity - ', v_company_name),
        'Prospecting',
        0,
        10,
        DATE_ADD(CURDATE(), INTERVAL 30 DAY),
        NOW(),
        p_user_id,
                NOW(),
        p_user_id,
        v_owner_user_id
    );

    SET p_opportunity_id = LAST_INSERT_ID();

    -- 5. Update LEAD
    UPDATE `lead`
    SET 
        is_converted = 1,
        converted_account_id = p_account_id,
        converted_contact_id = p_contact_id,
        updated_at = NOW(),
        updated_by = p_user_id
    WHERE id = p_lead_id;

    COMMIT;

END
SQL
        );
    }
}
