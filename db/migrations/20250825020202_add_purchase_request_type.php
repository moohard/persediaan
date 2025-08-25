<?php
use Phinx\Migration\AbstractMigration;

class AddPurchaseRequestType extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('tbl_permintaan_atk');
        $table->addColumn('tipe_permintaan', 'enum', [
                'values' => ['stok', 'pembelian'],
                'default' => 'stok',
                'after' => 'status_permintaan',
                'comment' => 'Membedakan antara permintaan stok internal dan permintaan pembelian baru'
            ])
            ->update();
    }
}