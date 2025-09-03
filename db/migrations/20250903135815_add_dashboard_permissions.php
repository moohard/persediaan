<?php

use Phinx\Migration\AbstractMigration;

class AddDashboardPermissions extends AbstractMigration
{

    public function up()
    {

        // Definisikan Permission baru
        $permissions = [
            [ 'id_permission'=>26,'nama_permission' => 'dashboard_view_stats', 'deskripsi_permission' => 'Melihat statistik ringkasan di dashboard', 'grup' => 'Dashboard' ],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        // Hubungkan ke Role Developer, Admin, dan Pimpinan
        // Asumsi ID terakhir 28, jadi yang baru adalah 29
        $p_dashboard_stats = 26;

        $developerId = 1;
        $adminId     = 2;
        $pimpinanId  = 3;

        $rolePermissions = [
            [ 'id_role' => $developerId, 'id_permission' => $p_dashboard_stats ],
            [ 'id_role' => $adminId, 'id_permission' => $p_dashboard_stats ],
            [ 'id_role' => $pimpinanId, 'id_permission' => $p_dashboard_stats ],
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();
    }

    public function down()
    {

        $this->execute("DELETE FROM tbl_permissions WHERE nama_permission = 'dashboard_view_stats'");
    }

}
