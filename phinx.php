<?php

// Muat autoloader Composer
require_once __DIR__ . '/vendor/autoload.php';

// Muat variabel dari file .env
if (class_exists('Dotenv\Dotenv'))
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

return
    [
        'paths'         => [
            'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
            'seeds'      => '%%PHINX_CONFIG_DIR%%/db/seeds',
        ],
        'environments'  => [
            'default_migration_table' => 'phinxlog',
            'default_database'        => 'development',
            'development'             => [
                'adapter' => 'mysql',
                'host'    => $_ENV['DB_HOST'] ?? 'localhost',
                'name'    => $_ENV['DB_NAME'] ?? '',
                'user'    => $_ENV['DB_USER'] ?? '',
                'pass'    => $_ENV['DB_PASS'] ?? '',
                'port'    => '3306',
                'charset' => 'utf8mb4',
            ],
            'production'              => [
                'adapter' => 'mysql',
                'host'    => $_ENV['DB_HOST'] ?? 'localhost',
                'name'    => $_ENV['DB_NAME'] ?? '',
                'user'    => $_ENV['DB_USER'] ?? '',
                'pass'    => $_ENV['DB_PASS'] ?? '',
                'port'    => '3306',
                'charset' => 'utf8mb4',
            ],
        ],
        'version_order' => 'creation',
    ];