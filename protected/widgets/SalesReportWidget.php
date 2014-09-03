<?php


class SalesReportWidget extends CWidget
{
	public static $timeSpreadBegin;

	public static $timeSpreadEnd;

	public static $jobStatus=Job::COMPLETED;

	public static $eventLogEvent=EventLog::JOB_PRINT;

	public static $jobs=array();

	//validate & introduce CClip

	public function init() {
	    self::$timeSpreadBegin=date('Y-m-01 00:00:00');
	    self::$timeSpreadEnd=date('Y-m-t 23:59:59');
	}

	/**
	 * Finds jobs meeting the time spread, status and leader critera
	 * 
	 */
	private static function setJobsForReport(){
		if(is_null(self::$timeSpreadBegin)) self::$timeSpreadBegin =  date('Y-m-01 00:00:00');
		if(is_null(self::$timeSpreadEnd)) self::$timeSpreadEnd =  date('Y-m-t 23:59:59');

		$criteria = new CDbCriteria;
	  	$criteria->join = 'INNER JOIN `event_log` ON `event_log`.`OBJECT_ID` = `t`.`ID`';
	  	$criteria->addCondition('`event_log`.`OBJECT_TYPE`=\'Job\'');
	  	$criteria->addCondition('`event_log`.`EVENT_ID`='.self::$eventLogEvent);
  		$criteria->addCondition('(`event_log`.`DATE` BETWEEN \''.self::$timeSpreadBegin.'\' AND \''.self::$timeSpreadEnd. '\')');

      	self::$jobs = Job::model()->findAllByAttributes(array(
              'LEADER_ID'=> Yii::app()->user->id,
              'STATUS'=>array(self::$jobStatus),
       	), $criteria);
 	}

 	public static function getSales(){
 		$sales=0;
 		self::setJobsForReport();
 		foreach(self::$jobs as $job){
 			$job = Job::model()->findByPk($job->ID);
          	$sales += $job->total;
 		}
 		return $sales;
 	}

 	public static function getCostOfGoodsSoldPercentage(){
 		$costOfGoodsSold=0;
 		self::setJobsForReport();
 		$sales = self::getSales();
 		if($sales <= 0) return 0;
 		foreach(self::$jobs as $job){
 			$job = Job::model()->findByPk($job->ID);
          	$costOfGoodsSold += $job->costOfGoodsSold;
 		}
 		return $costOfGoodsSold / $sales;
 	}

 	public function updateSales(CEvent $event) {
 		Yii::log('event : '.$event->params["status"], CLogger::LEVEL_INFO, 'application.widgets.SalesReportWidget');
	    $this->render('salesReportWidget', array(
			'sales'=>self::getSales(),
			'cog'=>self::getCostOfGoodsSoldPercentage(),
			'formatter'=>new Formatter
		));
	}

 	public function run()
	{	    
		$this->render('salesReportWidget', array(
			'sales'=>self::getSales(),
			'cog'=>self::getCostOfGoodsSoldPercentage(),
			'formatter'=>new Formatter
		));
	}
}