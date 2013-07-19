<?php
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'Dashboard | GUS';
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/css/job_dashboard.css');
	Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'dragAndDrop.js', CClientScript::POS_END);
	
	//it doesn't make sense to me either, but the yii framework doesn't let me add extra variables
	//to expressions in the grid view. so this is what I have to do...
	class StatusProvider {
		public static $statuses;
		
		public static function statusSelector($model){
			return CHtml::activeDropDownList($model, 'STATUS', StatusProvider::$statuses, array(
				'onchange'=>"statusChanged(".Job::COMPLETED.", ".Job::CANCELED.", '".CHtml::normalizeUrl(array('job/status', 'id'=>$model->ID))."', this);"
			));
		}
	}
	StatusProvider::$statuses = $statuses;
?>
<script type="text/javascript">
	function statusChanged(completedStatus, canceledStatus, updateUrl, selector){
			var status = $(selector).val(); 
			$.ajax({
				url: updateUrl,
				data: {
					status: status,
				},
				type: 'POST',
			});
		}
</script>

<h1>My Jobs</h1>				
<!--table goes here-->
<?php 
$this->widget('zii.widgets.grid.CGridView', array( 
	'dataProvider'=>$dataProvider,
	'formatter'=>new CFormatter,
	'columns'=>array(
		array(
			'class'=>'CLinkColumn',
			'header'=>'	Job',
			'labelExpression'=>"((\$data->RUSH != 0) ? '<span class=\"warning\">RUSH</span>&nbsp;' : '') . \$data->NAME;",
			'urlExpression'=>"CHtml::normalizeUrl(array('job/update', 'id'=>\$data->ID));"
		),
		array(
			'header'=>'Leader',
			'value'=>"\$data->LEADER->FIRST"
		),
		array(
			'header'=>'Due',
			'name'=>'dueDate',
			'value'=>"(strtotime(\$data->dueDate) <= 0) ? '(None)' : date('l (n/j)', strtotime(\$data->dueDate));"
		),
		array(
			'header'=>'Print',
			'name'=>'printDate',
			'value'=>"(strtotime(\$data->printDate) <= 0) ? '(None)' : date('l (n/j)', strtotime(\$data->printDate));",
		),
		array(
			'header'=>'Status',
			'type'=>'raw',
			'value'=>"StatusProvider::statusSelector(\$data)",
		)		
	)
));
?>