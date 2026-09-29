<?php

use yii\db\Migration;

/**
 * Quotation as an SPH (Surat Penawaran Harga) PDF:
 *
 * - quotation_layout: one-row template edited in Master Data -> Layout Quotation
 *   (letterhead, SPH number code, texts, default contract length), seeded with
 *   the PT MNC Kabel Mediacom SPH wording.
 * - quotation: contract_months, payment_method, and snapshots of the template
 *   texts (opening, terms, installation notes, closing) copied when the quotation
 *   is created, so a PDF already sent doesn't change when the template does.
 *   quotation_number becomes unique (new numbers look like
 *   0001/SPH/SLS-NHS/EXT/IX/2026, running per year).
 * - product.package_info ("Keterangan Paket", e.g. "101 Channel Terlampir").
 * - user.job_title ("Jabatan", printed under the salesperson's name).
 * - RBAC: Layout Quotation for admins (read for viewApplication), quotation PDF
 *   for Sales, Sales Manager and viewApplication.
 */
class m260929_100000_quotation_sph extends Migration
{
    public function up()
    {
        $this->createTable('{{%quotation_layout}}', [
            'id'                      => $this->primaryKey(),
            'company_name'            => $this->string(150)->notNull(),
            'company_address'         => $this->string(255)->null(),
            'company_phone'           => $this->string(100)->null(),
            'company_website'         => $this->string(100)->null(),
            'logo_file'               => $this->string(255)->null()->comment('stored under backend/runtime/quotation-layout'),
            'number_code'             => $this->string(50)->notNull()->comment('middle part of the SPH number'),
            'city'                    => $this->string(50)->notNull(),
            'recipient_title'         => $this->string(100)->null()->comment('line above the account name, e.g. Pimpinan'),
            'opening_text'            => $this->text()->null(),
            'terms_text'              => $this->text()->null()->comment('one clause per line'),
            'installation_notes'      => $this->text()->null()->comment('one note per line'),
            'closing_text'            => $this->text()->null(),
            'sign_left_label'         => $this->string(50)->notNull(),
            'sign_right_label'        => $this->string(50)->notNull(),
            'default_contract_months' => $this->integer()->null(),
            'created_at'              => $this->dateTime()->null(),
            'created_by'              => $this->integer()->null(),
            'updated_at'              => $this->dateTime()->null(),
            'updated_by'              => $this->integer()->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

        $this->insert('{{%quotation_layout}}', [
            'id' => 1,
            'company_name' => 'PT MNC Kabel Mediacom',
            'company_address' => 'MNC Tower lantai 10, 11, 12A, 14. Jl. Kebon Sirih No. 17-19 Jakarta 10340, Indonesia',
            'company_phone' => 'T. (62-21) 392 6933 | F. (62-21) 392 6911',
            'company_website' => 'www.mncplay.id',
            'number_code' => 'SPH/SLS-NHS/EXT',
            'city' => 'Jakarta',
            'recipient_title' => 'Pimpinan',
            'opening_text' => "Dengan Hormat,\n\nBersama ini kami sampaikan penawaran produk / Jasa PT MNC Kabel Mediacom sesuai dengan kebutuhan.\nBerikut rincian produk / jasa dicantumkan dalam lampiran proposal penawaran harga ini.",
            'terms_text' => implode("\n", [
                'Total harga belum termasuk PPN',
                'Harga yang tercantum hanya berlaku selama 30 (tiga puluh) hari kalender setelah tanggal proposal Penawaran Harga ini.',
                'Komitmen Jangka waktu layanan adalah minimal 36 (dua puluh empat) bulan sejak penerimaan produk / jasa dibuktikan dengan Berita Acara Operasional / Berita Acara Serah Terima dan akan diperpanjang secara otomatis jika tidak ada permintaan berhenti berlangganan.',
                'PT MNC Kabel Mediacom berhak untuk menagihkan biaya berlangganan kepada Pelanggan berdasarkan surat perintah kerja (SPK) dan Berita Acara Serah terima (BAST), tanggal penagihan disepakati pelanggan dan PT MNC Kabel Mediacom merujuk pada Billing freq yang tersedia.',
                'Pembayaran biaya berlangganan oleh Pelanggan wajib dilakukan 5-7 hari sejak tanggal tagihan.',
                'Dengan menyetujui Proposal penawaran harga ini, pelanggan telah menyetujui rincian produk yang ditawarkan dan tidak dapat dibatalkan.',
                'Hal-hal yang belum diatur dan perubahan yang perlu dilakukan akan diatur lebih lanjut dalam perjanjian berlangganan yang merupakan satu kesatuan dari proposal Penawaran Harga ini.',
                'Daftar channel dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya dan merupakan kewenangan penuh dari penyedia konten.',
                'Internet di sediakan oleh customer.',
                'Penagihan full flat setiap bulan.',
                'Jika customer berhenti kontrak sebelum selesai nya kontrak, maka untuk sisa kontrak nya dibayarkan full dari pihak customer.',
            ]),
            'installation_notes' => 'Biaya hotel, transportasi, akomodasi, dan instalasi ditanggung oleh pihak MNC',
            'closing_text' => "Dengan disetujuinya Proposal Penawaran harga ini, maka proposal Penawaran Harga ini berlaku pula sebagai kesepakatan awal untuk digunakan sebagai dasar proses pemenuhan produk / jasa dan proses penagihan sehingga pelanggan dan PT MNC Kabel Mediacom terikat secara hukum dengan ketentuan-ketentuan dalam proposal Penawaran Harga ini.\n\nDemikian Proposal Penawaran Harga ini kami sampaikan, besar harapan kami dapat menjalin kerjasama lebih lanjut. Atas perhatian dan kerja sama Pelanggan kami ucapkan terima kasih.",
            'sign_left_label' => 'Diajukan Oleh,',
            'sign_right_label' => 'Menyetujui,',
            'default_contract_months' => 36,
            'created_at' => new \yii\db\Expression('NOW()'),
            'updated_at' => new \yii\db\Expression('NOW()'),
        ]);

        $this->addColumn('{{%quotation}}', 'contract_months', $this->integer()->null()->after('valid_until'));
        $this->addColumn('{{%quotation}}', 'payment_method', $this->string(50)->null()->after('contract_months'));
        $this->addColumn('{{%quotation}}', 'opening_text', $this->text()->null()->after('payment_method'));
        $this->addColumn('{{%quotation}}', 'terms_text', $this->text()->null()->after('opening_text'));
        $this->addColumn('{{%quotation}}', 'installation_notes', $this->text()->null()->after('terms_text'));
        $this->addColumn('{{%quotation}}', 'closing_text', $this->text()->null()->after('installation_notes'));
        $this->createIndex('ux_quotation_number', '{{%quotation}}', 'quotation_number', true);

        $this->addColumn('{{%product}}', 'package_info', $this->string(255)->null()->after('description'));
        $this->addColumn('{{%user}}', 'job_title', $this->string(100)->null()->after('fullname'));

        // RBAC
        $auth = Yii::$app->authManager;
        $layoutAll = $this->perm('backend.master.quotationlayout.*', '[Controller permission] .*');
        foreach (['index', 'update', 'logo'] as $a) {
            $p = $this->perm("backend.master.quotationlayout.$a", "[Action permission] backend.master.quotationlayout.$a");
            $auth->addChild($layoutAll, $p);
        }
        $auth->addChild($auth->getRole('adminApplication'), $layoutAll);
        $auth->addChild($auth->getRole('viewApplication'), $auth->getPermission('backend.master.quotationlayout.index'));
        $auth->addChild($auth->getRole('viewApplication'), $auth->getPermission('backend.master.quotationlayout.logo'));

        $pdf = $this->perm('backend.sales.quotation.pdf', '[Action permission] backend.sales.quotation.pdf');
        $auth->addChild($auth->getPermission('backend.sales.quotation.*'), $pdf);
        $auth->addChild($auth->getRole('sales'), $pdf);
        $auth->addChild($auth->getRole('viewApplication'), $pdf);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        foreach (['backend.sales.quotation.pdf', 'backend.master.quotationlayout.index', 'backend.master.quotationlayout.update',
                  'backend.master.quotationlayout.logo', 'backend.master.quotationlayout.*'] as $name) {
            $item = $auth->getPermission($name);
            if ($item !== null) {
                $auth->remove($item);
            }
        }

        $this->dropColumn('{{%user}}', 'job_title');
        $this->dropColumn('{{%product}}', 'package_info');
        $this->dropIndex('ux_quotation_number', '{{%quotation}}');
        foreach (['closing_text', 'installation_notes', 'terms_text', 'opening_text', 'payment_method', 'contract_months'] as $c) {
            $this->dropColumn('{{%quotation}}', $c);
        }
        $this->dropTable('{{%quotation_layout}}');
    }

    private function perm($name, $description)
    {
        $auth = Yii::$app->authManager;
        $p = $auth->getPermission($name);
        if ($p === null) {
            $p = $auth->createPermission($name);
            $p->description = $description;
            $auth->add($p);
        }
        return $p;
    }
}
