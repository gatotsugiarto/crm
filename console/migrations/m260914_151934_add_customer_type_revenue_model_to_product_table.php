<?php

use yii\db\Migration;

/**
 * Adds `customer_type` and `revenue_model` to `product`, so a Product can carry
 * the business's "Revenue MKM" customer-segment/billing-model tags directly
 * (e.g. "B2B2C (ISP)" / "Recurring Revenue (Billing Bulanan)") instead of only
 * being representable via intermediate `parent_product_id` grouping nodes.
 *
 * Free-text VARCHAR rather than ENUM: the set of customer types and revenue
 * models is expected to keep growing (Online to Offline, Hospitality, etc.
 * were all added after the first classification pass), so a rigid ENUM would
 * need a migration every time a new business line shows up.
 */
class m260914_151934_add_customer_type_revenue_model_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}', 'customer_type', $this->string(50)->null()->after('type'));
        $this->addColumn('{{%product}}', 'revenue_model', $this->string(50)->null()->after('customer_type'));

        $this->createIndex('idx_product_customer_type', '{{%product}}', 'customer_type');
        $this->createIndex('idx_product_revenue_model', '{{%product}}', 'revenue_model');
    }

    public function down()
    {
        $this->dropIndex('idx_product_customer_type', '{{%product}}');
        $this->dropIndex('idx_product_revenue_model', '{{%product}}');
        $this->dropColumn('{{%product}}', 'customer_type');
        $this->dropColumn('{{%product}}', 'revenue_model');
    }
}
