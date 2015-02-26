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
            'id'=>'job-content-'.Job::CREATED,
            'active'=>true,
            'htmlOptions'=>array('class'=>'col-md-2', 'data-status'=>Job::CREATED),
            'label'=>'1. Created',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::CREATED,
                ), true),          
        ),
        array(
            'id'=>'job-content-'.Job::ORDERED,
            'htmlOptions'=>array('class'=>'col-md-2', 'data-status'=>Job::ORDERED),
            'active'=>false,
            'label'=>'2. Ordered',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::ORDERED,
                ), true),          
        ),

        array(
            'id'=>'job-content-'.Job::COUNTED,
            'htmlOptions'=>array('class'=>'col-md-2', 'data-status'=>Job::COUNTED),
            'active'=>false,
            'label'=>'3. Counted',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::COUNTED,
                ), true),
        ),

        array(
            'id'=>'job-content-'.Job::PRINTED,
            'htmlOptions'=>array('class'=>'col-md-2', 'data-status'=>Job::PRINTED),
            'active'=>false,
            'label'=>'4. Printed',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::PRINTED,
                ), true),
        ),

        array(
            'id'=>'job-content-'.Job::INVOICED,
            'htmlOptions'=>array('class'=>'col-md-2', 'data-status'=>Job::INVOICED),
            'active'=>false,
            'label'=>'5. Invoiced',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::INVOICED,
                ), true),
        ),

        array(
            'id'=>'job-content-'.Job::COMPLETED,
            'htmlOptions'=>array('class'=>'col-md-2', 'data-status'=>Job::COMPLETED),
            'active'=>false,
            'label'=>'6. Completed',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::COMPLETED,
                ), true),
        ),      
    ),
));
?>