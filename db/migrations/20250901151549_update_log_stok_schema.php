<?php

use Phinx\Migration\AbstractMigration;

class UpdateLogStokSchema extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('tbl_log_stok');

        // 1. Hapus kolom total yang lama untuk konsistensi
        if ($table->hasColumn('stok_sebelum_total')) {
            $table->removeColumn('stok_sebelum_total');
        }
        if ($table->hasColumn('stok_sesudah_total')) {
            $table->removeColumn('stok_sesudah_total');
        }
        // Hapus juga versi nama tanpa _total jika ada
        if ($table->hasColumn('stok_sebelum')) {
            $table->removeColumn('stok_sebelum');
        }
        if ($table->hasColumn('stok_sesudah')) {
            $table->removeColumn('stok_sesudah');
        }

        // 2. Tambahkan kolom baru yang lebih detail
        $table
            ->addColumn('stok_sebelum_umum', 'integer', ['signed' => false, 'after' => 'jumlah_ubah'])
            ->addColumn('stok_sesudah_umum', 'integer', ['signed' => false, 'after' => 'stok_sebelum_umum'])
            ->addColumn('stok_sebelum_perkara', 'integer', ['signed' => false, 'after' => 'stok_sesudah_umum'])
            ->addColumn('stok_sesudah_perkara', 'integer', ['signed' => false, 'after' => 'stok_sebelum_perkara'])
            ->update();
    }
}
