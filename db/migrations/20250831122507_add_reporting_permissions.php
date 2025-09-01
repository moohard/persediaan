<?php

use Phinx\Migration\AbstractMigration;

class AddReportingPermissions extends AbstractMigration
{

    public function up()
    {

        // 1. Definisikan Permissions baru untuk Pelaporan
        $permissions = [
            [ 'nama_permission' => 'laporan_view', 'deskripsi_permission' => 'Melihat halaman laporan', 'grup' => 'Laporan' ],
            [ 'nama_permission' => 'laporan_stok_print', 'deskripsi_permission' => 'Mencetak laporan stok barang', 'grup' => 'Laporan' ],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        // 2. Hubungkan Permissions baru ke Roles yang sesuai
        // Kita asumsikan ID terakhir adalah 16, jadi yang baru mulai dari 17.
        $p_laporan_view       = 17;
        $p_laporan_stok_print = 18;

        // ID untuk role Developer dan Pimpinan
        $developerId = 1;
        $pimpinanId  = 3;

        $rolePermissions = [
            // Berikan semua izin laporan ke Developer
            [ 'id_role' => $developerId, 'id_permission' => $p_laporan_view ],
            [ 'id_role' => $developerId, 'id_permission' => $p_laporan_stok_print ],

            // Berikan juga semua izin laporan ke Pimpinan
            [ 'id_role' => $pimpinanId, 'id_permission' => $p_laporan_view ],
            [ 'id_role' => $pimpinanId, 'id_permission' => $p_laporan_stok_print ],
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();
    }

    public function down()
    {

        $this->execute("DELETE FROM tbl_permissions WHERE grup = 'Laporan'");
    }

}
