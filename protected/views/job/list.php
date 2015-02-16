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
    'tabs'=>array(
        array(
            'id'=>'job-tab-created',
            'active'=>true,
            'label'=>'1. Created',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::CREATED,
                ), true),          
        ),
        array(
            'id'=>'job-tab-ordered',
            'active'=>false,
            'label'=>'2. Ordered',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::ORDERED,
                ), true),          
        ),

        array(
            'id'=>'job-tab-counted',
            'active'=>false,
            'label'=>'3. Counted',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::COUNTED,
                ), true),
        ),

        array(
            'id'=>'job-tab-printed',
            'active'=>false,
            'label'=>'4. Printed',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::PRINTED,
                ), true),
        ),

        array(
            'id'=>'job-tab-invoiced',
            'active'=>false,
            'label'=>'5. Invoiced',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::INVOICED,
                ), true),
        ),

        array(
            'id'=>'job-tab-completed',
            'active'=>false,
            'label'=>'6. Completed',
            'content'=>$this->renderPartial('_list', array(
                'statusId'=>Job::COMPLETED,
                ), true),
        ),      
    ),
));
?>