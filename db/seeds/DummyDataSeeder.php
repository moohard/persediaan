<?php

use Phinx\Seed\AbstractSeed;

class DummyDataSeeder extends AbstractSeed
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
            'tbl_bagian',
        ];
        foreach ($tables as $table)
        {
            $this->table($table)->truncate();
        }

        // 1. Definisikan Roles
        $roles = [
            [ 'id_role' => 1, 'nama_role' => 'Developer', 'deskripsi_role' => 'Akses penuh ke semua fitur sistem untuk pengembangan.' ],
            [ 'id_role' => 2, 'nama_role' => 'Admin', 'deskripsi_role' => 'Akses untuk tugas operasional harian.' ],
            [ 'id_role' => 3, 'nama_role' => 'Pimpinan', 'deskripsi_role' => 'Akses untuk menyetujui permintaan.' ],
            [ 'id_role' => 4, 'nama_role' => 'Pegawai', 'deskripsi_role' => 'Akses terbatas untuk membuat permintaan.' ],
        ];
        $this->table('tbl_roles')->insert($roles)->saveData();

        // 2. Definisikan Permissions
        $permissions = [
            // Manajemen Barang
            [ 'id_permission' => 1, 'nama_permission' => 'barang_view', 'deskripsi_permission' => 'Melihat daftar barang', 'grup' => 'Barang' ],
            [ 'id_permission' => 2, 'nama_permission' => 'barang_create', 'deskripsi_permission' => 'Membuat data barang baru', 'grup' => 'Barang' ],
            [ 'id_permission' => 3, 'nama_permission' => 'barang_update', 'deskripsi_permission' => 'Mengubah data barang', 'grup' => 'Barang' ],
            [ 'id_permission' => 4, 'nama_permission' => 'barang_delete', 'deskripsi_permission' => 'Menghapus (soft delete) data barang', 'grup' => 'Barang' ],
            [ 'id_permission' => 5, 'nama_permission' => 'barang_trash', 'deskripsi_permission' => 'Melihat dan memulihkan barang dari sampah', 'grup' => 'Barang' ],
            // Permintaan
            [ 'id_permission' => 6, 'nama_permission' => 'permintaan_view_all', 'deskripsi_permission' => 'Melihat semua permintaan', 'grup' => 'Permintaan' ],
            [ 'id_permission' => 7, 'nama_permission' => 'permintaan_view_own', 'deskripsi_permission' => 'Melihat permintaan milik sendiri', 'grup' => 'Permintaan' ],
            [ 'id_permission' => 8, 'nama_permission' => 'permintaan_create', 'deskripsi_permission' => 'Membuat permintaan baru', 'grup' => 'Permintaan' ],
            [ 'id_permission' => 9, 'nama_permission' => 'permintaan_approve', 'deskripsi_permission' => 'Menyetujui atau menolak permintaan', 'grup' => 'Permintaan' ],
            // Pembelian & Penerimaan
            [ 'id_permission' => 10, 'nama_permission' => 'pembelian_process', 'deskripsi_permission' => 'Memproses permintaan pembelian', 'grup' => 'Pembelian' ],
            [ 'id_permission' => 11, 'nama_permission' => 'barangmasuk_process', 'deskripsi_permission' => 'Memproses penerimaan barang', 'grup' => 'Pembelian' ],
            // Fitur Developer
            [ 'id_permission' => 12, 'nama_permission' => 'log_view', 'deskripsi_permission' => 'Melihat log query SQL', 'grup' => 'Developer' ],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        // 3. Hubungkan Roles dengan Permissions
        $rolePermissions = [
            // Developer (semua akses)
            [ 'id_role' => 1, 'id_permission' => 1 ],
            [ 'id_role' => 1, 'id_permission' => 2 ],
            [ 'id_role' => 1, 'id_permission' => 3 ],
            [ 'id_role' => 1, 'id_permission' => 4 ],
            [ 'id_role' => 1, 'id_permission' => 5 ],
            [ 'id_role' => 1, 'id_permission' => 6 ],
            [ 'id_role' => 1, 'id_permission' => 8 ],
            [ 'id_role' => 1, 'id_permission' => 9 ],
            [ 'id_role' => 1, 'id_permission' => 10 ],
            [ 'id_role' => 1, 'id_permission' => 11 ],
            [ 'id_role' => 1, 'id_permission' => 12 ],
            // Admin
            [ 'id_role' => 2, 'id_permission' => 1 ],
            [ 'id_role' => 2, 'id_permission' => 2 ],
            [ 'id_role' => 2, 'id_permission' => 3 ],
            [ 'id_role' => 2, 'id_permission' => 4 ],
            [ 'id_role' => 2, 'id_permission' => 5 ],
            [ 'id_role' => 2, 'id_permission' => 6 ],
            [ 'id_role' => 2, 'id_permission' => 10 ],
            [ 'id_role' => 2, 'id_permission' => 11 ],
            // Pimpinan
            [ 'id_role' => 3, 'id_permission' => 6 ],
            [ 'id_role' => 3, 'id_permission' => 9 ],
            // Pegawai
            [ 'id_role' => 4, 'id_permission' => 7 ],
            [ 'id_role' => 4, 'id_permission' => 8 ],
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();

        // 4. Isi data master lainnya
        $bagian = [
            [ 'id_bagian' => 1, 'nama_bagian' => 'Umum & Keuangan' ],
            [ 'id_bagian' => 2, 'nama_bagian' => 'Kepegawaian & Ortala' ],
        ];
        $this->table('tbl_bagian')->insert($bagian)->saveData();

        $kategori = [ [ 'id_kategori' => 1, 'nama_kategori' => 'Alat Tulis' ], [ 'id_kategori' => 2, 'nama_kategori' => 'Kertas' ] ];
        $this->table('tbl_kategori_barang')->insert($kategori)->saveData();

        $satuan = [ [ 'id_satuan' => 1, 'nama_satuan' => 'Pcs' ], [ 'id_satuan' => 2, 'nama_satuan' => 'Rim' ] ];
        $this->table('tbl_satuan_barang')->insert($satuan)->saveData();

        $pemasok = [ [ 'id_pemasok' => 1, 'nama_pemasok' => 'Pemasok Umum', 'alamat' => '-' ] ];
        $this->table('tbl_pemasok')->insert($pemasok)->saveData();

        // 5. Isi data pengguna dengan id_role yang benar
        $pengguna = [
            [
                'id_pengguna'  => 99,
                'nama_lengkap' => 'Developer',
                'username'     => 'developer',
                'password'     => password_hash('devpass', PASSWORD_DEFAULT),
                'id_bagian'    => 1,
                'id_role'      => 1,
                'is_active'    => 1,
            ],
            [
                'id_pengguna'  => 1,
                'nama_lengkap' => 'Admin Gudang',
                'username'     => 'admin',
                'password'     => password_hash('password123', PASSWORD_DEFAULT),
                'id_bagian'    => 1,
                'id_role'      => 2,
                'is_active'    => 1,
            ],
            [
                'id_pengguna'  => 2,
                'nama_lengkap' => 'Pimpinan PA',
                'username'     => 'pimpinan',
                'password'     => password_hash('password123', PASSWORD_DEFAULT),
                'id_bagian'    => 1,
                'id_role'      => 3,
                'is_active'    => 1,
            ],
            [
                'id_pengguna'  => 3,
                'nama_lengkap' => 'Pegawai Staff',
                'username'     => 'pegawai',
                'password'     => password_hash('password123', PASSWORD_DEFAULT),
                'id_bagian'    => 2,
                'id_role'      => 4,
                'is_active'    => 1,
            ],
        ];
        $this->table('tbl_pengguna')->insert($pengguna)->saveData();

        // 6. Isi data barang awal
        $barang = [
            [ 'kode_barang' => 'ATK-KRT-001', 'nama_barang' => 'Kertas HVS A4 70gr', 'id_kategori' => 2, 'id_satuan' => 2, 'stok_umum' => 10, 'stok_perkara' => 5 ],
            [ 'kode_barang' => 'ATK-ALT-001', 'nama_barang' => 'Pulpen Tinta Hitam', 'id_kategori' => 1, 'id_satuan' => 1, 'stok_umum' => 20, 'stok_perkara' => 10 ],
        ];
        $this->table('tbl_barang')->insert($barang)->saveData();

        // Aktifkan kembali foreign key checks
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }

}
