<?php 
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCoreScript('jquery.ui');
?>

<h1>All Jobs</h1>
<?php  $this->renderPartial('_search',array(
    'model'=>new Job('search'),
)); ?>
<?php 
$this->widget('yiistrap.widgets.TbTabs', array(
	'id'=>'job-tabs',
    'htmlOptions'=>array('menuOptions'=>array('class'=>'row')),
    'tabs'=>array(
        array(
            'id'=>'job-tab-created',
            'active'=>true,
            'htmlOptions'=>array('class'=>'col-md-2'),
            'label'=>'1. Created',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::CREATED,
                ), true),          
        ),
        array(
            'id'=>'job-tab-ordered',
            'htmlOptions'=>array('class'=>'col-md-2'),
            'active'=>false,
            'label'=>'2. Ordered',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::ORDERED,
                ), true),          
        ),

        array(
            'id'=>'job-tab-counted',
            'htmlOptions'=>array('class'=>'col-md-2'),
            'active'=>false,
            'label'=>'3. Counted',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::COUNTED,
                ), true),
        ),

        array(
            'id'=>'job-tab-printed',
            'htmlOptions'=>array('class'=>'col-md-2'),
            'active'=>false,
            'label'=>'4. Printed',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::PRINTED,
                ), true),
        ),

        array(
            'id'=>'job-tab-invoiced',
            'htmlOptions'=>array('class'=>'col-md-2'),
            'active'=>false,
            'label'=>'5. Invoiced',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::INVOICED,
                ), true),
        ),

        array(
            'id'=>'job-tab-completed',
            'htmlOptions'=>array('class'=>'col-md-2'),
            'active'=>false,
            'label'=>'6. Completed',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::COMPLETED,
                ), true),
        ),      
    ),
));
?>