<?php

use yii\db\Migration;

/**
 * Adds `parent_product_id` to `product` so a Product can represent a Sub Product
 * (variant) of another Product, e.g. "Vision+ TV" -> "Vision+ TV - With STB (Google Certified)".
 */
class m260911_160307_add_parent_product_id_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}', 'parent_product_id', $this->integer()->null()->after('category_id'));

        $this->addForeignKey(
            'fk_product_parent_product',
            '{{%product}}',
            'parent_product_id',
            '{{%product}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('idx_product_parent_product_id', '{{%product}}', 'parent_product_id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_product_parent_product', '{{%product}}');
        $this->dropIndex('idx_product_parent_product_id', '{{%product}}');
        $this->dropColumn('{{%product}}', 'parent_product_id');
    }
}
