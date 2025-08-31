<?php

use Phinx\Migration\AbstractMigration;

class ConsolidatedSchema extends AbstractMigration
{

    public function up()
    {

        // =====================================================================
        // BAGIAN 1: PEMBUATAN SEMUA TABEL TANPA FOREIGN KEY
        // =====================================================================

        // Tabel Master
        $this->table('tbl_kategori_barang', [ 'id' => FALSE, 'primary_key' => 'id_kategori' ])
            ->addColumn('id_kategori', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('nama_kategori', 'string', [ 'limit' => 100 ])
            ->addIndex([ 'nama_kategori' ], [ 'unique' => TRUE ])
            ->create();

        $this->table('tbl_satuan_barang', [ 'id' => FALSE, 'primary_key' => 'id_satuan' ])
            ->addColumn('id_satuan', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('nama_satuan', 'string', [ 'limit' => 50 ])
            ->addIndex([ 'nama_satuan' ], [ 'unique' => TRUE ])
            ->create();

        $this->table('tbl_bagian', [ 'id' => FALSE, 'primary_key' => 'id_bagian' ])
            ->addColumn('id_bagian', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('nama_bagian', 'string', [ 'limit' => 150 ])
            ->addIndex([ 'nama_bagian' ], [ 'unique' => TRUE ])
            ->create();

        $this->table('tbl_pemasok', [ 'id' => FALSE, 'primary_key' => 'id_pemasok' ])
            ->addColumn('id_pemasok', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('nama_pemasok', 'string', [ 'limit' => 255 ])
            ->addColumn('alamat', 'text', [ 'null' => TRUE ])
            ->addColumn('no_telepon', 'string', [ 'limit' => 20, 'null' => TRUE ])
            ->addColumn('email', 'string', [ 'limit' => 100, 'null' => TRUE ])
            ->create();

        $this->table('tbl_pengguna', [ 'id' => FALSE, 'primary_key' => 'id_pengguna' ])
            ->addColumn('id_pengguna', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('nama_lengkap', 'string', [ 'limit' => 255 ])
            ->addColumn('username', 'string', [ 'limit' => 100 ])
            ->addColumn('password', 'string', [ 'limit' => 255 ])
            ->addColumn('id_bagian', 'integer', [ 'signed' => FALSE ])
            ->addColumn('role', 'enum', [ 'values' => [ 'admin', 'pimpinan', 'pegawai', 'developer' ] ])
            ->addColumn('is_active', 'boolean', [ 'default' => TRUE ])
            ->addIndex([ 'username' ], [ 'unique' => TRUE ])
            ->create();

        $this->table('tbl_barang', [ 'id' => FALSE, 'primary_key' => 'id_barang' ])
            ->addColumn('id_barang', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('kode_barang', 'string', [ 'limit' => 50 ])
            ->addColumn('nama_barang', 'string', [ 'limit' => 255 ])
            ->addColumn('jenis_barang', 'enum', [ 'values' => [ 'habis_pakai', 'aset' ], 'default' => 'habis_pakai' ])
            ->addColumn('id_kategori', 'integer', [ 'signed' => FALSE ])
            ->addColumn('id_satuan', 'integer', [ 'signed' => FALSE ])
            ->addColumn('stok_umum', 'integer', [ 'signed' => FALSE, 'default' => 0 ])
            ->addColumn('stok_perkara', 'integer', [ 'signed' => FALSE, 'default' => 0 ])
            ->addColumn('deleted_at', 'timestamp', [ 'null' => TRUE ])
            ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
            ->addColumn('updated_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP' ])
            ->addIndex([ 'kode_barang' ], [ 'unique' => TRUE ])
            ->addIndex([ 'deleted_at' ])
            ->create();

        // Tabel Transaksi dan Log
        $this->table('tbl_log_stok', [ 'id' => FALSE, 'primary_key' => 'id_log' ])
            ->addColumn('id_log', 'biginteger', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('id_barang', 'integer', [ 'signed' => FALSE ])
            ->addColumn('jenis_transaksi', 'enum', [ 'values' => [ 'masuk', 'keluar', 'penyesuaian', 'dihapus' ] ])
            ->addColumn('jumlah_ubah', 'integer')
            ->addColumn('stok_sebelum_total', 'integer', [ 'signed' => FALSE ])
            ->addColumn('stok_sesudah_total', 'integer', [ 'signed' => FALSE ])
            ->addColumn('id_referensi', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('keterangan', 'string', [ 'limit' => 255, 'null' => TRUE ])
            ->addColumn('id_pengguna_aksi', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('tanggal_log', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
            ->create();

        $this->table('tbl_permintaan_atk', [ 'id' => FALSE, 'primary_key' => 'id_permintaan' ])
            ->addColumn('id_permintaan', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('kode_permintaan', 'string', [ 'limit' => 50 ])
            ->addColumn('id_pengguna_pemohon', 'integer', [ 'signed' => FALSE ])
            ->addColumn('tanggal_permintaan', 'date')
            ->addColumn('tipe_permintaan', 'enum', [ 'values' => [ 'stok', 'pembelian' ], 'default' => 'stok' ])
            ->addColumn('status_permintaan', 'enum', [ 'values' => [ 'Diajukan', 'Disetujui', 'Ditolak', 'Selesai', 'Diproses Pembelian', 'Sudah Dibeli' ], 'default' => 'Diajukan' ])
            ->addColumn('catatan_pemohon', 'text', [ 'null' => TRUE ])
            ->addColumn('id_pengguna_penyetuju', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('tanggal_diproses', 'datetime', [ 'null' => TRUE ])
            ->addColumn('catatan_penyetuju', 'text', [ 'null' => TRUE ])
            ->addColumn('id_pengguna_pembelian', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
            ->addIndex([ 'kode_permintaan' ], [ 'unique' => TRUE ])
            ->create();

        $this->table('tbl_detail_permintaan_atk', [ 'id' => FALSE, 'primary_key' => 'id_detail_permintaan' ])
            ->addColumn('id_detail_permintaan', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('id_permintaan', 'integer', [ 'signed' => FALSE ])
            ->addColumn('id_barang', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('nama_barang_custom', 'string', [ 'limit' => 255, 'null' => TRUE ])
            ->addColumn('jumlah_diminta', 'integer', [ 'signed' => FALSE ])
            ->addColumn('jumlah_disetujui', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('status_item', 'enum', [ 'values' => [ 'Selesai Diterima' ], 'null' => TRUE ])
            ->addColumn('id_barang_masuk_detail', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
            ->create();

        $this->table('tbl_barang_masuk', [ 'id' => FALSE, 'primary_key' => 'id_barang_masuk' ])
            ->addColumn('id_barang_masuk', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('no_transaksi_masuk', 'string', [ 'limit' => 50 ])
            ->addColumn('id_pemasok', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addColumn('tanggal_masuk', 'date')
            ->addColumn('id_pengguna_penerima', 'integer', [ 'signed' => FALSE ])
            ->addColumn('id_permintaan_terkait', 'integer', [ 'signed' => FALSE, 'null' => TRUE ])
            ->addIndex([ 'no_transaksi_masuk' ], [ 'unique' => TRUE ])
            ->create();

        $this->table('tbl_detail_barang_masuk', [ 'id' => FALSE, 'primary_key' => 'id_detail_masuk' ])
            ->addColumn('id_detail_masuk', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('id_barang_masuk', 'integer', [ 'signed' => FALSE ])
            ->addColumn('id_barang', 'integer', [ 'signed' => FALSE ])
            ->addColumn('jumlah_diterima', 'integer', [ 'signed' => FALSE ])
            ->addColumn('jumlah_umum', 'integer', [ 'signed' => FALSE, 'default' => 0 ])
            ->addColumn('jumlah_perkara', 'integer', [ 'signed' => FALSE, 'default' => 0 ])
            ->create();

        $this->table('tbl_barang_keluar', [ 'id' => FALSE, 'primary_key' => 'id_barang_keluar' ])
            ->addColumn('id_barang_keluar', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('id_detail_permintaan', 'integer', [ 'signed' => FALSE ])
            ->addColumn('jumlah_keluar', 'integer', [ 'signed' => FALSE ])
            ->addColumn('tanggal_keluar', 'date')
            ->addColumn('id_admin_gudang', 'integer', [ 'signed' => FALSE ])
            ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP' ])
            ->create();

        // =====================================================================
        // BAGIAN 2: PENAMBAHAN FOREIGN KEY SETELAH SEMUA TABEL DIBUAT
        // =====================================================================

        $this->table('tbl_pengguna')->addForeignKey('id_bagian', 'tbl_bagian', 'id_bagian', [ 'delete' => 'RESTRICT', 'update' => 'CASCADE' ])->update();
        $this->table('tbl_barang')->addForeignKey('id_kategori', 'tbl_kategori_barang', 'id_kategori', [ 'delete' => 'RESTRICT', 'update' => 'CASCADE' ])->update();
        $this->table('tbl_barang')->addForeignKey('id_satuan', 'tbl_satuan_barang', 'id_satuan', [ 'delete' => 'RESTRICT', 'update' => 'CASCADE' ])->update();
        $this->table('tbl_log_stok')->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [ 'delete' => 'CASCADE', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_permintaan_atk')->addForeignKey('id_pengguna_pemohon', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_permintaan_atk')->addForeignKey('id_pengguna_penyetuju', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_permintaan_atk')->addForeignKey('id_pengguna_pembelian', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_detail_permintaan_atk')->addForeignKey('id_permintaan', 'tbl_permintaan_atk', 'id_permintaan', [ 'delete' => 'CASCADE', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_detail_permintaan_atk')->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_barang_masuk')->addForeignKey('id_pemasok', 'tbl_pemasok', 'id_pemasok', [ 'delete' => 'SET_NULL', 'update' => 'CASCADE' ])->update();
        $this->table('tbl_barang_masuk')->addForeignKey('id_pengguna_penerima', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_barang_masuk')->addForeignKey('id_permintaan_terkait', 'tbl_permintaan_atk', 'id_permintaan', [ 'delete' => 'SET_NULL', 'update' => 'CASCADE' ])->update();
        $this->table('tbl_detail_barang_masuk')->addForeignKey('id_barang_masuk', 'tbl_barang_masuk', 'id_barang_masuk', [ 'delete' => 'CASCADE', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_detail_barang_masuk')->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_barang_keluar')->addForeignKey('id_detail_permintaan', 'tbl_detail_permintaan_atk', 'id_detail_permintaan', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();
        $this->table('tbl_barang_keluar')->addForeignKey('id_admin_gudang', 'tbl_pengguna', 'id_pengguna', [ 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION' ])->update();

        // =====================================================================
        // BAGIAN 3: PEMBUATAN VIEW
        // =====================================================================

        $this->execute("DROP VIEW IF EXISTS v_permintaan_lengkap;");
        $this->execute("
            CREATE VIEW v_permintaan_lengkap AS
            SELECT 
                p.*,
                u.nama_lengkap AS nama_pemohon,
                a.nama_lengkap AS nama_penyetuju,
                (SELECT COUNT(*) FROM tbl_detail_permintaan_atk WHERE id_permintaan = p.id_permintaan) as jumlah_item
            FROM tbl_permintaan_atk p
            JOIN tbl_pengguna u ON p.id_pengguna_pemohon = u.id_pengguna
            LEFT JOIN tbl_pengguna a ON p.id_pengguna_penyetuju = a.id_pengguna
        ");

        // =====================================================================
        // BAGIAN 4: PEMBUATAN TRIGGER
        // =====================================================================

        $this->execute("DROP TRIGGER IF EXISTS before_barang_delete;");
        $this->execute("
            CREATE TRIGGER `before_barang_delete` BEFORE DELETE ON `tbl_barang`
            FOR EACH ROW BEGIN
                INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum_total, stok_sesudah_total, keterangan, id_pengguna_aksi, tanggal_log)
                VALUES (OLD.id_barang, 'dihapus', -(OLD.stok_umum + OLD.stok_perkara), (OLD.stok_umum + OLD.stok_perkara), 0, CONCAT('Penghapusan permanen barang: ', OLD.nama_barang), @user_id, NOW());
            END
        ");
    }

    public function down()
    {

        // Phinx akan otomatis membatalkan pembuatan tabel.
        // Kita perlu membatalkan view dan trigger secara manual.
        $this->execute("DROP VIEW IF EXISTS v_permintaan_lengkap;");
        $this->execute("DROP TRIGGER IF EXISTS before_barang_delete;");
    }

}