<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__.'/../vendor/silex/silex.phar';

// Create application
$app = new Silex\Application();


// ---Add extensions---
$app->register(new Silex\Extension\DoctrineExtension(), array(
    'db.options'            => array(
        'driver'    => 'pdo_sqlite',
        'path'      => __DIR__.'/../resources/app.sqlite',
    ),
    'db.dbal.class_path'    => __DIR__.'/../vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/../vendor/doctrine-common/lib',
));

$app->register(new Silex\Extension\UrlGeneratorExtension());

$app->register(new Silex\Extension\SymfonyBridgesExtension(), array(
    'symfony_bridges.class_path' => __DIR__.'/../vendor',
));

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
    'twig.options'    => array('cache' => __DIR__.'/../cache'),
));


// ---Define routes---
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'style' => rand(1, 4),
    ));
})
->bind('homepage');

$app->get('/{slug}/{page}', function ($slug, $page) use ($app) {
    if (false === $content = $app['db']->fetchAssoc("SELECT id, title FROM content WHERE slug = ?", array($slug))) {
        throw new NotFoundHttpException();
    }
    $pages = $app['db']->fetchAll("SELECT page FROM page WHERE content_id = ? ORDER BY order_id ASC", array($content['id']));
    
    return $app['twig']->render('content.html.twig', array(
        'content' => array(
            'title' => $content['title'],
            'pages' => $pages
        ),
        'nav'     => array(
            'previous' => $page-1,
            'next'     => $page+1
        ),
        'slug'    => $slug,
        'page'    => ($page*2)-1, // correct page number as we are showing 2 pages every time
    ));
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


// ---Error handler---
$app->error(function (\Exception $e) {
    if ($e instanceof NotFoundHttpException) {
        return new Response('Die gewünschte Seite konnte leider nicht gefunden werden.', 404);
    }

    $code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;
    return new Response('Es tut uns leid, hier ist gerade etwas furchtbar schiefgelaufen.', $code);
});

// Return application for reuse
return $app;
