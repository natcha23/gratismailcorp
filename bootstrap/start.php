<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name for the host that matches a
| given environment, then we will automatically detect it for you.
|
*/

$env = $app->detectEnvironment(array(

	'local' => array('homestead'),
		
// 	'develop' => array('cl1dev.smm.ais.co.th'),
// 	'staging' => array(''),
// 	'production' => array(''),

));

$env = $app->detectEnvironment(function() {
	return 'develop';
});

// The environment is local
define('_HTTP_REQUEST_', $_SERVER['REQUEST_SCHEME']."://");
define('_DOMAIN_', _HTTP_REQUEST_. $_SERVER['HTTP_HOST']);

$project = explode(DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_NAME']);
define('_APP_DIR_', DIRECTORY_SEPARATOR . $project[1] . DIRECTORY_SEPARATOR . $project[2] . DIRECTORY_SEPARATOR); // equal /SMMGetInfo/gratismailcorp/

isset($emailAddress);
define('EMAIL_DEVELOP', '1149test1@ais.co.th');
define('EMAIL_PRODUCTION', 'corporatecallcenter@ais.co.th');
$mailAddress = ($env == 'production')? EMAIL_PRODUCTION : EMAIL_DEVELOP;
define('_MAIL_ADDRESS_', $emailAddress);

/* 
$publicHost = 'http://dev.smm.ais.co.th   /SMMGetInfo/gratismailcorp/';	// dev
$publicHost = 'http://202.149.30.144      /SMMGetInfo/gratismailcorp/';	// prod
$publicHost = 'http://crawl3.smm.ais.co.th/SMMGetInfo/gratismailcorp/';	// prod
*/
define('_PUBLIC_HOST_', _DOMAIN_ . _APP_DIR_ );

/* 
$publicFilePath = 'http://dev.smm.ais.co.th   /SMMGetInfo/gratismailcorp/public/attachments/'; // dev
$publicFilePath = 'http://202.149.30.144      /SMMGetInfo/gratismailcorp/public/attachments/'; // production
$publicFilePath = 'http://crawl3.smm.ais.co.th/SMMGetInfo/gratismailcorp/public/attachments/'; // production 
*/
define('_PUBLIC_ATTACHMENTS_PATH_', _DOMAIN_ . _APP_DIR_ . 'public/attachments/');
	

/*
|--------------------------------------------------------------------------
| Bind Paths
|--------------------------------------------------------------------------
|
| Here we are binding the paths configured in paths.php to the app. You
| should not be changing these here. If you need to change these you
| may do so within the paths.php file and they will be bound here.
|
*/

$app->bindInstallPaths(require __DIR__.'/paths.php');

/*
|--------------------------------------------------------------------------
| Load The Application
|--------------------------------------------------------------------------
|
| Here we will load this Illuminate application. We will keep this in a
| separate location so we can isolate the creation of an application
| from the actual running of the application with a given request.
|
*/

$framework = $app['path.base'].
                 '/vendor/laravel/framework/src';

require $framework.'/Illuminate/Foundation/start.php';

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
