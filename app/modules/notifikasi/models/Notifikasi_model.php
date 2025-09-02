<?php

require_once APP_PATH . '/core/Model.php';

class Notifikasi_model extends Model
{

    public function getUnreadNotifications($user_id)
    {

        $stmt = $this->db->prepare("
        SELECT id_notifikasi, pesan, tautan, created_at 
        FROM tbl_notifikasi 
        WHERE id_pengguna_tujuan = ? AND sudah_dibaca = 0
        ORDER BY created_at DESC
        LIMIT 10
    ");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function markAsRead($user_id, $notif_id = NULL)
    {

        // Jika notif_id diberikan, tandai satu notifikasi.
        // Jika tidak, tandai semua notifikasi milik pengguna.
        if ($notif_id)
        {
            $stmt = $this->db->prepare("UPDATE tbl_notifikasi SET sudah_dibaca = 1 WHERE id_notifikasi = ? AND id_pengguna_tujuan = ? AND sudah_dibaca = 0");
            $stmt->bind_param('ii', $notif_id, $user_id);
        } else
        {
            $stmt = $this->db->prepare("UPDATE tbl_notifikasi SET sudah_dibaca = 1 WHERE id_pengguna_tujuan = ? AND sudah_dibaca = 0");
            $stmt->bind_param('i', $user_id);
        }

        try
        {
            $stmt->execute();
            return [ 'success' => TRUE ];
        } catch (Exception $e)
        {
            return [ 'success' => FALSE, 'message' => 'Gagal menandai notifikasi.' ];
        }
    }

}