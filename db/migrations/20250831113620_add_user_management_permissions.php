<?php

use Phinx\Migration\AbstractMigration;

class AddUserManagementPermissions extends AbstractMigration
{

    public function up()
    {

        // 1. Definisikan Permissions baru untuk Manajemen Pengguna
        $permissions = [
            [ 'nama_permission' => 'user_management_view', 'deskripsi_permission' => 'Melihat daftar pengguna', 'grup' => 'Pengguna' ],
            [ 'nama_permission' => 'user_management_create', 'deskripsi_permission' => 'Membuat pengguna baru', 'grup' => 'Pengguna' ],
            [ 'nama_permission' => 'user_management_update', 'deskripsi_permission' => 'Mengubah data pengguna', 'grup' => 'Pengguna' ],
            [ 'nama_permission' => 'user_management_delete', 'deskripsi_permission' => 'Menghapus pengguna', 'grup' => 'Pengguna' ],
        ];

        // Simpan permissions baru ke database
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        // 2. Hubungkan Permissions baru ke Roles yang sesuai

        // Dapatkan ID dari permissions yang baru saja kita buat.
        // Kita asumsikan ID terakhir adalah 12, jadi yang baru mulai dari 13.
        // Untuk cara yang lebih dinamis, kita bisa melakukan query, tapi ini cukup untuk sekarang.
        $p_user_view   = 13;
        $p_user_create = 14;
        $p_user_update = 15;
        $p_user_delete = 16;

        // ID untuk role Developer dan Admin (sesuai seeder sebelumnya)
        $developerId = 1;
        $adminId     = 2;

        $rolePermissions = [
            // Berikan semua izin manajemen pengguna ke Developer
            [ 'id_role' => $developerId, 'id_permission' => $p_user_view ],
            [ 'id_role' => $developerId, 'id_permission' => $p_user_create ],
            [ 'id_role' => $developerId, 'id_permission' => $p_user_update ],
            [ 'id_role' => $developerId, 'id_permission' => $p_user_delete ],

            // Berikan juga semua izin manajemen pengguna ke Admin
            [ 'id_role' => $adminId, 'id_permission' => $p_user_view ],
            [ 'id_role' => $adminId, 'id_permission' => $p_user_create ],
            [ 'id_role' => $adminId, 'id_permission' => $p_user_update ],
            [ 'id_role' => $adminId, 'id_permission' => $p_user_delete ],
        ];

        // Simpan hubungan role-permission ke database
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();
    }

    public function down()
    {

        // Perintah untuk membatalkan migrasi (rollback)
        $this->execute("DELETE FROM tbl_permissions WHERE grup = 'Pengguna'");
        // Catatan: Baris di tbl_role_permissions akan otomatis terhapus jika Anda
        // menggunakan foreign key dengan ON DELETE CASCADE. Jika tidak,
        // Anda perlu menghapusnya secara manual di sini.
    }

}
