<!DOCTYPE html>
<html lang="en">
<?php echo $this->renderPartial('/layouts/_header'); ?>
<body >
	<div class="container-fluid gus-print">
		<div id="wrapper" class="row">
			<main class="col-md-12" id="main">		
				<?php echo $content; ?>
			</main>
		</div><!-- #wrapper -->
		<?php echo $this->renderPartial('/layouts/_footer'); ?>
	</div>
</body>
</html>