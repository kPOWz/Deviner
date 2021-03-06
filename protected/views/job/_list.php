<?php 
Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'jobList.js', CClientScript::POS_END);
$model = new Job();


$this->widget('yiistrap.widgets.TbGridView', array(
	'dataProvider' => isset($dataProvider) ? $dataProvider : $model->search($statusId, 7),
	'id'=> isset($statusId) ? 'job-grid-status-'.$statusId : 'job-grid',
	'pager'=> array('class' => '\TbPager', 'htmlOptions'=>array('class'=>'gus-pagination', 'align'=>TbHtml::PAGINATION_ALIGN_CENTER)),
	'rowHtmlOptionsExpression' => 'array("data-href"=>CHtml::normalizeUrl(array("job/update", "id"=>$data->ID)))',
	'ajaxUrl'=>'filter',
	'afterAjaxUpdate'=>'addPaginationListener',
	'itemsCssClass'=>'table-primary',
	'pagerCssClass'=>'text-center', //remove 'pager' class coming from zii - namespace conflict w/ TWBS css
   	'columns' => array(
   		array(
			'header'=>'Client',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->CUSTOMER->COMPANY == NULL ? $data->CUSTOMER->USER->FIRST ." ".$data->CUSTOMER->USER->LAST : $data->CUSTOMER->COMPANY)',
            'htmlOptions' => array('class'=>'td-clickable'),		
		),
        array(
			'header'=>'Job',
			'type' => 'raw',
			'value'=>"\$data->NAME . ((\$data->RUSH > 0) ? ' <span class=\"label label-warning\">RUSH</span>' : '');",
			'htmlOptions' => array('class'=>'td-clickable'),
		),
		array(
			'header'=>'Leader',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->LEADER->FIRST)',
       		'htmlOptions' => array('class'=>'td-clickable'),
		),
		array(
			'header'=>'Due',
			'name'=>'dueDate',
			'value'=>"(strtotime(\$data->dueDate) <= 0) ? '(None)' : date('l (n/j)', strtotime(\$data->dueDate));",
			'htmlOptions' => array('class'=>'td-clickable'),
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