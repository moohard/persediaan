<?php
use Phinx\Migration\AbstractMigration;

class AddSignatureSettings extends AbstractMigration
{
    public function up()
    {
        $defaultSettings = [
            ['pengaturan_key' => 'NAMA_PENANDATANGAN', 'pengaturan_value' => 'Nama Kasubbag Umum & Keuangan', 'deskripsi' => 'Nama lengkap yang akan menandatangani laporan.'],
            ['pengaturan_key' => 'NIP_PENANDATANGAN', 'pengaturan_value' => '123456789012345678', 'deskripsi' => 'NIP yang akan menandatangani laporan.'],
        ];
        $this->table('tbl_pengaturan')->insert($defaultSettings)->saveData();
    }

    public function down()
    {
        $this->execute("DELETE FROM tbl_pengaturan WHERE pengaturan_key IN ('NAMA_PENANDATANGAN', 'NIP_PENANDATANGAN')");
    }
}
