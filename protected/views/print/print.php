
<h4>
	<div class="row">
		<dl class="col-md-5 col-md-offset-1 dl-horizontal text-uppercase">
			<dt class="text-primary maybe">Project</dt>
			<dd><strong><?php echo $job->NAME ?></strong></dd>
		</dl>
		<dl class="col-md-6 dl-horizontal">
			<dt class="text-muted">Name</dt>
			<dd ><?php echo $customer->FIRST . " " . $customer->LAST ?></dd>
		</dl>
	</div>
	<div class="row">
		<dl class="col-md-5 col-md-offset-1 dl-horizontal text-uppercase">
			<dt class="text-primary">Manager</dt>
			<dd><?php echo $job->LEADER->FIRST . " " . $job->LEADER->LAST ?></dd>
		</dl>
		<dl class="col-md-6 dl-horizontal">
			<dt class=" text-muted">Email</dt>
			<dd><?php echo $customer->EMAIL ?></dd>
		</dl>
	</div>
	<div class="row">
		<dl class="col-md-5 col-md-offset-1 dl-horizontal text-uppercase">
			<dt class=" text-primary">Location</dt>
			<dd>
				<?php echo 	CHtml::encode($print->FRONT_PASS) ." / ".
							CHtml::encode($print->BACK_PASS) ." / ".
							CHtml::encode($print->SLEEVE_PASS);
				?>
			</dd>
		</dl>
		<dl class="col-md-6 dl-horizontal">
			<dt class="text-muted">Phone</dt>
			<dd><?php echo $customer->PHONE ?></dd>
		</dl>
	</div>
	<div class="row">
		<dl class="col-md-5 col-md-offset-1 dl-horizontal text-uppercase">
			<dt class="text-primary">Due Date</dt>
			<dd><?php echo $formatter->formatDate($job->dueDate); ?></dd>
		</dl>
		<dl class="col-md-6 dl-horizontal">
			<dt class="text-muted">Company</dt>
			<dd><?php echo $customer->COMPANY ?></dd>
		</dl>
	</div>
</h4>


<div class="job-print">

	<h4 class="text-primary">Product Count <span class="text-muted" style="margin-left: 1em; font-family: 'Avenir LT W01 35 Light','Helvetica Neue',Helvetica,Arial,sans-serif;"><?php echo $job->garmentCount ?></span></h4>
	<hr />
	<section id="garments" class="row">
		<?php
			foreach($job->uniqueJobLines as $product => $colors){
				$i = 0;
				foreach($colors as $color => $ujl){
					$i++;				
					$this->renderPartial('//print/_jobProduct', array(
						'jobLine'=>$ujl,
						'formatter'=>$formatter,
						'garmentClass'=> $i % 2 == 0 ? '' : 'col-md-offset-1'
					));
				}
			}?>
	</section>
	
	<h4 class="text-primary">Notes</h4>
	<hr />
	<p class="print-notes">
		<?php echo $job->NOTES ?>
	</p>
</div>


