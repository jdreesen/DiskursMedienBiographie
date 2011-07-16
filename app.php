<?php

require_once __DIR__.'/vendor/silex/silex.phar';

$app = new Silex\Application();

$app->get('/', function () {
    return 'Herzlich willkommen!';
})
->bind('homepage');

return $app;