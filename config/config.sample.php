<?php
/**
 * Contoh konfigurasi. File asli (config.php) akan dibuat otomatis
 * oleh installer. Jangan commit config.php yang berisi kredensial asli.
 */
return [
    'app' => [
        'name'     => 'TemanJuara',
        'base_url' => 'https://tryout.bimbeltemanjuara.com/',
        'env'      => 'production',
        'key'      => 'isi-dengan-string-acak-64-karakter',
        'debug'    => false,
    ],
    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'bimbelt1_tryoutapp',
        'user'    => 'bimbelt1_tryoutappuser',
        'pass'    => 'PASSWORD_ANDA',
        'charset' => 'utf8mb4',
    ],
];
