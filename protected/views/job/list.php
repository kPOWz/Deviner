<?php 
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCssFile($this->styleDirectory . 'job_list.css');
?>

<?php  $this->renderPartial('_search',array(
    'model'=>new Job('search'),
)); ?>

<script type="text/javascript">

function statusChanged(updateUrl, selector){
	var status = $(selector).val();
	var JobStatusConstants = {
			canceledStatus: '<?php echo Job::CANCELED ?>',
			paidStatus: '<?php echo Job::PAID ?>',
			completedStatus: '<?php echo Job::COMPLETED ?>',
			invoicedStatus: '<?php echo Job::INVOICED ?>',
			printedStatus: '<?php echo Job::PRINTED ?>',
			countedStatus: '<?php echo Job::COUNTED ?>',
			orderedStatus: '<?php echo Job::ORDERED ?>',
			scheduledStatus: '<?php echo Job::SCHEDULED ?>'
			//createdStatus not needed
	   }
	$.ajax({
		url: updateUrl,
		data: {
			status: status,
		},
		type: 'POST',
		success: function(data){
			var index = 0;	
			switch(1 * status){
				case JobStatusConstants.canceledStatus : index = 6; break;
				case JobStatusConstants.completedStatus : index = 5; break;
				case JobStatusConstants.invoicedStatus : index = 4; break;
				case JobStatusConstants.printedStatus : index = 3; break;
				case JobStatusConstants.countedStatus : index = 2; break;
				case JobStatusConstants.orderedStatus : index = 1; break;
				default : index = 0; break;
			}
			var tabControl = $(selector).parentsUntil('.ui-tabs').parent();
			var currentIndex = tabControl.tabs('option', 'selected');
			$(tabControl).tabs('load', index);
			$(tabControl).tabs('load', currentIndex);
		}
	});
}
</script>

<h3>Jobs by status</h3>
<?php 
	$this->widget('zii.widgets.jui.CJuiTabs', array(
		'id'=>'job-tabs',
		'tabs'=>array(
			'Created'=>array('ajax'=>array('job/loadList', 'list'=>'created'),
				
				'content'=>$this->renderPartial('_listSection', array(
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