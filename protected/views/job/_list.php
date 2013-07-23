<?php 
//it doesn't make sense to me either, but the yii framework doesn't let me add extra variables
//to expressions in the grid view. so this is what I have to do...
class StatusProvider {
	public static $statuses;
	
	public static function statusSelector($model){
		return CHtml::activeDropDownList($model, 'STATUS', StatusProvider::$statuses, array(
			'onchange'=>"statusChanged('".CHtml::normalizeUrl(array('job/status', 'id'=>$model->ID))."', this);"
					
		));
	}
}

StatusProvider::$statuses = $statuses;

$this->widget('zii.widgets.grid.CGridView', array( 
	'dataProvider'=>$dataProvider,
	'id'=>$tabId,
	'formatter'=>new Formatter,
	'columns'=>array(
		array(
			'class'=>'CLinkColumn',
			'header'=>'Job',
			'labelExpression'=>"((\$data->RUSH != 0) ? '<span class=\"warning\">RUSH</span>&nbsp;' : '') . \$data->NAME;",
			'urlExpression'=>"CHtml::normalizeUrl(array('job/view', 'id'=>\$data->ID));",
		),
		array(
			'header'=>'Leader',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->LEADER->FIRST)'			
		),
		array(
			'header'=>'Due',
			'name'=>'dueDate',
			'value'=>"(strtotime(\$data->dueDate) <= 0) ? '(None)' : date('l (n/j)', strtotime(\$data->dueDate));",
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

<script type="text/javascript">
	function statusChanged(updateUrl, selector){
			var status = $(selector).val(); 
			$.ajax({
				url: updateUrl,
				data: {
					status: status,
				},
				type: 'POST',
				success: function(data){
					/*var tabControl = $(selector).parentsUntil('.ui-tabs').parent();
					
					switch(1 * status){
						case JobStatusConstants.canceledStatus : index = 6; break;
						case JobStatusConstants.completedStatus : index = 5; break;
						case JobStatusConstants.invoicedStatus : index = 4; break;
						case JobStatusConstants.printedStatus : index = 3; break;
						case JobStatusConstants.countedStatus : index = 2; break;
						case JobStatusConstants.orderedStatus : index = 1; break;
						default : index = 0; break;
					}*/

					//TODO: they were init-loading the tab representing the target status & 
					//TODO: then reloading the current tab to awkwardly sync after the status change
					//TODO: this is where the above switch came into play
					//TODO: need to replace this approach with just removing any row that has a status chagne from the DOM
					//TODO: as long as the POST above was successful the target tab will laod reflecting it's new member 
					//TODO: automatically when its next navigated to
				}
			});
		}
</script>