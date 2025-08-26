<?php
use Phinx\Migration\AbstractMigration;

class AddPurchaseWorkflowStatus extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('tbl_permintaan_atk');
        
        // 1. Perbarui kolom status untuk menyertakan alur kerja pembelian
        $table->changeColumn('status_permintaan', 'enum', [
                'values' => ['Diajukan', 'Disetujui', 'Ditolak', 'Diproses Pembelian', 'Selesai'],
                'default' => 'Diajukan',
                'comment' => 'Menambahkan status untuk proses pembelian dan penyelesaian'
            ]);

        // 2. Tambahkan kolom untuk mencatat siapa operator pembelian yang memvalidasi
        $table->addColumn('id_operator_pembelian', 'integer', [
                'signed' => false,
                'null' => true,
                'after' => 'catatan_penyetuju',
                'comment' => 'ID pengguna (admin) yang memvalidasi pembelian'
            ])
            ->addForeignKey('id_operator_pembelian', 'tbl_pengguna', 'id_pengguna', [
                'delete' => 'SET_NULL', 'update' => 'CASCADE'
            ])
            ->update();
    }
}