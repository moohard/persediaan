<?php

require_once APP_PATH . '/core/Model.php';

class Auth_model extends Model
{

    public function getUserByUsername($username)
    {

        // Data dummy. Ganti dengan query database menggunakan prepared statement.
        $users = [
            'admin' => [
                'id' => 1,
                'nama' => 'Administrator',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            'developer' => [
                'id' => 99,
                'nama' => 'Developer',
                'password' => password_hash('devpass', PASSWORD_DEFAULT),
                'role' => 'developer'
            ]
        ];
        return $users[$username] ?? null;
    }

}