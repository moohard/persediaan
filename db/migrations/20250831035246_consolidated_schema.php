<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ConsolidatedSchema extends AbstractMigration
    {
    public function change(): void
        {
        $this->execute('DROP TABLE IF EXISTS phinxlog');

        // Buat ulang tabel phinxlog dengan struktur yang benar
        $phinxlog = $this->table('phinxlog', [
            'id'          => FALSE,
            'primary_key' => ['version'],
            'engine'      => 'InnoDB',
            'encoding'    => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
        ]);

        $phinxlog->addColumn('version', 'biginteger', ['null' => FALSE])
            ->addColumn('migration_name', 'string', ['limit' => 100, 'null' => TRUE])
            ->addColumn('start_time', 'timestamp', ['null' => TRUE])
            ->addColumn('end_time', 'timestamp', ['null' => TRUE])
            ->addColumn('breakpoint', 'boolean', ['default' => FALSE])
            ->create();

        // Table: tbl_bagian
        $bagian = $this->table('tbl_bagian', [
            'id'        => 'id_bagian',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $bagian->addColumn('nama_bagian', 'string', ['limit' => 150, 'null' => TRUE])
            ->addIndex(['nama_bagian'], ['unique' => TRUE])
            ->create();

        // Table: tbl_kategori_barang
        $kategori = $this->table('tbl_kategori_barang', [
            'id'        => 'id_kategori',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $kategori->addColumn('nama_kategori', 'string', ['limit' => 100, 'null' => TRUE])
            ->addIndex(['nama_kategori'], ['unique' => TRUE])
            ->create();

        // Table: tbl_satuan_barang
        $satuan = $this->table('tbl_satuan_barang', [
            'id'        => 'id_satuan',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $satuan->addColumn('nama_satuan', 'string', ['limit' => 50, 'null' => TRUE])
            ->addIndex(['nama_satuan'], ['unique' => TRUE])
            ->create();

        // Table: tbl_roles
        $roles = $this->table('tbl_roles', [
            'id'        => 'id_role',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $roles->addColumn('nama_role', 'string', ['limit' => 100, 'null' => TRUE])
            ->addColumn('deskripsi_role', 'text', ['null' => TRUE])
            ->addIndex(['nama_role'], ['unique' => TRUE])
            ->create();

        // Table: tbl_pengguna
        $pengguna = $this->table('tbl_pengguna', [
            'id'        => 'id_pengguna',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $pengguna->addColumn('nama_lengkap', 'string', ['limit' => 255, 'null' => TRUE])
            ->addColumn('username', 'string', ['limit' => 100, 'null' => TRUE])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => TRUE])
            ->addColumn('id_bagian', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('is_active', 'boolean', ['default' => TRUE])
            ->addColumn('id_role', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addIndex(['username'], ['unique' => TRUE])
            ->addIndex(['id_bagian'])
            ->addIndex(['id_role'])
            ->addForeignKey('id_bagian', 'tbl_bagian', 'id_bagian', [
                'update' => 'CASCADE',
                'delete' => 'RESTRICT',
            ])
            ->addForeignKey('id_role', 'tbl_roles', 'id_role', [
                'update' => 'CASCADE',
                'delete' => 'SET NULL',
            ])
            ->create();

        // Table: tbl_barang
        $barang = $this->table('tbl_barang', [
            'id'        => 'id_barang',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $barang->addColumn('kode_barang', 'string', ['limit' => 50, 'null' => TRUE])
            ->addColumn('nama_barang', 'string', ['limit' => 255, 'null' => TRUE])
            ->addColumn('jenis_barang', 'enum', [
                'values'  => ['habis_pakai', 'aset'],
                'default' => 'habis_pakai',
            ])
            ->addColumn('id_kategori', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('id_satuan', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('stok_umum', 'integer', ['default' => 0, 'signed' => FALSE])
            ->addColumn('stok_perkara', 'integer', ['default' => 0, 'signed' => FALSE])
            ->addColumn('deleted_at', 'timestamp', ['null' => TRUE])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update'  => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['kode_barang'], ['unique' => TRUE])
            ->addIndex(['deleted_at'])
            ->addIndex(['id_kategori'])
            ->addIndex(['id_satuan'])
            ->addForeignKey('id_kategori', 'tbl_kategori_barang', 'id_kategori', [
                'update' => 'CASCADE',
                'delete' => 'RESTRICT',
            ])
            ->addForeignKey('id_satuan', 'tbl_satuan_barang', 'id_satuan', [
                'update' => 'CASCADE',
                'delete' => 'RESTRICT',
            ])
            ->create();

        // Table: tbl_pemasok
        $pemasok = $this->table('tbl_pemasok', [
            'id'        => 'id_pemasok',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $pemasok->addColumn('nama_pemasok', 'string', ['limit' => 255, 'null' => TRUE])
            ->addColumn('alamat', 'text', ['null' => TRUE])
            ->addColumn('no_telepon', 'string', ['limit' => 20, 'null' => TRUE])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => TRUE])
            ->create();

        // Table: tbl_permintaan_atk
        $permintaan = $this->table('tbl_permintaan_atk', [
            'id'        => 'id_permintaan',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $permintaan->addColumn('kode_permintaan', 'string', ['limit' => 50, 'null' => TRUE])
            ->addColumn('id_pengguna_pemohon', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('tanggal_permintaan', 'date', ['null' => TRUE])
            ->addColumn('tipe_permintaan', 'enum', [
                'values'  => ['stok', 'pembelian'],
                'default' => 'stok',
            ])
            ->addColumn('status_permintaan', 'enum', [
                'values'  => ['Diajukan', 'Disetujui', 'Ditolak', 'Selesai', 'Diproses Pembelian', 'Sudah Dibeli'],
                'default' => 'Diajukan',
            ])
            ->addColumn('catatan_pemohon', 'text', ['null' => TRUE])
            ->addColumn('id_pengguna_penyetuju', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('tanggal_diproses', 'datetime', ['null' => TRUE])
            ->addColumn('catatan_penyetuju', 'text', ['null' => TRUE])
            ->addColumn('id_pengguna_pembelian', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['kode_permintaan'], ['unique' => TRUE])
            ->addIndex(['id_pengguna_pemohon'])
            ->addIndex(['id_pengguna_penyetuju'])
            ->addIndex(['id_pengguna_pembelian'])
            ->addForeignKey('id_pengguna_pemohon', 'tbl_pengguna', 'id_pengguna', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->addForeignKey('id_pengguna_penyetuju', 'tbl_pengguna', 'id_pengguna', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->addForeignKey('id_pengguna_pembelian', 'tbl_pengguna', 'id_pengguna', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->create();

        // Table: tbl_detail_permintaan_atk
        $detailPermintaan = $this->table('tbl_detail_permintaan_atk', [
            'id'        => 'id_detail_permintaan',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $detailPermintaan->addColumn('id_permintaan', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('id_barang', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('nama_barang_custom', 'string', ['limit' => 255, 'null' => TRUE])
            ->addColumn('jumlah_diminta', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('jumlah_disetujui', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('status_item', 'enum', [
                'values' => ['Selesai Diterima'],
                'null'   => TRUE,
            ])
            ->addColumn('id_barang_masuk_detail', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['id_permintaan'])
            ->addIndex(['id_barang'])
            ->addForeignKey('id_permintaan', 'tbl_permintaan_atk', 'id_permintaan', [
                'update' => 'NO ACTION',
                'delete' => 'CASCADE',
            ])
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->create();

        // Table: tbl_barang_masuk
        $barangMasuk = $this->table('tbl_barang_masuk', [
            'id'        => 'id_barang_masuk',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $barangMasuk->addColumn('no_transaksi_masuk', 'string', ['limit' => 50, 'null' => TRUE])
            ->addColumn('id_pemasok', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('tanggal_masuk', 'date', ['null' => TRUE])
            ->addColumn('id_pengguna_penerima', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('id_permintaan_terkait', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addIndex(['no_transaksi_masuk'], ['unique' => TRUE])
            ->addIndex(['id_pemasok'])
            ->addIndex(['id_pengguna_penerima'])
            ->addIndex(['id_permintaan_terkait'])
            ->addForeignKey('id_pemasok', 'tbl_pemasok', 'id_pemasok', [
                'update' => 'CASCADE',
                'delete' => 'SET NULL',
            ])
            ->addForeignKey('id_pengguna_penerima', 'tbl_pengguna', 'id_pengguna', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->addForeignKey('id_permintaan_terkait', 'tbl_permintaan_atk', 'id_permintaan', [
                'update' => 'CASCADE',
                'delete' => 'SET NULL',
            ])
            ->create();

        // Table: tbl_detail_barang_masuk
        $detailBarangMasuk = $this->table('tbl_detail_barang_masuk', [
            'id'        => 'id_detail_masuk',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $detailBarangMasuk->addColumn('id_barang_masuk', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('id_barang', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('jumlah_diterima', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('jumlah_umum', 'integer', ['default' => 0, 'signed' => FALSE])
            ->addColumn('jumlah_perkara', 'integer', ['default' => 0, 'signed' => FALSE])
            ->addIndex(['id_barang_masuk'])
            ->addIndex(['id_barang'])
            ->addForeignKey('id_barang_masuk', 'tbl_barang_masuk', 'id_barang_masuk', [
                'update' => 'NO ACTION',
                'delete' => 'CASCADE',
            ])
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->create();

        // Table: tbl_barang_keluar
        $barangKeluar = $this->table('tbl_barang_keluar', [
            'id'        => 'id_barang_keluar',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $barangKeluar->addColumn('id_detail_permintaan', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('jumlah_keluar', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('tanggal_keluar', 'date', ['null' => TRUE])
            ->addColumn('id_admin_gudang', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['id_detail_permintaan'])
            ->addIndex(['id_admin_gudang'])
            ->addForeignKey('id_detail_permintaan', 'tbl_detail_permintaan_atk', 'id_detail_permintaan', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->addForeignKey('id_admin_gudang', 'tbl_pengguna', 'id_pengguna', [
                'update' => 'NO ACTION',
                'delete' => 'NO ACTION',
            ])
            ->create();

        // Table: tbl_log_stok
        $logStok = $this->table('tbl_log_stok', [
            'id'        => 'id_log',
            'signed'    => FALSE,
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $logStok->addColumn('id_barang', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('jenis_transaksi', 'enum', [
                'values' => ['masuk', 'keluar', 'penyesuaian', 'dihapus'],
                'null'   => TRUE,
            ])
            ->addColumn('jumlah_ubah', 'integer', ['null' => TRUE])
            ->addColumn('stok_sebelum_umum', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('stok_sesudah_umum', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('stok_sebelum_perkara', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('stok_sesudah_perkara', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('id_referensi', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('keterangan', 'string', ['limit' => 255, 'null' => TRUE])
            ->addColumn('id_pengguna_aksi', 'integer', ['null' => TRUE, 'signed' => FALSE])
            ->addColumn('tanggal_log', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['id_barang'])
            ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [
                'update' => 'NO ACTION',
                'delete' => 'CASCADE',
            ])
            ->create();

        // Table: tbl_permissions
        $permissions = $this->table('tbl_permissions', [
            'id'        => 'id_permission',
            'engine'    => 'InnoDB',
            'encoding'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
        $permissions->addColumn('nama_permission', 'string', ['limit' => 100, 'null' => TRUE])
            ->addColumn('deskripsi_permission', 'text', ['null' => TRUE])
            ->addColumn('grup', 'string', ['limit' => 50, 'default' => 'lainnya'])
            ->addIndex(['nama_permission'], ['unique' => TRUE])
            ->create();

        // Table: tbl_role_permissions
        $rolePermissions = $this->table('tbl_role_permissions', [
            'id'          => FALSE,
            'primary_key' => ['id_role', 'id_permission'],
            'engine'      => 'InnoDB',
            'encoding'    => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
        ]);
        $rolePermissions->addColumn('id_role', 'integer', ['null' => FALSE, 'signed' => FALSE])
            ->addColumn('id_permission', 'integer', ['null' => FALSE, 'signed' => FALSE])
            ->addIndex(['id_permission'])
            ->addForeignKey('id_role', 'tbl_roles', 'id_role', [
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ])
            ->addForeignKey('id_permission', 'tbl_permissions', 'id_permission', [
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ])
            ->create();

        // View: v_permintaan_lengkap
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

        // Trigger: before_barang_delete
        $this->execute("
            CREATE TRIGGER before_barang_delete BEFORE DELETE ON tbl_barang
            FOR EACH ROW
            BEGIN
                INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum_umum, stok_sesudah_umum, stok_sebelum_perkara, stok_sesudah_perkara, keterangan, id_pengguna_aksi, tanggal_log)
                VALUES (OLD.id_barang, 'dihapus', -(OLD.stok_umum + OLD.stok_perkara), OLD.stok_umum, 0, OLD.stok_perkara, 0, CONCAT('Penghapusan permanen barang: ', OLD.nama_barang), @user_id, NOW());
            END
        ");
        }
    }