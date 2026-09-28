<?php

use yii\db\Migration;

/**
 * Brings Account in line with the BRD (Account / Contacts / Account Address /
 * Document Attachment):
 *
 * - account_address.address_type: Invoice/Branch -> Billing/Shipping/Office
 *   (existing rows mapped Invoice -> Billing, Branch -> Office).
 * - account.customer_segment: customer segment/channel (B2B2C (ISP), B2C, ...),
 *   same taxonomy as product.customer_type. account.account_type stays and is
 *   labelled "Customer Type" in the UI.
 * - account.assigned_user_id: the individual salesperson (FK user). The existing
 *   owner_user_id keeps pointing to the sales team.
 * - account_attachment: documents attached to an account. Files are stored
 *   outside the web root and served through AccountController.
 * - RBAC: permissions for the attachment actions; downloading is added to
 *   viewApplication so read-only roles can open documents.
 *
 * Plain MySQL 5.5 compatible DDL.
 */
class m260928_130000_account_brd_fields extends Migration
{
    private $attachmentActions = ['uploadattachment', 'downloadattachment', 'deleteattachment'];

    public function up()
    {
        // 1. Address types
        $this->execute("ALTER TABLE `account_address` MODIFY `address_type` ENUM('Invoice','Branch','Billing','Shipping','Office') NOT NULL DEFAULT 'Billing'");
        $this->update('{{%account_address}}', ['address_type' => 'Billing'], ['address_type' => 'Invoice']);
        $this->update('{{%account_address}}', ['address_type' => 'Office'], ['address_type' => 'Branch']);
        $this->execute("ALTER TABLE `account_address` MODIFY `address_type` ENUM('Billing','Shipping','Office') NOT NULL DEFAULT 'Billing'");

        // 2. Customer segment + assigned salesperson
        $this->addColumn('{{%account}}', 'customer_segment', $this->string(50)->null()->after('account_type'));
        $this->createIndex('idx_account_customer_segment', '{{%account}}', 'customer_segment');

        $this->addColumn('{{%account}}', 'assigned_user_id', $this->integer(11)->null()->after('owner_user_id'));
        $this->createIndex('idx_account_assigned_user', '{{%account}}', 'assigned_user_id');
        $this->addForeignKey('fk_account_assigned_user', '{{%account}}', 'assigned_user_id', '{{%user}}', 'id', 'SET NULL', 'NO ACTION');

        // 3. Attachments
        $this->createTable('{{%account_attachment}}', [
            'id'          => $this->primaryKey(),
            'account_id'  => $this->integer(11)->notNull(),
            'file_name'   => $this->string(255)->notNull()->comment('original file name'),
            'stored_name' => $this->string(255)->notNull()->comment('file name on disk'),
            'mime_type'   => $this->string(100)->null(),
            'file_size'   => $this->integer(11)->null()->comment('bytes'),
            'description' => $this->string(255)->null(),
            'created_at'  => $this->dateTime()->null(),
            'created_by'  => $this->integer(11)->null(),
            'updated_at'  => $this->dateTime()->null(),
            'updated_by'  => $this->integer(11)->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $this->createIndex('idx_account_attachment_account', '{{%account_attachment}}', 'account_id');
        $this->addForeignKey('fk_account_attachment_account', '{{%account_attachment}}', 'account_id', '{{%account}}', 'id', 'CASCADE', 'NO ACTION');

        // 4. RBAC
        $auth = Yii::$app->authManager;
        $controllerPerm = $auth->getPermission('backend.sales.account.*');
        foreach ($this->attachmentActions as $action) {
            $name = "backend.sales.account.$action";
            $perm = $auth->getPermission($name);
            if ($perm === null) {
                $perm = $auth->createPermission($name);
                $perm->description = "[Action permission] $name";
                $auth->add($perm);
            }
            if ($controllerPerm !== null && !$auth->hasChild($controllerPerm, $perm)) {
                $auth->addChild($controllerPerm, $perm);
            }
        }
        $viewRole = $auth->getRole('viewApplication');
        $download = $auth->getPermission('backend.sales.account.downloadattachment');
        if ($viewRole !== null && !$auth->hasChild($viewRole, $download)) {
            $auth->addChild($viewRole, $download);
        }
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->attachmentActions as $action) {
            $perm = $auth->getPermission("backend.sales.account.$action");
            if ($perm !== null) {
                $auth->remove($perm);
            }
        }

        $this->dropTable('{{%account_attachment}}');

        $this->dropForeignKey('fk_account_assigned_user', '{{%account}}');
        $this->dropIndex('idx_account_assigned_user', '{{%account}}');
        $this->dropColumn('{{%account}}', 'assigned_user_id');
        $this->dropIndex('idx_account_customer_segment', '{{%account}}');
        $this->dropColumn('{{%account}}', 'customer_segment');

        $this->execute("ALTER TABLE `account_address` MODIFY `address_type` ENUM('Invoice','Branch','Billing','Shipping','Office') NOT NULL DEFAULT 'Invoice'");
        $this->update('{{%account_address}}', ['address_type' => 'Invoice'], ['address_type' => 'Billing']);
        // Shipping has no old equivalent; Office and Shipping both go back to Branch.
        $this->update('{{%account_address}}', ['address_type' => 'Branch'], ['address_type' => ['Office', 'Shipping']]);
        $this->execute("ALTER TABLE `account_address` MODIFY `address_type` ENUM('Invoice','Branch') NOT NULL DEFAULT 'Invoice'");
    }
}
