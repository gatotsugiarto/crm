<?php

use yii\db\Migration;

/**
 * Some products are sold under more than one (Customer Type, Revenue Model)
 * combination at once (e.g. "Vision+ TV" spans B2B2C-Recurring, B2B2C-OneTime,
 * and B2C-Retail). A junction table (`product_customer_segment`) was tried for
 * this, but pricing (`product_price.price`, `product.base_price`) and sales
 * line items (`opportunity_product`, `quotation_item`) are all keyed by a
 * single `product_id` — there was nowhere to hang a distinct price per
 * combination. So instead each combination becomes its own Product row
 * (same name, distinct `code`), keeping `customer_type`/`revenue_model` as
 * plain columns on `product`.
 */
class m260914_170500_split_multi_segment_products extends Migration
{
    /**
     * code => [
     *     'primary' => [customer_type, revenue_model] kept on the existing row,
     *     'extra' => [[code, customer_type, revenue_model], ...] cloned into new rows,
     * ]
     */
    private function splits()
    {
        return [
            'GOOGLE-NETFLIX' => [
                'primary' => ['B2B2C (ISP)', 'Recurring Revenue (Billing Bulanan)'],
                'extra' => [
                    ['GOOGLE-NETFLIX-OT', 'B2B2C (ISP)', 'One Time (1 Tahun di Depan)'],
                ],
            ],
            'LITE' => [
                'primary' => ['B2B2C (ISP)', 'Recurring Revenue (Billing Bulanan)'],
                'extra' => [
                    ['LITE-OT', 'B2B2C (ISP)', 'One Time (1 Tahun di Depan)'],
                ],
            ],
            'GOOGLE-C' => [
                'primary' => ['B2B2C (ISP)', 'Recurring Revenue (Billing Bulanan)'],
                'extra' => [
                    ['GOOGLE-C-OT', 'B2B2C (ISP)', 'One Time (1 Tahun di Depan)'],
                ],
            ],
            'STB' => [
                'primary' => ['B2B2C (ISP)', 'Recurring Revenue (Billing Bulanan)'],
                'extra' => [
                    ['STB-OT', 'B2B2C (ISP)', 'One Time (1 Tahun di Depan)'],
                ],
            ],
            'APP' => [
                'primary' => ['B2B2C (ISP)', 'Recurring Revenue (Billing Bulanan)'],
                'extra' => [
                    ['APP-OT', 'B2B2C (ISP)', 'One Time (1 Tahun di Depan)'],
                ],
            ],
            'VTV' => [
                'primary' => ['B2B2C (ISP)', 'Recurring Revenue (Billing Bulanan)'],
                'extra' => [
                    ['VTV-OT', 'B2B2C (ISP)', 'One Time (1 Tahun di Depan)'],
                    ['VTV-RETAIL', 'B2C', 'Retail (Beli Putus)'],
                ],
            ],
        ];
    }

    public function up()
    {
        foreach ($this->splits() as $code => $split) {
            [$customerType, $revenueModel] = $split['primary'];
            $this->update('{{%product}}', [
                'customer_type' => $customerType,
                'revenue_model' => $revenueModel,
            ], ['code' => $code]);

            foreach ($split['extra'] as [$newCode, $extraCustomerType, $extraRevenueModel]) {
                $this->execute("
                    INSERT INTO {{%product}}
                        (code, name, category_id, parent_product_id, uom_id, type, bundle_price_type,
                         description, base_price, is_bundle_expand, status_id, customer_type, revenue_model,
                         created_at, updated_at)
                    SELECT :newCode, name, category_id, parent_product_id, uom_id, type, bundle_price_type,
                           description, base_price, is_bundle_expand, status_id, :customerType, :revenueModel,
                           NOW(), NOW()
                    FROM {{%product}}
                    WHERE code = :code
                ", [
                    ':newCode' => $newCode,
                    ':customerType' => $extraCustomerType,
                    ':revenueModel' => $extraRevenueModel,
                    ':code' => $code,
                ]);
            }
        }
    }

    public function down()
    {
        $extraCodes = [];
        foreach ($this->splits() as $split) {
            foreach ($split['extra'] as [$newCode, , ]) {
                $extraCodes[] = $newCode;
            }
        }

        $this->delete('{{%product}}', ['code' => $extraCodes]);
    }
}
