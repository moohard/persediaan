<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialSchema extends AbstractMigration
{
    public function up(): void
    {
        // Tabel Roles
        $roles = $this->table('tbl_roles', ['id' => 'id_role']);
        $roles->addColumn('nama_role', 'string', ['limit' => 100])
              ->addColumn('deskripsi_role', 'text', ['null' => true])
              ->addIndex(['nama_role'], ['unique' => true])
              ->create();

        // Tabel Permissions
        $permissions = $this->table('tbl_permissions', ['id' => 'id_permission']);
        $permissions->addColumn('nama_permission', 'string', ['limit' => 100])
                   ->addColumn('deskripsi_permission', 'text', ['null' => true])
                   ->addColumn('grup', 'string', ['limit' => 50, 'default' => 'lainnya'])
                   ->addIndex(['nama_permission'], ['unique' => true])
                   ->create();

        // Tabel Role Permissions
        $rolePermissions = $this->table('tbl_role_permissions', ['id' => false]);
        $rolePermissions->addColumn('id_role', 'integer')
                       ->addColumn('id_permission', 'integer')
                       ->addForeignKey('id_role', 'tbl_roles', 'id_role', [
                           'delete' => 'CASCADE',
                           'update' => 'CASCADE'
                       ])
                       ->addForeignKey('id_permission', 'tbl_permissions', 'id_permission', [
                           'delete' => 'CASCADE',
                           'update' => 'CASCADE'
                       ])
                       ->addIndex(['id_role', 'id_permission'], ['unique' => true])
                       ->create();

        // Tabel Bagian
        $bagian = $this->table('tbl_bagian', ['id' => 'id_bagian']);
        $bagian->addColumn('nama_bagian', 'string', ['limit' => 150])
               ->addIndex(['nama_bagian'], ['unique' => true])
               ->create();

        // Tabel Pengguna
        $pengguna = $this->table('tbl_pengguna', ['id' => 'id_pengguna']);
        $pengguna->addColumn('nama_lengkap', 'string', ['limit' => 255])
                ->addColumn('username', 'string', ['limit' => 100])
                ->addColumn('password', 'string', ['limit' => 255])
                ->addColumn('id_bagian', 'integer', ['null' => true])
                ->addColumn('is_active', 'boolean', ['default' => true])
                ->addColumn('id_role', 'integer', ['null' => true])
                ->addIndex(['username'], ['unique' => true])
                ->addForeignKey('id_bagian', 'tbl_bagian', 'id_bagian', [
                    'delete' => 'RESTRICT',
                    'update' => 'CASCADE'
                ])
                ->addForeignKey('id_role', 'tbl_roles', 'id_role', [
                    'delete' => 'SET_NULL',
                    'update' => 'CASCADE'
                ])
                ->create();

        // Tabel Kategori Barang
        $kategori = $this->table('tbl_kategori_barang', ['id' => 'id_kategori']);
        $kategori->addColumn('nama_kategori', 'string', ['limit' => 100])
                ->addIndex(['nama_kategori'], ['unique' => true])
                ->create();

        // Tabel Satuan Barang
        $satuan = $this->table('tbl_satuan_barang', ['id' => 'id_satuan']);
        $satuan->addColumn('nama_satuan', 'string', ['limit' => 50])
              ->addIndex(['nama_satuan'], ['unique' => true])
              ->create();

        // Tabel Barang
        $barang = $this->table('tbl_barang', ['id' => 'id_barang']);
        $barang->addColumn('kode_barang', 'string', ['limit' => 50])
              ->addColumn('nama_barang', 'string', ['limit' => 255])
              ->addColumn('jenis_barang', 'enum', [
                  'values' => ['habis_pakai', 'aset'],
                  'default' => 'habis_pakai'
              ])
              ->addColumn('id_kategori', 'integer', ['null' => true])
              ->addColumn('id_satuan', 'integer', ['null' => true])
              ->addColumn('stok_umum', 'integer', ['default' => 0])
              ->addColumn('stok_perkara', 'integer', ['default' => 0])
              ->addColumn('deleted_at', 'timestamp', ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', [
                  'default' => 'CURRENT_TIMESTAMP',
                  'update' => 'CURRENT_TIMESTAMP'
              ])
              ->addIndex(['kode_barang'], ['unique' => true])
              ->addIndex(['deleted_at'])
              ->addForeignKey('id_kategori', 'tbl_kategori_barang', 'id_kategori', [
                  'delete' => 'RESTRICT',
                  'update' => 'CASCADE'
              ])
              ->addForeignKey('id_satuan', 'tbl_satuan_barang', 'id_satuan', [
                  'delete' => 'RESTRICT',
                  'update' => 'CASCADE'
              ])
              ->create();

        // Tabel Pemasok
        $pemasok = $this->table('tbl_pemasok', ['id' => 'id_pemasok']);
        $pemasok->addColumn('nama_pemasok', 'string', ['limit' => 255])
               ->addColumn('alamat', 'text', ['null' => true])
               ->addColumn('no_telepon', 'string', ['limit' => 20, 'null' => true])
               ->addColumn('email', 'string', ['limit' => 100, 'null' => true])
               ->create();

        // Tabel Permintaan ATK
        $permintaan = $this->table('tbl_permintaan_atk', ['id' => 'id_permintaan']);
        $permintaan->addColumn('kode_permintaan', 'string', ['limit' => 50])
                  ->addColumn('id_pengguna_pemohon', 'integer')
                  ->addColumn('tanggal_permintaan', 'date')
                  ->addColumn('tipe_permintaan', 'enum', [
                      'values' => ['stok', 'pembelian'],
                      'default' => 'stok'
                  ])
                  ->addColumn('status_permintaan', 'enum', [
                      'values' => ['Diajukan', 'Disetujui', 'Ditolak', 'Selesai', 'Diproses Pembelian', 'Sudah Dibeli'],
                      'default' => 'Diajukan'
                  ])
                  ->addColumn('catatan_pemohon', 'text', ['null' => true])
                  ->addColumn('id_pengguna_penyetuju', 'integer', ['null' => true])
                  ->addColumn('tanggal_diproses', 'datetime', ['null' => true])
                  ->addColumn('catatan_penyetuju', 'text', ['null' => true])
                  ->addColumn('id_pengguna_pembelian', 'integer', ['null' => true])
                  ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                  ->addIndex(['kode_permintaan'], ['unique' => true])
                  ->addForeignKey('id_pengguna_pemohon', 'tbl_pengguna', 'id_pengguna')
                  ->addForeignKey('id_pengguna_penyetuju', 'tbl_pengguna', 'id_pengguna')
                  ->addForeignKey('id_pengguna_pembelian', 'tbl_pengguna', 'id_pengguna')
                  ->create();

        // Tabel Detail Permintaan ATK
        $detailPermintaan = $this->table('tbl_detail_permintaan_atk', ['id' => 'id_detail_permintaan']);
        $detailPermintaan->addColumn('id_permintaan', 'integer')
                        ->addColumn('id_barang', 'integer', ['null' => true])
                        ->addColumn('nama_barang_custom', 'string', ['limit' => 255, 'null' => true])
                        ->addColumn('jumlah_diminta', 'integer')
                        ->addColumn('jumlah_disetujui', 'integer', ['null' => true])
                        ->addColumn('status_item', 'enum', [
                            'values' => ['Selesai Diterima'],
                            'null' => true
                        ])
                        ->addColumn('id_barang_masuk_detail', 'integer', ['null' => true])
                        ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                        ->addForeignKey('id_permintaan', 'tbl_permintaan_atk', 'id_permintaan', [
                            'delete' => 'CASCADE'
                        ])
                        ->addForeignKey('id_barang', 'tbl_barang', 'id_barang')
                        ->create();

        // Tabel Barang Masuk
        $barangMasuk = $this->table('tbl_barang_masuk', ['id' => 'id_barang_masuk']);
        $barangMasuk->addColumn('no_transaksi_masuk', 'string', ['limit' => 50])
                   ->addColumn('id_pemasok', 'integer', ['null' => true])
                   ->addColumn('tanggal_masuk', 'date')
                   ->addColumn('id_pengguna_penerima', 'integer')
                   ->addColumn('id_permintaan_terkait', 'integer', ['null' => true])
                   ->addIndex(['no_transaksi_masuk'], ['unique' => true])
                   ->addForeignKey('id_pemasok', 'tbl_pemasok', 'id_pemasok', [
                       'delete' => 'SET_NULL',
                       'update' => 'CASCADE'
                   ])
                   ->addForeignKey('id_pengguna_penerima', 'tbl_pengguna', 'id_pengguna')
                   ->addForeignKey('id_permintaan_terkait', 'tbl_permintaan_atk', 'id_permintaan', [
                       'delete' => 'SET_NULL',
                       'update' => 'CASCADE'
                   ])
                   ->create();

        // Tabel Detail Barang Masuk
        $detailBarangMasuk = $this->table('tbl_detail_barang_masuk', ['id' => 'id_detail_masuk']);
        $detailBarangMasuk->addColumn('id_barang_masuk', 'integer')
                         ->addColumn('id_barang', 'integer', ['null' => true])
                         ->addColumn('jumlah_diterima', 'integer')
                         ->addColumn('jumlah_umum', 'integer', ['default' => 0])
                         ->addColumn('jumlah_perkara', 'integer', ['default' => 0])
                         ->addForeignKey('id_barang_masuk', 'tbl_barang_masuk', 'id_barang_masuk', [
                             'delete' => 'CASCADE'
                         ])
                         ->addForeignKey('id_barang', 'tbl_barang', 'id_barang')
                         ->create();

        // Tabel Barang Keluar
        $barangKeluar = $this->table('tbl_barang_keluar', ['id' => 'id_barang_keluar']);
        $barangKeluar->addColumn('id_detail_permintaan', 'integer', ['null' => true])
                    ->addColumn('jumlah_keluar', 'integer')
                    ->addColumn('tanggal_keluar', 'date')
                    ->addColumn('id_admin_gudang', 'integer')
                    ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('id_detail_permintaan', 'tbl_detail_permintaan_atk', 'id_detail_permintaan')
                    ->addForeignKey('id_admin_gudang', 'tbl_pengguna', 'id_pengguna')
                    ->create();

        // Tabel Stock Opname
        $stockOpname = $this->table('tbl_stock_opname', ['id' => 'id_opname']);
        $stockOpname->addColumn('kode_opname', 'string', ['limit' => 50])
                   ->addColumn('tanggal_opname', 'date')
                   ->addColumn('id_pengguna_penanggung_jawab', 'integer')
                   ->addColumn('keterangan', 'text', ['null' => true])
                   ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                   ->addColumn('status', 'enum', [
                       'values' => ['Selesai', 'Dibatalkan'],
                       'null' => true
                   ])
                   ->addIndex(['kode_opname'], ['unique' => true])
                   ->addForeignKey('id_pengguna_penanggung_jawab', 'tbl_pengguna', 'id_pengguna', [
                       'update' => 'CASCADE'
                   ])
                   ->create();

        // Tabel Detail Stock Opname
        $detailStockOpname = $this->table('tbl_detail_stock_opname', ['id' => 'id_detail_opname']);
        $detailStockOpname->addColumn('id_opname', 'integer')
                         ->addColumn('id_barang', 'integer', ['null' => true])
                         ->addColumn('stok_sistem_umum', 'integer')
                         ->addColumn('stok_sistem_perkara', 'integer')
                         ->addColumn('stok_fisik_umum', 'integer')
                         ->addColumn('stok_fisik_perkara', 'integer')
                         ->addColumn('selisih_umum', 'integer')
                         ->addColumn('selisih_perkara', 'integer')
                         ->addColumn('catatan', 'string', ['limit' => 255, 'null' => true])
                         ->addForeignKey('id_opname', 'tbl_stock_opname', 'id_opname', [
                             'delete' => 'CASCADE',
                             'update' => 'CASCADE'
                         ])
                         ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [
                             'delete' => 'CASCADE',
                             'update' => 'CASCADE'
                         ])
                         ->create();

        // Tabel Log Stok
        $logStok = $this->table('tbl_log_stok', ['id' => 'id_log']);
        $logStok->addColumn('id_barang', 'integer', ['null' => true])
               ->addColumn('jenis_transaksi', 'enum', [
                   'values' => ['masuk', 'keluar', 'penyesuaian', 'dihapus']
               ])
               ->addColumn('jumlah_ubah', 'integer')
               ->addColumn('stok_sebelum_umum', 'integer')
               ->addColumn('stok_sesudah_umum', 'integer')
               ->addColumn('stok_sebelum_perkara', 'integer')
               ->addColumn('stok_sesudah_perkara', 'integer')
               ->addColumn('id_referensi', 'integer', ['null' => true])
               ->addColumn('keterangan', 'string', ['limit' => 255, 'null' => true])
               ->addColumn('id_pengguna_aksi', 'integer', ['null' => true])
               ->addColumn('tanggal_log', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
               ->addForeignKey('id_barang', 'tbl_barang', 'id_barang', [
                   'delete' => 'CASCADE'
               ])
               ->create();

        // Tabel Notifikasi
        $notifikasi = $this->table('tbl_notifikasi', ['id' => 'id_notifikasi']);
        $notifikasi->addColumn('id_pengguna_tujuan', 'integer', ['null' => true])
                  ->addColumn('pesan', 'string', ['limit' => 255])
                  ->addColumn('tautan', 'string', ['limit' => 255, 'null' => true])
                  ->addColumn('sudah_dibaca', 'boolean', ['default' => false])
                  ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                  ->addIndex(['id_pengguna_tujuan', 'sudah_dibaca'])
                  ->addForeignKey('id_pengguna_tujuan', 'tbl_pengguna', 'id_pengguna', [
                      'delete' => 'CASCADE',
                      'update' => 'CASCADE'
                  ])
                  ->create();

        // Tabel Pengaturan
        $pengaturan = $this->table('tbl_pengaturan', ['id' => 'id_pengaturan']);
        $pengaturan->addColumn('pengaturan_key', 'string', ['limit' => 100])
                  ->addColumn('pengaturan_value', 'text')
                  ->addColumn('deskripsi', 'string', ['limit' => 255, 'null' => true])
                  ->addColumn('updated_at', 'timestamp', [
                      'default' => 'CURRENT_TIMESTAMP',
                      'update' => 'CURRENT_TIMESTAMP'
                  ])
                  ->addIndex(['pengaturan_key'], ['unique' => true])
                  ->create();
    }

    public function down(): void
    {
        $this->table('tbl_notifikasi')->drop()->save();
        $this->table('tbl_log_stok')->drop()->save();
        $this->table('tbl_detail_stock_opname')->drop()->save();
        $this->table('tbl_stock_opname')->drop()->save();
        $this->table('tbl_barang_keluar')->drop()->save();
        $this->table('tbl_detail_barang_masuk')->drop()->save();
        $this->table('tbl_barang_masuk')->drop()->save();
        $this->table('tbl_detail_permintaan_atk')->drop()->save();
        $this->table('tbl_permintaan_atk')->drop()->save();
        $this->table('tbl_pengaturan')->drop()->save();
        $this->table('tbl_pemasok')->drop()->save();
        $this->table('tbl_barang')->drop()->save();
        $this->table('tbl_satuan_barang')->drop()->save();
        $this->table('tbl_kategori_barang')->drop()->save();
        $this->table('tbl_pengguna')->drop()->save();
        $this->table('tbl_bagian')->drop()->save();
        $this->table('tbl_role_permissions')->drop()->save();
        $this->table('tbl_permissions')->drop()->save();
        $this->table('tbl_roles')->drop()->save();
    }
}