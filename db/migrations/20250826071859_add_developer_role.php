<?php
use Phinx\Migration\AbstractMigration;

class AddDeveloperRole extends AbstractMigration
    {
    public function change()
        {
        $table = $this->table('tbl_pengguna');

        // Perbarui kolom 'role' untuk menambahkan 'developer'
        // Pastikan semua nilai yang sudah ada tetap dipertahankan
        $table->changeColumn('role', 'enum', [
            'values'  => ['admin', 'pimpinan', 'pegawai', 'developer'],
            'comment' => 'Menambahkan peran developer untuk akses penuh',
        ])
            ->update();
        }
    }