<?php
namespace common\components\payroll;

use yii\base\Component;

use Yii;
use yii\db\Query; 
use yii\db\Expression;


class OtherDoc extends Component
{
    public static function BuktiPotong($year, $month, $period_code, $status_id, $user_id, $employee_id=array())
    {
    	// $sql = "DELETE FROM bukti_potong_pph21 WHERE masa_pajak = $month AND tahun_pajak = $year";
        // \Yii::$app->db->createCommand($sql)->execute();

        if($employee_id){
            $list_employee_id = implode(',', $employee_id);
            $kode_objek_pajak = '21-100-01'; // Default
            $sql = "INSERT INTO bukti_potong_pph21 SELECT 0, e.id, p.month AS masa_pajak, p.year AS tahun_pajak, c.npwp AS npwp_perusahaan, c.company AS nama_perusahaan, 
    CASE WHEN e.npwp_id IS NOT NULL THEN e.npwp_id ELSE e.e_number END AS npwp_nik_pegawai, e.fullname AS nama_pegawai,
    CASE WHEN e.employee_status_id < 3 THEN 'TETAP' WHEN e.employee_status_id = 3 THEN 'TIDAK_TETAP' ELSE 'BUKAN_PEGAWAI' END AS status_pegawai, 
    '$kode_objek_pajak' AS kode_objek_pajak, e.ptkp AS status_ptkp, 
    IFNULL(MAX(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount END),0) AS penghasilan_bruto, 
    IFNULL(MAX(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) AS biaya_jabatan,
    IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS iuran_pensiun_jht,
    IFNULL(MAX(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount END),0) - IFNULL(MAX(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) - IFNULL(MAX(CASE WHEN pd.item_code = 'JP_JHT_KARY' THEN pd.amount END),0) AS penghasilan_neto,
    CASE WHEN epp.profile_id > 2 THEN 
    IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
    ELSE IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21' THEN pd.amount END),0) 
    END AS pph21_terutang,
    CASE WHEN epp.profile_id > 2 THEN 0 ELSE IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21' THEN pd.amount END),0) END AS pph21_dipotong_karyawan,
    CASE WHEN epp.profile_id > 2 THEN 
    IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
    ELSE 0 
    END AS pph21_ditanggung_perusahaan, 
    CASE WHEN epp.profile_id = 1 THEN 'GROSS' WHEN epp.profile_id = 2 THEN 'GROSS-UP' ELSE 'NET' END AS skema_pajak, 
    $status_id, NOW(), $user_id, NOW(), $user_id 
    FROM payroll_detail pd INNER JOIN payroll p ON pd.employee_id IN ($list_employee_id) AND pd.employee_id = p.employee_id AND pd.period_code = p.period_code 
    INNER JOIN employee e ON p.employee_id = e.id AND e.status_id = 1 
    INNER JOIN employee_payroll_profile epp ON e.id = epp.employee_id 
    INNER JOIN company c ON e.company_id = c.id 
    WHERE pd.period_code = '$period_code' 
    GROUP BY pd.employee_id";
            \Yii::$app->db->createCommand($sql)->execute();

            $kode_objek_pajak = '21-100-02'; // THR, BONUS
            $sql = "INSERT INTO bukti_potong_pph21 SELECT 
    0, e.id, p.month AS masa_pajak, p.year AS tahun_pajak, c.npwp AS npwp_perusahaan, c.company AS nama_perusahaan,
    CASE WHEN e.npwp_id IS NOT NULL THEN e.npwp_id ELSE e.e_number END AS npwp_nik_pegawai, e.fullname AS nama_pegawai,
    CASE WHEN e.employee_status_id < 3 THEN 'TETAP' WHEN e.employee_status_id = 3 THEN 'TIDAK_TETAP' ELSE 'BUKAN_PEGAWAI' END AS status_pegawai,
    '$kode_objek_pajak' AS kode_objek_pajak, e.ptkp AS status_ptkp,
    pd.amount AS penghasilan_bruto, 
    0 AS biaya_jabatan,
    0 AS iuran_pensiun_jht,
    pd.amount AS penghasilan_neto, 
    LEAST(pd.amount * pi.percent, pi.cap) AS pph21_terutang, 
    CASE WHEN epp.profile_id > 2 THEN 0 ELSE LEAST(pd.amount * pi.percent, pi.cap) END AS pph21_dipotong_karyawan,
    CASE WHEN epp.profile_id > 2 THEN LEAST(pd.amount * pi.percent, pi.cap) ELSE 0 END AS pph21_ditanggung_perusahaan,
    CASE WHEN epp.profile_id = 1 THEN 'GROSS' WHEN epp.profile_id = 2 THEN 'GROSS-UP' ELSE 'NET' END AS skema_pajak,
    $status_id, NOW(), $user_id, NOW(), $user_id 
    FROM payroll_detail pd INNER JOIN payroll p ON pd.employee_id IN ($list_employee_id) AND pd.employee_id = p.employee_id AND pd.period_code = p.period_code
    INNER JOIN employee e ON p.employee_id = e.id AND e.status_id = 1
    INNER JOIN employee_payroll_profile epp ON e.id = epp.employee_id
    INNER JOIN company c ON e.company_id = c.id 
    INNER JOIN payroll_item pi ON pd.item_code = pi.code AND pi.tax_nature = 'TIDAK_TERATUR' AND pd.amount > 0 
    WHERE pd.period_code = '$period_code'
    GROUP BY pd.employee_id";
            \Yii::$app->db->createCommand($sql)->execute();

        }else{

            $kode_objek_pajak = '21-100-01'; // Default
        $sql = "INSERT INTO bukti_potong_pph21 SELECT 0, e.id, p.month AS masa_pajak, p.year AS tahun_pajak, c.npwp AS npwp_perusahaan, c.company AS nama_perusahaan, 
CASE WHEN e.npwp_id IS NOT NULL THEN e.npwp_id ELSE e.e_number END AS npwp_nik_pegawai, e.fullname AS nama_pegawai,
CASE WHEN e.employee_status_id < 3 THEN 'TETAP' WHEN e.employee_status_id = 3 THEN 'TIDAK_TETAP' ELSE 'BUKAN_PEGAWAI' END AS status_pegawai, 
'$kode_objek_pajak' AS kode_objek_pajak, e.ptkp AS status_ptkp, 
IFNULL(MAX(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount END),0) AS penghasilan_bruto, 
IFNULL(MAX(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) AS biaya_jabatan,
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS iuran_pensiun_jht,
IFNULL(MAX(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount END),0) - IFNULL(MAX(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) - IFNULL(MAX(CASE WHEN pd.item_code = 'JP_JHT_KARY' THEN pd.amount END),0) AS penghasilan_neto,
CASE WHEN epp.profile_id > 2 THEN 
IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
ELSE IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21' THEN pd.amount END),0) 
END AS pph21_terutang,
CASE WHEN epp.profile_id > 2 THEN 0 ELSE IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21' THEN pd.amount END),0) END AS pph21_dipotong_karyawan,
CASE WHEN epp.profile_id > 2 THEN 
IFNULL(MAX(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
ELSE 0 
END AS pph21_ditanggung_perusahaan, 
CASE WHEN epp.profile_id = 1 THEN 'GROSS' WHEN epp.profile_id = 2 THEN 'GROSS-UP' ELSE 'NET' END AS skema_pajak, 
$status_id, NOW(), $user_id, NOW(), $user_id 
FROM payroll_detail pd INNER JOIN payroll p ON pd.employee_id = p.employee_id AND pd.period_code = p.period_code 
INNER JOIN employee e ON p.employee_id = e.id AND e.status_id = 1 
INNER JOIN employee_payroll_profile epp ON e.id = epp.employee_id 
INNER JOIN company c ON e.company_id = c.id 
WHERE pd.period_code = '$period_code' 
GROUP BY pd.employee_id";
        \Yii::$app->db->createCommand($sql)->execute();

        $kode_objek_pajak = '21-100-02'; // THR, BONUS
        $sql = "INSERT INTO bukti_potong_pph21 SELECT 
0, e.id, p.month AS masa_pajak, p.year AS tahun_pajak, c.npwp AS npwp_perusahaan, c.company AS nama_perusahaan,
CASE WHEN e.npwp_id IS NOT NULL THEN e.npwp_id ELSE e.e_number END AS npwp_nik_pegawai, e.fullname AS nama_pegawai,
CASE WHEN e.employee_status_id < 3 THEN 'TETAP' WHEN e.employee_status_id = 3 THEN 'TIDAK_TETAP' ELSE 'BUKAN_PEGAWAI' END AS status_pegawai,
'$kode_objek_pajak' AS kode_objek_pajak, e.ptkp AS status_ptkp,
pd.amount AS penghasilan_bruto, 
0 AS biaya_jabatan,
0 AS iuran_pensiun_jht,
pd.amount AS penghasilan_neto, 
LEAST(pd.amount * pi.percent, pi.cap) AS pph21_terutang, 
CASE WHEN epp.profile_id > 2 THEN 0 ELSE LEAST(pd.amount * pi.percent, pi.cap) END AS pph21_dipotong_karyawan,
CASE WHEN epp.profile_id > 2 THEN LEAST(pd.amount * pi.percent, pi.cap) ELSE 0 END AS pph21_ditanggung_perusahaan,
CASE WHEN epp.profile_id = 1 THEN 'GROSS' WHEN epp.profile_id = 2 THEN 'GROSS-UP' ELSE 'NET' END AS skema_pajak,
$status_id, NOW(), $user_id, NOW(), $user_id 
FROM payroll_detail pd INNER JOIN payroll p ON pd.employee_id = p.employee_id AND pd.period_code = p.period_code
INNER JOIN employee e ON p.employee_id = e.id AND e.status_id = 1
INNER JOIN employee_payroll_profile epp ON e.id = epp.employee_id
INNER JOIN company c ON e.company_id = c.id 
INNER JOIN payroll_item pi ON pd.item_code = pi.code AND pi.tax_nature = 'TIDAK_TERATUR' AND pd.amount > 0 
WHERE pd.period_code = '$period_code'
GROUP BY pd.employee_id";
        \Yii::$app->db->createCommand($sql)->execute();
        }
    }

    public static function Formulir1721($year, $month, $period_code, $status_id, $user_id, $employee_id=array())
    {
    	// $sql = "DELETE FROM formulir_1721_a1 WHERE tahun_pajak = $year";
        // \Yii::$app->db->createCommand($sql)->execute();

        if($employee_id){
            $list_employee_id = implode(',', $employee_id);
            $sql = "INSERT INTO formulir_1721_a1 SELECT 0, e.id AS employee_id, $year, c.npwp, c.company, c.address, e.fullname, 
CASE WHEN e.npwp_id IS NOT NULL THEN e.npwp_id ELSE e.e_number END AS npwp_nik_pegawai, e.ptkp AS status_ptkp, e.address, 
IFNULL(SUM(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount ELSE 0 END),0) AS penghasilan_bruto, 
IFNULL(SUM(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) AS biaya_jabatan,
IFNULL(SUM(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS iuran_pensiun_jht,
IFNULL(SUM(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount END),0) - IFNULL(SUM(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) - IFNULL(SUM(CASE WHEN pd.item_code = 'JP_JHT_KARY' THEN pd.amount END),0) AS penghasilan_neto, 
IFNULL(MAX(CASE WHEN pd.item_code = 'PKP' THEN pd.amount END),0) AS pkp, 
CASE WHEN epp.profile_id > 2 THEN IFNULL(SUM(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
ELSE IFNULL(SUM(CASE WHEN pd.item_code = 'PPH21' THEN pd.amount END),0) END AS pph21_terutang, 
CASE WHEN epp.profile_id > 2 THEN 
IFNULL(SUM(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
ELSE 0 
END AS pph21_dipotong_perusahaan, c.nama_pejabat, c.sign_name, c.sign_image, 
$status_id, NOW(), $user_id, NOW(), $user_id 
FROM payroll_detail pd INNER JOIN payroll p ON pd.employee_id IN ($list_employee_id) AND pd.employee_id = p.employee_id AND pd.period_code = p.period_code 
INNER JOIN employee e ON p.employee_id = e.id AND e.status_id = 1 
INNER JOIN employee_payroll_profile epp ON e.id = epp.employee_id 
INNER JOIN company c ON e.company_id = c.id 
WHERE DATE_FORMAT(STR_TO_DATE(pd.period_code, '%Y-%m'), '%Y') = $year 
GROUP BY pd.employee_id";
        }else{
            $sql = "INSERT INTO formulir_1721_a1 SELECT 0, e.id AS employee_id, $year, c.npwp, c.company, c.address, e.fullname, 
CASE WHEN e.npwp_id IS NOT NULL THEN e.npwp_id ELSE e.e_number END AS npwp_nik_pegawai, e.ptkp AS status_ptkp, e.address AS alamat_pegawai, 
IFNULL(SUM(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount ELSE 0 END),0) AS penghasilan_bruto, 
IFNULL(SUM(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) AS biaya_jabatan,
IFNULL(SUM(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS iuran_pensiun_jht,
IFNULL(SUM(CASE WHEN pd.item_code = 'BRUTO' THEN pd.amount END),0) - IFNULL(SUM(CASE WHEN pd.item_code = 'BIAYA_JABATAN' THEN pd.amount END),0) - IFNULL(SUM(CASE WHEN pd.item_code = 'JP_JHT_KARY' THEN pd.amount END),0) AS penghasilan_neto, 
IFNULL(MAX(CASE WHEN pd.item_code = 'PKP' THEN pd.amount END),0) AS pkp, 
CASE WHEN epp.profile_id > 2 THEN IFNULL(SUM(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
ELSE IFNULL(SUM(CASE WHEN pd.item_code = 'PPH21' THEN pd.amount END),0) END AS pph21_terutang, 
CASE WHEN epp.profile_id > 2 THEN 
IFNULL(SUM(CASE WHEN pd.item_code = 'PPH21_NET_EMPLOYER' THEN pd.amount END),0) 
ELSE 0 
END AS pph21_dipotong_perusahaan, c.nama_pejabat, c.sign_name, c.sign_image, 
$status_id, NOW(), $user_id, NOW(), $user_id 
FROM payroll_detail pd INNER JOIN payroll p ON pd.employee_id = p.employee_id AND pd.period_code = p.period_code 
INNER JOIN employee e ON p.employee_id = e.id AND e.status_id = 1 
INNER JOIN employee_payroll_profile epp ON e.id = epp.employee_id 
INNER JOIN company c ON e.company_id = c.id 
WHERE DATE_FORMAT(STR_TO_DATE(pd.period_code, '%Y-%m'), '%Y') = $year 
GROUP BY pd.employee_id";
        }   
        \Yii::$app->db->createCommand($sql)->execute();
    }

    public static function BPJSFiling($year, $month, $period_code, $status_id, $user_id, $employee_id=array())
    {
        // $sql = "DELETE FROM bpjs_filing WHERE period_code = $period_code";
        // \Yii::$app->db->createCommand($sql)->execute();

        if($employee_id){
            $list_employee_id = implode(',', $employee_id);

        $sql = "INSERT INTO bpjs_filing 
SELECT 0, pd.employee_id, $month, $year, '$period_code', 
IFNULL(MAX(CASE WHEN pd.item_code = 'BASIC' THEN pd.amount END),0) AS basic,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_PERUSAHAAN' THEN pd.amount END),0) AS kes_perush,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_KARY' THEN pd.amount END),0) AS kes_kary,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_KARY' THEN pd.amount END),0) AS total_kes,
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_PERUSAHAAN' THEN pd.amount END),0) AS jht_perush,
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS jht_kary,
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS total_jht,
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_PERUSAHAAN' THEN pd.amount END),0) AS jp_perush,
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_KARY' THEN pd.amount END),0) AS jp_kary,
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JP_KARY' THEN pd.amount END),0) AS total_jp,
IFNULL(MAX(CASE WHEN pd.item_code = 'JKK' THEN pd.amount END),0) AS jkk,
IFNULL(MAX(CASE WHEN pd.item_code = 'JKM' THEN pd.amount END),0) AS jkm,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_KARY' THEN pd.amount END),0) + 
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) + 
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JP_KARY' THEN pd.amount END),0) + 
IFNULL(MAX(CASE WHEN pd.item_code = 'JKK' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JKM' THEN pd.amount END),0)
AS total, NULL, NULL, NULL, $status_id, NOW(), $user_id, NOW(), $user_id  
FROM payroll_detail pd WHERE pd.employee_id IN ($list_employee_id) AND pd.period_code = '$period_code' 
GROUP BY pd.employee_id";
        }else{
            $sql = "INSERT INTO bpjs_filing 
SELECT 0, pd.employee_id, $month, $year, '$period_code', 
IFNULL(MAX(CASE WHEN pd.item_code = 'BASIC' THEN pd.amount END),0) AS basic,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_PERUSAHAAN' THEN pd.amount END),0) AS kes_perush,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_KARY' THEN pd.amount END),0) AS kes_kary,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_KARY' THEN pd.amount END),0) AS total_kes,
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_PERUSAHAAN' THEN pd.amount END),0) AS jht_perush,
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS jht_kary,
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) AS total_jht,
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_PERUSAHAAN' THEN pd.amount END),0) AS jp_perush,
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_KARY' THEN pd.amount END),0) AS jp_kary,
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JP_KARY' THEN pd.amount END),0) AS total_jp,
IFNULL(MAX(CASE WHEN pd.item_code = 'JKK' THEN pd.amount END),0) AS jkk,
IFNULL(MAX(CASE WHEN pd.item_code = 'JKM' THEN pd.amount END),0) AS jkm,
IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'BPJS_KES_KARY' THEN pd.amount END),0) + 
IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JHT_KARY' THEN pd.amount END),0) + 
IFNULL(MAX(CASE WHEN pd.item_code = 'JP_PERUSAHAAN' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JP_KARY' THEN pd.amount END),0) + 
IFNULL(MAX(CASE WHEN pd.item_code = 'JKK' THEN pd.amount END),0) + IFNULL(MAX(CASE WHEN pd.item_code = 'JKM' THEN pd.amount END),0)
AS total, NULL, NULL, NULL, $status_id, NOW(), $user_id, NOW(), $user_id  
FROM payroll_detail pd WHERE pd.period_code = '$period_code' 
GROUP BY pd.employee_id";
        }
        \Yii::$app->db->createCommand($sql)->execute();
    }

}