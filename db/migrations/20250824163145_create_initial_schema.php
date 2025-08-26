<?php
use Phinx\Migration\AbstractMigration;

class CreateInitialSchema extends AbstractMigration
{
    public function change()
    {
        // =============================================================
        // LANGKAH 1: BUAT SEMUA TABEL TANPA FOREIGN KEY
        // =============================================================

        if (!$this->hasTable('tbl_kategori_barang')) {
            $this->table('tbl_kategori_barang', ['id' => false, 'primary_key' => ['id_kategori']])
                ->addColumn('id_kategori', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('nama_kategori', 'string', ['limit' => 100])
                ->addIndex(['nama_kategori'], ['unique' => true])
                ->create();
        }

        if (!$this->hasTable('tbl_satuan_barang')) {
            $this->table('tbl_satuan_barang', ['id' => false, 'primary_key' => ['id_satuan']])
                ->addColumn('id_satuan', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('nama_satuan', 'string', ['limit' => 50])
                ->addIndex(['nama_satuan'], ['unique' => true])
                ->create();
        }

        if (!$this->hasTable('tbl_bagian')) {
            $this->table('tbl_bagian', ['id' => false, 'primary_key' => ['id_bagian']])
                ->addColumn('id_bagian', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('nama_bagian', 'string', ['limit' => 150])
                ->addIndex(['nama_bagian'], ['unique' => true])
                ->create();
        }

        if (!$this->hasTable('tbl_pemasok')) {
            $this->table('tbl_pemasok', ['id' => false, 'primary_key' => ['id_pemasok']])
                ->addColumn('id_pemasok', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('nama_pemasok', 'string', ['limit' => 255])
                ->addColumn('alamat', 'text', ['null' => true])
                ->addColumn('no_telepon', 'string', ['limit' => 20, 'null' => true])
                ->addColumn('email', 'string', ['limit' => 100, 'null' => true])
                ->create();
        }

        if (!$this->hasTable('tbl_pengguna')) {
            $this->table('tbl_pengguna', ['id' => false, 'primary_key' => ['id_pengguna']])
                ->addColumn('id_pengguna', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('nama_lengkap', 'string', ['limit' => 255])
                ->addColumn('username', 'string', ['limit' => 100])
                ->addColumn('password', 'string', ['limit' => 255])
                ->addColumn('id_bagian', 'integer', ['signed' => false])
                ->addColumn('role', 'enum', ['values' => ['admin', 'pimpinan', 'pegawai']])
                ->addColumn('is_active', 'boolean', ['default' => true])
                ->addIndex(['username'], ['unique' => true])
                ->create();
        }

        if (!$this->hasTable('tbl_barang')) {
            $this->table('tbl_barang', ['id' => false, 'primary_key' => ['id_barang']])
                ->addColumn('id_barang', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('kode_barang', 'string', ['limit' => 50])
                ->addColumn('nama_barang', 'string', ['limit' => 255])
                ->addColumn('jenis_barang', 'enum', ['values' => ['habis_pakai', 'aset'], 'default' => 'habis_pakai'])
                ->addColumn('id_kategori', 'integer', ['signed' => false])
                ->addColumn('id_satuan', 'integer', ['signed' => false])
                ->addColumn('stok_saat_ini', 'integer', ['default' => 0])
                ->addColumn('deleted_at', 'timestamp', ['null' => true])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['kode_barang'], ['unique' => true])
                ->addIndex(['deleted_at'])
                ->create();
        }

        if (!$this->hasTable('tbl_log_stok')) {
            $this->table('tbl_log_stok', ['id' => false, 'primary_key' => ['id_log']])
                ->addColumn('id_log', 'biginteger', ['identity' => true, 'signed' => false])
                ->addColumn('id_barang', 'integer', ['signed' => false])
                ->addColumn('jenis_transaksi', 'enum', ['values' => ['masuk', 'keluar', 'penyesuaian', 'dihapus']])
                ->addColumn('jumlah_ubah', 'integer')
                ->addColumn('stok_sebelum', 'integer')
                ->addColumn('stok_sesudah', 'integer')
                ->addColumn('id_referensi', 'integer', ['null' => true, 'signed' => false])
                ->addColumn('keterangan', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('id_pengguna_aksi', 'integer', ['null' => true, 'signed' => false])
                ->addColumn('tanggal_log', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->create();
        }

        if (!$this->hasTable('tbl_permintaan_atk')) {
            $this->table('tbl_permintaan_atk', ['id' => false, 'primary_key' => ['id_permintaan']])
                ->addColumn('id_permintaan', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('kode_permintaan', 'string', ['limit' => 50])
                ->addColumn('id_pengguna_pemohon', 'integer', ['signed' => false])
                ->addColumn('tanggal_permintaan', 'date')
                ->addColumn('status_permintaan', 'enum', ['values' => ['Diajukan', 'Disetujui', 'Ditolak', 'Selesai'], 'default' => 'Diajukan'])
                ->addColumn('catatan_pemohon', 'text', ['null' => true])
                ->addColumn('id_pengguna_penyetuju', 'integer', ['null' => true, 'signed' => false])
                ->addColumn('tanggal_diproses', 'datetime', ['null' => true])
                ->addColumn('catatan_penyetuju', 'text', ['null' => true])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['kode_permintaan'], ['unique' => true])
                ->create();
        }

        if (!$this->hasTable('tbl_detail_permintaan_atk')) {
            $this->table('tbl_detail_permintaan_atk', ['id' => false, 'primary_key' => ['id_detail_permintaan']])
                ->addColumn('id_detail_permintaan', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('id_permintaan', 'integer', ['signed' => false])
                ->addColumn('id_barang', 'integer', ['signed' => false])
                ->addColumn('jumlah_diminta', 'integer')
                ->addColumn('jumlah_disetujui', 'integer', ['null' => true])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->create();
        }

        if (!$this->hasTable('tbl_barang_keluar')) {
            $this->table('tbl_barang_keluar', ['id' => false, 'primary_key' => ['id_barang_keluar']])
                ->addColumn('id_barang_keluar', 'integer', ['identity' => true, 'signed' => false])
                ->addColumn('id_detail_permintaan', 'integer', ['signed' => false])
                ->addColumn('jumlah_keluar', 'integer')
                ->addColumn('tanggal_keluar', 'date')
                ->addColumn('id_admin_gudang', 'integer', ['signed' => false])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->create();
        }
            
        // =============================================================
        // LANGKAH 2: TAMBAHKAN SEMUA FOREIGN KEY SETELAH TABEL DIBUAT
        // =============================================================
        
        $this->table('tbl_pengguna')
            ->addForeignKey('id_bagian', 'tbl_bagian', 'id_bagian', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->update();

        $this->table('tbl_barang')
            ->addForeignKey('id_kategori', 'tbl_kategori_barang', 'id_kategori', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->addForeignKey('id_satuan', 'tbl_satuan_barang', 'id_satuan', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->update();

        $this->table('tbl_log_stok')
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->update();

        $this->table('tbl_permintaan_atk')
            ->addForeignKey('id_pengguna_pemohon', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->addForeignKey('id_pengguna_penyetuju', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->update();
            
        $this->table('tbl_detail_permintaan_atk')
            ->addForeignKey('id_permintaan', 'tbl_permintaan_atk', 'id_permintaan', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->update();

        $this->table('tbl_barang_keluar')
            ->addForeignKey('id_detail_permintaan', 'tbl_detail_permintaan_atk', 'id_detail_permintaan', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->addForeignKey('id_admin_gudang', 'tbl_pengguna', 'id_pengguna', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->update();
            
        // =============================================================
        // LANGKAH 3: BUAT VIEW SETELAH SEMUA TABEL DAN RELASI SELESAI
        // =============================================================
        $viewQuery = "
            CREATE OR REPLACE VIEW `v_permintaan_lengkap` AS
            SELECT 
                p.id_permintaan,
                p.kode_permintaan,
                p.tanggal_permintaan,
                p.tipe_permintaan,
                p.status_permintaan,
                p.catatan_pemohon,
                p.id_pengguna_pemohon,
                pemohon.nama_lengkap AS nama_pemohon,
                pemohon.id_bagian AS id_bagian_pemohon,
                p.id_pengguna_penyetuju,
                penyetuju.nama_lengkap AS nama_penyetuju,
                p.tanggal_diproses,
                p.catatan_penyetuju,
                (SELECT COUNT(*) FROM tbl_detail_permintaan_atk dp WHERE dp.id_permintaan = p.id_permintaan) AS jumlah_item
            FROM 
                `tbl_permintaan_atk` p
            JOIN 
                `tbl_pengguna` pemohon ON p.id_pengguna_pemohon = pemohon.id_pengguna
            LEFT JOIN 
                `tbl_pengguna` penyetuju ON p.id_pengguna_penyetuju = penyetuju.id_pengguna;
        ";
        
        $this->execute($viewQuery);

        // =============================================================
        // LANGKAH 4: BUAT TRIGGER OTOMATIS
        // =============================================================
        $triggerInsert = "
            CREATE TRIGGER `after_barang_insert`
            AFTER INSERT ON `tbl_barang`
            FOR EACH ROW
            BEGIN
                INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, keterangan)
                VALUES (NEW.id_barang, 'penyesuaian', NEW.stok_saat_ini, 0, NEW.stok_saat_ini, 'Stok awal barang baru');
            END
        ";
        $this->execute($triggerInsert);

        $triggerUpdate = "
            CREATE TRIGGER `after_barang_update`
            AFTER UPDATE ON `tbl_barang`
            FOR EACH ROW
            BEGIN
                IF OLD.stok_saat_ini <> NEW.stok_saat_ini THEN
                    INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, keterangan)
                    VALUES (NEW.id_barang, 'penyesuaian', (NEW.stok_saat_ini - OLD.stok_saat_ini), OLD.stok_saat_ini, NEW.stok_saat_ini, 'Penyesuaian stok manual');
                END IF;
            END
        ";
        $this->execute($triggerUpdate);
    }
}