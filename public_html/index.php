<?php 
/* 
	Parse Config with sections 
	
	by default, configs are loaded by hostname
	
	Example: 
		testsite-com.conf.ini
		my-testsite-com.conf.ini

*/

if( !file_exists( "../configuration.ini" ) ){
	die("check configuration.ini");
}else{
	define( '_SETTINGS',  parse_ini_file("../configuration.ini", true)) ;
}
	



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

