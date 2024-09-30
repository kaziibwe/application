<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit286e41e24e70197b714f7a7bc8765843
{
    public static $files = array (
        '941748b3c8cae4466c827dfb5ca9602a' => __DIR__ . '/..' . '/rmccue/requests/library/Deprecated.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WpOrg\\Requests\\' => 15,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WpOrg\\Requests\\' => 
        array (
            0 => __DIR__ . '/..' . '/rmccue/requests/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Requests' => __DIR__ . '/..' . '/rmccue/requests/library/Requests.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit286e41e24e70197b714f7a7bc8765843::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit286e41e24e70197b714f7a7bc8765843::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit286e41e24e70197b714f7a7bc8765843::$classMap;

        }, null, ClassLoader::class);
    }
}
