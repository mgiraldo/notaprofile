<?php
ob_start();

global $app;

$app['sitename']				= 'not_a_profile';
#Paths
$app['url']						= 'http://xxxx.xxxx.com/';
$app['urlroot']					= "/html/";
$app['siteroot']				= '/xxxx/xxxx/xxxx/xxxx.xxxx.com/html/';
$app['libpath']					= $app["siteroot"] . "lib/";
$app['liburl']					= $app["url"] . "lib/";
$app['photoroot']				= "up/";

$app['templatepath']			= $app["siteroot"] . "template/";

#DB
$app['dbhost']					= "localhost";
$app['db']						= "notaprofile";
$app['dbuser']					= "root";
$app['dbpassword']				= "";

#Images
$app['photo_max_size']			= 999999999999999;
$app['convert_path']			= "convert";
$app['full_size']				= "640x480";

#JOBS
$app['new_job_days']			= 5;// Durante cuantos días un empleo es nuevo.

#DATE
$app['dateformat_short']		= "%d/%m/%Y";
$app['dateformat_long']			= "%d de %B de %Y"; //20 de Septiembre de 2008

#Others
$app['defaults']['localization'] = Array( 'esp', 'spanish', 'es', 'es_ES' );
$app['debug']					= 0;
$app['siteemail']				= "xxxx@xxxx.com";

#GoogleMaps
$app['apiKey'] = "ABQIAAAA18qEiHphe4mikuwMZVbDfBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxR3ccS3aeoyDwYsLjU2Gj3FcJ7nNw";

#Definiendo Localización
foreach( $app["defaults"]["localization"] as $row ) $loc[] = "'" . $row . "'";
eval("setlocale( LC_ALL, " . implode( ", ", $loc ) . ");");
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//set_include_path(get_include_path() . PATH_SEPARATOR . "/var/www/vhosts/pepsi.com/subdomains/new/httpdocs/PEAR" );

define(APP, serialize($app));
?>