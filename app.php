<?php

require_once __DIR__.'/vendor/silex/silex.phar';

// Create application
$app = new Silex\Application();

// Add extensions
$app->register(new Silex\Extension\UrlGeneratorExtension());

$app->register(new Silex\Extension\SymfonyBridgesExtension(), array(
    'symfony_bridges.class_path' => __DIR__.'/vendor',
));

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/vendor/Twig/lib',
));

// Define routes
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'style' => rand(1, 4),
    ));
})
->bind('homepage');

// Return application for reuse
return $app;