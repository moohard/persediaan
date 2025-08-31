<?php
use Phinx\Seed\AbstractSeed;

class RbacDataSeeder extends AbstractSeed
{
    public function run(): void
    {
        // 1. Definisikan Roles
        $roles = [
            ['id_role' => 1, 'nama_role' => 'Developer', 'deskripsi_role' => 'Akses penuh ke semua fitur sistem untuk pengembangan.'],
            ['id_role' => 2, 'nama_role' => 'Admin', 'deskripsi_role' => 'Akses untuk tugas operasional harian.'],
            ['id_role' => 3, 'nama_role' => 'Pimpinan', 'deskripsi_role' => 'Akses untuk menyetujui permintaan.'],
            ['id_role' => 4, 'nama_role' => 'Pegawai', 'deskripsi_role' => 'Akses terbatas untuk membuat permintaan.'],
        ];
        $this->table('tbl_roles')->insert($roles)->saveData();

        // 2. Definisikan Permissions
        $permissions = [
            // Manajemen Barang
            ['nama_permission' => 'barang_view', 'deskripsi_permission' => 'Melihat daftar barang', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_create', 'deskripsi_permission' => 'Membuat data barang baru', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_update', 'deskripsi_permission' => 'Mengubah data barang', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_delete', 'deskripsi_permission' => 'Menghapus (soft delete) data barang', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_trash', 'deskripsi_permission' => 'Melihat dan memulihkan barang dari sampah', 'grup' => 'Barang'],
            // Permintaan
            ['nama_permission' => 'permintaan_view_all', 'deskripsi_permission' => 'Melihat semua permintaan', 'grup' => 'Permintaan'],
            ['nama_permission' => 'permintaan_view_own', 'deskripsi_permission' => 'Melihat permintaan milik sendiri', 'grup' => 'Permintaan'],
            ['nama_permission' => 'permintaan_create', 'deskripsi_permission' => 'Membuat permintaan baru', 'grup' => 'Permintaan'],
            ['nama_permission' => 'permintaan_approve', 'deskripsi_permission' => 'Menyetujui atau menolak permintaan', 'grup' => 'Permintaan'],
            // Pembelian & Penerimaan
            ['nama_permission' => 'pembelian_process', 'deskripsi_permission' => 'Memproses permintaan pembelian', 'grup' => 'Pembelian'],
            ['nama_permission' => 'barangmasuk_process', 'deskripsi_permission' => 'Memproses penerimaan barang', 'grup' => 'Pembelian'],
            // Fitur Developer
            ['nama_permission' => 'log_view', 'deskripsi_permission' => 'Melihat log query SQL', 'grup' => 'Developer'],
        ];
        $this->table('tbl_permissions')->insert($permissions)->saveData();

        // 3. Hubungkan Roles dengan Permissions
        // Ambil ID dari data yang baru saja dimasukkan
        $developerId = 1;
        $adminId = 2;
        $pimpinanId = 3;
        $pegawaiId = 4;

        $p_barang_view = 1;
        $p_barang_create = 2;
        $p_barang_update = 3;
        $p_barang_delete = 4;
        $p_barang_trash = 5;
        $p_permintaan_view_all = 6;
        $p_permintaan_view_own = 7;
        $p_permintaan_create = 8;
        $p_permintaan_approve = 9;
        $p_pembelian_process = 10;
        $p_barangmasuk_process = 11;
        $p_log_view = 12;

        $rolePermissions = [
            // Developer (semua akses)
            ['id_role' => $developerId, 'id_permission' => $p_barang_view],
            ['id_role' => $developerId, 'id_permission' => $p_barang_create],
            ['id_role' => $developerId, 'id_permission' => $p_barang_update],
            ['id_role' => $developerId, 'id_permission' => $p_barang_delete],
            ['id_role' => $developerId, 'id_permission' => $p_barang_trash],
            ['id_role' => $developerId, 'id_permission' => $p_permintaan_view_all],
            ['id_role' => $developerId, 'id_permission' => $p_permintaan_create],
            ['id_role' => $developerId, 'id_permission' => $p_permintaan_approve],
            ['id_role' => $developerId, 'id_permission' => $p_pembelian_process],
            ['id_role' => $developerId, 'id_permission' => $p_barangmasuk_process],
            ['id_role' => $developerId, 'id_permission' => $p_log_view],

            // Admin
            ['id_role' => $adminId, 'id_permission' => $p_barang_view],
            ['id_role' => $adminId, 'id_permission' => $p_barang_create],
            ['id_role' => $adminId, 'id_permission' => $p_barang_update],
            ['id_role' => $adminId, 'id_permission' => $p_barang_delete],
            ['id_role' => $adminId, 'id_permission' => $p_barang_trash],
            ['id_role' => $adminId, 'id_permission' => $p_permintaan_view_all],
            ['id_role' => $adminId, 'id_permission' => $p_pembelian_process],
            ['id_role' => $adminId, 'id_permission' => $p_barangmasuk_process],

            // Pimpinan
            ['id_role' => $pimpinanId, 'id_permission' => $p_permintaan_view_all],
            ['id_role' => $pimpinanId, 'id_permission' => $p_permintaan_approve],
            
            // Pegawai
            ['id_role' => $pegawaiId, 'id_permission' => $p_permintaan_view_own],
            ['id_role' => $pegawaiId, 'id_permission' => $p_permintaan_create],
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->saveData();

        // 4. Update pengguna yang ada untuk menggunakan id_role baru
        // Kita asumsikan ID pengguna awal masih sama
        $this->execute("UPDATE tbl_pengguna SET id_role = 1 WHERE username = 'developer'");
        $this->execute("UPDATE tbl_pengguna SET id_role = 2 WHERE username = 'admin'");
        $this->execute("UPDATE tbl_pengguna SET id_role = 3 WHERE username = 'pimpinan'");
        $this->execute("UPDATE tbl_pengguna SET id_role = 4 WHERE username = 'pegawai'");
    }
}