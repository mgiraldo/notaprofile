<?php
ob_start();

global $app;

$app['sitename']				= 'not_a_profile';

#Paths
$app['url']						= 'http://notaprofile.manuelvieda.com/';
$app['urlroot']					= "/html/";
$app['siteroot']				= '/xxxx/xxxx/xxxx/xxxx.xxxx.com/html/';
$app['libpath']					= $app["siteroot"] . "lib/";
$app['liburl']					= $app["url"] . "lib/";
$app['photoroot']				= "photos/";
$app['templatepath']			= $app["siteroot"] . "template/";

#DB
$app['dbhost']					= "db2019.perfora.net";
$app['db']						= "db295480523";
$app['dbuser']					= "dbo295480523";
$app['dbpassword']				= "dise3220_nap";

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
$app['siteemail']				= "notaprofile@manuelvieda.com";

#GoogleMaps
$app['apiKey'] = "ABQIAAAA18qEiHphe4mikuwMZVbDfBR446503xKeWs3_h9WUvSMDtqkhXBSu0DLVqlKGM_fDey78REtRKSjCMQ";

#Definiendo Localización
foreach( $app["defaults"]["localization"] as $row ) $loc[] = "'" . $row . "'";
eval("setlocale( LC_ALL, " . implode( ", ", $loc ) . ");");
error_reporting(E_ERROR | E_WARNING | E_PARSE);

define(APP, serialize($app));
?>