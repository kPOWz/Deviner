<?php
	Yii::app()->clientScript->registerCoreScript('jquery');
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'Calendar | GUS';
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/css/job_dashboard.css');
	Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'dragAndDrop.js', CClientScript::POS_END);
?>


<?php 
Yii::app()->clientScript->registerScript('init-calendar', "" .
"(function init(){" .
	"initCalendar('".CHtml::normalizeUrl(array('job/validatePrintDate'))."','".CHtml::normalizeUrl(array('job/updatePrintDate'))."');" .
"})();", CClientScript::POS_END);
?>

<header class="row">				
	<h1 class="col-md-4">Print Calendar</h1>
	<div class="col-md-3 pull-right" name="labels">
		<span class=" label label-primary">Blue = my responsibility</span>
		<span class=" label label-default">
			<span class="text-primary">&#10003;</span> 
			<span>&#10003;</span> 
		= printed job</span>
	</div>
</header>

<?php for($i = 0; $i < 4; $i++){?>
	<div id="cal<?php echo $i;?>" class="cal_container">
		<?php $this->widget('application.widgets.CalendarWidget', array(
			'droppable'=>false,
			'itemView'=>'//job/_eventDetail',
			'headerView'=>'//job/_dayHeader',
			'calendarData'=>$calendarData[$i],
		));?>
	</div>
<?php }?>