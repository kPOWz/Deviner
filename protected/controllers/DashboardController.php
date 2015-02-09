<?php
class DashboardController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index'),
				'users'=>array('@'),
				'expression'=>"Yii::app()->user->getState('isAdmin');",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{

		$this->render('index');
	}
	
}



/**
 * $view = 'invoice_quicken';
			$name = $model->ID . '_invoice.iif';
			$mime = 'text/iif';
			$model->attachBehavior('quickbooks', 'application.behaviors.Job.QBInitializer');						
			Yii::app()->request->sendFile($name, $this->renderPartial($view, $params, true), $mime);	
	
 */