<?php
class DashboardController extends Controller
{


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