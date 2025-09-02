<?php

require_once APP_PATH . '/core/Model.php';

class Hakakses_model extends Model
{

    public function getAllRoles()
    {

        return $this->db->query("SELECT * FROM tbl_roles ORDER BY nama_role")->fetch_all(MYSQLI_ASSOC);
    }

    public function getPermissionsByRole($id_role)
    {

        $stmt = $this->db->prepare("
            SELECT 
                p.id_permission, p.nama_permission, p.deskripsi_permission, p.grup,
                (CASE WHEN rp.id_role IS NOT NULL THEN 1 ELSE 0 END) as diizinkan
            FROM tbl_permissions p
            LEFT JOIN tbl_role_permissions rp ON p.id_permission = rp.id_permission AND rp.id_role = ?
            ORDER BY p.grup, p.nama_permission
        ");
        $stmt->bind_param('i', $id_role);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Kelompokkan berdasarkan grup
        $grouped = [];
        foreach ($result as $row)
        {
            $grouped[$row['grup']][] = $row;
        }

        return $grouped;
    }

    public function updateRolePermissions($id_role, $permission_ids)
    {

        if (empty($id_role))
        {
            return [ 'success' => FALSE, 'message' => 'Role tidak valid.' ];
        }

        $this->db->begin_transaction();
        try
        {
            // 1. Hapus semua permission lama untuk role ini
            $stmt_delete = $this->db->prepare("DELETE FROM tbl_role_permissions WHERE id_role = ?");
            $stmt_delete->bind_param('i', $id_role);
            $stmt_delete->execute();

            // 2. Masukkan permission baru yang dipilih
            if (!empty($permission_ids))
            {
                $stmt_insert = $this->db->prepare("INSERT INTO tbl_role_permissions (id_role, id_permission) VALUES (?, ?)");
                foreach ($permission_ids as $id_permission)
                {
                    $stmt_insert->bind_param('ii', $id_role, $id_permission);
                    $stmt_insert->execute();
                }
            }

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Hak akses berhasil diperbarui.' ];
        } catch (Exception $e)
        {
            $this->db->rollback();
            log_query("Update Hak Akses", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal memperbarui hak akses.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

}