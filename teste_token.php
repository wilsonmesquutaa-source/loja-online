<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

echo '<pre>';

echo "EMAIL SERVICE\n";

var_dump(
    class_exists(
        'App\\Services\\EmailService'
    )
);


echo "\nCLIENTE CONTROLLER\n";

var_dump(
    class_exists(
        'App\\Controllers\\Cliente\\ClienteController'
    )
);


echo "\nARQUIVO DO EMAIL SERVICE\n";

$reflectionEmail =
    new ReflectionClass(
        'App\\Services\\EmailService'
    );

var_dump(
    $reflectionEmail->getFileName()
);


echo "\nARQUIVO DO CLIENTE CONTROLLER\n";

$reflectionCliente =
    new ReflectionClass(
        'App\\Controllers\\Cliente\\ClienteController'
    );

var_dump(
    $reflectionCliente->getFileName()
);


echo '</pre>';