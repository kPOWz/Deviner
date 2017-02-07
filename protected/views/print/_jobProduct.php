<article class="col-md-5 <?php echo $garmentClass; ?>">
	<header>
		<dl class="dl-horizontal">
			<dt class="text-muted">Style</dt>
			<dd><?php echo $jobLine->product->VENDOR_ITEM_ID . " - " . $jobLine->product->VENDOR->NAME;?></dd>
		</dl>
		<dl class="dl-horizontal">
			<dt class="text-muted">Shirt Color</dt>		
			<dd><?php echo $formatter->formatLookup($jobLine->PRODUCT_COLOR);?></dd>
		</dl>
		<dl class="dl-horizontal">
			<dt class="text-muted">Ink Color(s)</dt>
			<dd class="row">
				<span class="ink-color col-md-offset-1 col-md-2"></span>
				<span class="ink-color col-md-2"></span>
				<span class="col-md-2"></span>
			</dd>
		</dl>
	</header>
	<ol class="row list-size">
		<?php
			foreach($jobLine->sizes as $size){
				if($size->QUANTITY < 1) continue;
				$this->renderPartial('//jobLineSize/_view', array(
					'model'=>$size,
					'formatter'=>new Formatter
				));
		}?>
	</ol>
</article>