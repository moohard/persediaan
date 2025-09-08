<?php

use Phinx\Seed\AbstractSeed;

class InitialDataSeeder extends AbstractSeed
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks untuk menghindari error urutan
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel secara berurutan untuk menghindari konflik
        $tables = [
            'tbl_role_permissions',
            'tbl_permissions',
            'tbl_roles',
            'tbl_pengguna',
            'tbl_barang',
            'tbl_pemasok',
            'tbl_satuan_barang',
            'tbl_kategori_barang',
            'tbl_bagian'
        ];
        foreach ($tables as $table) {
            $this->table($table)->truncate();
        }

        // 1. DATA MASTER DASAR
        $this->table('tbl_bagian')->insert([
            ['id_bagian' => 1, 'nama_bagian' => 'Umum & Keuangan'],
            ['id_bagian' => 2, 'nama_bagian' => 'Kepegawaian & Ortala'],
        ])->saveData();
        $this->table('tbl_kategori_barang')->insert([
            ['id_kategori' => 1, 'nama_kategori' => 'Alat Tulis'],
            ['id_kategori' => 2, 'nama_kategori' => 'Kertas']
        ])->saveData();
        $this->table('tbl_satuan_barang')->insert([
            ['id_satuan' => 1, 'nama_satuan' => 'Pcs'],
            ['id_satuan' => 2, 'nama_satuan' => 'Rim']
        ])->saveData();
        $this->table('tbl_pemasok')->insert([
            ['id_pemasok' => 1, 'nama_pemasok' => 'Pemasok Umum', 'alamat' => '-']
        ])->saveData();

        // 2. DATA RBAC (ROLES & PERMISSIONS)
        $this->table('tbl_roles')->insert([
            ['id_role' => 1, 'nama_role' => 'Developer', 'deskripsi_role' => 'Akses penuh ke semua fitur.'],
            ['id_role' => 2, 'nama_role' => 'Admin', 'deskripsi_role' => 'Akses operasional harian.'],
            ['id_role' => 3, 'nama_role' => 'Pimpinan', 'deskripsi_role' => 'Akses persetujuan permintaan.'],
            ['id_role' => 4, 'nama_role' => 'Pegawai', 'deskripsi_role' => 'Akses membuat permintaan.']
        ])->saveData();

        $permissions = [
            ['id_permission' => 1, 'nama_permission' => 'barang_view', 'grup' => 'Barang'],
            ['id_permission' => 2, 'nama_permission' => 'barang_create', 'grup' => 'Barang'],
            ['id_permission' => 3, 'nama_permission' => 'barang_update', 'grup' => 'Barang'],
            ['id_permission' => 4, 'nama_permission' => 'barang_delete', 'grup' => 'Barang'],
            ['id_permission' => 5, 'nama_permission' => 'barang_trash', 'grup' => 'Barang'],
            ['id_permission' => 6, 'nama_permission' => 'permintaan_view_all', 'grup' => 'Permintaan'],
            ['id_permission' => 7, 'nama_permission' => 'permintaan_view_own', 'grup' => 'Permintaan'],
            ['id_permission' => 8, 'nama_permission' => 'permintaan_create', 'grup' => 'Permintaan'],
            ['id_permission' => 9, 'nama_permission' => 'permintaan_approve', 'grup' => 'Permintaan'],
            ['id_permission' => 10, 'nama_permission' => 'pembelian_process', 'grup' => 'Pembelian'],
            ['id_permission' => 11, 'nama_permission' => 'barangmasuk_process', 'grup' => 'Pembelian'],
            ['id_permission' => 12, 'nama_permission' => 'log_view', 'grup' => 'Developer'],
            ['id_permission' => 13, 'nama_permission' => 'user_management_view', 'grup' => 'Pengguna'],
            ['id_permission' => 14, 'nama_permission' => 'user_management_create', 'grup' => 'Pengguna'],
            ['id_permission' => 15, 'nama_permission' => 'user_management_update', 'grup' => 'Pengguna'],
            ['id_permission' => 16, 'nama_permission' => 'user_management_delete', 'grup' => 'Pengguna'],
            ['id_permission' => 17, 'nama_permission' => 'laporan_view', 'grup' => 'Laporan'],
            ['id_permission' => 18, 'nama_permission' => 'laporan_stok_print', 'grup' => 'Laporan'],
            ['id_permission' => 19, 'nama_permission' => 'laporan_kartu_stok_view', 'grup' => 'Laporan'],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        $rolePermissions = [
            // Admin (tanpa akses developer & laporan)
            ['id_role' => 2, 'id_permission' => 1],
            ['id_role' => 2, 'id_permission' => 2],
            ['id_role' => 2, 'id_permission' => 3],
            ['id_role' => 2, 'id_permission' => 4],
            ['id_role' => 2, 'id_permission' => 5],
            ['id_role' => 2, 'id_permission' => 6],
            ['id_role' => 2, 'id_permission' => 10],
            ['id_role' => 2, 'id_permission' => 11],
            ['id_role' => 2, 'id_permission' => 13],
            ['id_role' => 2, 'id_permission' => 14],
            ['id_role' => 2, 'id_permission' => 15],
            ['id_role' => 2, 'id_permission' => 16],

            // Pimpinan
            ['id_role' => 3, 'id_permission' => 6],
            ['id_role' => 3, 'id_permission' => 9],
            ['id_role' => 3, 'id_permission' => 17],
            ['id_role' => 3, 'id_permission' => 18],
            ['id_role' => 3, 'id_permission' => 19],

            // Pegawai
            ['id_role' => 4, 'id_permission' => 7],
            ['id_role' => 4, 'id_permission' => 8],
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();

        // 3. DATA PENGGUNA
        $pengguna = [
            [
                'id_pengguna' => 99,
                'nama_lengkap' => 'Developer',
                'username' => 'developer',
                'password' => password_hash('devpass', PASSWORD_DEFAULT),
                'id_bagian' => 1,
                'id_role' => 1,
                'is_active' => 1
            ],
            [
                'id_pengguna' => 1,
                'nama_lengkap' => 'Admin Gudang',
                'username' => 'admin',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'id_bagian' => 1,
                'id_role' => 2,
                'is_active' => 1
            ],
            [
                'id_pengguna' => 2,
                'nama_lengkap' => 'Pimpinan PA',
                'username' => 'pimpinan',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'id_bagian' => 1,
                'id_role' => 3,
                'is_active' => 1
            ],
            [
                'id_pengguna' => 3,
                'nama_lengkap' => 'Pegawai Staff',
                'username' => 'pegawai',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'id_bagian' => 2,
                'id_role' => 4,
                'is_active' => 1
            ],
        ];
        $this->table('tbl_pengguna')->insert($pengguna)->saveData();

        // 4. DATA BARANG AWAL
        $barang = [
            ['kode_barang' => 'ATK-KRT-001', 'nama_barang' => 'Kertas HVS A4 70gr', 'id_kategori' => 2, 'id_satuan' => 2, 'stok_umum' => 10, 'stok_perkara' => 5],
            ['kode_barang' => 'ATK-ALT-001', 'nama_barang' => 'Pulpen Tinta Hitam', 'id_kategori' => 1, 'id_satuan' => 1, 'stok_umum' => 20, 'stok_perkara' => 10],
        ];
        $this->table('tbl_barang')->insert($barang)->saveData();

        // Aktifkan kembali foreign key checks
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }
}
