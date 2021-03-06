<?php
Yii::import('application.widgets.SalesReportWidget');

class JobController extends Controller
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
				'actions'=>array('newLine', 'garmentCost', 'estimate', 'invoice'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('invoice'),
				'users'=>array('@'),
				'expression'=>"Yii::app()->user->getState('isCustomer');",
			),
			array('allow',
				'actions'=>array('status', 'create', 'update', 'calendar', 'search', 'filter', 'searchResult'
					, 'deleteLine', 'approveLine', 'newLine', 'view', 'list', 'loadList', 'index', 'garmentCost', 'addArt', 'deleteArt', 'art'),
				'users'=>array('@'),
				'expression'=>"Yii::app()->user->getState('isDefaultRole');",
			),
			array('allow',
				'actions'=>array('status', 'create', 'update', 'search', 'filter', 'searchResult'
						, 'calendar', 'validatePrintDate', 'updatePrintDate'
						, 'setupFee', 'deleteLine', 'approveLine', 'newLine', 'view', 'list', 'loadList', 'index', 'garmentCost', 'addArt', 'deleteArt', 'art'),
				'users'=>array('@'),
				'expression'=>"Yii::app()->user->getState('isLead');",
			),
			array('allow',
				'users'=>array('@'),
				'expression'=>"Yii::app()->user->getState('isAdmin');",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionFilter(){
		if (Yii::app()->request->isAjaxRequest && isset($_GET['term']) && isset($_GET['status'])) {
			Yii::log('reached filter action AJAX', CLogger::LEVEL_TRACE, 'application.controllers.Job');

			$term = $_GET['term'];
			$status = $_GET['status'];
			$model = new Job('search');
			$model->unsetAttributes();
			
			$model->NAME = $term;
			$model->customer_search = $term;
					
			$this->renderPartial('_list', array(
            	'dataProvider' => $model->search($status, 7),
            	'statusId' => $status
        	));
		}	

	}
	
	public function actionSearch(){
		if (Yii::app()->request->isAjaxRequest && isset($_GET['term'])) {
			$term = $_GET['term'];
			$model = new Job('search');
			$model->unsetAttributes();
			
			$model->NAME = $term;
			$model->customer_search = $term;
					
			$results = $model->search();

			$juiResults = array();	
	
			foreach($results->data as $result){
				$company = $result->CUSTOMER->COMPANY;
				$clientName = $result->CUSTOMER->USER->FIRST .' '.$result->CUSTOMER->USER->LAST;

				$client = strlen($company) > 0 ? 
							strlen($clientName) > 0 ? $company.' ('.$clientName.')': $company
								:  $clientName;

				$label = strlen($client) > 0 ? 'Client: '.$client . ' - Job: ' . $result->NAME : 'Job: '.$result->NAME;
				$juiResults[] = array(
						'label'=> $label,
						'value'=>$result->NAME,
						'id'=>$result->ID,
				);
			}
			header('Content-Type: text/json');
			echo CJSON::encode($juiResults);
		}
	}
	
	
	/**
	 * Displays a particular model. 
	 * Alternative view action for scenarios where Job ID dynamically determined via javascript (or any means).
	 * 
	 */
	public function actionSearchResult()
	{
		echo Yii::log('reached search result action', CLogger::LEVEL_TRACE, 'application.controllers.Job');
		if(isset($_GET['id']) ){			
			echo Yii::log('reached search result action - had id', CLogger::LEVEL_TRACE, 'application.controllers.Job');
			$this->redirect(array('update','id'=>$_GET['id']));
		}
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		echo Yii::log('reached view action', CLogger::LEVEL_INFO, 'application.controllers.Job');
		$model = $this->loadModel($id);
		$sizes = Lookup::listItems('Size');
		$lineData = array();
		$products = array();
		$groupedLines = array();
		foreach($model->jobLines as $line){
			echo Yii::log(count($model->jobLines).' numberOfJobLines', CLogger::LEVEL_INFO, 'application.controllers.Job');
			$groupedSizes = array();
			foreach($line->sizes as $sizeLine){
				$groupedSizes[(string) $sizeLine->SIZE] = $sizeLine;
			}
			echo Yii::log(count($groupedSizes).' numberOfSizeLines', CLogger::LEVEL_INFO, 'application.controllers.Job');
			$groupedLines[(string) $line->product->vendorStyle][(string) $line->PRODUCT_COLOR] = array('line'=>$line, 'sizes'=>$groupedSizes);
		}
		
		foreach($groupedLines as $style=>$styleGroup){			
			if($style){
				foreach($styleGroup as $color=>$colorGroup){
					$productSizes = array();
					$approved = false;
					$line = $colorGroup['line'];
					foreach($sizes as $size){ //iterating through sizes because we want ALL of them
						if(isset($colorGroup['sizes'][(string) $size->ID])){							
							$sizeLine = $colorGroup['sizes'][(string) $size->ID];
							$productLine = $sizeLine->productLine;							
						} else {
							$sizeLine = new JobLineSize;
							$sizeLine->SIZE = $size->ID;
							$productLine = new ProductLine;
							$productLine->PRODUCT_ID = $line->product->ID;
							$productLine->COLOR = $line->PRODUCT_COLOR;
							$productLine->SIZE = $size->ID;
						}						
						$productSizes[] = array(
							'productLine' => $productLine,
							'line'=>$sizeLine,
						);
							
					}
					
					if(count($productSizes) > 0){
						$latestProduct = $line->product;
						$products['model'] = $line;
						$products['lines'] = $productSizes;
						$products['style'] = $latestProduct->vendorStyle;
						$products['availableColors'] = CHtml::listData($latestProduct->allowedColors, 'ID', 'TEXT');
						$products['product'] = CJSON::encode($latestProduct);
						$products['sizes'] = CJSON::encode($latestProduct->allowedSizes);
						$products['currentColor'] = $line->PRODUCT_COLOR;
						$products['approved'] = $line->isApproved;
						$products['saved'] = !($line->isNewRecord); //we're guaranteed that some of the lines in this group are persistent
						$lineData[] = $products;
						$products = array();
					}
				}
			}
		}
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'customer'=>$model->CUSTOMER,
			'print'=>$model->printJob,
			'lineData'=>$lineData,
			'artLink'=>null,
			'mockupLink'=>null,
			'formatter'=>new Formatter,
		));
	}
	
	/**
	 * If parameter s is e, the multiEstimate should be returned, otherwise returned the multiForm
	 */
	public function actionNewLine($s='f'){
		$namePrefix = $_POST['namePrefix'];
		$count = $_POST['count'];
		
		$sizes = Lookup::listItems('Size');
		$products = array();	
		foreach($sizes as $size){
			$sizeLine = new JobLineSize;
			$sizeLine->SIZE = $size->ID;
			$product = new ProductLine;
			$product->SIZE = $size->ID;
			$products[] = array(
				'productLine'=>$product,
				'line'=>$sizeLine,
			);	
		}
		
		if($s == 'e'){
			$view = '//jobLine/_multiEstimate';
		} else {
			$view = '//jobLine/_multiForm';			
		}
		
		//per request of Ben, will be including three "standard" styles under radio buttons. [M.T.]
		$standardStyles = array(
			Product::STANDARD=>'Standard',
			Product::DELUXE=>'Deluxe',
			Product::ECONOMY=>'Economy',
			''=>'Custom',
		);	
		
		$products['model'] = new JobLine;
		$products['lines'] = $products;
		$products['standardStyles'] = $standardStyles;
		$products['style'] = '';
		$products['availableColors'] = array();
		$products['currentColor'] = null;
		$products['approved'] = false;
		$products['saved'] = false;
		$products['product'] = null;
		$products['sizes'] = '{}';
		
		Yii::log('newLine count ' . $count, CLogger::LEVEL_TRACE, 'application.controllers.job');
		$this->renderPartial($view, array(
			
			'namePrefix'=>$namePrefix,
			'startIndex'=>$count,
			'products'=>$products,
			'formatter'=>new Formatter,
			'estimate'=>0,
		));
	}
	
	public function actionApproveLine(){
		$namePrefix = $_POST['namePrefix'];
		$startIndex = $_POST['startIndex'];
		$idList = $_POST['idList'];
		$models = JobLine::model()->findAllByPk($idList);
		$job = null;
		$sizes = Lookup::model()->findAllByAttributes(array('TYPE'=>'Size'));
		$products = array();
		$groupedLines = array();
		foreach($models as $model){			
			if($model){
				$job = $model->job;
				$model->approve();
				$groupedSizes = array();
				foreach($model->sizes as $sizeLine){
					$groupedSizes[(string) $sizeLine->SIZE] = $sizeLine;
				}
				$groupedLines[(string) $model->product->vendorStyle][(string) $model->PRODUCT_COLOR] = array('line'=>$model, 'sizes'=>$groupedSizes);
			}
		}
		
		foreach($groupedLines as $style=>$styleGroup){
			if($style){
				foreach($styleGroup as $color=>$colorGroup){
					$approved = false;
					$line = $colorGroup['line'];
					foreach($sizes as $size){ //iterating through sizes because we want ALL of them
						if(isset($colorGroup['sizes'][(string) $size->ID])){							
							$sizeLine = $colorGroup['sizes'][(string) $size->ID];
							$productLine = $sizeLine->productLine;							
						} else {
							$sizeLine = new JobLineSize;
							$sizeLine->SIZE = $size->ID;
							$productLine = new ProductLine;
							$productLine->PRODUCT_ID = $line->product->ID;
							$productLine->COLOR = $line->PRODUCT_COLOR;
							$productLine->SIZE = $size->ID;
						}						
						$products[] = array(
							'productLine' => $productLine,
							'line'=>$sizeLine,
						);
					}
					if(count($products) > 0){
						$latestProduct = $line->product;
						$products['model'] = $line;
						$products['lines'] = $products;
						$products['style'] = $latestProduct->vendorStyle;
						$products['availableColors'] = CHtml::listData($latestProduct->allowedColors, 'ID', 'TEXT');
						$products['product'] = CJSON::encode($latestProduct);
						$products['sizes'] = CJSON::encode($latestProduct->allowedSizes);
						$products['currentColor'] = $line->PRODUCT_COLOR;
						$products['approved'] = $line->isApproved;
						$products['saved'] = !($line->isNewRecord); //we're guaranteed that some of the lines in this group are persistent
					}
				}
			}
		}
		
		if($approved && !Yii::app()->user->getState('isAdmin')){
			$view = '//jobLine/_multiView';
		} else {
			$view = '//jobLine/_multiForm';
		}
		
		$this->renderPartial($view, array(
			'namePrefix'=>$namePrefix,
			'startIndex'=>$startIndex,
			'products'=>$products,
			'formatter'=>new Formatter,
			'estimate'=>CostCalculator::calculateTotal($job->garmentCount, $job->printJob->FRONT_PASS, $job->printJob->BACK_PASS, $job->printJob->SLEEVE_PASS, 0),
		));
	}
	
	public function actionUnapproveLine(){
		$namePrefix = $_POST['namePrefix'];
		$startIndex = $_POST['startIndex'];
		$idList = $_POST['idList'];
		$models = JobLine::model()->findAllByPk($idList);
		$job = null;
		$sizes = Lookup::model()->findAllByAttributes(array('TYPE'=>'Size'));
		$products = array();
		$groupedLines = array();
		foreach($models as $model){			
			if($model){
				$job = $model->job;
				$model->unapprove();
				$groupedSizes = array();
				foreach($model->sizes as $sizeLine){
					$groupedSizes[(string) $sizeLine->SIZE] = $sizeLine;
				}
				$groupedLines[(string) $model->product->vendorStyle][(string) $model->PRODUCT_COLOR] = array('line'=>$model, 'sizes'=>$groupedSizes);
			}
		}
		
		foreach($groupedLines as $style=>$styleGroup){
			if($style){
				foreach($styleGroup as $color=>$colorGroup){
					$approved = false;
					$line = $colorGroup['line'];
					foreach($sizes as $size){ //iterating through sizes because we want ALL of them
						if(isset($colorGroup['sizes'][(string) $size->ID])){							
							$sizeLine = $colorGroup['sizes'][(string) $size->ID];
							$productLine = $sizeLine->productLine;							
						} else {
							$sizeLine = new JobLineSize;
							$sizeLine->SIZE = $size->ID;
							$productLine = new ProductLine;
							$productLine->PRODUCT_ID = $line->product->ID;
							$productLine->COLOR = $line->PRODUCT_COLOR;
							$productLine->SIZE = $size->ID;
						}						
						$products[] = array(
							'productLine' => $productLine,
							'line'=>$sizeLine,
						);
					}
					if(count($products) > 0){
						$latestProduct = $line->product;
						$products['model'] = $line;
						$products['lines'] = $products;
						$products['style'] = $latestProduct->vendorStyle;
						$products['availableColors'] = CHtml::listData($latestProduct->allowedColors, 'ID', 'TEXT');
						$products['product'] = CJSON::encode($latestProduct);
						$products['sizes'] = CJSON::encode($latestProduct->allowedSizes);
						$products['currentColor'] = $line->PRODUCT_COLOR;
						$products['approved'] = $line->isApproved;
						$products['saved'] = !($line->isNewRecord); //we're guaranteed that some of the lines in this group are persistent
					}
				}
			}
		}
		
		$view = '//jobLine/_multiForm';
		
		$this->renderPartial($view, array(
			'namePrefix'=>$namePrefix,
			'startIndex'=>$startIndex,
			'products'=>$products,
			'formatter'=>new Formatter,
			'estimate'=>CostCalculator::calculateTotal($job->garmentCount, $job->printJob->FRONT_PASS, $job->printJob->BACK_PASS, $job->printJob->SLEEVE_PASS, 0),
		));
	}
	
	public function actionDeleteLine(){
		$model = JobLine::model()->findByPk((int) $_POST['id']);
		if($model){
			if(!$model->delete()){
				throw new CException('Could not delete the job line.');
			}
		}
	}
	
	public function actionGarmentCost($garments, $front, $back, $sleeve){
		$result = array('result'=>CostCalculator::calculateTotal($garments, $front, $back, $sleeve, 0));
		echo CJSON::encode($result);
	}
	
	public function actionSetupFee($garments){
		$result = array('result'=>CostCalculator::calculateSetupFee($garments));
		echo CJSON::encode($result);
	}
	
	/**
	 * Allows unauthenticated users to estimate the total cost of an order.
	 * Essentially a copy of action create, but without any handling of persistence.
	 * */
	public function actionEstimate(){
		$model=new Job;
		$sizes = Lookup::model()->findAllByAttributes(array('TYPE'=>'Size'));
		$passes = array(0, 1, 2, 3, 4, 5, 6); //as instructed by Ben, number of passes
		//should be limited to a few numbers.
		$print = new PrintJob;
		
		//per request of Ben, will be including three "standard" styles under radio buttons.
		$standardStyles = array(
			Product::STANDARD=>'Standard',
			Product::DELUXE=>'Deluxe',
			Product::ECONOMY=>'Economy',
			''=>'Custom',
		);
		
		$lineData = array();
		$products = array();	
		foreach($sizes as $size){
			$sizeLine = new JobLineSize;
			$sizeLine->SIZE = $size->ID;
			$product = new ProductLine;
			$product->SIZE = $size->ID;
			$products[] = array(
				'productLine'=>$product,
				'line'=>$sizeLine,
			);	
		}
				
		$products['model'] = new JobLine;
		$products['lines'] = $products;
		$products['style'] = '';
		$products['standardStyles'] = $standardStyles;
		$products['availableColors'] = array();
		$products['currentColor'] = null;
		$products['approved'] = false;
		$products['saved'] = false;
		$products['product'] = null;
		$products['sizes'] = array();
		$lineData[] = $products;
		
		$this->render('estimate',array(
			'model'=>$model,
			'print'=>$print,
			'passes'=>$passes,
			'lineData'=>$lineData,
		));
	}
	
	/**
	 * Views the invoice for the job with the given ID.
	 */
	public function actionInvoice($id, $type="view"){
		$model = $this->loadModel($id);
		$formatter = new Formatter;
		$view = 'invoice';
		switch($type){
			case "iif" : $view = "invoice_quicken"; break;
			default : $view = "invoice"; break;
		}
		$params = array('model'=>$model, 'formatter'=>$formatter);
		if($type == "iif"){
			$view = 'invoice_quicken';
			$name = $model->ID . '_invoice.iif';
			$mime = 'text/iif';
			$model->attachBehavior('quickbooks', 'application.behaviors.Job.QBInitializer');						
			Yii::app()->request->sendFile($name, $this->renderPartial($view, $params, true), $mime);	
		} else {
			$this->render($view, $params);
		}		
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'update' page.
	 */
	public function actionCreate()
	{
		$model= new Job();
		
		//on job create, default the due date to today
		$model->formattedDueDate = isset($model->formattedDueDate) ? $model->formattedDueDate : date('l, M j, Y');
		
		//If the logged-in user has the role of 'Lead' 
		// select that user as the default Leader choice
		if(Yii::app()->user->getState('isLead')){
			$model->LEADER_ID = Yii::app()->user->id;
		}
		
		$customer = new Customer;
		$existingCustomers = Customer::model()->findAll();
		$leaders = User::listUsersWithRole(User::LEAD_ROLE);
		$printers = User::listUsersWithRole(User::DEFAULT_ROLE);
		//$sizes = Lookup::model()->findAllByAttributes(array('TYPE'=>'Size'));
		$sizes = Lookup::listItems('Size');
		$colors = Lookup::model()->findAllByAttributes(array('TYPE'=>'Color'));
		$passes = array(0, 1, 2, 3, 4, 5, 6); //as instructed by Ben, number of passes
		//should be limited to a few numbers.
		$print = new PrintJob;
		
		$lineData = array();
		$products = array();
		$productSizes = array();	
		foreach($sizes as $size){
			$sizeLine = new JobLineSize;
			$sizeLine->SIZE = $size->ID;
			$product = new ProductLine;
			$product->SIZE = $size->ID;
			$productSizes[] = array(
				'productLine'=>$product,
				'line'=>$sizeLine,
			);	
		}
				
		$products['model'] = new JobLine;
		$products['lines'] = $productSizes;
		$products['style'] = '';
		$products['availableColors'] = array();
		$products['currentColor'] = null;
		$products['approved'] = false;
		$products['saved'] = false;
		$products['product'] = null;
		$products['sizes'] = '{}';
		$lineData[] = $products;
		
		/*
		 * Now that I've totally forgotten the format, I think it's time to 
		 * document what the format of the "lineData" array is. The parent array,
		 * "lineData" is a list of lists. For each combination of style and color,
		 * there is a list in "lineData". Each child list is composed of children 
		 * with two elements: a "product" element of type Product which
		 * has its "SIZE" property set to the corresponding size from the DB, and
		 * a "line" element of type JobLine which represents the job line itself.
		 * 
		 * Every list in "lineData" should be grouped by color.
		 * 
		 * New change: each item of lineData is now a triplet of "lines", "style", "currentColor", and 
		 * "availableColors". "lines" contains what was originally the item of lineData,
		 * "style" contains text describing the selected vendor style, "availableColors"
		 * contains the colors available for the selected vendor style (if any), already processed
		 * with CHtml::listData, and "currentColor" contains the ID of the color for the group.
		 * "approved" is true if the set of lines has been approved, otherwise false.
		 * 
		 * Another new change: lineData will now contain a "sizes" and a "products" array, which will
		 * be filled with json-encoded calls to Product::getProducts(<item ID>) and getAllowedSizes(<item ID>)*/

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Job']))
		{
			$model->loadFromArray($_POST['Job']);
			$customerWasNew = true;
			if(isset($_POST['Customer']['ID']) && $_POST['Customer']['ID'] != null){
				$customer = Customer::model()->findByPk((int) $_POST['Customer']['ID']);
				$customerWasNew = false;
			} else {
				unset($_POST['Customer']['ID']);
			}
			unset($_POST['Customer']['summary']);
			$customer->attributes = $_POST['Customer'];
			
			if(isset($_FILES['PrintJob'])){
				$print->loadFromArray($_POST['PrintJob'], $_FILES);
			} else {
				$print->loadFromArray($_POST['PrintJob'], array());
			}
			
			
			$saved = true;
			if($saved){
				$saved = $saved && $print->save();
			} 
			if($saved) {
				$saved = $saved && $customer->save();
			}
			if($saved){
				$model->CUSTOMER_ID = $customer->ID;
				$model->PRINT_ID = $print->ID;
				$model->printDate = $model->dueDate;
				$saved = $saved && $model->save();
			}
			if($saved){
				//if saved, redirect
				Yii::app()->user->setFlash('success', 'Success! New job created.');
				$this->redirect(array('update', 'id'=>$model->ID));
			} else {
				//otherwise, delete everything
				if(!$model->isNewRecord) {$model->delete();}
				if(!$customer->isNewRecord && $customerWasNew) {$customer->delete();}
				if(!$print->isNewRecord) {$print->delete();}				
			}
		}	
		

		$this->render('create',array(
			'model'=>$model,
			'customerList'=>$existingCustomers,
			'newCustomer'=>$customer,
			'print'=>$print,
			'leaders'=>$leaders,
			'printers'=>$printers,
			'colors'=>$colors,
			'sizes'=>$sizes,
			'passes'=>$passes,
			'lineData'=>$lineData,
			'fileTypes'=>Lookup::listItems('ArtFileType'),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'update' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$customer = $model->CUSTOMER;
		$print = $model->printJob;
		$existingCustomers = Customer::model()->findAll();
		$leaders = User::listUsersWithRole(User::LEAD_ROLE);
		$printers = User::listUsersWithRole(User::DEFAULT_ROLE);
		$sizes = Lookup::model()->findAllByAttributes(array('TYPE'=>'Size'));
		$colors = Lookup::model()->findAllByAttributes(array('TYPE'=>'Color'));
		//Yii::log('color count : '.sizeof($colors) CLogger::LEVEL_TRACE, 'application.controllers.job');
		$passes = array(0, 1, 2, 3, 4, 5, 6); //as instructed by Ben, number of passes
		//should be limited to a few numbers.
		
		$lineData = array();
		$products = array();
		$groupedLines = array();
		
		foreach($model->jobLines as $line){
			$groupedSizes = array();
			foreach($line->sizes as $sizeLine){
				$groupedSizes[(string) $sizeLine->SIZE] = $sizeLine;
			}
			$groupedLines[(string) $line->product->vendorStyle][(string) $line->PRODUCT_COLOR] = array('line'=>$line, 'sizes'=>$groupedSizes);
		}
		
		foreach($groupedLines as $style=>$styleGroup){
			if($style){
				foreach($styleGroup as $color=>$colorGroup){
					$approved = false;
					$line = $colorGroup['line'];
					foreach($sizes as $size){ //iterating through sizes because we want ALL of them	
						$sizeLine = isset($colorGroup['sizes'][(string) $size->ID]) ? $colorGroup['sizes'][(string) $size->ID] : NULL;
						if(isset($sizeLine) && isset($sizeLine->productLine)){						
							$productLine = $sizeLine->productLine;							
						} else {
							$sizeLine = new JobLineSize;
							$sizeLine->SIZE = $size->ID;
							$productLine = new ProductLine;
							$productLine->PRODUCT_ID = $line->product->ID;
							$productLine->COLOR = $line->PRODUCT_COLOR;
							$productLine->SIZE = $size->ID;
						}						
						$products[] = array(
							'productLine'=>$productLine,
							'line'=>$sizeLine,
						);
					}
					if(count($products) > 0){
						$latestProduct = $line->product;
						$products['model'] = $line;
						$products['lines'] = $products;
						$products['style'] = $latestProduct->vendorStyle;
						$products['availableColors'] = CHtml::listData($latestProduct->allowedColors, 'ID', 'TEXT');
						$products['product'] = CJSON::encode($latestProduct);
						$products['sizes'] = CJSON::encode($latestProduct->allowedSizes);
						$products['currentColor'] = $line->PRODUCT_COLOR;
						$products['approved'] = $line->isApproved;
						$products['saved'] = !($line->isNewRecord); //we're guaranteed that some of the lines in this group are persistent
						$lineData[] = $products;
						$products = array();
					}
				}
			}
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Job']))
		{
			$model->loadFromArray($_POST['Job']);

			if(isset($_POST['Customer']['ID'])){
				$customer = Customer::model()->findByPk((int) $_POST['Customer']['ID']);
			} else {
				unset($_POST['Customer']['ID']);
			}
			unset($_POST['Customer']['summary']);
			$customer->attributes = $_POST['Customer'];
			$print->loadFromArray($_POST['PrintJob'], $_FILES || array());
			
			$saved = true;
			if($saved){
				$saved = $saved && $print->save();
			} 
			if($saved) {
				$saved = $saved && $customer->save();
			}
			if($saved){
				$model->CUSTOMER_ID = $customer->ID;
				$model->PRINT_ID = $print->ID;
				$saved = $saved && $model->save();
			}
			if($saved){
				if(!Yii::app()->request->isAjaxRequest)
				{
					Yii::app()->user->setFlash('success', 'Success! Job changes saved.');
					$this->redirect(array('update', 'id'=>$model->ID));
				}else{
					header('Content-type: application/json');		
					echo CJSON::encode(array('SAVED'=>true, 'MESSAGE'=>'Success! Job changes saved.'));					
					Yii::app()->end();
					return;
				}
			}
		}else{
			Yii::log('Invalid job POST attempt', CLogger::LEVEL_INFO, 'application.controllers.job');
		}

		$this->render('update',array(
			'model'=>$model,
			'customerList'=>$existingCustomers,
			'newCustomer'=>$customer,
			'print'=>$print,
			'leaders'=>$leaders,
			'printers'=>$printers,
			'colors'=>$colors,
			'sizes'=>$sizes,
			'passes'=>$passes,
			'lineData'=>$lineData,
			'fileTypes'=>Lookup::listItems('ArtFileType'),
		));
		
	}
	
	/**
	 * Lets the user download the art associated with a job.
	 * @param integer $art_id the ID of the print art to download
	 * @param integer $id the ID of the job associated with the art
	 * @return an image file via browser downloads
	 */
	public function actionArt($art_id, $id){
		$model = PrintArt::model()->findByPk((int) $art_id);
		if($model){
			$file = $model->FILE;
			if($file){
				$name = basename($file);
				//code below obtained from http://iamcam.wordpress.com/2007/03/20/clean-file-names-using-php-preg_replace/
				$replace="_";
				$pattern="/([[:alnum:]_\.-]*)/";
				$name=str_replace(str_split(preg_replace($pattern,$replace,$name)),$replace,$name);
				//end snippet

				Yii::log('art file looked for at ' . $file, CLogger::LEVEL_INFO, 'application.controllers.job');
				
				//TODO: after file names are corrected in database, switch to this commented out lookup
				//if(($fileContents = @file_get_contents(Yii::app()->basePath . $file)) === false)
				if(($fileContents = @file_get_contents($file)) === false)
				{
					$e = error_get_last();
					
					Yii::log('error get last : ' . print_r(error_get_last()), CLogger::LEVEL_ERROR, 'application.controllers.job');
					Yii::log('error exception caught ', CLogger::LEVEL_ERROR, 'application.controllers.job');
					Yii::log('Request to download print art file ' .$file . ' failed with the following exception : '. $e['message'] . 'code : ' . $e['code'], CLogger::LEVEL_ERROR, 'application.controllers.job');
					
					Yii::log('flash error set ', CLogger::LEVEL_INFO, 'application.controllers.job');
					Yii::app()->user->setFlash('failure','Failed to download file. File may be missing or blank.');
					
					//TODO: check that request was ajax ?
					$this->redirect(array('view', 'id' => $id)); //could also get job id from Yii::app()->request->getUrlReferrer()
				} 
				/* catch(ErrorException $e){
					Yii::log('error exception caught ', CLogger::LEVEL_ERROR, 'application.controllers.job');
					Yii::log('Request to download print art file ' .$file . ' failed with the following exception : '. $e->getMessage() . 'code : ' . $e->getCode(), CLogger::LEVEL_ERROR, 'application.controllers.job');					
				}catch(CException $e){
					Yii::log('unexpected exception caught ', CLogger::LEVEL_ERROR, 'application.controllers.job');
					Yii::log('Request to download print art file ' .$file . ' failed with the following exception : '. $e, CLogger::LEVEL_ERROR, 'application.controllers.job');					
				} */
			}
		}

		Yii::log('send file attempt ', CLogger::LEVEL_INFO, 'application.controllers.job');
		Yii::app()->request->sendFile($name, $fileContents);
	}
	
	/**
	 * Adds an art record. This will not create any files, but will simply return
	 * a new form section to be used on the job entry form.
	 */
	public function actionAddArt($namePrefix, $fileCount, $fileType, $print_id = null){
		$this->renderPartial('//print/_artForm', array(
			'model'=>new PrintArt,
			'print_id'=>$print_id,
			'fileType'=>$fileType,
			'namePrefix'=>$namePrefix . '['.++$fileCount.']',
			'fileCount'=>$fileCount,
			'artLink'=>null,
		));
	}
	
	/**
	 * Deletes an art record. This will delete any associated file, as well as the
	 * record in the database.
	 * @param int $id The identifier of the art file record. 
	 */
	public function actionDeleteArt(){
		if(Yii::app()->request->isPostRequest){
			$model = PrintArt::model()->findByPk((int) $_POST['id']);
			if($model){
				$model->delete();
			}
		} else {
			throw new CHttpException('403', 'Not authorized');
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models related to current user.
	 */
	public function actionIndex()
	{
		$this->render('dashboard',array(
			'dataProvider'=>$this->findLedJobsCurrentUser()
		));
	}
	
	/**
	 * Lists all models data for calendar widget.
	 */
	public function actionCalendar()
	{
		$calendarData = array();
		for($i = 0; $i < 4; $i++){
			$calendarData[] = $this->getCalendarWeek($i);
		}
		$this->render('calendar',array(
				'calendarData'=>$calendarData,
		));
	}
	
	/**
	 * Formats a week of jobs for a calendar widget. If no jobs found,
	 * the data returned contains today's date and an empty array.
	 * @param int $weekOffset The number of weeks from the current week to find in the schedule.
	 */
	private function getCalendarWeek($weekOffset = 0){
		$jobsInWeek = $this->findLedJobsForWeekAll($weekOffset);
	
		$jobsInWeek = $this->resultToCalendarData($jobsInWeek);
		if(count($jobsInWeek) == 0){
			$jobsInWeek[date('l')] = array(
					'jobs'=>array(),
					'date'=>time() + $weekOffset * GlobalConstants::SECONDS_IN_WEEK,
			);
		}
		return $jobsInWeek;
	}
	
	/**
	 * Gets the list of jobs for an entire week, with a week offset of zero meaning
	 * that the jobs for the current week (sunday - saturday) should be retreived,
	 * an offset of one meaning the next week, and so on.
	 * @param int $weekOffset The number of weeks from the current week to find in the schedule.
	 */
	private function findLedJobsForWeekAll($weekOffset = 0){	
		$lastSunday = strtotime('last sunday', time());
		$nextSaturday = $lastSunday + GlobalConstants::SECONDS_IN_WEEK - 1;
	
		$lastSunday += $weekOffset * GlobalConstants::SECONDS_IN_WEEK;
		$nextSaturday += $weekOffset * GlobalConstants::SECONDS_IN_WEEK;
	
		$criteria = new CDbCriteria;
		$criteria->addCondition('LEADER_ID IS NOT NULL');
		$criteria->addCondition('STATUS <> '.Job::COMPLETED.' AND STATUS <> '.Job::CANCELED);
		$criteria->join = 'INNER JOIN `event_log` ON `event_log`.`OBJECT_ID` = `t`.`ID`';
		$criteria->addCondition('`event_log`.`OBJECT_TYPE`=\'Job\'');
		$criteria->addCondition('`event_log`.`EVENT_ID`='.EventLog::JOB_PRINT);
		$criteria->addCondition('`event_log`.`DATE` BETWEEN FROM_UNIXTIME(' . $lastSunday . ') AND FROM_UNIXTIME(' . $nextSaturday . ')');
	
		return Job::model()->findAll($criteria);
	}

	private function findLedJobsCurrentUser(){			
		//TODO: if no leader role, just return an info message

		$criteria=new CDbCriteria;
		$criteria->compare('LEADER_ID', Yii::app()->user->id, false, 'AND');
		$criteria->addCondition('STATUS <> '.Job::COMPLETED.' AND STATUS <> '.Job::CANCELED);
		$criteria->join = 'INNER JOIN `event_log` ON `event_log`.`OBJECT_ID` = `t`.`ID`';
		$criteria->addCondition('`event_log`.`OBJECT_TYPE`=\'Job\'');
		$criteria->addCondition('`event_log`.`EVENT_ID`='.EventLog::JOB_DUE);
		$criteria->order ='`DATE`ASC';
		return new CActiveDataProvider('Job', array(
			'criteria'=>$criteria,
			'pagination'=>array(
						'pageSize'=>8,
				),
		));
	}
	
	private function resultToCalendarData($jobs){
		$calendarData = array();
		foreach($jobs as $job){
			$eventDate = strtotime($job->printDate);
			$dayName = date('l', $eventDate);
			$calendarData[$dayName]['date'] = $eventDate;
			$calendarData[$dayName]['jobs'][] = $job;
		}
		return $calendarData;
	}
	
	public function actionList(){		
		$this->render('list');
	}
	
	public function actionStatus(){
		$newStatus = $_POST['status'];
		$id = $_POST['id'];
		$model = $this->loadModel($id);		
		$model->STATUS = $newStatus;
		
		if($model->save()){
			$sales = SalesReportWidget::getSales();
			$costOfGoodsSoldPercentage = SalesReportWidget::getCostOfGoodsSoldPercentage();
			echo CJSON::encode(array('sales'=> Yii::app()->numberFormatter->formatCurrency($sales, 'USD')
									,'cogsPercentage'=>Yii::app()->numberFormatter->formatPercentage($costOfGoodsSoldPercentage, 'USD')
							));
			
		}else{
			Yii::app()->user->setFlash('failure','Something when wrong updating job status.');
			echo Yii::app()->user->getFlash('failure');
		}
	}
	
	public function actionUpdatePrintDate(){		
		if(Yii::app()->request->isAjaxRequest)
		{
			foreach (Yii::app()->log->routes as $route) {
				if($route instanceof CWebLogRoute) {
					$route->enabled = false; // disable any weblogroutes
				}
			}
		
			if(isset($_POST['id']) && isset($_POST['newPrintDate'])){
			
				$id = $_POST['id'];
				$model = $this->loadModel($id);
				$newPrintDate = $_POST['newPrintDate'];
				$model->printDate = date('Y-m-d', $newPrintDate);
				
				$model->setScenario("update_printDate");
				header('Content-type: application/json');
				
				if($model->save()){
					Yii::trace('new print date saved', 'application.controllers.job');
					//$this->renderJSON(array("SAVED"=>true));				
					echo CJSON::encode(array('SAVED'=>true));
					
					Yii::app()->end();
					return;
				}else{
					Yii::trace('new print date not saved', 'application.controllers.job');
					//$this->renderJSON(array("SAVED"=>false));
					echo CJSON::encode(array('SAVED'=>false
											,'DUEDATE'=>$model->formattedDueDate
							));
					
					foreach (Yii::app()->log->routes as $route) {
						if($route instanceof CWebLogRoute) {
							$route->enabled = false; // disable any weblogroutes
						}
					}
					Yii::app()->end();
				}
			}
		}
	}
	
	/*
	* A valid print date cannot be after the set due date. 
	*/
	public function actionValidatePrintDate(){
		if(isset($_GET['id']) && isset($_GET['newPrintDate'])){
			$id = $_GET['id'];
			$model = $this->loadModel($id);
			$newPrintDate = date('Y-m-d', $_GET['newPrintDate']);
			
			header('Content-type: application/json');
			
			//if($model->validate(array('printDate'))) { //TODO: need to do conversion on printDate or will beforeValidation kick in?
			if($newPrintDate <= date('Y-m-d', strtotime($model->dueDate))){
				Yii::trace('new print date valid', 'application.controllers.job');
				echo CJSON::encode(array('VALID'=>true));
					
				Yii::app()->end();
				return;
			}
		}
		Yii::trace('new print date not valid', 'application.controllers.job');
		echo CJSON::encode(array("VALID"=>false));
		Yii::app()->end();
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Job('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Job']))
			$model->attributes=$_GET['Job'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Manages all models.
	 */
	public function actionExport()
	{	
		Yii::log('hello action export', CLogger::LEVEL_INFO, 'application.controllers.job');
		//Yii::log($options['data'].'ajaxData', CLogger::LEVEL_INFO, 'farmework.web.helpers');
			
		if(isset($_GET)) //TODO check is ajax
		{
			Yii::log('hello action export GET', CLogger::LEVEL_INFO, 'application.controllers.job');
			
			//Start data
			if(isset($_GET['export_begin'])){
				$exportBegin = $_GET['export_begin'];
			}
			else $exportBegin = date("Y-m-d");
			Yii::log($exportBegin.'exportBegin', CLogger::LEVEL_INFO, 'application.controllers.job');

			
			//End date
			if(isset($_GET['export_end'])){
				$exportEnd = date("Y-m-d", strtotime($_GET['export_end'] . '+1 day'));
			}
			else $exportEnd = date("Y-m-d", strtotime('+1 day'));
			Yii::log($exportEnd.'exportEnd', CLogger::LEVEL_INFO, 'application.controllers.job');
			
			
			//Status
			if(isset($_GET['export_status'])){
				$status = array($_GET['export_status']);
			}
			else $status = Job::COMPLETED;
			Yii::log($status.'exportStatus', CLogger::LEVEL_INFO, 'application.controllers.job');
			

			$exportData = Job::getJobsByStatusDateRangeForEvent($status, $exportBegin, $exportEnd);

			$exportDataProvider = new CArrayDataProvider($exportData, array(
					'keyField'=>'ID',
					'pagination'=>array(
							'pageSize'=>15,
					),
			));

			$this->renderPartial('_exportGrid', array(
					'exportData'=>$exportDataProvider,
					'gridId'=> 'export_grid'
					), false, true);	
		}
		
		if(isset($_POST)) //TODO check is ajax
		{
			Yii::log('hello action export POST', CLogger::LEVEL_INFO, 'application.controllers.job');
			if(isset($_POST['ids']))
			{			
				Yii::log('hello action export POST ids', CLogger::LEVEL_INFO, 'application.controllers.job');
			
				//Yii::log(CVarDumper::dump($_POST).'checkedIds', CLogger::LEVEL_INFO, 'application.controllers.job');
				//post handling to render partial of mimetype text/iif
				//get subset of checked submissions only
				//attach behavior

				echo print_r( $_POST['ids']);
				foreach($_POST['ids'] as $val) {
					echo $val . '<br/>';
				}
			}
				
		}
		/*
		else 
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');*/
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Job::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.'.$id);
		return $model;
	}
	
	public function loadList($type){
		return CHtml::listData(Lookup::listItems($type), 'ID', 'TEXT');
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && ($_POST['ajax']==='job-form-create' || $_POST['ajax']==='job-form-update'))
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
