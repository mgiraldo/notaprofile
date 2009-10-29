<?php
ob_start();

global $app;

$app['sitename']				= 'not_a_profile';

#Paths
$app['url']						= 'http://www.xxxxx.com/';
$app['urlroot']					= "/";
$app['siteroot']				= '/path/to/html/';
$app['libpath']					= $app["siteroot"] . "lib/";
$app['liburl']					= $app["url"] . "lib/";
$app['photoroot']				= "photos/";
$app['templatepath']			= $app["siteroot"] . "template/";

#DB
$app['dbhost']					= "dbserver";
$app['db']						= "dbname";
$app['dbuser']					= "dbuser";
$app['dbpassword']				= "dbpass";

#Images
$app['photo_max_size']			= 9999999999;
$app['convert_path']			= "convert";
$app['full_size']				= "640x480";


#DATE
$app['dateformat_short']		= "%d/%m/%Y";
$app['dateformat_long']			= "%d de %B de %Y"; //20 de Septiembre de 2008

#Others
$app['defaults']['localization'] = Array( 'esp', 'spanish', 'es', 'es_ES' );
$app['debug']					= 0;
$app['siteemail']				= "something@email.com";

#GoogleMaps
$app['apiKey'] = "googleapikey";

#Definiendo Localización
foreach( $app["defaults"]["localization"] as $row ) $loc[] = "'" . $row . "'";
eval("setlocale( LC_ALL, " . implode( ", ", $loc ) . ");");
error_reporting(E_ERROR | E_WARNING | E_PARSE);

define(APP, serialize($app));
?>