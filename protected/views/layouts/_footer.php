<footer>
	<?php 
		Yii::app()->yiistrap->bootstrapPath = Yii::getPathOfAlias('vendor.kpowz.gus-bootstrap.dist');
		Yii::app()->yiistrap->register();
	?>
	<script>
		var cog = document.getElementById('cog');
		if(cog)
			cog.addEventListener('click', function(event) {
				var adminNav = document.getElementById('admin-nav');
				if(adminNav)					
					$(adminNav).toggleClass('target');
			});
	</script>
</footer>