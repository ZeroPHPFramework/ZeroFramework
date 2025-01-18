<?php
/**
 * @var array<string, class-string>
 */


$aliases = [
    'View' => Zero\Lib\View::class,
    'DB' => Zero\Lib\Database::class,
];

$helpers =  [
    [
        'path' => lib_path('Console/Helper.php'),
        'enabled' => [
            'console' => true,
            'http' => true,
        ],
    ], [
        'path' => lib_path('Config/Env.php'),
        'enabled' => [
            'console' => true,
            'http' => true,
        ],
    ]
];

return [
    'aliases' => $aliases,
    'helpers' => $helpers,
];