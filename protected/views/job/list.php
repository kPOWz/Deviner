<?php 
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCoreScript('jquery.ui');
?>

<?php  $this->renderPartial('_search',array(
    'model'=>new Job('search'),
)); ?>

<h1>Jobs by Status</h1>
<?php 
	$this->widget('zii.widgets.jui.CJuiTabs', array(
		'id'=>'job-tabs',
		'tabs'=>array(
			'Created'=>array('ajax'=>array('job/loadList', 'list'=>'created'),
				
				'content'=>$this->renderPartial('_list', array(
					'statuses'=>$statuses,
					'dataProvider'=>$currentDataProvider,
					'tabId'=>'job-tab-current',
				), true),
			),
			'Ordered'=>array('ajax'=>array('job/loadList', 'list'=>'ordered')),
			'Counted'=>array('ajax'=>array('job/loadList', 'list'=>'counted')),
			'Printed'=>array('ajax'=>array('job/loadList', 'list'=>'printed')),
			'Invoiced'=>array('ajax'=>array('job/loadList', 'list'=>'invoiced')),
			'Completed'=>array('ajax'=>array('job/loadList', 'list'=>'completed')),
			'Canceled'=>array('ajax'=>array('job/loadList', 'list'=>'canceled')),
		),
		'options'=>array(
			'ajaxOptions'=>array(
				'cache'=>false,
			),
		),
	));
?>