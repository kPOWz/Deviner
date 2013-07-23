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
				
<h1>Calendar</h1>
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