<?php

<<<<<<< HEAD
if (!@include __DIR__ . '/../vendor/autoload.php') {
=======
$loader = @include __DIR__ . '/../vendor/autoload.php';

if (!$loader) {
>>>>>>> 22bd46e5f556efef9a74c10a7d94214a5b13c8c8
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
<<<<<<< HEAD
}
=======
}

$loader->add('Vespolina\Tests', __DIR__ . '/../tests');
>>>>>>> 22bd46e5f556efef9a74c10a7d94214a5b13c8c8
