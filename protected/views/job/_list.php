<?php 
Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'jobList.js', CClientScript::POS_END);
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