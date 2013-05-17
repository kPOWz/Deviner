<?php

// remove the following line when in production mode
// enables all Yii::trace logging
//defined('YII_DEBUG') or define('YII_DEBUG',true);

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
//defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

//stop Yii from handling errors
//defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);

require_once($yii);
Yii::createWebApplication($config)->run();
