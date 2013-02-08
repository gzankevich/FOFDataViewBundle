<?php

/**
 * Note: these tests assume that this library has been installed as a Symfony2 vendor
 */

if (!$loader = @include __DIR__.'/../../../../vendor/autoload.php') {
    echo <<<EOM
You must set up the project dependencies by running the following commands:

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

EOM;

    exit(1);
}

$loader->add('./', __DIR__);
