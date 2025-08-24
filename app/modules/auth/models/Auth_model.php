<?php

class Auth_model
{

    public function getUserByUsername($username)
    {

        $users = [
            'admin' => [
                'id'       => 1,
                'nama'     => 'Administrator',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
        ];

        return $users[$username] ?? NULL;
    }

}