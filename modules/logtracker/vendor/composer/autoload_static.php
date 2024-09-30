<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit20fad51902f91e7fd3039e016a6556b5
{
    public static $files = array (
        '941748b3c8cae4466c827dfb5ca9602a' => __DIR__ . '/..' . '/rmccue/requests/library/Deprecated.php',
        '4ea1bc7628a69f4030eed01c83b33443' => __DIR__ . '/..' . '/composer/files_autoload.php',
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit20fad51902f91e7fd3039e016a6556b5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit20fad51902f91e7fd3039e016a6556b5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit20fad51902f91e7fd3039e016a6556b5::$classMap;

        }, null, ClassLoader::class);
    }
}
