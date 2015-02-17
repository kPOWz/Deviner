<?php
Yii::import('application.extensions.validators.*');
require_once('compareDate.php');

/**
 * This is the model class for table "job".
 *
 * The followings are the available columns in table 'job':
 * @property integer $ID
 * @property integer $CUSTOMER_ID
 * @property integer $LEADER_ID
 * @property integer $PRINTER_ID
 * @property integer $PRINT_ID
 * @property string $NAME
 * @property string $DESCRIPTION
 * @property string $NOTES
 * @property string $ISSUES
 * @property integer $RUSH
 * @property string $SET_UP_FEE
 * @property integer $SCORE
 * @property string $QUOTE
 * @property integer $STATUS
 *
 * The followings are the available model relations:
 * @property Customer $CUSTOMER
 * @property User $LEADER
 * @property Printer $PRINTER
 * @property JobLine[] $jobLines
 * @property PrintJob $printJob
 * @property EventLog[] $events
 * @property Lookup $status
 * 
 */
class Job extends CActiveRecord
{
	public $customer_search;
	
	//job statuses 
	const CREATED = 26; //the job has just been created, and perhaps a quote has been given
	const INVOICED = 31; //a formal invoice has been sent.//deprecated
	const PAID = 27; //the invoice was received and the customer has paid for it.//deprecated
	const SCHEDULED = 28; //the job has been scheduled on the timeline.//deprecated
	const COMPLETED = 29; //the job has been completed.
	const CANCELED = 30; //the job has been canceled.
	//Job::CREATED, Job::INVOICED, Job::PAID, Job::SCHEDULED, Job::COMPLETED, Job::CANCELED
	const ORDERED = 268; //the garments have been ordered
	const COUNTED = 269; //the garments have been received and counted.
	const PRINTED = 270; //the garments have been printed.
	
	const FEE_TAX_RATE = 185; //identifies the tax rate field.
	
	private $_additionalFees; //cache this value here.
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Job the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function statusListData(){
		$statusesArray =CHtml::listData(Lookup::listItems('JobStatus'), 'ID', 'TEXT');
		// $statusListData=array();
  //       foreach ($statusesArray as $statusId=>$statusText)
  //       {
  //           $statusListData[]=array('label'=>$statusText,'url'=>$statusId);
  //       }
  //       return $statusListData;
		return $statusesArray;
	}

	public static function statusButtonData(){
		$statusesArray =CHtml::listData(Lookup::listItems('JobStatus'), 'ID', 'TEXT');
		$statusListData=array();
        foreach ($statusesArray as $statusId=>$statusText)
        {
            $statusListData[]=array('label'=>$statusText,'url'=>$statusId);
        }
        return $statusListData;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'job';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('SCORE', 'numerical', 'integerOnly'=>true),
			array('RUSH', 'numerical'),
			array('SET_UP_FEE', 'numerical'),
			array('ID, NAME, DESCRIPTION, NOTES, ISSUES, STATUS, additionalFees', 'safe'),
			array('printDate', 'compareDate', 'compareAttribute'=>'dueDate', 'operator'=>'<='),	
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, CUSTOMER_ID, LEADER_ID, NAME, DESCRIPTION, NOTES, ISSUES, RUSH, SET_UP_FEE, SCORE, QUOTE, customer_search', 
					'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'CUSTOMER' => array(self::BELONGS_TO, 'Customer', 'CUSTOMER_ID'),
			'LEADER' => array(self::BELONGS_TO, 'User', 'LEADER_ID'),
			'PRINTER'=> array(self::BELONGS_TO, 'User', 'PRINTER_ID'),
			'jobLines' => array(self::HAS_MANY, 'JobLine', 'JOB_ID'),			
			'printJob' => array(self::BELONGS_TO, 'PrintJob', 'PRINT_ID'),
			'events'=> array(self::HAS_MANY, 'EventLog', 'OBJECT_ID', 'condition'=>'events.OBJECT_TYPE = \'Job\'', 'index'=>'EVENT_ID'),
			'status'=>array(self::BELONGS_TO, 'Lookup', 'STATUS'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'CUSTOMER_ID' => 'Customer',
			'LEADER_ID' => 'Leader',
			'DESCRIPTION' => 'Description',
			'NOTES' => 'Notes',
			'ISSUES' => 'Issues',
			'RUSH' => 'Rush',
			'SET_UP_FEE' => 'Set Up Fee',
			'SCORE' => 'Score',
			'QUOTE' => 'Quoted',
			'score' => 'Auto Score',
			'quote' => 'Auto Quote Total',
			'totalPasses' => 'Passes',
			'formattedDueDate'=> 'Due Date',
			'formattedPrintDate'=> 'Print Date',
			'formattedPickUpDate' =>'Pickup Date', /* not used as of 05/13 */
			'NAME'=>'Name',
			'PRINTER'=>'Printer',
			'PRINTER_ID'=>'Printer',
		);
	}
	
	/**
	 * Gets an array mapping attribute names to event IDs.
	 * @return array The resultant array.
	 */
	protected function eventAttributes(){
		return array(
			'dueDate'=>EventLog::JOB_DUE,
			'printDate'=>EventLog::JOB_PRINT,
			'pickUpDate'=>EventLog::JOB_PICKUP,
		);
	}
	
	/**
	 * Gets an inaccessible (properties not yet declared or not visible in the current scope) property.
	 * Never triggered in a static context nor when assignment chaining.
	 * Special handling for getting properties' display formats and Jobs' EventLog events (dueDate, printDate & pickUpDate)
	 * @param name The name of the property being read.
	 * @return value assoicated with name parameter.
	 */
	public function __get($name){
		$formatted=false;
		//first, determine if client code is requesting a "formatted" attribute
		if(($pos = strpos($name, 'formatted')) === 0){
			$formatted = true;
			Yii::log('Formatted date requested : '.$name, CLogger::LEVEL_TRACE, 'application.models.job');
			$name = substr($name, 9); //9 is length of "formatted"
			$first = substr($name, 0, 1); //get first character
			$first = strtolower($first);
			$name = substr_replace($name, $first, 0, 1);
		}
		
		//then, if the (unformatted) attribute is an event attribute,
		//get the event value
		foreach($this->eventAttributes() as $attrName => $eventID){
			if(strcmp($name, $attrName) == 0){
				$event = $this->getEventModel($eventID);
				$value = $event->DATE;
				
				if($formatted){
					Yii::log('Formatting date :  '.$value, CLogger::LEVEL_TRACE, 'application.models.job');
					//format the date like so - Monday, January 2, 2013 and
					//	don't let strtotime default the date to 1969 when null - preserve the null
					$date = strtotime($value);
					$value = ($date === false) ? null : date('l, F j, Y', $date);
					
					Yii::log('Formatted date :  '.$value, CLogger::LEVEL_TRACE, 'application.models.job');
				}
				return $value;
			}
		}
		//property not found - let the parent have a go
		return parent::__get($name);		
	}
	
	/**
	 * Sets an inaccessible (properties not yet declared or not visible in the current scope) property.
	 * Never triggered in a static context nor when assignment chaining.
	 * Special handling for setting properties' display formats and Jobs' EventLog events (dueDate, printDate & pickUpDate)
	 * @param name The name of the property being written.
	 * @param value The value of the property being written.
	 * @return return value of __set() is ignored by PHP.
	 */
	public function __set($name, $value){
		$found = false;
		//first, determine if client code is requesting a "formatted" attribute
		if(strlen($name) > 9 && substr($name, 0, 9) === 'formatted'){
			$name = substr($name, 9); //9 is length of "formatted"
			$first = substr($name, 0, 1); //get first character
			$first = strtolower($first);
			$name = substr_replace($name, $first, 0, 1);
		}
		
		//if the attribute is an event attribute, set it's date
		foreach($this->eventAttributes() as $eventAttrName => $eventID){
			if(strcmp($name, $eventAttrName) == 0){
				$event = $this->getEventModel($eventID);
				$event->DATE = $value;
				$found = true;
			}
		}
		
		//If property not found, let the parent have a go
		if(!$found){
			parent::__set($name, $value);
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 * TODO: may need to set specific scenario for AutoComplete search vs a form search ...
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('NAME', $this->NAME, true, 'OR');
		$criteria->with = array('CUSTOMER', 'CUSTOMER.USER');
		$criteria->compare('CUSTOMER.COMPANY', $this->customer_search, true, 'OR');
		$criteria->compare('USER.FIRST', $this->customer_search, true, 'OR');
		$criteria->compare('USER.LAST', $this->customer_search, true, 'OR');

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
						'pageSize'=>25,
				),
		));
	}

	/* 
	 * Gets a paged list of jobs with the given status value.
	 * @param mixed $status The status value, or array of status values, by which to filter.
	 * @return CActiveDataProvider The set of jobs with the given status(es).
	 */
	public function searchByStatus($status)
	{
		$criteria=new CDbCriteria;
		$criteria->compare('STATUS', $status, false, 'OR');
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
						'pageSize'=>15,
				),
		));

		//option 2  (slower, but can handle status array)
		// $activeData = $this->listJobsByStatus($status);
		// $dataProvider =  new CActiveDataProvider(get_class($this), array(
		// 		'pagination'=>array(
		// 				'pageSize'=>15,
		// 		),
		// 	));
		// $dataProvider->setData($activeData);
		// return $dataProvider;
	}
	
	/**
	 * Gets a list of jobs with the given status value.
	 * @param mixed $status The status value, or array of status values, by which to filter.
	 * @return array The set of jobs with the given status(es).
	 */
	public static function listJobsByStatus($status){
		return Job::model()->findAllByAttributes(array('STATUS'=>$status));
	}
	
	/**
	 * Gets a list of jobs with the given status value.
	 * @param mixed $status The status value, or array of status values, by which to filter.
	 * @param mixed $beginDate The status value, or array of status values, by which to filter.
	 * @param mixed $endDate The status value, or array of status values, by which to filter.
	 * @param mixed $eventId The status value, or array of status values, by which to filter. Defaults to Job Print event.
	 * @return array The set of jobs with the given status(es).
	 */
	public static function getJobsByStatusDateRangeForEvent($status, $beginDate, $endDate, $eventId = EventLog::JOB_PRINT){

		$criteria=new CDbCriteria;
		$criteria->alias = 'job';
		$criteria->select = array('job.ID', 'NAME', 'STATUS');
		//$critera->with = array('events');
		
		$criteria->join='JOIN event_log ON event_log.OBJECT_ID=job.ID';	
		$criteria->addCondition('event_log.EVENT_ID='. $eventId);
		$criteria->addCondition('event_log.TIMESTAMP>= STR_TO_DATE(\''. $beginDate .'\', \'%Y-%m-%d\')');
		$criteria->addCondition('event_log.TIMESTAMP< STR_TO_DATE(\''. $endDate .'\', \'%Y-%m-%d\')');
		$criteria->addInCondition('STATUS', $status);
		
		return Job::model()->with('events')->together()->findAll($criteria);

	}
	
	/**
	 * Loops through the jobs' events copying them into another array.
	 * If the eventID argument is not in that copy, create a new EventLog object -
	 * 	otherwise, set the event to the existing copy.
	 * @param integer $eventID
	 * @return EventLog
	 */
	protected function getEventModel($eventID){
		$eventsCopy = array();
		foreach($this->events as $event){
			$eventsCopy[(string) $event->EVENT_ID] = $event;
		}
		if(!isset($eventsCopy[(string) $eventID])){
			$event = new EventLog;
			$event->assocObject = $this;
			$event->EVENT_ID = $eventID; //TODO: need to make sure this is a valid event ID for a job (i.e. EventLog::JOB_DUE, EventLog::JOBPRINT, EventLog::JOBPICKUP)
			if($this->events === null){
				$this->events = array();
			}
			//$this->events[(string) $eventID] = $event;
			$eventsCopy[(string) $eventID] = $event;
		} else {
			$event = $eventsCopy[(string) $eventID];
		}
		$this->events = $eventsCopy;		
		return $event;
	}
	
	/** Fills this model's attributes and relations from an array of attributes.
	 * @param array $attributes The attribute array. This may contain values for
	 * all of the attributes as well as the "jobLines" relation, which should
	 * be the key to an array with sets of attributes of JobLine models.
	 */
	public function loadFromArray($attributes){
		$attributesInternal = $attributes;
		if(isset($attributesInternal['jobLines'])){
			$jobLines = $attributesInternal['jobLines'];
			unset($attributesInternal['jobLines']);
		} else {
			$jobLines = null;
		}
		foreach($attributesInternal as $name=>$value){
			$this->$name = $value;
		}
		if($jobLines){
			$keyedJobLines = array();
			foreach($this->jobLines as $line){
				$keyedJobLines[(string) $line->ID] = $line;
			}
			$newJobLines = array();
			foreach($jobLines as $i => $value){
				if(isset($jobLines[$i]) && is_array($jobLines[$i])){
					$lineID = $jobLines[$i]['ID'];
					if(isset($keyedJobLines[$lineID])){
						$line = $keyedJobLines[$lineID];
					} else {
						$line = new JobLine;
					}
					$line->loadFromArray($jobLines[$i]);
					if($line->PRODUCT_ID){ //can't have a line that isn't associated with a product
						$newJobLines[] = $line;
					}
				}
			}
			$this->jobLines = $newJobLines;
		}		
	}
	
	protected function beforeValidate(){

		//set print & due dates to more standard format that 
		//	will work with CDateTimeParser rather than the display format
		Yii::log('Due date formatted:  '.$this->formattedDueDate, CLogger::LEVEL_TRACE, 'application.models.job');
		Yii::log('Print date formatted :  '.$this->formattedPrintDate, CLogger::LEVEL_TRACE, 'application.models.job');
		
		Yii::log('Due date before:  '.$this->dueDate, CLogger::LEVEL_TRACE, 'application.models.job');
		Yii::log('Print date before :  '.$this->printDate, CLogger::LEVEL_TRACE, 'application.models.job');
		
		//TODO: should preserve null through strtotime here?
		$this->dueDate = date('Y-m-d', strtotime($this->dueDate));
		$this->printDate = date('Y-m-d', strtotime($this->printDate));
		
		Yii::log('job scenario :  '.$this->getScenario(), CLogger::LEVEL_TRACE, 'application.models.job');
		
		//TODO: only want to do this if we aren't in the update print date scenario
		if($this->isNewRecord === false && $this->printDate > $this->dueDate
				&& $this->getScenario() != "update_printDate"){
			$printEvent = $this->getEventModel(EventLog::JOB_PRINT);
			$printEvent->DATE = $this->dueDate;
		}
		
		Yii::log('Due date after:  '.$this->dueDate, CLogger::LEVEL_TRACE, 'application.models.job');
		Yii::log('Print date after:  '.$this->printDate, CLogger::LEVEL_TRACE, 'application.models.job');
		
		if(parent::beforeValidate()){
			if($this->STATUS == null){
				$this->STATUS = Job::CREATED;
			}
			$valid = true;
			foreach($this->jobLines as $line){
				$line->JOB_ID = $this->ID;
				$valid = $valid && $line->validate();
			}
			return $valid;
		} else {
			return false;
		}
	}
	
	protected function beforeSave(){
		if(parent::beforeSave()){
			//ensures that there is an event created for each event attribute. 
			foreach($this->eventAttributes() as $eventID){
				$this->getEventModel($eventID);
			}

			$dueDateEvent = $this->getEventModel(EventLog::JOB_DUE);
			$printEvent = $this->getEventModel(EventLog::JOB_PRINT);
			$pickUpDateEvent = $this->getEventModel(EventLog::JOB_PICKUP);

			Yii::log('Due date :  '.$dueDateEvent->DATE, CLogger::LEVEL_TRACE, 'application.models.job');
			
			if($this->isNewRecord){
				Yii::log('New Job Record ', CLogger::LEVEL_INFO, 'application.models.job');							
			}
			
			//Automatically assign Job printer as the event log assigned user
			$printEvent->USER_ASSIGNED = $dueDateEvent->USER_ASSIGNED = $this->PRINTER_ID;
			
			//Prep for comarission of stored and current due date
			
			//Usually the case of a new record; don't need to compare due dates as none exists, so bail
			$savedDueDateString = EventLog::model()->findByPk($dueDateEvent->getPrimaryKey())->DATE;
			if(!isset($savedDueDateString) || trim($savedDueDateString)==='') return true;
			
			//Convert due date back to a Date before comparison because of EventLog's afterFind() DateConverter behavior
			//If Due Date has changed, also change Print and Pickup Date to match it
			$savedDueDate =  date('Y-m-d', strtotime($savedDueDateString));
			if($dueDateEvent->DATE != $savedDueDate){
				//set Print Date to user-specified Due Date by default any time the dueDate changes
				$printEvent->DATE = $this->dueDate;
				Yii::log('Print date after :  '. $printEvent->DATE, CLogger::LEVEL_TRACE, 'application.models.job');
				
				//set Pickup Date to user-specified Due Date by default any time dueDate changes
				$pickUpDateEvent->DATE = $this->dueDate;
				Yii::log('Pickup date after :  '.$pickUpDateEvent->DATE, CLogger::LEVEL_TRACE, 'application.models.job');					
			}		
			return true;
		} else {
			return false;
		}
	}
	
	protected function afterSave(){
		parent::afterSave();
		if(isset($this->events)){
			foreach($this->events as $event){				
				$event->OBJECT_ID = $this->ID;				
				$event->save();
			}
		}
		if(isset($this->jobLines)){
			foreach($this->jobLines as $line){
				$line->JOB_ID = $this->ID;
				$line->save();
			}
		}
		if(isset($this->_additionalFees)){
			$deleter = Yii::app()->db->createCommand();
			$deleter->delete('job_fee', 'JOB_ID=:job_id', array(':job_id'=>$this->ID));
			$inserter = Yii::app()->db->createCommand();
			foreach($this->_additionalFees as $fee_id=>$value){
				$realValue = $value;
				if(is_array($realValue)){
					$realValue = $realValue['VALUE'];
				}
				$inserter->insert('job_fee', array('FEE_ID'=>$fee_id, 'JOB_ID'=>$this->ID, 'VALUE'=>$realValue));
			}
		}
	}
	
	public function getTotalPasses(){
		$passes = $this->printJob == null ? 0 : $this->printJob->PASS;
		$lines = 0; //for quantity of all lines
		foreach($this->jobLines as $line) {			
			$lines += $line->garmentCount;
		}
		return $passes * $lines;
	}
	
	public function getHasArt(){
		$result = false;
		if($this->printJob !== null){
			return $this->printJob->hasArt;
		}
		return $result;
	}
	
	//this was in the mockup, but I'm not quite sure what it's for!
	public function getHasSizes(){
		$result = true;
		if(count($this->jobLines) == 0){
			$result = false;
		} else {
			foreach($this->jobLines as $line){
				if($line->PRODUCT_ID){ //we don't really care about lines without products in this case
					$result = $result && $line->isApproved;
				}
			}
		}
		return $result; 
	}
	
	/**
	 * Gets the total cost (for the customer) of the job.
	 */
	public function getTotal(){
		$additionalFees = $this->additionalFees;
		$totalFee = 0;
		foreach($additionalFees as $fee){
			if($fee['CONSTRAINTS']['part'] !== false){
				$totalFee += $fee['VALUE'];
			}
		}		
		
		$garmentTotal = $this->garmentTotal;
		return $garmentTotal + $totalFee + $this->SET_UP_FEE + ($this->printJob == null ? 0 : $this->printJob->COST);		
	}
	
	/**
	 * Gets the total cost to the customer directly attributable to garments.
	 */
	public function getGarmentTotal(){
		$garmentTotal = 0;
		foreach($this->jobLines as $line){
			$garmentTotal += $line->total;
		}
		return $garmentTotal;
	}
	 /**
	   * Gets the total cost of goods sold.
	   * TODO: analize job lines beforehand & throw duplicate product error (when same product and color ?& price?)
	   */
   	public function getCostOfGoodsSold(){
       $costOfGoodsSold = 0;
       foreach($this->jobLines as $line){
               $costOfGoodsSold += $line->product->COST * $line->garmentCount;
       }
       return $costOfGoodsSold; 
  	}
	
	/**
	 * Gets the total auto-generated cost (for the customer) for each garment.
	 */
	public function getGarmentPrice(){
		$garments = $this->garmentCount;		
		return ($garments == 0 ? 0 : $this->total / $garments);
	}
	
	public function getGarmentCount(){
		$garments = 0;
		foreach($this->jobLines as $line){			
			$garments += $line->garmentCount;		
		}
		return $garments;
	}
	
	/**
	 * Gets the total auto-generated cost (for 8/7 central) for each garment. This
	 * is in contrast to getGarmentPrice() which retrieves the cost <i>for the customer</i>.
	 */
	public function getGarmentCost(){
		$garments = 0;
		foreach($this->jobLines as $line){
			$garments += $line->total;
		}
		return $garments;
	}
	
	/**
	 * Gets the score of the job. The score is essentially a time
	 * estimate, in minutes, of how long the job will take.
	 */
	public function getScore(){
		$base = 30; //Ben's request
		/*$passes = $this->printJob == null ? 0 : $this->printJob->PASS;
		$lines = 0; //for quantity of all lines
		foreach($this->jobLines as $line) {
			$lines += $line->QUANTITY;
		}*/
		return $base + $this->totalPasses;
	}
	
	/**
	 * Gets a value indicating whether or not all orders associated with the 
	 * job have been checked in.
	 */
	public function getIsCheckedIn(){
		$checkedIn = count($this->jobLines) > 0;
		foreach($this->jobLines as $line){
			$checkedIn = $checkedIn && $line->isCheckedIn;
		}
		return $checkedIn;
	}
	
	/**
	 * Gets a list of orders associated with this job.
	 */
	public function getOrders(){
		$orders = array();
		foreach($this->jobLines as $jobLine){
			$order = $jobLine->ORDER_LINE->ORDER;
			$orders[(string) $order->ID] = $order;
		}
		return $orders;
	}
	
	/**
	 * Creates a list of "additional" fees associated with a job. These are indexed
	 * by the fee ID (a Lookup key), and contain a "TEXT", "VALUE", and "CONSTRAINTS" field.
	 * @param array $values A map from fee ID to value used to set the value of the associated fee from the default.
	 * @return array The list.
	 */
	private function loadFees($values = false){
		$fees = Lookup::listItems('JobFeeType');
		$result = array();
		foreach($fees as $fee){
			$constraints = CJSON::decode($fee->EXTENDED);
			$result[(string) $fee->ID] = array(
				'TEXT'=>$fee->TEXT,
				'VALUE'=>isset($constraints['default']) ? $constraints['default'] : ($values ? $values[(string) $fee->ID] : 0),
				'CONSTRAINTS'=>$constraints,
			);
		}
		return $result;	
	}
	
	/**
	 * Gets the list of "additional" fees associated with a Job. These
	 * are indexed by the fee ID (a Lookup key), and contain a "TEXT" and "VALUE" field.
	 * There is also an additional "CONSTRAINTS" field which can be used to determine
	 * how to interpret the field.
	 */
	public function getAdditionalFees(){
		if(!$this->_additionalFees){
			if(!$this->isNewRecord){
				$command = Yii::app()->db->createCommand();
				$command->select('FEE_ID, VALUE')
					->from('job_fee')
					->where('JOB_ID=:job_id', array(':job_id'=>$this->ID));
				$existingFees = $command->queryAll();
				$values = array();
				foreach($existingFees as $additional){
					$values[(string) $additional['FEE_ID']] = $additional['VALUE'];
				}
				$this->_additionalFees = $this->loadFees($values);
			} else {
				$this->_additionalFees = $this->loadFees();	
			}			
		}
		return $this->_additionalFees;
	}
	
	/**
	 * Sets the list of additional fees. Fees should be indexed by Fee ID (a Lookup key), 
	 * and simply need to contain a numeric value.
	 */
	public function setAdditionalFees($value){
		$this->_additionalFees = $this->loadFees($value);
	}
}