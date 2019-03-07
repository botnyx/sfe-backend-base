<?php
/* Load the Configuration */
if( !file_exists( "../configuration.ini" ) ){ die("check configuration.ini"); }else{
	define( '_SETTINGS',  parse_ini_file("../configuration.ini", true)) ;
}

/* Composer autoloader */
require _SETTINGS['paths']['root'] .'/vendor/autoload.php';

/* Include the shared core, which adds the deps, middleware and routes to the app. */
require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/dependencies.php");

/* Create the Sfe instance with settings. */
$sfe = new Botnyx\Sfe\Shared\Application(_SETTINGS);
/* Enable errors on screen */
$sfe->show_errors();

/* Start the Slim application */
$app = $sfe->start();

/* Setup the container  */
$container = $app->getContainer();

/* Include the middleware. */
require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/container.php");

/* Include the middleware. */
require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/middleware.php");

/* Include the routes. */
require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/routes.php");

/* Finally, run the app. */
$app->run();


#use Slim\Http;
#use Slim\Views;
#use Psr\Http\Message\ServerRequestInterface;
#use Psr\Http\Message\ResponseInterface;
#use Doctrine\Common\Cache\FilesystemCache;
#use Doctrine\Common\Cache\PredisCache;
#use Kevinrob\GuzzleCache\CacheMiddleware;
#use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
#use Kevinrob\GuzzleCache\Strategy\PublicCacheStrategy;
#use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
#use Kevinrob\GuzzleCache\KeyValueHttpHeader;
#use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
#use \Psr\Http\Message\ServerRequestInterface as Request;
#use \Psr\Http\Message\ResponseInterface as Response;
