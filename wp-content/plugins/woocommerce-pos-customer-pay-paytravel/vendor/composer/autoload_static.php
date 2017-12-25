<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit276661be4a73005de32c8677766e6e90
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PhpMimeMailParser\\' => 18,
            'PhpImap\\' => 8,
        ),
        'D' => 
        array (
            'Ddeboer\\Imap\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PhpMimeMailParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-mime-mail-parser/php-mime-mail-parser/src',
        ),
        'PhpImap\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-imap/php-imap/src/PhpImap',
        ),
        'Ddeboer\\Imap\\' => 
        array (
            0 => __DIR__ . '/..' . '/ddeboer/imap/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit276661be4a73005de32c8677766e6e90::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit276661be4a73005de32c8677766e6e90::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
