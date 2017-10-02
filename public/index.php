<?php

  session_start();

  require_once __DIR__ . '/../vendor/autoload.php';

  use Monolog\Logger;
  use Monolog\Handler\StreamHandler;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  $config = [
      'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
        'debug' => true,
        'addContentLengthHeader' => true,
        'routerCacheFile' => __DIR__ . '/../cache/routes.cache'
      ]
  ];

  $app = new \Slim\App($config);

  $container = $app -> getContainer();

  $container['logger'] = function()
  {
    $logger = new \Monolog\Logger('phaziz');
    $file_handler = new \Monolog\Handler\StreamHandler(__DIR__ . '/../logs/' . date('Y-m-d') . '-log.logfile');
    $logger -> pushHandler($file_handler);
    return $logger;
  };

  $container['twig'] = function()
  {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../views/');
    $twig = new Twig_Environment($loader, [
      'cache' => __DIR__ . '/../cache/',
      'debug' => false,
      'strict_variables' => true,
      'autoescape' => 'html',
      'optimizations' => -1,
      'charset' => 'utf-8'
    ]);

    return $twig;
  };

  $app -> get('/', function (Request $request, Response $response) use ($app)
    {
      $this -> logger -> addInfo('Root Path');

      return $this -> twig -> render('index.html', [
        'PageTitle' => 'Homepage'
      ]);
    }
  );

  $app -> get('/404', function (Request $request, Response $response) use ($app)
    {
      $this -> logger -> addInfo('404 Path');

      return $this -> twig -> render('404.html', [
        'PageTitle' => 'Ups Uh Oh 404'
      ]);
    }
  );

  $app -> run();
