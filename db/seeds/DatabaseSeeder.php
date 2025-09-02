<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DatabaseSeeder extends AbstractSeed
    {
    public function run(): void
        {
        // Seed data untuk tbl_bagian
        $bagianData = [
            ['nama_bagian' => 'Umum & Keuangan'],
            ['nama_bagian' => 'Kepegawaian & Ortala'],
        ];
        $this->table('tbl_bagian')->insert($bagianData)->save();

        // Seed data untuk tbl_kategori_barang
        $kategoriData = [
            ['nama_kategori' => 'Alat Tulis'],
            ['nama_kategori' => 'Kertas'],
        ];
        $this->table('tbl_kategori_barang')->insert($kategoriData)->save();

        // Seed data untuk tbl_satuan_barang
        $satuanData = [
            ['nama_satuan' => 'Pcs'],
            ['nama_satuan' => 'Rim'],
        ];
        $this->table('tbl_satuan_barang')->insert($satuanData)->save();

        // Seed data untuk tbl_roles
        $rolesData = [
            [
                'nama_role'      => 'Developer',
                'deskripsi_role' => 'Akses penuh ke semua fitur sistem untuk pengembangan.',
            ],
            [
                'nama_role'      => 'Admin',
                'deskripsi_role' => 'Akses untuk tugas operasional harian.',
            ],
            [
                'nama_role'      => 'Pimpinan',
                'deskripsi_role' => 'Akses untuk menyetujui permintaan.',
            ],
            [
                'nama_role'      => 'Pegawai',
                'deskripsi_role' => 'Akses terbatas untuk membuat permintaan.',
            ],
        ];
        $this->table('tbl_roles')->insert($rolesData)->save();

        // Seed data untuk tbl_pengguna
        $penggunaData = [
            [
                'nama_lengkap' => 'Developer',
                'username'     => 'developer',
                'password'     => '$2y$12$UAQ.q.U.59r7/PnLcqpgoeFqxW8oDBt2aEkGTaD2N4v3Bfakmthnq',
                'id_bagian'    => 1,
                'is_active'    => 1,
                'id_role'      => 1,
            ],
            [
                'nama_lengkap' => 'Admin Gudang',
                'username'     => 'admin',
                'password'     => '$2y$12$KQpFr22dnJLsn0/h77chGegilv3lGlp4rM75TvyPUipqJfoxC2ksi',
                'id_bagian'    => 1,
                'is_active'    => 1,
                'id_role'      => 2,
            ],
            [
                'nama_lengkap' => 'Pimpinan PA',
                'username'     => 'pimpinan',
                'password'     => '$2y$12$WlTJGYxNHFhYOYS4BHf5VeKm9A2rLuE1a8bpToHbo9PJzvyfNtnS6',
                'id_bagian'    => 1,
                'is_active'    => 1,
                'id_role'      => 3,
            ],
            [
                'nama_lengkap' => 'Pegawai Staff',
                'username'     => 'pegawai',
                'password'     => '$2y$12$Q0xhRHSkqEgXWxrAheY5BuV9bYZXw7ShRPmDdhgkN7SRorGjaMGrW',
                'id_bagian'    => 2,
                'is_active'    => 1,
                'id_role'      => 4,
            ],
        ];
        $this->table('tbl_pengguna')->insert($penggunaData)->save();

        // Seed data untuk tbl_barang
        $barangData = [
            [
                'kode_barang'  => 'ATK-KRT-001',
                'nama_barang'  => 'Kertas HVS A4 70gr',
                'jenis_barang' => 'habis_pakai',
                'id_kategori'  => 2,
                'id_satuan'    => 2,
                'stok_umum'    => 0,
                'stok_perkara' => 0,
                'deleted_at'   => NULL,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kode_barang'  => 'ATK-ALT-001',
                'nama_barang'  => 'Pulpen Tinta Hitam',
                'jenis_barang' => 'habis_pakai',
                'id_kategori'  => 1,
                'id_satuan'    => 1,
                'stok_umum'    => 0,
                'stok_perkara' => 0,
                'deleted_at'   => NULL,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kode_barang'  => 'ATK-PLP-001',
                'nama_barang'  => 'Pulpen Joyko',
                'jenis_barang' => 'habis_pakai',
                'id_kategori'  => 1,
                'id_satuan'    => 1,
                'stok_umum'    => 0,
                'stok_perkara' => 0,
                'deleted_at'   => NULL,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];
        $this->table('tbl_barang')->insert($barangData)->save();

        // Seed data untuk tbl_pemasok
        $pemasokData = [
            [
                'nama_pemasok' => 'Pemasok Umum',
                'alamat'       => '-',
                'no_telepon'   => NULL,
                'email'        => NULL,
            ],
        ];
        $this->table('tbl_pemasok')->insert($pemasokData)->save();

        // Seed data untuk tbl_permissions
        $permissionsData = [
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
        ];
        $this->table('tbl_permissions')->insert($permissionsData)->save();

        // Seed data untuk tbl_role_permissions
        $rolePermissionsData = [
            // Developer - semua permission
            [1, 1],
            [1, 2],
            [1, 3],
            [1, 4],
            [1, 5],
            [1, 6],
            [1, 8],
            [1, 9],
            [1, 10],
            [1, 11],
            [1, 12],
            [1, 13],
            [1, 14],
            [1, 15],
            [1, 16],
            [1, 17],
            [1, 18],
            [1, 19],
            [1, 20],
            [1, 21],
            [1, 22],

            // Admin
            [2, 1],
            [2, 2],
            [2, 3],
            [2, 4],
            [2, 5],
            [2, 6],
            [2, 10],
            [2, 11],
            [2, 13],
            [2, 14],
            [2, 15],
            [2, 16],
            [2, 19],
            [2, 20],

            // Pimpinan
            [3, 6],
            [3, 9],
            [3, 17],
            [3, 18],
            [3, 19],
            [3, 20],

            // Pegawai
            [4, 7],
            [4, 8],
        ];

        $rolePermissionsRows = [];
        foreach ($rolePermissionsData as $data) {
            $rolePermissionsRows[] = [
                'id_role'       => $data[0],
                'id_permission' => $data[1],
            ];
            }
        $this->table('tbl_role_permissions')->insert($rolePermissionsRows)->save();
        }
    }