<?php 
$model = new Job();

$this->widget('yiistrap.widgets.TbGridView', array(
	'dataProvider' => isset($dataProvider) ? $dataProvider : $model->searchByStatus($statusId),
	'itemsCssClass'=>'table-primary',
   	'columns' => array(
   		array(
			'header'=>'Client',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->CUSTOMER->COMPANY == NULL ? $data->CUSTOMER->USER->FIRST ." ".$data->CUSTOMER->USER->LAST : $data->CUSTOMER->COMPANY)'			
		),
        array(
			'class'=>'CLinkColumn',
			'header'=>'Job',
			'labelExpression'=>"((\$data->RUSH != 0) ? '<span class=\"warning\">RUSH</span>&nbsp;' : '') . \$data->NAME;",
			'urlExpression'=>"CHtml::normalizeUrl(array('job/update', 'id'=>\$data->ID));",
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
			'header'=>'Status',
			'type'=>'raw',
			'value'=>'TbHtml::activeDropDownList($data, "STATUS", Job::statusListData(), array("onchange"=>"statusChanged(this,$data->ID)"))',
		),
		array(            
            'class'=>'CButtonColumn',
			'template'=>'{delete}',
			'deleteButtonLabel' => 'Delete job permanently',
			'deleteConfirmation'=>"js:'Job \''+$(this).parent().parent().children(':nth-child(2)').text()+'\' will be deleted! Continue?'",
			'buttons'=>array(
					'delete' => array
			        (
			            'options'=>array('class'=>'btn btn-default', 'type'=>'button'),
			            'label'=>'<span class="glyphicon glyphicon-remove text-danger"><span>',
			            'imageUrl'=>NULL,
			        ),
				)			
        ),
    ),
));
?>

<script type="text/javascript">
	function statusChanged(selector,jobId){
			var status = $(selector).val(); 
			$.ajax({
				url: 'status',
				dataType: "json",
				data: {
					status: status,
					id: jobId,
				},
				type: 'POST',
				success: function(data){
					$('[name=salesNumber]').text(data.sales);
					$('[name=salesPercentage]').text(data.cogsPercentage);
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