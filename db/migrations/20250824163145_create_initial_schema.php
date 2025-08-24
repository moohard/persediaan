<?php

use Phinx\Migration\AbstractMigration;

class CreateInitialSchema extends AbstractMigration
{

    public function change()
    {

        // =============================================================
        // LANGKAH 1: BUAT SEMUA TABEL TANPA FOREIGN KEY
        // =============================================================

        if (!$this->hasTable('tbl_kategori_barang'))
        {
            $this->table('tbl_kategori_barang', [ 'id' => FALSE, 'primary_key' => [ 'id_kategori' ] ])
                ->addColumn('id_kategori', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('nama_kategori', 'string', [ 'limit' => 100 ])
                ->addIndex([ 'nama_kategori' ], [ 'unique' => TRUE ])
                ->create();
        }

        if (!$this->hasTable('tbl_satuan_barang'))
        {
            $this->table('tbl_satuan_barang', [ 'id' => FALSE, 'primary_key' => [ 'id_satuan' ] ])
                ->addColumn('id_satuan', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('nama_satuan', 'string', [ 'limit' => 50 ])
                ->addIndex([ 'nama_satuan' ], [ 'unique' => TRUE ])
                ->create();
        }

        if (!$this->hasTable('tbl_bagian'))
        {
            $this->table('tbl_bagian', [ 'id' => FALSE, 'primary_key' => [ 'id_bagian' ] ])
                ->addColumn('id_bagian', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('nama_bagian', 'string', [ 'limit' => 150 ])
                ->addIndex([ 'nama_bagian' ], [ 'unique' => TRUE ])
                ->create();
        }

        if (!$this->hasTable('tbl_pemasok'))
        {
            $this->table('tbl_pemasok', [ 'id' => FALSE, 'primary_key' => [ 'id_pemasok' ] ])
                ->addColumn('id_pemasok', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('nama_pemasok', 'string', [ 'limit' => 255 ])
                ->addColumn('alamat', 'text', [ 'null' => TRUE ])
                ->addColumn('no_telepon', 'string', [ 'limit' => 20, 'null' => TRUE ])
                ->addColumn('email', 'string', [ 'limit' => 100, 'null' => TRUE ])
                ->create();
        }

        if (!$this->hasTable('tbl_pengguna'))
        {
            $this->table('tbl_pengguna', [ 'id' => FALSE, 'primary_key' => [ 'id_pengguna' ] ])
                ->addColumn('id_pengguna', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('nama_lengkap', 'string', [ 'limit' => 255 ])
                ->addColumn('username', 'string', [ 'limit' => 100 ])
                ->addColumn('password', 'string', [ 'limit' => 255 ])
                ->addColumn('id_bagian', 'integer', [ 'signed' => FALSE ])
                ->addColumn('role', 'enum', [ 'values' => [ 'admin', 'pimpinan', 'pegawai' ] ])
                ->addColumn('is_active', 'boolean', [ 'default' => TRUE ])
                ->addIndex([ 'username' ], [ 'unique' => TRUE ])
                ->create();
        }

        if (!$this->hasTable('tbl_barang'))
        {
            $this->table('tbl_barang', [ 'id' => FALSE, 'primary_key' => [ 'id_barang' ] ])
                ->addColumn('id_barang', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('kode_barang', 'string', [ 'limit' => 50 ])
                ->addColumn('nama_barang', 'string', [ 'limit' => 255 ])
                ->addColumn('jenis_barang', 'enum', [ 'values' => [ 'habis_pakai', 'aset' ], 'default' => 'habis_pakai' ])
                ->addColumn('id_kategori', 'integer', [ 'signed' => FALSE ])
                ->addColumn('id_satuan', 'integer', [ 'signed' => FALSE ])
                ->addColumn('stok_saat_ini', 'integer', [ 'default' => 0 ])
                ->addColumn('deleted_at', 'timestamp', [ 'null' => TRUE ])
                ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
                ->addColumn('updated_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP' ])
                ->addIndex([ 'kode_barang' ], [ 'unique' => TRUE ])
                ->addIndex([ 'deleted_at' ])
                ->create();
        }

        if (!$this->hasTable('tbl_log_stok'))
        {
            $this->table('tbl_log_stok', [ 'id' => FALSE, 'primary_key' => [ 'id_log' ] ])
                ->addColumn('id_log', 'biginteger', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('id_barang', 'integer', [ 'signed' => FALSE ])
                ->addColumn('jenis_transaksi', 'enum', [ 'values' => [ 'masuk', 'keluar', 'penyesuaian', 'dihapus' ] ])
                ->addColumn('jumlah_ubah', 'integer')
                ->addColumn('stok_sebelum', 'integer')
                ->addColumn('stok_sesudah', 'integer')
                ->addColumn('id_referensi', 'integer', [ 'null' => TRUE, 'signed' => FALSE ])
                ->addColumn('keterangan', 'string', [ 'limit' => 255, 'null' => TRUE ])
                ->addColumn('id_pengguna_aksi', 'integer', [ 'null' => TRUE, 'signed' => FALSE ])
                ->addColumn('tanggal_log', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
                ->create();
        }

        if (!$this->hasTable('tbl_permintaan_atk'))
        {
            $this->table('tbl_permintaan_atk', [ 'id' => FALSE, 'primary_key' => [ 'id_permintaan' ] ])
                ->addColumn('id_permintaan', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('kode_permintaan', 'string', [ 'limit' => 50 ])
                ->addColumn('id_pengguna_pemohon', 'integer', [ 'signed' => FALSE ])
                ->addColumn('tanggal_permintaan', 'date')
                ->addColumn('status_permintaan', 'enum', [ 'values' => [ 'Diajukan', 'Disetujui', 'Ditolak', 'Selesai' ], 'default' => 'Diajukan' ])
                ->addColumn('catatan_pemohon', 'text', [ 'null' => TRUE ])
                ->addColumn('id_pengguna_penyetuju', 'integer', [ 'null' => TRUE, 'signed' => FALSE ])
                ->addColumn('tanggal_diproses', 'datetime', [ 'null' => TRUE ])
                ->addColumn('catatan_penyetuju', 'text', [ 'null' => TRUE ])
                ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
                ->addIndex([ 'kode_permintaan' ], [ 'unique' => TRUE ])
                ->create();
        }

        if (!$this->hasTable('tbl_detail_permintaan_atk'))
        {
            $this->table('tbl_detail_permintaan_atk', [ 'id' => FALSE, 'primary_key' => [ 'id_detail_permintaan' ] ])
                ->addColumn('id_detail_permintaan', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('id_permintaan', 'integer', [ 'signed' => FALSE ])
                ->addColumn('id_barang', 'integer', [ 'signed' => FALSE ])
                ->addColumn('jumlah_diminta', 'integer')
                ->addColumn('jumlah_disetujui', 'integer', [ 'null' => TRUE ])
                ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
                ->create();
        }

        if (!$this->hasTable('tbl_barang_keluar'))
        {
            $this->table('tbl_barang_keluar', [ 'id' => FALSE, 'primary_key' => [ 'id_barang_keluar' ] ])
                ->addColumn('id_barang_keluar', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
                ->addColumn('id_detail_permintaan', 'integer', [ 'signed' => FALSE ])
                ->addColumn('jumlah_keluar', 'integer')
                ->addColumn('tanggal_keluar', 'date')
                ->addColumn('id_admin_gudang', 'integer', [ 'signed' => FALSE ])
                ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
                ->create();
        }

        // =============================================================
        // LANGKAH 2: TAMBAHKAN SEMUA FOREIGN KEY SETELAH TABEL DIBUAT
        // =============================================================

        $this->table('tbl_pengguna')
            ->addForeignKey('id_bagian', 'tbl_bagian', 'id_bagian', [ 'delete' => 'RESTRICT', 'update' => 'CASCADE' ])
            ->update();

        $this->table('tbl_barang')
            ->addForeignKey('id_kategori', 'tbl_kategori_barang', 'id_kategori', [ 'delete' => 'RESTRICT', 'update' => 'CASCADE' ])
            ->addForeignKey('id_satuan', 'tbl_satuan_barang', 'id_satuan', [ 'delete' => 'RESTRICT', 'update' => 'CASCADE' ])
            ->update();

        $this->table('tbl_log_stok')
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [ 'delete' => 'CASCADE', 'update' => 'NO_ACTION' ])
            ->update();

        $this->table('tbl_permintaan_atk')
            ->addForeignKey('id_pengguna_pemohon', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])
            ->addForeignKey('id_pengguna_penyetuju', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])
            ->update();

        $this->table('tbl_detail_permintaan_atk')
            ->addForeignKey('id_permintaan', 'tbl_permintaan_atk', 'id_permintaan', [ 'delete' => 'CASCADE', 'update' => 'NO_ACTION' ])
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])
            ->update();

        $this->table('tbl_barang_keluar')
            ->addForeignKey('id_detail_permintaan', 'tbl_detail_permintaan_atk', 'id_detail_permintaan', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])
            ->addForeignKey('id_admin_gudang', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])
            ->update();
    }

}