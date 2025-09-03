<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DatabaseSeeder extends AbstractSeed
{
    public function run(): void
    {
        // Roles
        $roles = [
            [
                'nama_role' => 'Developer',
                'deskripsi_role' => 'Akses penuh ke semua fitur sistem untuk pengembangan.'
            ],
            [
                'nama_role' => 'Admin',
                'deskripsi_role' => 'Akses untuk tugas operasional harian.'
            ],
            [
                'nama_role' => 'Pimpinan',
                'deskripsi_role' => 'Akses untuk menyetujui permintaan.'
            ],
            [
                'nama_role' => 'Pegawai',
                'deskripsi_role' => 'Akses terbatas untuk membuat permintaan.'
            ]
        ];
        $this->table('tbl_roles')->insert($roles)->save();

        // Permissions
        $permissions = [
            ['nama_permission' => 'barang_view', 'deskripsi_permission' => 'Melihat daftar barang', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_create', 'deskripsi_permission' => 'Membuat data barang baru', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_update', 'deskripsi_permission' => 'Mengubah data barang', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_delete', 'deskripsi_permission' => 'Menghapus (soft delete) data barang', 'grup' => 'Barang'],
            ['nama_permission' => 'barang_trash', 'deskripsi_permission' => 'Melihat dan memulihkan barang dari sampah', 'grup' => 'Barang'],
            ['nama_permission' => 'permintaan_view_all', 'deskripsi_permission' => 'Melihat semua permintaan', 'grup' => 'Permintaan'],
            ['nama_permission' => 'permintaan_view_own', 'deskripsi_permission' => 'Melihat permintaan milik sendiri', 'grup' => 'Permintaan'],
            ['nama_permission' => 'permintaan_create', 'deskripsi_permission' => 'Membuat permintaan baru', 'grup' => 'Permintaan'],
            ['nama_permission' => 'permintaan_approve', 'deskripsi_permission' => 'Menyetujui atau menolak permintaan', 'grup' => 'Permintaan'],
            ['nama_permission' => 'pembelian_process', 'deskripsi_permission' => 'Memproses permintaan pembelian', 'grup' => 'Pembelian'],
            ['nama_permission' => 'barangmasuk_process', 'deskripsi_permission' => 'Memproses penerimaan barang', 'grup' => 'Pembelian'],
            ['nama_permission' => 'log_view', 'deskripsi_permission' => 'Melihat log query SQL', 'grup' => 'Developer'],
            ['nama_permission' => 'user_management_view', 'deskripsi_permission' => 'Melihat daftar pengguna', 'grup' => 'Pengguna'],
            ['nama_permission' => 'user_management_create', 'deskripsi_permission' => 'Membuat pengguna baru', 'grup' => 'Pengguna'],
            ['nama_permission' => 'user_management_update', 'deskripsi_permission' => 'Mengubah data pengguna', 'grup' => 'Pengguna'],
            ['nama_permission' => 'user_management_delete', 'deskripsi_permission' => 'Menghapus pengguna', 'grup' => 'Pengguna'],
            ['nama_permission' => 'laporan_view', 'deskripsi_permission' => 'Melihat halaman laporan', 'grup' => 'Laporan'],
            ['nama_permission' => 'laporan_stok_print', 'deskripsi_permission' => 'Mencetak laporan stok barang', 'grup' => 'Laporan'],
            ['nama_permission' => 'laporan_kartu_stok_view', 'deskripsi_permission' => 'Melihat detail riwayat barang (Kartu Stok)', 'grup' => 'Laporan'],
            ['nama_permission' => 'laporan_kartu_stok_print', 'deskripsi_permission' => 'Mencetak/Ekspor data Kartu Stok', 'grup' => 'Laporan'],
            ['nama_permission' => 'pengaturan_view', 'deskripsi_permission' => 'Melihat halaman pengaturan', 'grup' => 'Pengaturan'],
            ['nama_permission' => 'pengaturan_clear_transactions', 'deskripsi_permission' => 'Mengosongkan semua data transaksi', 'grup' => 'Pengaturan'],
            ['nama_permission' => 'laporan_permintaan_view', 'deskripsi_permission' => 'Melihat laporan permintaan', 'grup' => 'Laporan'],
            ['nama_permission' => 'laporan_pembelian_view', 'deskripsi_permission' => 'Melihat laporan pembelian', 'grup' => 'Laporan'],
            ['nama_permission' => 'pengaturan_update', 'deskripsi_permission' => 'Mengubah pengaturan aplikasi', 'grup' => 'Pengaturan'],
            ['nama_permission' => 'dashboard_view_stats', 'deskripsi_permission' => 'Melihat statistik ringkasan di dashboard', 'grup' => 'Dashboard'],
            ['nama_permission' => 'role_management_view', 'deskripsi_permission' => 'Melihat halaman manajemen peran & izin', 'grup' => 'Hak Akses'],
            ['nama_permission' => 'role_management_update', 'deskripsi_permission' => 'Mengubah izin untuk sebuah peran', 'grup' => 'Hak Akses'],
            ['nama_permission' => 'stock_opname_view', 'deskripsi_permission' => 'Melihat riwayat stock opname', 'grup' => 'Stock Opname'],
            ['nama_permission' => 'stock_opname_create', 'deskripsi_permission' => 'Melakukan proses stock opname baru', 'grup' => 'Stock Opname'],
            ['nama_permission' => 'stock_opname_print', 'deskripsi_permission' => 'Melihat detail dan mencetak hasil stock opname', 'grup' => 'Stock Opname'],
            ['nama_permission' => 'notifikasi_view', 'deskripsi_permission' => 'Melihat notifikasi', 'grup' => 'Notifikasi']
        ];
        $this->table('tbl_permissions')->insert($permissions)->save();

        // Role Permissions
        $rolePermissions = [
            // Developer - semua permission
            [1, 1], [1, 2], [1, 3], [1, 4], [1, 5], [1, 6], [1, 8], [1, 9], [1, 10], [1, 11],
            [1, 12], [1, 13], [1, 14], [1, 15], [1, 16], [1, 17], [1, 18], [1, 19], [1, 20],
            [1, 21], [1, 22], [1, 23], [1, 24], [1, 25], [1, 26], [1, 35], [1, 36], [1, 37],
            [1, 38], [1, 39], [1, 40],
            
            // Admin
            [2, 1], [2, 2], [2, 3], [2, 4], [2, 5], [2, 6], [2, 10], [2, 11], [2, 13], [2, 14],
            [2, 15], [2, 16], [2, 19], [2, 20], [2, 26], [2, 37], [2, 38], [2, 39], [2, 40],
            
            // Pimpinan
            [3, 6], [3, 9], [3, 17], [3, 18], [3, 19], [3, 20], [3, 23], [3, 24], [3, 26], [3, 40],
            
            // Pegawai
            [4, 7], [4, 8], [4, 40]
        ];
        $this->table('tbl_role_permissions')->insert($rolePermissions)->save();

        // Bagian
        $bagian = [
            ['nama_bagian' => 'Umum & Keuangan'],
            ['nama_bagian' => 'Kepegawaian & Ortala']
        ];
        $this->table('tbl_bagian')->insert($bagian)->save();

        // Pengguna
        $pengguna = [
            [
                'nama_lengkap' => 'Developer',
                'username' => 'developer',
                'password' => '$2y$12$UAQ.q.U.59r7/PnLcqpgoeFqxW8oDBt2aEkGTaD2N4v3Bfakmthnq',
                'id_bagian' => 1,
                'is_active' => 1,
                'id_role' => 1
            ],
            [
                'nama_lengkap' => 'Admin Gudang',
                'username' => 'admin',
                'password' => '$2y$12$KQpFr22dnJLsn0/h77chGegilv3lGlp4rM75TvyPUipqJfoxC2ksi',
                'id_bagian' => 1,
                'is_active' => 1,
                'id_role' => 2
            ],
            [
                'nama_lengkap' => 'Pimpinan PA',
                'username' => 'pimpinan',
                'password' => '$2y$12$WlTJGYxNHFhYOYS4BHf5VeKm9A2rLuE1a8bpToHbo9PJzvyfNtnS6',
                'id_bagian' => 1,
                'is_active' => 1,
                'id_role' => 3
            ],
            [
                'nama_lengkap' => 'Pegawai Staff',
                'username' => 'pegawai',
                'password' => '$2y$12$qUf8T.bpEotZOVrId69EyOMouPAJpeQgEMrrJyRkfNl03iVt/R4XG',
                'id_bagian' => 2,
                'is_active' => 1,
                'id_role' => 4
            ]
        ];
        $this->table('tbl_pengguna')->insert($pengguna)->save();

        // Kategori Barang
        $kategori = [
            ['nama_kategori' => 'Alat Tulis'],
            ['nama_kategori' => 'Kertas']
        ];
        $this->table('tbl_kategori_barang')->insert($kategori)->save();

        // Satuan Barang
        $satuan = [
            ['nama_satuan' => 'Pcs'],
            ['nama_satuan' => 'Rim']
        ];
        $this->table('tbl_satuan_barang')->insert($satuan)->save();

        // Barang
        $barang = [
            [
                'kode_barang' => 'ATK-KRT-001',
                'nama_barang' => 'Kertas HVS A4 70gr',
                'jenis_barang' => 'habis_pakai',
                'id_kategori' => 2,
                'id_satuan' => 2,
                'stok_umum' => 0,
                'stok_perkara' => 0,
                'deleted_at' => '2025-08-31 23:13:07'
            ],
            [
                'kode_barang' => 'ATK-ALT-001',
                'nama_barang' => 'Pulpen Tinta Hitam',
                'jenis_barang' => 'habis_pakai',
                'id_kategori' => 1,
                'id_satuan' => 1,
                'stok_umum' => 0,
                'stok_perkara' => 0,
                'deleted_at' => '2025-08-31 23:13:12'
            ],
            [
                'kode_barang' => 'ATK-PLP-001',
                'nama_barang' => 'Pulpen Joyko',
                'jenis_barang' => 'habis_pakai',
                'id_kategori' => 1,
                'id_satuan' => 1,
                'stok_umum' => 11,
                'stok_perkara' => 0,
                'deleted_at' => null
            ],
            [
                'kode_barang' => 'ATK-PLP-002',
                'nama_barang' => 'pulpen joyko biru',
                'jenis_barang' => 'habis_pakai',
                'id_kategori' => 1,
                'id_satuan' => 1,
                'stok_umum' => 0,
                'stok_perkara' => 0,
                'deleted_at' => null
            ]
        ];
        $this->table('tbl_barang')->insert($barang)->save();

        // Pemasok
        $pemasok = [
            [
                'nama_pemasok' => 'Pemasok Umum',
                'alamat' => '-',
                'no_telepon' => null,
                'email' => null
            ]
        ];
        $this->table('tbl_pemasok')->insert($pemasok)->save();

        // Pengaturan
        $pengaturan = [
            [
                'pengaturan_key' => 'APP_NAME',
                'pengaturan_value' => 'Sistem Persediaan Alat Tulis Kantor Pengadilan Agama Penajam',
                'deskripsi' => 'Nama aplikasi yang ditampilkan di header.'
            ],
            [
                'pengaturan_key' => 'ITEMS_PER_PAGE',
                'pengaturan_value' => '10',
                'deskripsi' => 'Jumlah item yang ditampilkan per halaman pada tabel.'
            ],
            [
                'pengaturan_key' => 'NAMA_PENANDATANGAN',
                'pengaturan_value' => 'Nama Kasubbag Umum & Keuangan',
                'deskripsi' => 'Nama lengkap yang akan menandatangani laporan.'
            ],
            [
                'pengaturan_key' => 'NIP_PENANDATANGAN',
                'pengaturan_value' => '123456789012345678',
                'deskripsi' => 'NIP yang akan menandatangani laporan.'
            ]
        ];
        $this->table('tbl_pengaturan')->insert($pengaturan)->save();
    }
}