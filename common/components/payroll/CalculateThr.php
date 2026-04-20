<?php
namespace common\components\payroll;

use yii\base\Component;

use common\modules\payroll\models\PayrollDetailThr;
use common\modules\payroll\models\PayrollThr;
// use common\modules\payroll\models\PayrollDetailL3;
// use common\modules\payroll\models\PayrollDetailL1;
// use common\modules\payroll\models\BuktiPotongPph21;
// use common\modules\payroll\models\Formulir1721A1;
// use common\modules\payroll\models\BpjsFiling;


class CalculateThr extends Component
{
    public static function PayrollGenerateSingle($employee_id, $year, $month, $period_code, $status_id, $user_id)
    {
        $generate_mode = 'Single';
        $list_employee_id = implode(',', $employee_id);

        // Delete exec from Payroll Model

        self::insertSalaryToPayroll($generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // DATA

        self::sumByCategory($category_id=1, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // FIXED_INCOME

        // self::sumByCategory($category_id=2, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // VAR_INCOME

        // self::sumByCategoryWithSign($item_id=46, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // OTHER_INCOME

        // self::sumCodeWithSign(0, 0, $item_id=11, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // ACTUAL_SALARY

        // /**
        //  * BPJS PERUSAHAAN
        //  */
        // self::calculateWithCapBefore($item_id=12, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JHT_PERUSAHAAN
        // self::calculateWithCapBefore($item_id=13, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JP_PERUSAHAAN
        // self::calculateWithCapBefore($item_id=14, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JKM
        // self::calculateWithCapBefore($item_id=15, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JKK
        // self::calculateWithCapBefore($item_id=16, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BPJS_KES_PERUSAHAAN


        // /**
        //  * BPJS KARYAWAN
        //  */
        // self::calculateWithCapBefore($item_id=32, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JHT_KARY
        // self::calculateWithCapBefore($item_id=33, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JP_KARY
        // self::calculateWithCapBefore($item_id=34, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BPJS_KES_KARY

        // /**
        //  * MANUAL QUERY
        //  */
        // // JKK -> Relate other table
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, ROUND(t4.amount * t.amount,0) AS amount, t3.type, CONCAT('base=',t.item_code,';percent=',t3.percent,';cap=',t3.cap) AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id AND t.employee_id IN ($list_employee_id) INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 15 INNER JOIN jkk t4 ON t2.jkk_id = t4.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier";
        // \Yii::$app->db->createCommand($sql)->execute();

        // self::sumCodeWithSign(0, 0, $item_id=47, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // EMPLOYER_BPJS
        
        // self::sumCodeWithSign(0, 0, $item_id=48, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // EMPLOYEE_BPJS
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=51, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // EMPLOYEE_BPJS_YEAR

        // self::sumCodeWithSign(0, 0, $item_id=62, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JP_JHT_KARY
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=63, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // JP_JHT_KARY_YEAR
        
        // self::sumCodeWithSign(0, 0, $item_id=26, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BRUTO
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=49, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BRUTO_YEAR

        // self::calculateWithCapAfterResult(0, 0, $item_id=50, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BIAYA_JABATAN
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=60, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BIAYA_JABATAN_YEAR

        // self::sumCodeWithSign(0, 0, $item_id=28, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BRUTO_TAX
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=61, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // BRUTO_TAX_YEAR

        // // TER  -> TER Category
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, description, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.id, '$period_code' AS period_code, t4.code, t4.name, t4.code, t3.ter, t4.type, NULL, t4.display_order, 'Batch', t4.slip_display, t4.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM v_employee t INNER JOIN ptkp t2 ON t2.id = t.ptkp_id INNER JOIN ter t3 ON t3.id = t2.ter_id INNER JOIN payroll_item t4 ON t4.status_id = 1 AND t4.id = 29 AND t4.monthly_exec <> $month INNER JOIN v_employee_profile_item t5 ON t.id IN ($list_employee_id) AND t.id = t5.employee_id AND t5.item_id = t4.id";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // TER_RATE
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, CASE WHEN t3.monthly_exec = $month THEN 0 ELSE t4.ter END AS amount, t3.type, CONCAT('base=',t.item_code,';percent=',t3.percent,';cap=',t3.cap) AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 30 AND t3.monthly_exec <> $month INNER JOIN ter_prosen t4 ON t2.ptkp_id = t4.ptkp_id AND t.amount BETWEEN t4.bruto_from AND t4.bruto_to INNER JOIN v_employee_profile_item t5 ON t2.id IN ($list_employee_id) AND t2.id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // NETO_YEAR
        // self::sumCodeWithSign($year, $month, $item_id=52, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id);

        // // PTKP
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, t4.value AS amount, t3.type, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id IN ($list_employee_id) AND t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 40 INNER JOIN ptkp t4 ON t2.ptkp_id = t4.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t3.monthly_exec = $month GROUP BY t2.id";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // PKP
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t5.code, FLOOR((t.amount - t4.value) / 1000) * 1000 AS amount, t3.type, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id IN ($list_employee_id) AND t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 53 INNER JOIN ptkp t4 ON t2.ptkp_id = t4.id INNER JOIN payroll_category t5 ON t3.category_id = t5.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier AND ( (t6.payroll_mode = 'GROSS_UP' AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month) OR (t6.payroll_mode <> 'GROSS_UP') )";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // PPH21_YEAR
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t5.code, SUM(CASE WHEN t.amount > pp.batas_bawah THEN LEAST(IFNULL(pp.batas_atas, t.amount),t.amount) - pp.batas_bawah ELSE 0 END * pp.tarif) AS amount, t3.type, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id IN ($list_employee_id) AND t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 54 INNER JOIN pph21_tarif_progresif pp ON t.amount > pp.batas_bawah INNER JOIN payroll_category t5 ON t3.category_id = t5.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier AND t3.monthly_exec = $month GROUP BY t.employee_id";
        // \Yii::$app->db->createCommand($sql)->execute();

        // self::sumPeriodBaseMultiplier($year, $month, $item_id=44, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_JAN_NOV

        // // self::multiplyCode(0, 0, $item_id=31, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_TER_GROSS_UP

        // self::sumCodeWithSign($year, $month, $item_id=38, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_DEC
        // self::sumCodeWithSign($year, $month, $item_id=22, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_DEC_NET
        // self::fixedValue(0, 0, $item_id=56, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_NET

        // self::multiplyCode($year, $month, $item_id=43, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_GROSS_UP
        // self::multiplyCode($year, $month, $item_id=64, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_NET_EMPLOYER
        // self::multiplyCode($year, $month, $item_id=39, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21_GROSS

        // self::sumCodeWithSign(0, 0, $item_id=31, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // PPH21

        // // NON_NPWP
        // $sql = "UPDATE payroll_detail_thr t INNER JOIN payroll_item t3 ON t3.status_id = 1 INNER JOIN v_employee_profile_item t5 ON t.employee_id IN ($list_employee_id) AND t.employee_id = t5.employee_id AND t5.item_id = t3.id INNER JOIN payroll_item t4 ON t4.id = 58 AND FIND_IN_SET(t.item_code, t4.item_code) > 0 SET t.amount = t.amount * t4.default_value WHERE t.status_id = 1 AND t.period_code = '$period_code' AND t5.is_npwp = 0";
        // \Yii::$app->db->createCommand($sql)->execute();

        // self::sumCodeWithSign(0, 0, $item_id=57, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // TUNJ_PPH21

        // self::sumCodeMultiplier(0, 0, $item_id=27, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // EMPLOYER_COST

        // self::sumCodeMultiplier(0, 0, $item_id=36, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // TOTAL_POTONGAN

        // self::updateComponent(0, 0, $item_id=55, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // LOOP_BRUTO

        // self::sumCodeWithSign(0, 0, $item_id=37, $generate_mode, $period_code, $status_id, $user_id, $list_employee_id); // THP
    }

    public static function PayrollGenerateBatch($year, $month, $period_code, $status_id, $user_id)
    {
        /*
        1. DATA
        2. INCOME_FIXED dan INCOME_VAR Pola Sama
        3. ACTUAL_SALARY
         */
        $generate_mode = 'Batch';

        // $sql = "DELETE FROM payroll_detail_thr WHERE period_code = '$period_code' AND status_id = 1";
        // \Yii::$app->db->createCommand($sql)->execute();

        self::insertSalaryToPayroll($generate_mode, $period_code, $status_id, $user_id); // DATA

        self::sumByCategory($category_id=1, $generate_mode, $period_code, $status_id, $user_id); // FIXED_INCOME

        // self::sumByCategory($category_id=2, $generate_mode, $period_code, $status_id, $user_id); // VAR_INCOME

        // self::sumByCategoryWithSign($item_id=46, $generate_mode, $period_code, $status_id, $user_id); // OTHER_INCOME

        // self::sumCodeWithSign(0, 0, $item_id=11, $generate_mode, $period_code, $status_id, $user_id); // ACTUAL_SALARY
        
        // /**
        //  * BPJS PERUSAHAAN
        //  */
        // self::calculateWithCapBefore($item_id=12, $generate_mode, $period_code, $status_id, $user_id); // JHT_PERUSAHAAN
        // self::calculateWithCapBefore($item_id=13, $generate_mode, $period_code, $status_id, $user_id); // JP_PERUSAHAAN
        // self::calculateWithCapBefore($item_id=14, $generate_mode, $period_code, $status_id, $user_id); // JKM
        // self::calculateWithCapBefore($item_id=15, $generate_mode, $period_code, $status_id, $user_id); // JKK
        // self::calculateWithCapBefore($item_id=16, $generate_mode, $period_code, $status_id, $user_id); // BPJS_KES_PERUSAHAAN


        // /**
        //  * BPJS KARYAWAN
        //  */
        // self::calculateWithCapBefore($item_id=32, $generate_mode, $period_code, $status_id, $user_id); // JHT_KARY
        // self::calculateWithCapBefore($item_id=33, $generate_mode, $period_code, $status_id, $user_id); // JP_KARY
        // self::calculateWithCapBefore($item_id=34, $generate_mode, $period_code, $status_id, $user_id); // BPJS_KES_KARY

        // /**
        //  * MANUAL QUERY
        //  */
        // // JKK -> Relate other table
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, ROUND(t4.amount * t.amount,0) AS amount, t3.type, CONCAT('base=',t.item_code,';percent=',t3.percent,';cap=',t3.cap) AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 15 INNER JOIN jkk t4 ON t2.jkk_id = t4.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier";
        // \Yii::$app->db->createCommand($sql)->execute();

        // self::sumCodeWithSign(0, 0, $item_id=47, $generate_mode, $period_code, $status_id, $user_id); // EMPLOYER_BPJS
        
        // self::sumCodeWithSign(0, 0, $item_id=48, $generate_mode, $period_code, $status_id, $user_id); // EMPLOYEE_BPJS
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=51, $generate_mode, $period_code, $status_id, $user_id); // EMPLOYEE_BPJS_YEAR

        // self::sumCodeWithSign(0, 0, $item_id=62, $generate_mode, $period_code, $status_id, $user_id); // JP_JHT_KARY
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=63, $generate_mode, $period_code, $status_id, $user_id); // JP_JHT_KARY_YEAR
        
        // self::sumCodeWithSign(0, 0, $item_id=26, $generate_mode, $period_code, $status_id, $user_id); // BRUTO
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=49, $generate_mode, $period_code, $status_id, $user_id); // BRUTO_YEAR

        // self::calculateWithCapAfterResult(0, 0, $item_id=50, $generate_mode, $period_code, $status_id, $user_id); // BIAYA_JABATAN
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=60, $generate_mode, $period_code, $status_id, $user_id); // BIAYA_JABATAN_YEAR

        // self::sumCodeWithSign(0, 0, $item_id=28, $generate_mode, $period_code, $status_id, $user_id); // BRUTO_TAX
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=61, $generate_mode, $period_code, $status_id, $user_id); // BRUTO_TAX_YEAR

        // // TER  -> TER Category
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, description, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.id, '$period_code' AS period_code, t4.code, t4.name, t4.code, t3.ter, t4.type, NULL, t4.display_order, 'Batch', t4.slip_display, t4.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM v_employee t INNER JOIN ptkp t2 ON t2.id = t.ptkp_id INNER JOIN ter t3 ON t3.id = t2.ter_id INNER JOIN payroll_item t4 ON t4.status_id = 1 AND t4.id = 29 AND t4.monthly_exec <> $month INNER JOIN v_employee_profile_item t5 ON t.id = t5.employee_id AND t5.item_id = t4.id";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // TER_RATE
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, CASE WHEN t3.monthly_exec = $month THEN 0 ELSE t4.ter END AS amount, t3.type, CONCAT('base=',t.item_code,';percent=',t3.percent,';cap=',t3.cap) AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 30 AND t3.monthly_exec <> $month INNER JOIN ter_prosen t4 ON t2.ptkp_id = t4.ptkp_id AND t.amount BETWEEN t4.bruto_from AND t4.bruto_to INNER JOIN v_employee_profile_item t5 ON t2.id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // NETO_YEAR
        // self::sumCodeWithSign($year, $month, $item_id=52, $generate_mode, $period_code, $status_id, $user_id);

        // // PTKP
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, t4.value AS amount, t3.type, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 40 INNER JOIN ptkp t4 ON t2.ptkp_id = t4.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t3.monthly_exec = $month GROUP BY t2.id";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // PKP
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t5.code, FLOOR((t.amount - t4.value) / 1000) * 1000 AS amount, t3.type, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 53 INNER JOIN ptkp t4 ON t2.ptkp_id = t4.id INNER JOIN payroll_category t5 ON t3.category_id = t5.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier AND ( (t6.payroll_mode = 'GROSS_UP' AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month) OR (t6.payroll_mode <> 'GROSS_UP') )";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // PPH21_YEAR
        // $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t5.code, SUM(CASE WHEN t.amount > pp.batas_bawah THEN LEAST(IFNULL(pp.batas_atas, t.amount),t.amount) - pp.batas_bawah ELSE 0 END * pp.tarif) AS amount, t3.type, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = 54 INNER JOIN pph21_tarif_progresif pp ON t.amount > pp.batas_bawah INNER JOIN payroll_category t5 ON t3.category_id = t5.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND t.item_code = t3.base_multiplier AND t3.monthly_exec = $month GROUP BY t.employee_id";
        // \Yii::$app->db->createCommand($sql)->execute();
        
        // self::sumPeriodBaseMultiplier($year, $month, $item_id=44, $generate_mode, $period_code, $status_id, $user_id); // PPH21_JAN_NOV

        // // self::multiplyCode(0, 0, $item_id=31, $generate_mode, $period_code, $status_id, $user_id); // PPH21_TER_GROSS_UP

        // self::sumCodeWithSign($year, $month, $item_id=38, $generate_mode, $period_code, $status_id, $user_id); // PPH21_DEC
        // self::sumCodeWithSign($year, $month, $item_id=22, $generate_mode, $period_code, $status_id, $user_id); // PPH21_DEC_NET
        // self::fixedValue(0, 0, $item_id=56, $generate_mode, $period_code, $status_id, $user_id); // PPH21_NET

        // self::multiplyCode($year, $month, $item_id=43, $generate_mode, $period_code, $status_id, $user_id); // PPH21_GROSS_UP
        // self::multiplyCode($year, $month, $item_id=64, $generate_mode, $period_code, $status_id, $user_id); // PPH21_NET_EMPLOYER
        // self::multiplyCode($year, $month, $item_id=39, $generate_mode, $period_code, $status_id, $user_id); // PPH21_GROSS

        // self::sumCodeWithSign(0, 0, $item_id=31, $generate_mode, $period_code, $status_id, $user_id); // PPH21
        
        // // NON_NPWP
        // $sql = "UPDATE payroll_detail_thr t INNER JOIN payroll_item t3 ON t3.status_id = 1 INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id INNER JOIN payroll_item t4 ON t4.id = 58 AND FIND_IN_SET(t.item_code, t4.item_code) > 0 SET t.amount = t.amount * t4.default_value WHERE t.status_id = 1 AND t.period_code = '$period_code' AND t5.is_npwp = 0";
        // \Yii::$app->db->createCommand($sql)->execute();

        // self::sumCodeWithSign(0, 0, $item_id=57, $generate_mode, $period_code, $status_id, $user_id); // TUNJ_PPH21

        // self::sumCodeMultiplier(0, 0, $item_id=27, $generate_mode, $period_code, $status_id, $user_id); // EMPLOYER_COST

        // self::sumCodeMultiplier(0, 0, $item_id=36, $generate_mode, $period_code, $status_id, $user_id); // TOTAL_POTONGAN

        // self::updateComponent(0, 0, $item_id=55, $generate_mode, $period_code, $status_id, $user_id); // LOOP_BRUTO

        // self::sumCodeWithSign(0, 0, $item_id=37, $generate_mode, $period_code, $status_id, $user_id); // THP
    }

    /**
     * Basic salary
     * Fixed allowance
     * Variable alowance
    */
    public static function insertSalaryToPayroll($generate_mode, $period_code, $status_id, $user_id, $list_employee_id=array())
    {
        if($list_employee_id){
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.id AS employee_id, '$period_code' AS period_code, t3.code, t3.name, t4.code, t2.amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM v_employee t INNER JOIN salary t2 ON t.id IN ($list_employee_id) AND t.id = t2.employee_id AND t2.status_id = 1 INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t2.payroll_item_id = t3.id AND t3.type = 'DATA' INNER JOIN payroll_category t4 ON t3.category_id = t4.id INNER JOIN v_employee_profile_item t5 ON t.id = t5.employee_id AND t5.item_id = t3.id";
        }else{
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.id AS employee_id, '$period_code' AS period_code, t3.code, t3.name, t4.code, t2.amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM v_employee t INNER JOIN salary t2 ON t.id = t2.employee_id AND t2.status_id = 1 INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t2.payroll_item_id = t3.id AND t3.type = 'DATA' INNER JOIN payroll_category t4 ON t3.category_id = t4.id INNER JOIN v_employee_profile_item t5 ON t.id = t5.employee_id AND t5.item_id = t3.id";
        }
        \Yii::$app->db->createCommand($sql)->execute(); 
    }

    /**
     * Summary by Category
    */
    public static function sumByCategory($category_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t2.code, SUM(t.amount), t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t4.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_category t2 ON t2.id = $category_id AND t.category_code = t2.code INNER JOIN payroll_item t3 ON t.category_code = t3.code INNER JOIN v_employee_profile_item t4 ON t.employee_id IN ($employee_id) AND t.employee_id = t4.employee_id AND t4.item_id = t3.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
        }else{
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t2.code, SUM(t.amount), t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t4.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_category t2 ON t2.id = $category_id AND t.category_code = t2.code INNER JOIN payroll_item t3 ON t.category_code = t3.code INNER JOIN v_employee_profile_item t4 ON t.employee_id = t4.employee_id AND t4.item_id = t3.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Summary by Category + SIGN
    */
    public static function sumByCategoryWithSign($item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t4.code, t4.name, t4.code, SUM(CASE WHEN t3.code = t4.code AND t2.sign='PLUS' THEN t.amount ELSE 0 END) - SUM(CASE WHEN t3.code = t4.code AND t2.sign='MINUS' THEN t.amount ELSE 0 END) AS amount, t4.type, t4.display_order, '$generate_mode', t4.slip_display, t4.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t2 ON t.employee_id IN ($employee_id) AND t.item_code = t2.code AND t2.status_id = 1 INNER JOIN payroll_category t3 ON t2.category_id = t3.id INNER JOIN payroll_item t4 ON t4.status_id = 1 AND t4.id = $item_id INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t4.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
        }else{
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t4.code, t4.name, t4.code, SUM(CASE WHEN t3.code = t4.code AND t2.sign='PLUS' THEN t.amount ELSE 0 END) - SUM(CASE WHEN t3.code = t4.code AND t2.sign='MINUS' THEN t.amount ELSE 0 END) AS amount, t4.type, t4.display_order, '$generate_mode', t4.slip_display, t4.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t2 ON t.item_code = t2.code AND t2.status_id = 1 INNER JOIN payroll_category t3 ON t2.category_id = t3.id INNER JOIN payroll_item t4 ON t4.status_id = 1 AND t4.id = $item_id INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t4.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Summary by Code
    */
    public static function sumCodeMultiplier($year, $month, $item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, CASE WHEN t3.sign2 = 'MULTIPLY' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) * t3.period_multiplier WHEN t3.sign2 = 'DEVIDE' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) / NULLIF(t3.period_multiplier,0) ELSE SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) END AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN v_employee_profile_item t5 ON t.employee_id IN ($employee_id) AND t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND ( (t5.payroll_mode = 'GROSS_UP' AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month) OR (t5.payroll_mode <> 'GROSS_UP') ) GROUP BY t.employee_id";
            }else{
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, CASE WHEN t3.sign2 = 'MULTIPLY' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) * t3.period_multiplier WHEN t3.sign2 = 'DEVIDE' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) / NULLIF(t3.period_multiplier,0) ELSE SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) END AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN v_employee_profile_item t5 ON t.employee_id IN ($employee_id) AND t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
            }
        }else{
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, CASE WHEN t3.sign2 = 'MULTIPLY' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) * t3.period_multiplier WHEN t3.sign2 = 'DEVIDE' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) / NULLIF(t3.period_multiplier,0) ELSE SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) END AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND ( (t5.payroll_mode = 'GROSS_UP' AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month) OR (t5.payroll_mode <> 'GROSS_UP') ) GROUP BY t.employee_id";
            }else{
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, CASE WHEN t3.sign2 = 'MULTIPLY' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) * t3.period_multiplier WHEN t3.sign2 = 'DEVIDE' THEN SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) / NULLIF(t3.period_multiplier,0) ELSE SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 THEN t.amount ELSE 0 END) END AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
            }
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Summary by Code + SIGN
    */
    public static function sumCodeWithSign($year, $month, $item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, ABS(SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'PLUS' THEN t.amount ELSE 0 END - CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'MINUS' THEN t.amount ELSE 0 END)) AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.employee_id IN ($employee_id) AND t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN payroll_item t2 ON t.item_code = t2.code INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month GROUP BY t.employee_id";
            }else{
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, ABS(SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'PLUS' THEN t.amount ELSE 0 END - CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'MINUS' THEN t.amount ELSE 0 END)) AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.employee_id IN ($employee_id) AND t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN payroll_item t2 ON t.item_code = t2.code INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
            }
        }else{
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, ABS(SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'PLUS' THEN t.amount ELSE 0 END - CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'MINUS' THEN t.amount ELSE 0 END)) AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN payroll_item t2 ON t.item_code = t2.code INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month GROUP BY t.employee_id";
            }else{
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t.employee_id, '$period_code' AS period_code, t3.code, t3.name, t3.code, ABS(SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'PLUS' THEN t.amount ELSE 0 END - CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'MINUS' THEN t.amount ELSE 0 END)) AS amount, t3.type, t3.display_order, '$generate_mode', t3.slip_display, t3.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN payroll_item t2 ON t.item_code = t2.code INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' GROUP BY t.employee_id";
            }
        }
        \Yii::$app->db->createCommand($sql)->execute();

        $sql = "UPDATE payroll_detail_thr t INNER JOIN payroll_item t2 ON t.item_code = t2.code SET t.amount = 0 WHERE t.period_code = '$period_code' AND t2.id = $item_id AND t.amount < 0";
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Calculate with Cap
    */
    public static function calculateWithCapBefore($item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, pi.code, pi.name, pc.code, ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) AS amount, pi.type, CONCAT('base=',t.item_code,';percent=',pi.percent,';cap=',pi.cap) AS trace, pi.display_order, '$generate_mode', pi.slip_display, pi.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN payroll_category pc ON pi.category_id = pc.id INNER JOIN v_employee_profile_item t5 ON t.employee_id IN ($employee_id) AND t.employee_id = t5.employee_id AND t5.item_id = pi.id WHERE t.period_code = '$period_code' AND t.item_code = pi.base_multiplier AND ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) > 0";
        }else{
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, pi.code, pi.name, pc.code, ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) AS amount, pi.type, CONCAT('base=',t.item_code,';percent=',pi.percent,';cap=',pi.cap) AS trace, pi.display_order, '$generate_mode', pi.slip_display, pi.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN payroll_category pc ON pi.category_id = pc.id INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = pi.id WHERE t.period_code = '$period_code' AND t.item_code = pi.base_multiplier AND ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) > 0";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Calculate with Cap After
    */
    public static function calculateWithCapAfterResult($year, $month, $item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, pi.code, pi.name, pc.code, ROUND(LEAST(IFNULL(t.amount*pi.percent, 0), COALESCE(pi.cap, IFNULL(t.amount*pi.percent, 0))),0) AS amount,pi.type, CONCAT('base=',t.item_code,';percent=',pi.percent,';cap=',pi.cap) AS trace, pi.display_order, '$generate_mode', pi.slip_display, pi.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN payroll_category pc ON pi.category_id = pc.id INNER JOIN v_employee_profile_item t5 ON t.employee_id IN ($employee_id) AND t.employee_id = t5.employee_id AND t5.item_id = pi.id WHERE t.period_code = '$period_code' AND t.item_code = pi.base_multiplier AND ( (t5.payroll_mode = 'GROSS_UP' AND pi.monthly_exec <> 0 AND pi.monthly_exec = $month) OR (t5.payroll_mode <> 'GROSS_UP') ) AND ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) > 0";
            }else{
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, pi.code, pi.name, pc.code, ROUND(LEAST(IFNULL(t.amount*pi.percent, 0), COALESCE(pi.cap, IFNULL(t.amount*pi.percent, 0))),0) AS amount,pi.type, CONCAT('base=',t.item_code,';percent=',pi.percent,';cap=',pi.cap) AS trace, pi.display_order, '$generate_mode', pi.slip_display, pi.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN payroll_category pc ON pi.category_id = pc.id INNER JOIN v_employee_profile_item t5 ON t.employee_id IN ($employee_id) AND t.employee_id = t5.employee_id AND t5.item_id = pi.id WHERE t.period_code = '$period_code' AND t.item_code = pi.base_multiplier AND ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) > 0";
            }
        }else{
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, pi.code, pi.name, pc.code, ROUND(LEAST(IFNULL(t.amount*pi.percent, 0), COALESCE(pi.cap, IFNULL(t.amount*pi.percent, 0))),0) AS amount,pi.type, CONCAT('base=',t.item_code,';percent=',pi.percent,';cap=',pi.cap) AS trace, pi.display_order, '$generate_mode', pi.slip_display, pi.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN payroll_category pc ON pi.category_id = pc.id INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = pi.id WHERE t.period_code = '$period_code' AND t.item_code = pi.base_multiplier AND ( (t5.payroll_mode = 'GROSS_UP' AND pi.monthly_exec <> 0 AND pi.monthly_exec = $month) OR (t5.payroll_mode <> 'GROSS_UP') ) AND ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) > 0";
            }else{
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, pi.code, pi.name, pc.code, ROUND(LEAST(IFNULL(t.amount*pi.percent, 0), COALESCE(pi.cap, IFNULL(t.amount*pi.percent, 0))),0) AS amount,pi.type, CONCAT('base=',t.item_code,';percent=',pi.percent,';cap=',pi.cap) AS trace, pi.display_order, '$generate_mode', pi.slip_display, pi.slip_position, t5.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN payroll_category pc ON pi.category_id = pc.id INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = pi.id WHERE t.period_code = '$period_code' AND t.item_code = pi.base_multiplier AND ROUND(pi.percent * LEAST(t.amount, COALESCE(pi.cap, t.amount)),0) > 0";
            }
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Multiply by Code
    */
    public static function multiplyCode($year, $month, $item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t5.code, CASE WHEN SUM(CASE WHEN t.amount = 0 THEN 1 ELSE 0 END) > 0 THEN 0 ELSE EXP(SUM(LOG(NULLIF(t.amount,0)))) END AS amount, t3.type, CONCAT('base=',t.item_code,';percent=',t3.percent,';cap=',t3.cap) AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id IN ($employee_id) AND t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = $item_id AND t3.monthly_exec <> $month INNER JOIN payroll_category t5 ON t3.category_id = t5.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND FIND_IN_SET(t.item_code, t3.item_code) GROUP BY t.employee_id";
        }else{
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t5.code, CASE WHEN SUM(CASE WHEN t.amount = 0 THEN 1 ELSE 0 END) > 0 THEN 0 ELSE EXP(SUM(LOG(NULLIF(t.amount,0)))) END AS amount, t3.type, CONCAT('base=',t.item_code,';percent=',t3.percent,';cap=',t3.cap) AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = $item_id AND t3.monthly_exec <> $month INNER JOIN payroll_category t5 ON t3.category_id = t5.id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE t.period_code = '$period_code' AND FIND_IN_SET(t.item_code, t3.item_code) GROUP BY t.employee_id";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Multiply by Code
    */
    public static function sumPeriodBaseMultiplier($year, $month, $item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, SUM(t.amount) AS amount, t3.type, NULL AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t.employee_id IN ($employee_id) AND t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = $item_id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE FIND_IN_SET(t.item_code, t3.base_multiplier) > 0 AND DATE_FORMAT(STR_TO_DATE(t.period_code, '%Y-%m'), '%m') <= $month AND DATE_FORMAT(STR_TO_DATE(t.period_code, '%Y-%m'), '%Y') = $year AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month GROUP BY t.employee_id";
            }
        }else{
            if($month){
                $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT t2.id, '$period_code' AS period_code, t3.code, t3.name, t3.code, SUM(t.amount) AS amount, t3.type, NULL AS trace, t3.display_order, 'Batch', t3.slip_display, t3.slip_position, t6.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr t INNER JOIN v_employee t2 ON t2.id = t.employee_id INNER JOIN payroll_item t3 ON t3.status_id = 1 AND t3.id = $item_id INNER JOIN v_employee_profile_item t6 ON t2.id = t6.employee_id AND t6.item_id = t3.id WHERE FIND_IN_SET(t.item_code, t3.base_multiplier) > 0 AND DATE_FORMAT(STR_TO_DATE(t.period_code, '%Y-%m'), '%m') <= $month AND DATE_FORMAT(STR_TO_DATE(t.period_code, '%Y-%m'), '%Y') = $year AND t3.monthly_exec <> 0 AND t3.monthly_exec = $month GROUP BY t.employee_id";
            }
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Default/fixed value
    */
    public static function fixedValue($year, $month, $item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT ve.id, '$period_code' AS period_code, pi.code, pi.name, pi.code, pi.default_value AS amount, pi.type, NULL AS trace, pi.display_order, 'Batch', pi.slip_display, pi.slip_position, epi.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM v_employee ve INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN v_employee_profile_item epi ON ve.id IN ($employee_id) AND ve.id = epi.employee_id AND epi.item_id = pi.id";
        }else{
            $sql = "INSERT INTO payroll_detail_thr (employee_id, period_code, item_code, item_name, category_code, amount, source, trace, display_order, generate_mode, slip_display, slip_position, profile_id, status_id, created_at, created_by, updated_at, updated_by) SELECT ve.id, '$period_code' AS period_code, pi.code, pi.name, pi.code, pi.default_value AS amount, pi.type, NULL AS trace, pi.display_order, 'Batch', pi.slip_display, pi.slip_position, epi.profile_id, $status_id, NOW(), $user_id, NOW(), $user_id FROM v_employee ve INNER JOIN payroll_item pi ON pi.status_id = 1 AND pi.id = $item_id INNER JOIN v_employee_profile_item epi ON ve.id = epi.employee_id AND epi.item_id = pi.id";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Update specifik field
    */
    public static function updateComponent($year, $month, $item_id, $generate_mode, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            $sql = "UPDATE payroll_detail_thr x INNER JOIN (SELECT t.employee_id, t.period_code, t.item_code, SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'PLUS' THEN t.amount ELSE 0 END - CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'MINUS' THEN t.amount ELSE 0 END) AS amount FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN payroll_item t2 ON t.item_code = t2.code INNER JOIN v_employee_profile_item t5 ON t.employee_id IN ($employee_id) AND t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND FIND_IN_SET(t.item_code, t3.item_code) > 0 GROUP BY t.employee_id) y ON x.employee_id = y.employee_id AND x.period_code = y.period_code AND x.item_code = y.item_code SET x.amount = y.amount WHERE x.period_code = '$period_code'";
        }else{
            $sql = "UPDATE payroll_detail_thr x INNER JOIN (SELECT t.employee_id, t.period_code, t.item_code, SUM(CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'PLUS' THEN t.amount ELSE 0 END - CASE WHEN FIND_IN_SET(t.item_code, t3.item_code) > 0 AND t2.sign = 'MINUS' THEN t.amount ELSE 0 END) AS amount FROM payroll_detail_thr t INNER JOIN payroll_item t3 ON t.status_id = 1 AND t3.status_id = 1 AND t3.id = $item_id INNER JOIN payroll_item t2 ON t.item_code = t2.code INNER JOIN v_employee_profile_item t5 ON t.employee_id = t5.employee_id AND t5.item_id = t3.id WHERE t.period_code = '$period_code' AND FIND_IN_SET(t.item_code, t3.item_code) > 0 GROUP BY t.employee_id) y ON x.employee_id = y.employee_id AND x.period_code = y.period_code AND x.item_code = y.item_code SET x.amount = y.amount WHERE x.period_code = '$period_code'";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    public static function PayrollApproveBatch($year, $month, $period_code, $status_id, $user_id)
    {
        PayrollDetailThr::updateAll([
                'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
            ],
            [
                'and', ['status_id' => 1], ['period_code' => $period_code]
        ]);

        PayrollThr::updateAll([
                'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
            ],
            [
                'and', ['status_id' => 1], ['period_code' => $period_code]
        ]);

        // PayrollDetailL3::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['period_code' => $period_code]
        // ]);

        // PayrollDetailL1::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['period_code' => $period_code]
        // ]);

        // BuktiPotongPph21::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['masa_pajak' => $month], ['tahun_pajak' => $year]
        // ]);

        // Formulir1721A1::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['tahun_pajak' => $year]
        // ]);

        // BpjsFiling::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['period_code' => $period_code]
        // ]);
    }

    public static function PayrollApproveSingle($employee_id, $year, $month, $period_code, $status_id, $user_id)
    {
        PayrollDetailThr::updateAll([
                'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
            ],
            [
                'and', ['status_id' => 1], ['period_code' => $period_code], ['employee_id' => $employee_id]
        ]);

        PayrollDetailThr::updateAll([
                'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
            ],
            [
                'and', ['status_id' => 1], ['period_code' => $period_code], ['employee_id' => $employee_id]
        ]);

        // PayrollDetailL3::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['period_code' => $period_code], ['employee_id' => $employee_id]
        // ]);

        // PayrollDetailL1::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['period_code' => $period_code]
        // ]);

        // BuktiPotongPph21::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['masa_pajak' => $month], ['tahun_pajak' => $year], ['employee_id' => $employee_id]
        // ]);

        // Formulir1721A1::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['tahun_pajak' => $year], ['employee_id' => $employee_id]
        // ]);

        // BpjsFiling::updateAll([
        //         'status_id' => 2, 'updated_by' => $user_id, 'updated_at' => new \yii\db\Expression('NOW()')
        //     ],
        //     [
        //         'and', ['status_id' => 1], ['period_code' => $period_code], ['employee_id' => $employee_id]
        // ]);
    }

    public static function PayrollCancelBatch($year, $month, $period_code, $status_id, $user_id, $employee_id=array())
    {
        if($employee_id){
            PayrollDetailThr::deleteAll(
                [
                    'and',
                    ['status_id' => 1],
                    ['period_code' => $period_code],
                    ['employee_id' => $employee_id]
                ]
            );

            PayrollThr::deleteAll(
                [
                    'and',
                    ['status_id' => 1],
                    ['period_code' => $period_code],
                    ['employee_id' => $employee_id]
                ]
            );

            // PayrollDetailL3::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['period_code' => $period_code],
            //         ['employee_id' => $employee_id]
            //     ]
            // );

            // PayrollDetailL1::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['period_code' => $period_code]
            //     ]
            // );

            // BuktiPotongPph21::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['masa_pajak' => $month],
            //         ['tahun_pajak' => $year],
            //         ['employee_id' => $employee_id]
            //     ]
            // );

            // Formulir1721A1::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['tahun_pajak' => $year],
            //         ['employee_id' => $employee_id]
            //     ]
            // );

            // BpjsFiling::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['period_code' => $period_code],
            //         ['employee_id' => $employee_id]
            //     ]
            // );

        }else{
            PayrollDetailThr::deleteAll(
                [
                    'and',
                    ['status_id' => 1],
                    ['period_code' => $period_code]
                ]
            );

            PayrollThr::deleteAll(
                [
                    'and',
                    ['status_id' => 1],
                    ['period_code' => $period_code]
                ]
            );

            // PayrollDetailL3::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['period_code' => $period_code]
            //     ]
            // );

            // PayrollDetailL1::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['period_code' => $period_code]
            //     ]
            // );

            // BuktiPotongPph21::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['masa_pajak' => $month],
            //         ['tahun_pajak' => $year]
            //     ]
            // );

            // Formulir1721A1::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['tahun_pajak' => $year]
            //     ]
            // );

            // BpjsFiling::deleteAll(
            //     [
            //         'and',
            //         ['status_id' => 1],
            //         ['period_code' => $period_code]
            //     ]
            // );
        }
    }

    public static function PayrollHeaderBatch($year, $month, $period_code, $status_id, $user_id, $employee_id=array())
    {
        // $sql = "DELETE FROM payroll WHERE period_code = '$period_code' AND status_id = 1";
        // \Yii::$app->db->createCommand($sql)->execute();

        if($employee_id){
            $list_employee_id = implode(',', $employee_id);
            $sql = "INSERT INTO payroll_thr (employee_id, month, year, period_code, gross, thp, thr_accrual, status_id, created_at, created_by, updated_at, updated_by) SELECT employee_id, $month, $year, period_code, IFNULL(MAX(CASE WHEN item_code = 'FIXED_INCOME' THEN amount END),0) AS gross, IFNULL(MAX(CASE WHEN item_code = 'FIXED_INCOME' THEN amount END),0) AS thp, IFNULL(MAX(CASE WHEN item_code = 'FIXED_INCOME' THEN amount END),0) AS thr_acrual, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr WHERE employee_id IN ($list_employee_id) AND period_code = '$period_code' GROUP BY employee_id";
        }else{
            $sql = "INSERT INTO payroll_thr (employee_id, month, year, period_code, gross, thp, thr_accrual, status_id, created_at, created_by, updated_at, updated_by) SELECT employee_id, $month, $year, period_code, IFNULL(MAX(CASE WHEN item_code = 'FIXED_INCOME' THEN amount END),0) AS gross, IFNULL(MAX(CASE WHEN item_code = 'FIXED_INCOME' THEN amount END),0) AS thp, IFNULL(MAX(CASE WHEN item_code = 'FIXED_INCOME' THEN amount END),0) AS thr_acrual, $status_id, NOW(), $user_id, NOW(), $user_id FROM payroll_detail_thr WHERE period_code = '$period_code' GROUP BY employee_id";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

    public static function PayrollCloseSalary($year, $month, $period_code, $status_id, $user_id)
    {
        // Insert Tunj THR Salary
        $sql = "INSERT INTO salary SELECT 0, pdt.employee_id, pi.id, pdt.amount, 1 AS status_id, 'WEB', 1, NULL, NOW(), $user_id, NOW(), $user_id FROM payroll_item pi INNER JOIN payroll_detail_thr pdt ON pdt.item_code = 'FIXED_INCOME' WHERE pi.tax_nature = 'TIDAK_TERATUR'";
        \Yii::$app->db->createCommand($sql)->execute();

        // // Salary -> is_processed = 1 (Y), 2 = (N)
        // $sql = "UPDATE salary s INNER JOIN payroll_item pi ON s.payroll_item_id = pi.id AND s.status_id = 2 SET s.is_processed = 1, s.processed_at = NOW() WHERE pi.salary_type = 'ONETIME' AND pi.tax_nature = 'TIDAK_TERATUR'";
        // \Yii::$app->db->createCommand($sql)->execute();

        // // Resign employee
        // $sql = "UPDATE employee INNER JOIN payroll b ON a.id = b.employee_id AND a.status_id = 1 AND a.resign_date IS NOT NULL AND b.year = $year AND b.month = $month AND b.component_id = 1 SET a.status_id = 2, a.updated_at = NOW()";
        // \Yii::$app->db->createCommand($sql)->execute();
    }

}
