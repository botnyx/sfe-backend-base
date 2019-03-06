<?php 

/* 
	Load the Configuration 
*/

if( !file_exists( "../configuration.ini" ) ){
	die("check configuration.ini");
}else{
	define( '_SETTINGS',  parse_ini_file("../configuration.ini", true)) ;
}
	


//print_r(_SETTINGS);

/* quit if no settings.*/ 
if(_SETTINGS==false){die("check configuration.ini");}

/* Composer autoloader */
require _SETTINGS['paths']['root'] .'/vendor/autoload.php';





use Slim\Http;
use Slim\Views;



use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\PredisCache;

use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Strategy\PublicCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;

use Kevinrob\GuzzleCache\KeyValueHttpHeader;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;

#use \Psr\Http\Message\ServerRequestInterface as Request;
#use \Psr\Http\Message\ResponseInterface as Response;



/*
	
	Include the shared core, which adds the deps, middleware and routes to the app.
	
*/
require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/dependencies.php");






/* 
	Create the Slim application 

*/
$app = new \Slim\App([
	'debug' => _SETTINGS['slim']['debug'],
	'settings' => [
		
		'displayErrorDetails' => _SETTINGS['slim']['debug'], // set to false in production
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : _SETTINGS['paths']['logs'].'/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
		'addContentLengthHeader'=>false,  
/*		'addContentLengthHeader'=>false 
			ALWAYS disable this, else  the error 
			PHP Fatal error:  Uncaught TypeError: fread() expects parameter 2 to be integer, string given 
			Zend\\Diactoros\\Stream->read('173') 
		*/
	],
]);

/* Setup the container  */
$container = $app->getContainer();

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


function someFunction(){
	return "somefunction";
}
/* Register view component on container */
//$container['view'] = function ($container) {
//    return new \Slim\Views\PhpRenderer(_SETTINGS['paths']['templates']);
//};


require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/middleware.php");


require_once(_SETTINGS['paths']['root']."/vendor/botnyx/sfe-shared-core/src/includes/routes.php");






var_dump( class_exists("Botnyx\\Sfe\\Shared\\Application"));
die();




/* Cookie helper func. */
function getCookieValue(Slim\Http\Request $request, $cookieName)
{
	$cookies = $request->getCookieParams();
	return isset($cookies[$cookieName]) ? $cookies[$cookieName] : null;
}
/* dev helper to show errors. */
function show_errors(){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE);
}

 show_errors();



if(!array_key_exists('sfeFrontend',_SETTINGS)){
	$app->get('/robots.txt',  function ( $request,  $response, array $args){
		$res = "User-agent: *".PHP_EOL."Disallow: /";
		return $response->write($res);
		
	});		
}



/* Finally, run the app. */
$app->run();




