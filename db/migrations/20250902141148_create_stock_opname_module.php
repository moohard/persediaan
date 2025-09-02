<?php

use Phinx\Migration\AbstractMigration;

class CreateStockOpnameModule extends AbstractMigration
{

    public function up()
    {

        // =====================================================================
        // BAGIAN 1: BUAT TABEL BARU UNTUK STOCK OPNAME
        // =====================================================================

        $this->table('tbl_stock_opname', [ 'id' => FALSE, 'primary_key' => 'id_opname' ])
            ->addColumn('id_opname', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('kode_opname', 'string', [ 'limit' => 50 ])
            ->addColumn('tanggal_opname', 'date')
            ->addColumn('id_pengguna_penanggung_jawab', 'integer', [ 'signed' => FALSE ])
            ->addColumn('keterangan', 'text', [ 'null' => TRUE ])
            ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
            ->addIndex([ 'kode_opname' ], [ 'unique' => TRUE ])
            ->addForeignKey('id_pengguna_penanggung_jawab', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'CASCADE' ])
            ->create();

        $this->table('tbl_detail_stock_opname', [ 'id' => FALSE, 'primary_key' => 'id_detail_opname' ])
            ->addColumn('id_detail_opname', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('id_opname', 'integer', [ 'signed' => FALSE ])
            ->addColumn('id_barang', 'integer', [ 'signed' => FALSE ])
            ->addColumn('stok_sistem_umum', 'integer', [ 'signed' => FALSE ])
            ->addColumn('stok_sistem_perkara', 'integer', [ 'signed' => FALSE ])
            ->addColumn('stok_fisik_umum', 'integer', [ 'signed' => FALSE ])
            ->addColumn('stok_fisik_perkara', 'integer', [ 'signed' => FALSE ])
            ->addColumn('selisih_umum', 'integer')
            ->addColumn('selisih_perkara', 'integer')
            ->addColumn('catatan', 'string', [ 'limit' => 255, 'null' => TRUE ])
            ->addForeignKey('id_opname', 'tbl_stock_opname', 'id_opname', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        // =====================================================================
        // BAGIAN 2: TAMBAHKAN PERMISSIONS BARU
        // =====================================================================

        $permissions = [
            [ 'nama_permission' => 'stock_opname_view', 'deskripsi_permission' => 'Melihat riwayat stock opname', 'grup' => 'Stock Opname' ],
            [ 'nama_permission' => 'stock_opname_create', 'deskripsi_permission' => 'Melakukan proses stock opname baru', 'grup' => 'Stock Opname' ],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        // Hubungkan ke Role Developer dan Admin
        // Asumsi ID terakhir 23, jadi yang baru mulai dari 24
        $p_opname_view   = 24;
        $p_opname_create = 25;

        $developerId = 1;
        $adminId     = 2;

        $rolePermissions = [
            [ 'id_role' => $developerId, 'id_permission' => $p_opname_view ],
            [ 'id_role' => $developerId, 'id_permission' => $p_opname_create ],
            [ 'id_role' => $adminId, 'id_permission' => $p_opname_view ],
            [ 'id_role' => $adminId, 'id_permission' => $p_opname_create ],
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();
    }

    public function down()
    {

        $this->table('tbl_detail_stock_opname')->drop()->save();
        $this->table('tbl_stock_opname')->drop()->save();
        $this->execute("DELETE FROM tbl_permissions WHERE grup = 'Stock Opname'");
    }

}
