<?php
use Phinx\Migration\AbstractMigration;

class CreateGoodsReceiptTables extends AbstractMigration
{
    public function change()
    {
        // =============================================================
        // LANGKAH 1: BUAT TABEL BARANG MASUK
        // =============================================================
        if (!$this->hasTable('tbl_barang_masuk')) {
            $this->table('tbl_barang_masuk', ['id' => false, 'primary_key' => ['id_barang_masuk']])
                ->addColumn('id_barang_masuk', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('no_transaksi_masuk', 'string', ['limit' => 50])
                ->addColumn('id_pemasok', 'integer', ['signed' => false])
                ->addColumn('tanggal_masuk', 'date')
                ->addColumn('id_pengguna_penerima', 'integer', ['signed' => false])
                ->addColumn('keterangan', 'text', ['null' => true])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['no_transaksi_masuk'], ['unique' => true])
                ->create();
        }

        if (!$this->hasTable('tbl_detail_barang_masuk')) {
            $this->table('tbl_detail_barang_masuk', ['id' => false, 'primary_key' => ['id_detail_masuk']])
                ->addColumn('id_detail_masuk', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('id_barang_masuk', 'integer', ['signed' => false])
                ->addColumn('id_barang', 'integer', ['signed' => false])
                ->addColumn('jumlah_masuk', 'integer')
                ->addColumn('harga_satuan', 'decimal', ['precision' => 15, 'scale' => 2, 'null' => true])
                ->create();
        }

        // =============================================================
        // LANGKAH 2: TAMBAHKAN FOREIGN KEY
        // =============================================================
        $this->table('tbl_barang_masuk')
            ->addForeignKey('id_pemasok', 'tbl_pemasok', 'id_pemasok', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->addForeignKey('id_pengguna_penerima', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->update();

        $this->table('tbl_detail_barang_masuk')
            ->addForeignKey('id_barang_masuk', 'tbl_barang_masuk', 'id_barang_masuk', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->update();
    }
}
