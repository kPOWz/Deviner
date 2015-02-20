<?php
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'PrivateField.php');
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'GUS',

	// preloading 'log' component
	'preload'=>array('log'),
	'aliases'=>array(
		'vendor' => realpath(__DIR__ . '/../../vendor'),
		'yiistrap' => 'vendor.crisu83.yiistrap',
	),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.controllers.*',
		'application.config.PrivateField',
		'zii.widgets.jui.*',
		'zii.widgets.*',
		//'vendor.crisu83.yiistrap.helpers.TbHtml',
		'vendor.crisu83.yiistrap.behaviors.*',
        'vendor.crisu83.yiistrap.components.*',
        'vendor.crisu83.yiistrap.form.*',
        'vendor.crisu83.yiistrap.helpers.*',
        'vendor.crisu83.yiistrap.widgets.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'gii',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),*/
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'quote'=>'job/estimate',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'yiistrap' => array(
            'class' => 'yiistrap.components.TbApi',   
        ),
		
		/*'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		'db'=>array(
			'connectionString' => PrivateField::get('connectionString'),
			'emulatePrepare' => true,
			'username' => PrivateField::get('username'),
			'password' => PrivateField::get('dbPass'),
			'charset' => 'utf8',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, info, trace, vardump',
					'maxFileSize' => '10240',
				),
				// uncomment the following to show log messages on web pages
				
				// array(
				// 	'class'=>'CWebLogRoute',
				// 	'levels'=>'error, warning, info, trace, vardump',
				// ),
				
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>PrivateField::get('email'),
	),
);