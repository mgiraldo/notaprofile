<?php
ob_start();

global $app;

$app['sitename']				= 'not_a_profile';

#Paths
$app['url']						= 'http://www.notaprofile.com/';
$app['urlroot']					= "/";
$app['siteroot']				= '/nfs/c01/h12/mnt/9117/domains/notaprofile.com/html/';
$app['libpath']					= $app["siteroot"] . "lib/";
$app['liburl']					= $app["url"] . "lib/";
$app['photoroot']				= "photos/";
$app['templatepath']			= $app["siteroot"] . "template/";

#DB
$app['dbhost']					= $ENV{'DATABASE_SERVER'};
$app['db']						= "db9117_notaprofile";
$app['dbuser']					= "db9117_uniandes";
$app['dbpassword']				= "noesunperfil";

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