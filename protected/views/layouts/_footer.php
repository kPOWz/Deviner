<footer>
	<?php Yii::app()->yiistrap->register(); ?>
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