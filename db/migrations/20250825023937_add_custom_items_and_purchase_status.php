<?php
use Phinx\Migration\AbstractMigration;

class AddCustomItemsAndPurchaseStatus extends AbstractMigration
{
    public function change()
    {
        // 1. Perbarui tabel detail permintaan untuk item kustom
        $detailTable = $this->table('tbl_detail_permintaan_atk');
        $detailTable
            ->addColumn('nama_barang_custom', 'string', [
                'limit' => 255,
                'null' => true,
                'after' => 'id_barang',
                'comment' => 'Nama barang jika item ini adalah permintaan pembelian baru'
            ])
            // Ubah id_barang agar bisa NULL untuk item baru
            ->changeColumn('id_barang', 'integer', ['signed' => false, 'null' => true])
            ->update();

        // 2. Perbarui tabel permintaan dengan status baru
        $permintaanTable = $this->table('tbl_permintaan_atk');
        $permintaanTable
            ->changeColumn('status_permintaan', 'enum', [
                'values' => ['Diajukan', 'Disetujui', 'Ditolak', 'Dibeli', 'Selesai'],
                'default' => 'Diajukan'
            ])
            ->update();
    }
}