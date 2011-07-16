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

$app->get('/{slug}/{page}', function ($slug, $page) use ($app) {
    // correct page number as we are showing 2 pages every time
    $page = ($page*2)-1;
    
    return "$slug; $page";
})
->assert('page', '\d+')
->value('page', '1')
->bind('content');

// compat route for old URL layout
$app->get('/{slug}/page/{page}', function ($slug, $page) use ($app) {
    return $app->redirect($app['url_generator']->generate('content', array(
        'slug' => $slug,
        'page' => $page
    )));
})
->assert('page', '\d+');

// Return application for reuse
return $app;
