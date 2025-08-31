<?php

use Phinx\Migration\AbstractMigration;

class ImplementRbacSystem extends AbstractMigration
{

    public function up()
    {

        // =====================================================================
        // BAGIAN 1: BUAT TABEL BARU UNTUK RBAC
        // =====================================================================

        // Tabel Roles
        $this->table('tbl_roles', [ 'id' => FALSE, 'primary_key' => 'id_role' ])
            ->addColumn('id_role', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('nama_role', 'string', [ 'limit' => 100 ])
            ->addColumn('deskripsi_role', 'text', [ 'null' => TRUE ])
            ->addIndex([ 'nama_role' ], [ 'unique' => TRUE ])
            ->create();

        // Tabel Permissions
        $this->table('tbl_permissions', [ 'id' => FALSE, 'primary_key' => 'id_permission' ])
            ->addColumn('id_permission', 'integer', [ 'identity' => TRUE, 'signed' => FALSE ])
            ->addColumn('nama_permission', 'string', [ 'limit' => 100 ])
            ->addColumn('deskripsi_permission', 'text', [ 'null' => TRUE ])
            ->addColumn('grup', 'string', [ 'limit' => 50, 'default' => 'lainnya' ])
            ->addIndex([ 'nama_permission' ], [ 'unique' => TRUE ])
            ->create();

        // Tabel Role_Permissions (Tabel Penghubung)
        $this->table('tbl_role_permissions', [ 'id' => FALSE, 'primary_key' => [ 'id_role', 'id_permission' ] ])
            ->addColumn('id_role', 'integer', [ 'signed' => FALSE ])
            ->addColumn('id_permission', 'integer', [ 'signed' => FALSE ])
            ->create();

        // =====================================================================
        // BAGIAN 2: UBAH TABEL PENGGUNA YANG SUDAH ADA
        // =====================================================================

        $tablePengguna = $this->table('tbl_pengguna');

        // Tambahkan kolom id_role baru
        if (!$tablePengguna->hasColumn('id_role'))
        {
            $tablePengguna->addColumn('id_role', 'integer', [ 'signed' => FALSE, 'null' => TRUE, 'after' => 'is_active' ]);
        }

        // Hapus kolom 'role' yang lama (enum)
        if ($tablePengguna->hasColumn('role'))
        {
            $tablePengguna->removeColumn('role');
        }

        $tablePengguna->update();

        // =====================================================================
        // BAGIAN 3: TAMBAHKAN FOREIGN KEY
        // =====================================================================

        $this->table('tbl_role_permissions')
            ->addForeignKey('id_role', 'tbl_roles', 'id_role', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->addForeignKey('id_permission', 'tbl_permissions', 'id_permission', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->update();

        $this->table('tbl_pengguna')
            ->addForeignKey('id_role', 'tbl_roles', 'id_role', [ 'delete' => 'SET_NULL', 'update' => 'CASCADE' ])
            ->update();
    }

    public function down()
    {

        // Batalkan perubahan pada tbl_pengguna
        $tablePengguna = $this->table('tbl_pengguna');
        $tablePengguna->dropForeignKey('id_role')->update();
        if ($tablePengguna->hasColumn('id_role'))
        {
            $tablePengguna->removeColumn('id_role');
        }
        if (!$tablePengguna->hasColumn('role'))
        {
            $tablePengguna->addColumn('role', 'enum', [ 'values' => [ 'admin', 'pimpinan', 'pegawai', 'developer' ] ]);
        }
        $tablePengguna->update();

        // Hapus tabel baru
        $this->table('tbl_role_permissions')->drop()->save();
        $this->table('tbl_permissions')->drop()->save();
        $this->table('tbl_roles')->drop()->save();
    }

}
