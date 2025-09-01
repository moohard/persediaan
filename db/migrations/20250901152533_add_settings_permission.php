<?php

use Phinx\Migration\AbstractMigration;

class AddSettingsPermission extends AbstractMigration
{

    public function up()
    {

        // 1. Definisikan Permissions baru
        $permissions = [
            [ 'nama_permission' => 'pengaturan_view', 'deskripsi_permission' => 'Melihat halaman pengaturan', 'grup' => 'Pengaturan' ],
            [ 'nama_permission' => 'pengaturan_clear_transactions', 'deskripsi_permission' => 'Mengosongkan semua data transaksi', 'grup' => 'Pengaturan' ],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        // 2. Berikan permission ke Developer
        // Asumsi ID terakhir adalah 20, jadi yang baru mulai dari 21
        $p_settings_view  = 21;
        $p_settings_clear = 22;
        $developerId      = 1; // ID untuk role Developer

        $rolePermissions = [
            [ 'id_role' => $developerId, 'id_permission' => $p_settings_view ],
            [ 'id_role' => $developerId, 'id_permission' => $p_settings_clear ],
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();
    }

    public function down()
    {

        $this->execute("DELETE FROM tbl_permissions WHERE grup = 'Pengaturan'");
    }

}
