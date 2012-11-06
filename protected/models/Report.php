<?php

/**
 * This is the model class for table "report".
 *
 * The followings are the available columns in table 'report':
 * @property integer $ID
 * @property integer $CUSTOMER_ID
 * @property integer $LEADER_ID
 * @property string $DESCRIPTION
 * @property string $NOTES
 * @property string $ISSUES
 * @property integer $RUSH
 * @property string $SET_UP_FEE
 * @property integer $SCORE
 * @property string $QUOTE
 *
 * The followings are the available model relations:
 * @property Customer $cUSTOMER
 * @property User $lEADER
 * @property reportLine[] $reportLines
 */
class report extends CActiveRecord
{
	//report statuses 
	const CREATED = 26; //the report has just been created, and perhaps a quote has been given
	const INVOICED = 31; //a formal invoice has been sent.
	const SCHEDULED = 28; //the report has been scheduled on the timeline.
	const COMPLETED = 29; //the report has been completed.
	const CANCELED = 30; //the report has been canceled.
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return report the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('RUSH, SCORE', 'numerical', 'integerOnly'=>true),
			array('ID, NAME, DESCRIPTION, NOTES, ISSUES', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, CUSTOMER_ID, LEADER_ID, NAME, DESCRIPTION, NOTES, ISSUES, RUSH, SET_UP_FEE, SCORE, QUOTE', 'safe', 'on'=>'search'),
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
			'reportLines' => array(self::HAS_MANY, 'reportLine', 'report_ID'),			
			'printreport' => array(self::BELONGS_TO, 'Printreport', 'PRINT_ID'),
			'events'=> array(self::HAS_MANY, 'EventLog', 'OBJECT_ID', 'condition'=>'OBJECT_TYPE = \'report\'', 'index'=>'EVENT_ID'),
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
			'formattedPickUpDate' =>'Pickup Date',
			'NAME'=>'Name',
		);
	}
	
	/**
	 * Gets an array mapping attribute names to event IDs.
	 * @return array The resultant array.
	 */
	protected function eventAttributes(){
		return array(
			'dueDate'=>EventLog::report_DUE,
			'printDate'=>EventLog::report_PRINT,
			'pickUpDate'=>EventLog::report_PICKUP,
		);
	}
	
	public function __get($name){
		$found = false;
		$originalName = $name;
		//first, determine if client code is requesting a "formatted" attribute
		if(($pos = strpos($name, 'formatted')) === 0){
			$formatted = true;
			$name = substr($name, 9); //9 is length of "formatted"
			$first = substr($name, 0, 1); //get first character
			$first = strtolower($first);
			$name = substr_replace($name, $first, 0, 1);
		} else {
			$formatted = false;
		}
		
		//then, if the (unformatted) attribute is an event attribute,
		//get the event value
		foreach($this->eventAttributes() as $attrName => $eventID){
			if(strcmp($name, $attrName) == 0){
				$event = $this->getEventModel($eventID);
				if($formatted){
					if($event->DATE !== null){
						$value = $event->DATE;
					} else {
						$value = null;
					}
				} else {
					$value = $event->DATE;
				}
				$found = true;
			}
		}
		
		//if we found it, return it, otherwise, return what the parent thinks 
		//is a matching attribute 
		if(!$found){
			return parent::__get($name);
		} else {
			return $value;
		}
	}
	
	public function __set($name, $value){
		$found = false;
		$originalName = $name;
		//first, determine if client code is requesting a "formatted" attribute
		if(strlen($name) > 9 && substr($name, 0, 9) === 'formatted'){
			$formatted = true;
			$name = substr($name, 9); //9 is length of "formatted"
			$first = substr($name, 0, 1); //get first character
			$first = strtolower($first);
			$name = substr_replace($name, $first, 0, 1);
		} else {
			$formatted = false;
		}
		
		//then, if the (unformatted) attribute is an event attribute,
		//set the event value
		foreach($this->eventAttributes() as $attrName => $eventID){
			if(strcmp($name, $attrName) == 0){
				$event = $this->getEventModel($eventID);
				if($formatted){
					$event->DATE = $value;
				} else {
					$event->DATE = $value;
				}
				$found = true;
			}
		}
		
		//if we found it, set it, otherwise, set what the parent thinks 
		//is a matching attribute 
		if(!$found){
			parent::__set($name, $value);
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ID',$this->ID);
		$criteria->compare('CUSTOMER_ID',$this->CUSTOMER_ID);
		$criteria->compare('LEADER_ID',$this->LEADER_ID);
		$criteria->compare('DESCRIPTION',$this->DESCRIPTION,true);
		$criteria->compare('NOTES',$this->NOTES,true);
		$criteria->compare('ISSUES',$this->ISSUES,true);
		$criteria->compare('RUSH',$this->RUSH);
		$criteria->compare('SET_UP_FEE',$this->SET_UP_FEE,true);
		$criteria->compare('SCORE',$this->SCORE);
		$criteria->compare('QUOTE',$this->QUOTE,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	protected function getEventModel($eventID){
		$events = array();
		foreach($this->events as $event){
			$events[(string) $event->EVENT_ID] = $event;
		}
		if(!isset($events[(string) $eventID])){
			$event = new EventLog;
			$event->assocObject = $this;
			$event->EVENT_ID = $eventID;
			if($this->events === null){
				$this->events = array();
			}
			//$this->events[(string) $eventID] = $event;
			$events[(string) $eventID] = $event;
		} else {
			$event = $events[(string) $eventID];
		}
		$this->events = $events;		
		return $event;
	}
	
	/** Fills this model's attributes and relations from an array of attributes.
	 * @param array $attributes The attribute array. This may contain values for
	 * all of the attributes as well as the "reportLines" relation, which should
	 * be the key to an array with sets of attributes of reportLine models.
	 */
	public function loadFromArray($attributes){
		$attributesInternal = $attributes;
		if(isset($attributesInternal['reportLines'])){
			$reportLines = $attributesInternal['reportLines'];
			unset($attributesInternal['reportLines']);
		} else {
			$reportLines = null;
		}
		foreach($attributesInternal as $name=>$value){
			$this->$name = $value;
		}
		if($reportLines){
			$keyedreportLines = array();
			foreach($this->reportLines as $line){
				$keyedreportLines[(string) $line->ID] = $line;
			}
			$newreportLines = array();
			for($i = 0; $i < count($reportLines); $i++){
				if(isset($reportLines[$i]) && is_array($reportLines[$i])){
					$lineID = $reportLines[$i]['ID'];
					if(isset($keyedreportLines[$lineID])){
						$line = $keyedreportLines[$lineID];
					} else {
						$line = new reportLine;
					}
					$line->attributes = $reportLines[$i];
					$newreportLines[] = $line;
				}
			}
			$this->reportLines = $newreportLines;
		}		
	}
	
	protected function beforeValidate(){
		if(parent::beforeValidate()){
			if($this->STATUS == null){
				$this->STATUS = report::CREATED;
			}
			$valid = true;
			foreach($this->reportLines as $line){
				$line->report_ID = $this->ID;
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
		if(isset($this->reportLines)){
			foreach($this->reportLines as $line){
				$line->report_ID = $this->ID;
				$line->save();
			}
		}
		
	}
	
	public function getTotalPasses(){
		return count($this->reportLines) * $this->printreport->PASS;
	}
	
	public function getHasArt(){
		$result = false;
		if($this->printreport !== null){
			if($this->printreport->ART != null){
				$result = true;
			}
		}
		return $result;
	}
	
	//this was in the mockup, but I'm not quite sure what it's for!
	public function getHasSizes(){
		$result = true;
		if(count($this->reportLines) == 0){
			$result = false;
		} else {
			foreach($this->reportLines as $line){
				$result = $result && $line->isApproved;
			}
		}
		return $result; 
	}
	
	/**
	 * Gets the total cost (for the customer) of the report.
	 */
	public function getTotal(){
		$front = 0;
		$back = 0;
		$sleeve = 0;
		if($this->printreport){
			$front = $this->printreport->FRONT_PASS;
			$back = $this->printreport->BACK_PASS;
			$sleeve = $this->printreport->SLEEVE_PASS;
		}
		
		$garmentTotal = CostCalculator::calculateTotal($this->garmentCount, $front, $back, $sleeve, 0);
		$garmentTotal += $this->garmentCost;
		return $garmentTotal + $this->SET_UP_FEE + ($this->printreport == null ? 0 : $this->printreport->COST);
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
		foreach($this->reportLines as $line){
			$garments += $line->QUANTITY;
		}
		return $garments;
	}
	
	/**
	 * Gets the total auto-generated cost (for 8/7 central) for each garment. This
	 * is in contrast to getGarmentPrice() which retrieves the cost <i>for the customer</i>.
	 */
	public function getGarmentCost(){
		$garments = 0;
		foreach($this->reportLines as $line){
			$garments += $line->QUANTITY * $line->product->COST;
		}
		return $garments;
	}
	
	/**
	 * Gets the score of the report. The score is essentially a time
	 * estimate, in minutes, of how long the report will take.
	 */
	public function getScore(){
		$base = 30; //Ben's request
		$passes = $this->printreport == null ? 0 : $this->printreport->PASS;
		$lines = 0; //for quantity of all lines
		foreach($this->reportLines as $line) {
			$lines += $line->QUANTITY;
		}
		return $base + $passes * $lines;
	}
	
	/**
	 * Gets a value indicating whether or not all orders associated with the 
	 * report have been checked in.
	 */
	public function getIsCheckedIn(){
		$checkedIn = count($this->reportLines) > 0;
		foreach($this->reportLines as $line){
			$checkedIn = $checkedIn && $line->isCheckedIn;
		}
		return $checkedIn;
	}
	
	/**
	 * Gets a list of orders associated with this report.
	 */
	public function getOrders(){
		$orders = array();
		foreach($this->reportLines as $reportLine){
			$order = $reportLine->ORDER_LINE->ORDER;
			$orders[(string) $order->ID] = $order;
		}
		return $orders;
	}
}