<?php

use Phinx\Migration\AbstractMigration;

class AddRoleManagementPermissions extends AbstractMigration
{

    public function up()
    {

        // 1. Definisikan Permissions baru
        $permissions = [
            [ 'nama_permission' => 'role_management_view', 'deskripsi_permission' => 'Melihat halaman manajemen peran & izin', 'grup' => 'Hak Akses' ],
            [ 'nama_permission' => 'role_management_update', 'deskripsi_permission' => 'Mengubah izin untuk sebuah peran', 'grup' => 'Hak Akses' ],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();
        // 2. Hubungkan Permissions baru ke Role Developer
        // Kita asumsikan ID permission terakhir adalah 21, jadi yang baru mulai dari 22.
        // $p_role_view   = 25;
        // $p_role_update = 26;

        // $developerId = 1; // ID untuk role Developer

        // $rolePermissions = [
        //     [ 'id_role' => $developerId, 'id_permission' => $p_role_view ],
        //     [ 'id_role' => $developerId, 'id_permission' => $p_role_update ],
        // ];
        // $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();
    }

    public function down()
    {

        $this->execute("DELETE FROM tbl_permissions WHERE grup = 'Hak Akses'");
    }

}
