<?php 

/* Load the Configuration */
if( !file_exists( "../configuration.ini" ) ){
	die("check configuration.ini");
}else{
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

var_dump( class_exists("Botnyx\\Sfe\\Shared\\Application"));
die();






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




$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};


//var_dump(_SETTINGS['twig']['cache']);
//die();

$container['view'] = function ($c){
	
	$view = new \Slim\Views\Twig(_SETTINGS['paths']['templates'], [
		'cache' => false /*_SETTINGS['twig']['cache']*/,
		'debug' => _SETTINGS['twig']['debug']
	]);
		
	// Instantiate and add Slim specific extension
	$basePath = rtrim(str_ireplace('index.php', '', $c->get('request')->getUri()->getBasePath()), '/');
	
	// Add twigExtension
	$view->addExtension(new \Slim\Views\TwigExtension($c->get('router'), $basePath));
	
	// Add the debug extension
	if(_SETTINGS['twig']['debug']==true){
		$view->addExtension(new \Twig_Extension_Debug() );
	}
		
	// add Translation extensions.
	$view->addExtension(new \Twig_Extensions_Extension_I18n());
	
	//$view->addExtension(new \Twig_Extensions_Extension_Intl());
	//$twig->addExtension(new Project_Twig_Extension());
	//
	//$twig->addFunction('functionName', new Twig_Function_Function('someFunction'));
	
	if(array_key_exists('sfeBackend',_SETTINGS)){
		//$view->addFunction('functionName', new Twig_Function_Function('someFunction'));
	
		//$view->addExtension ( new \Botnyx\sfeBackend\twigExtension\Userinfo() );
	}
	return $view;
};


/* Include the middleware. */
require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/middleware.php");
/* Include the routes. */
require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/routes.php");



if(!array_key_exists('sfeFrontend',_SETTINGS)){
	$app->get('/robots.txt',  function ( $request,  $response, array $args){
		$res = "User-agent: *".PHP_EOL."Disallow: /";
		return $response->write($res);
		
	});		
}



/* Finally, run the app. */
$app->run();
