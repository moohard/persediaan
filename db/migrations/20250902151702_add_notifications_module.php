<?php

use Phinx\Migration\AbstractMigration;

class AddNotificationsModule extends AbstractMigration
{

    public function up()
    {

        // =====================================================================
        // BAGIAN 1: BUAT TABEL BARU UNTUK NOTIFIKASI
        // =====================================================================

        // $this->table('tbl_notifikasi', [ 'id' => FALSE, 'primary_key' => 'id_notifikasi' ])
        //     ->addColumn('id_notifikasi', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
        //     ->addColumn('id_pengguna_tujuan', 'integer', [ 'signed' => FALSE ])
        //     ->addColumn('pesan', 'string', [ 'limit' => 255 ])
        //     ->addColumn('tautan', 'string', [ 'limit' => 255, 'null' => TRUE ])
        //     ->addColumn('sudah_dibaca', 'boolean', [ 'default' => FALSE ])
        //     ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
        //     ->addIndex([ 'id_pengguna_tujuan', 'sudah_dibaca' ])
        //     ->addForeignKey('id_pengguna_tujuan', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
        //     ->create();

        // // =====================================================================
        // // BAGIAN 2: BUAT TRIGGER UNTUK MEMBUAT NOTIFIKASI SECARA OTOMATIS
        // // =====================================================================

        // $this->execute("
        //     CREATE TRIGGER after_permintaan_insert_notification
        //     AFTER INSERT ON tbl_permintaan_atk
        //     FOR EACH ROW
        //     BEGIN
        //         -- Kirim notifikasi ke semua pengguna yang memiliki izin 'permintaan_approve' (Pimpinan & Developer)
        //         INSERT INTO tbl_notifikasi (id_pengguna_tujuan, pesan, tautan)
        //         SELECT DISTINCT u.id_pengguna, 
        //                CONCAT('Permintaan baru (', NEW.kode_permintaan, ') telah diajukan.'),
        //                '/permintaan'
        //         FROM tbl_pengguna u
        //         JOIN tbl_role_permissions rp ON u.id_role = rp.id_role
        //         JOIN tbl_permissions p ON rp.id_permission = p.id_permission
        //         WHERE p.nama_permission = 'permintaan_approve';
        //     END
        // ");

        // =====================================================================
        // BAGIAN 3: TAMBAHKAN PERMISSIONS BARU
        // =====================================================================

        // $permissions = [
        //     [ 'nama_permission' => 'notifikasi_view', 'deskripsi_permission' => 'Melihat notifikasi', 'grup' => 'Notifikasi' ],
        // ];
        // $this->table('tbl_permissions')->insert($permissions)->saveData();exit;
        // Hubungkan ke Role Developer, Admin, dan Pimpinan
        // Asumsi ID terakhir 25, jadi yang baru adalah 26
        $p_notif_view = 40;

        $rolePermissions = [
            [ 'id_role' => 1, 'id_permission' => $p_notif_view ], // Developer
            [ 'id_role' => 2, 'id_permission' => $p_notif_view ], // Admin
            [ 'id_role' => 3, 'id_permission' => $p_notif_view ], // Pimpinan
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();
    }

    public function down()
    {

        $this->execute("DROP TRIGGER IF EXISTS after_permintaan_insert_notification;");
        $this->table('tbl_notifikasi')->drop()->save();
        $this->execute("DELETE FROM tbl_permissions WHERE grup = 'Notifikasi'");
    }

}
