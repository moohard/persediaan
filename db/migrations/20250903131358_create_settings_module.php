<?php

use Phinx\Migration\AbstractMigration;

class CreateSettingsModule extends AbstractMigration
{

    public function up()
    {

        // =====================================================================
        // BAGIAN 1: BUAT TABEL BARU UNTUK PENGATURAN
        // =====================================================================

        $this->table('tbl_pengaturan', [ 'id' => FALSE, 'primary_key' => 'id_pengaturan' ])
            ->addColumn('id_pengaturan', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('pengaturan_key', 'string', [ 'limit' => 100 ])
            ->addColumn('pengaturan_value', 'text', [ 'null' => TRUE ])
            ->addColumn('deskripsi', 'string', [ 'limit' => 255, 'null' => TRUE ])
            ->addColumn('updated_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP' ])
            ->addIndex([ 'pengaturan_key' ], [ 'unique' => TRUE ])
            ->create();

        // =====================================================================
        // BAGIAN 2: ISI DATA PENGATURAN AWAL
        // =====================================================================

        $defaultSettings = [
            [ 'pengaturan_key' => 'APP_NAME', 'pengaturan_value' => 'Sistem Persediaan ATK', 'deskripsi' => 'Nama aplikasi yang ditampilkan di header.' ],
            [ 'pengaturan_key' => 'ITEMS_PER_PAGE', 'pengaturan_value' => '10', 'deskripsi' => 'Jumlah item yang ditampilkan per halaman pada tabel.' ],
        ];
        $this->table('tbl_pengaturan')->insert($defaultSettings)->saveData();
    }

    public function down()
    {

        $this->table('tbl_pengaturan')->drop()->save();
    }

}
