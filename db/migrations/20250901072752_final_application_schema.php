<?php
use Phinx\Migration\AbstractMigration;

class FinalApplicationSchema extends AbstractMigration
{
    public function up()
    {
        // =====================================================================
        // BAGIAN 1: PEMBUATAN SEMUA TABEL TANPA FOREIGN KEY
        // =====================================================================

        // Tabel Master
        $this->table('tbl_kategori_barang', ['id' => false, 'primary_key' => 'id_kategori'])
            ->addColumn('id_kategori', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('nama_kategori', 'string', ['limit' => 100])
            ->addIndex(['nama_kategori'], ['unique' => true])
            ->create();

        $this->table('tbl_satuan_barang', ['id' => false, 'primary_key' => 'id_satuan'])
            ->addColumn('id_satuan', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('nama_satuan', 'string', ['limit' => 50])
            ->addIndex(['nama_satuan'], ['unique' => true])
            ->create();

        $this->table('tbl_bagian', ['id' => false, 'primary_key' => 'id_bagian'])
            ->addColumn('id_bagian', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('nama_bagian', 'string', ['limit' => 150])
            ->addIndex(['nama_bagian'], ['unique' => true])
            ->create();

        $this->table('tbl_pemasok', ['id' => false, 'primary_key' => 'id_pemasok'])
            ->addColumn('id_pemasok', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('nama_pemasok', 'string', ['limit' => 255])
            ->addColumn('alamat', 'text', ['null' => true])
            ->addColumn('no_telepon', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => true])
            ->create();

        // Tabel RBAC
        $this->table('tbl_roles', ['id' => false, 'primary_key' => 'id_role'])
            ->addColumn('id_role', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('nama_role', 'string', ['limit' => 100])
            ->addColumn('deskripsi_role', 'text', ['null' => true])
            ->addIndex(['nama_role'], ['unique' => true])
            ->create();

        $this->table('tbl_permissions', ['id' => false, 'primary_key' => 'id_permission'])
            ->addColumn('id_permission', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('nama_permission', 'string', ['limit' => 100])
            ->addColumn('deskripsi_permission', 'text', ['null' => true])
            ->addColumn('grup', 'string', ['limit' => 50, 'default' => 'lainnya'])
            ->addIndex(['nama_permission'], ['unique' => true])
            ->create();

        $this->table('tbl_role_permissions', ['id' => false, 'primary_key' => ['id_role', 'id_permission']])
            ->addColumn('id_role', 'integer', ['signed' => false])
            ->addColumn('id_permission', 'integer', ['signed' => false])
            ->create();

        // Tabel Pengguna
        $this->table('tbl_pengguna', ['id' => false, 'primary_key' => 'id_pengguna'])
            ->addColumn('id_pengguna', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('nama_lengkap', 'string', ['limit' => 255])
            ->addColumn('username', 'string', ['limit' => 100])
            ->addColumn('password', 'string', ['limit' => 255])
            ->addColumn('id_bagian', 'integer', ['signed' => false])
            ->addColumn('id_role', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('is_active', 'boolean', ['default' => true])
            ->addIndex(['username'], ['unique' => true])
            ->create();

        // Tabel Barang
        $this->table('tbl_barang', ['id' => false, 'primary_key' => 'id_barang'])
            ->addColumn('id_barang', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('kode_barang', 'string', ['limit' => 50])
            ->addColumn('nama_barang', 'string', ['limit' => 255])
            ->addColumn('jenis_barang', 'enum', ['values' => ['habis_pakai', 'aset'], 'default' => 'habis_pakai'])
            ->addColumn('id_kategori', 'integer', ['signed' => false])
            ->addColumn('id_satuan', 'integer', ['signed' => false])
            ->addColumn('stok_umum', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('stok_perkara', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['kode_barang'], ['unique' => true])
            ->addIndex(['deleted_at'])
            ->create();

        // Tabel Transaksi dan Log
        $this->table('tbl_log_stok', ['id' => false, 'primary_key' => 'id_log'])
            ->addColumn('id_log', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('id_barang', 'integer', ['signed' => false])
            ->addColumn('jenis_transaksi', 'enum', ['values' => ['masuk', 'keluar', 'penyesuaian', 'dihapus']])
            ->addColumn('jumlah_ubah', 'integer')
            ->addColumn('stok_sebelum_total', 'integer', ['signed' => false])
            ->addColumn('stok_sesudah_total', 'integer', ['signed' => false])
            ->addColumn('id_referensi', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('keterangan', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('id_pengguna_aksi', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('tanggal_log', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $this->table('tbl_permintaan_atk', ['id' => false, 'primary_key' => 'id_permintaan'])
            ->addColumn('id_permintaan', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('kode_permintaan', 'string', ['limit' => 50])
            ->addColumn('id_pengguna_pemohon', 'integer', ['signed' => false])
            ->addColumn('tanggal_permintaan', 'date')
            ->addColumn('tipe_permintaan', 'enum', ['values' => ['stok', 'pembelian'], 'default' => 'stok'])
            ->addColumn('status_permintaan', 'enum', ['values' => ['Diajukan', 'Disetujui', 'Ditolak', 'Selesai', 'Diproses Pembelian', 'Sudah Dibeli'], 'default' => 'Diajukan'])
            ->addColumn('catatan_pemohon', 'text', ['null' => true])
            ->addColumn('id_pengguna_penyetuju', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('tanggal_diproses', 'datetime', ['null' => true])
            ->addColumn('catatan_penyetuju', 'text', ['null' => true])
            ->addColumn('id_pengguna_pembelian', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['kode_permintaan'], ['unique' => true])
            ->create();

        $this->table('tbl_detail_permintaan_atk', ['id' => false, 'primary_key' => 'id_detail_permintaan'])
            ->addColumn('id_detail_permintaan', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('id_permintaan', 'integer', ['signed' => false])
            ->addColumn('id_barang', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('nama_barang_custom', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('jumlah_diminta', 'integer', ['signed' => false])
            ->addColumn('jumlah_disetujui', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('status_item', 'enum', ['values' => ['Selesai Diterima'], 'null' => true])
            ->addColumn('id_barang_masuk_detail', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $this->table('tbl_barang_masuk', ['id' => false, 'primary_key' => 'id_barang_masuk'])
            ->addColumn('id_barang_masuk', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('no_transaksi_masuk', 'string', ['limit' => 50])
            ->addColumn('id_pemasok', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('tanggal_masuk', 'date')
            ->addColumn('id_pengguna_penerima', 'integer', ['signed' => false])
            ->addColumn('id_permintaan_terkait', 'integer', ['signed' => false, 'null' => true])
            ->addIndex(['no_transaksi_masuk'], ['unique' => true])
            ->create();

        $this->table('tbl_detail_barang_masuk', ['id' => false, 'primary_key' => 'id_detail_masuk'])
            ->addColumn('id_detail_masuk', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('id_barang_masuk', 'integer', ['signed' => false])
            ->addColumn('id_barang', 'integer', ['signed' => false])
            ->addColumn('jumlah_diterima', 'integer', ['signed' => false])
            ->addColumn('jumlah_umum', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('jumlah_perkara', 'integer', ['signed' => false, 'default' => 0])
            ->create();

        $this->table('tbl_barang_keluar', ['id' => false, 'primary_key' => 'id_barang_keluar'])
            ->addColumn('id_barang_keluar', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('id_detail_permintaan', 'integer', ['signed' => false])
            ->addColumn('jumlah_keluar', 'integer', ['signed' => false])
            ->addColumn('tanggal_keluar', 'date')
            ->addColumn('id_admin_gudang', 'integer', ['signed' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();


        // BAGIAN 2: PENAMBAHAN FOREIGN KEY
        $this->table('tbl_role_permissions')->addForeignKey('id_role', 'tbl_roles', 'id_role', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_role_permissions')->addForeignKey('id_permission', 'tbl_permissions', 'id_permission', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_pengguna')->addForeignKey('id_bagian', 'tbl_bagian', 'id_bagian', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_pengguna')->addForeignKey('id_role', 'tbl_roles', 'id_role', ['delete'=> 'SET_NULL', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_barang')->addForeignKey('id_kategori', 'tbl_kategori_barang', 'id_kategori', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_barang')->addForeignKey('id_satuan', 'tbl_satuan_barang', 'id_satuan', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_log_stok')->addForeignKey('id_barang', 'tbl_barang', 'id_barang', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_permintaan_atk')->addForeignKey('id_pengguna_pemohon', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_permintaan_atk')->addForeignKey('id_pengguna_penyetuju', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_permintaan_atk')->addForeignKey('id_pengguna_pembelian', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_detail_permintaan_atk')->addForeignKey('id_permintaan', 'tbl_permintaan_atk', 'id_permintaan', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_detail_permintaan_atk')->addForeignKey('id_barang', 'tbl_barang', 'id_barang', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_barang_masuk')->addForeignKey('id_pemasok', 'tbl_pemasok', 'id_pemasok', ['delete'=> 'SET_NULL', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_barang_masuk')->addForeignKey('id_pengguna_penerima', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_barang_masuk')->addForeignKey('id_permintaan_terkait', 'tbl_permintaan_atk', 'id_permintaan', ['delete'=> 'SET_NULL', 'update'=> 'CASCADE'])->update();
        $this->table('tbl_detail_barang_masuk')->addForeignKey('id_barang_masuk', 'tbl_barang_masuk', 'id_barang_masuk', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_detail_barang_masuk')->addForeignKey('id_barang', 'tbl_barang', 'id_barang', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_barang_keluar')->addForeignKey('id_detail_permintaan', 'tbl_detail_permintaan_atk', 'id_detail_permintaan', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();
        $this->table('tbl_barang_keluar')->addForeignKey('id_admin_gudang', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])->update();

        // BAGIAN 3: PEMBUATAN VIEW
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

        // BAGIAN 4: PEMBUATAN TRIGGER
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
        // Phinx akan otomatis membatalkan pembuatan tabel jika memungkinkan.
        // Untuk yang lebih kompleks, kita bisa definisikan di sini.
        // Cukup drop semua tabel untuk reset.
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->table('tbl_barang_keluar')->drop()->save();
        $this->table('tbl_detail_barang_masuk')->drop()->save();
        $this->table('tbl_barang_masuk')->drop()->save();
        $this->table('tbl_detail_permintaan_atk')->drop()->save();
        $this->table('tbl_permintaan_atk')->drop()->save();
        $this->table('tbl_log_stok')->drop()->save();
        $this->table('tbl_barang')->drop()->save();
        $this->table('tbl_pengguna')->drop()->save();
        $this->table('tbl_role_permissions')->drop()->save();
        $this->table('tbl_permissions')->drop()->save();
        $this->table('tbl_roles')->drop()->save();
        $this->table('tbl_pemasok')->drop()->save();
        $this->table('tbl_bagian')->drop()->save();
        $this->table('tbl_satuan_barang')->drop()->save();
        $this->table('tbl_kategori_barang')->drop()->save();
        $this->execute("DROP VIEW IF EXISTS v_permintaan_lengkap;");
        $this->execute("DROP TRIGGER IF EXISTS before_barang_delete;");
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }
}