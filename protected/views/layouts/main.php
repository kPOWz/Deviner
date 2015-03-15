<!DOCTYPE html>
<html lang="en">
<?php echo $this->renderPartial('/layouts/_header'); ?>
<body >
<div class="container-fluid">
<?php Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'flashMessages.js', CClientScript::POS_END);?>

<div id="wrapper" class="row">
	<div class="col-md-2 gus-nav-container">
		<section class="inverse " id='outterContainer' data-spy="affix">				
			<?php $isAdmin = Yii::app()->user->getState('isAdmin');?>
			<nav class="navbar navbar-vertical row gus-nav" >
				<div class="navbar-header">		
					<h1><a href="#"><?php echo CHtml::encode(Yii::app()->name); ?></a></h1>			
				</div>
				<?php if(!Yii::app()->user->isGuest){?>
				<nav id='nav-main' class='collapse navbar-collapse'>			
					<?php $this->widget('zii.widgets.CMenu', array(
						'items'=>array(
							array('encodeLabel'=>false, 'label'=>'<span class="glyphicon glyphicon-plus text-primary" aria-hidden="true"></span> Create Job', 'url'=>array('/job/create')),
							array('label'=>'My Jobs', 'url'=>array('/job/index')),					
							array('label'=>'All Jobs', 'url'=>array('/job/list')),
							array('label'=>'Print Calendar', 'url'=>array('/job/calendar')),
						),
						'htmlOptions'=>array('class'=>'nav navbar-nav'),
						'lastItemCssClass'=>'lastmenu',
					)); ?>
				</nav>
				<div id="nav-bottom">
					<nav id="nav-admin" class="collapse">
						<?php 
							if($isAdmin){
								$this->widget('zii.widgets.CMenu', array(
									'htmlOptions'=>array( 'id'=>'admin-nav'),
									'items'=>array(
										array('label'=>'Colors, Etc.', 'url'=>array('/lookup/index', 'Color'=>1, 'Style'=>1, 'Size'=>1)),
										array('label'=>'View Vendors', 'url'=>array('/vendor/index')),
										array('label'=>'View Customers', 'url'=>array('/customer/index')),
										array('label'=>'View Users', 'url'=>array('/user/index')),
										array('encodeLabel'=>false, 'label'=>'View Products<strong class="caret"></strong>', 'url'=>array('/product/index')
											, 'items'=>$this->products
											, 'itemOptions'=>array('class'=>'dropdown')
											, 'linkOptions'=>array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown')),
										array('label'=>'Add Vendor', 'url'=>array('/vendor/create')),
										array('label'=>'Add User', 'url'=>array('/user/create')),
										array('label'=>'Add Product', 'url'=>array('/product/create')),
										array('encodeLabel'=>false, 'label'=>'<span class="glyphicon glyphicon-off text-primary" aria-hidden="true"></span> Logout', 'url'=>array('/site/logout')),
									),
									'htmlOptions'=>array('class'=>'nav navbar-nav'),
									'lastItemCssClass'=>'lastmenu',
								));
							}else{
								$this->widget('zii.widgets.CMenu', array(
									'htmlOptions'=>array( 'id'=>'admin-nav'),
									'items'=>array(
										array('encodeLabel'=>false, 'label'=>'<span class="glyphicon glyphicon-off text-primary" aria-hidden="true"></span> Logout', 'url'=>array('/site/logout')),
									),
									'htmlOptions'=>array('class'=>'nav navbar-nav'),
									'lastItemCssClass'=>'lastmenu',
								));
							}
						?>
					</nav>
					<?php }?>
					<div id="nav-footer" >
						<div class="row">
							Hey, <?php echo Yii::app()->user->name;?>!
							<a id="cog" title='access admin tasks'
								aria-expanded="false" aria-controls="nav-admin"
								data-toggle="collapse" href="#nav-admin">
								<span class="glyphicon glyphicon-cog text-primary" aria-hidden="true"></span>
							</a>
						</div>
						<div class="row gray-dark" id="sales">
							<?php $this->widget('application.widgets.SalesReportWidget'); ?>
						</div>	
					</div>		
				</div>
			</nav>
		</section>
	</div>
	<main class="col-md-10" id="main">		
		<?php if(Yii::app()->user->hasFlash('success')){?>
			<div class="alert alert-success" role="alert" id="flash-success" >
				<?php echo Yii::app()->user->getFlash('success');?>
			</div>
		<?php } else if(Yii::app()->user->hasFlash('failure')){?>
			<div class="alert alert-danger" role="alert" id="flash-error" >
				<?php echo Yii::app()->user->getFlash('failure');?>
			</div>
		<?php }?>
		<?php echo $content; ?>
	</main>

</div><!-- #wrapper -->
<?php echo $this->renderPartial('/layouts/_footer'); ?>
</div>
</body>
</html>