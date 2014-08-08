<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	
	<script type="text/javascript" src="http://use.typekit.com/skq5ysu.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'flashMessages.js', CClientScript::POS_END);?>

<div id="wrapper" class="container_12 clearfix">
	<div class="grid_2" id="menu">		
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
			<?php $isAdmin = Yii::app()->user->getState('isAdmin');?>

			<nav>
			<?php if(!Yii::app()->user->isGuest){?>
				<?php $this->widget('application.extensions.emenu.EMenu', array(
					'items'=>array(
						array('label'=>'New Job', 'url'=>array('/job/create')),
						array('label'=>'My Jobs', 'url'=>array('/job/index')),					
						array('label'=>'All Jobs', 'url'=>array('/job/list')),
						array('label'=>'Calendar', 'url'=>array('/job/calendar')),
						array('label'=>'Logout', 'url'=>array('/site/logout')),
					),
					'themeCssFile'=>$this->styleDirectory . 'dropdown/default.css',
					'lastItemCssClass'=>'lastmenu',
					'vertical'=>true,
				)); ?>
				<?php 
					if($isAdmin){
						$this->widget('application.extensions.emenu.EMenu', array(
							'items'=>array(
								array('label'=>'Colors, Etc.', 'url'=>array('/lookup/index', 'Color'=>1, 'Style'=>1, 'Size'=>1)),
								array('label'=>'View Vendors', 'url'=>array('/vendor/index')),
								array('label'=>'View Customers', 'url'=>array('/customer/index')),
								array('label'=>'View Products', 'url'=>array('/product/index'), 'items'=>$this->products),
								array('label'=>'Add Vendor', 'url'=>array('/vendor/create')),
								array('label'=>'Add User', 'url'=>array('/user/create')),
								array('label'=>'View Users', 'url'=>array('/user/index')),
								array('label'=>'Add Product', 'url'=>array('/product/create')),
								array('label'=>'Dashboard', 'url'=>array('/dashboard'))
							),
							'themeCssFile'=>$this->styleDirectory . 'dropdown/default.css',
							'lastItemCssClass'=>'lastmenu',
							'vertical'=>true,
						));
					}
				?>
			<?php }?>
			</nav>
			<a>Hey, <?php echo Yii::app()->user->name;?>!</a> <!-- when functional, this will toggle the admin menu/nav and the logout option for all user types -->
			<div>
				$00,000
				Monthly Sales
			</div>
			<div>
				00%
				Monthly COG
			</div>
	</div>
	<div class="grid_10" id="main">
	<?php if(!Yii::app()->user->isGuest){?>
		<div class="bonnet">
			<span class="greeting">Welcome <?php echo Yii::app()->user->name;?></span>&nbsp;
			<span class="note"><?php echo date('l F j');?></span>
			<br/>
			<!--<div class="messages">
				<?php foreach($this->messages as $message){?>
					<strong><?php echo $message;?></strong>
					<br/>
				<?php }?>
			</div>-->
			<br/>
		</div>
	<?php }?>
	
	<?php if(Yii::app()->user->hasFlash('success')){?>
		<div class="flash-success" id="flash-success" >
			<?php echo Yii::app()->user->getFlash('success');?>
		</div>
	<?php } else if(Yii::app()->user->hasFlash('failure')){?>
		<div class="flash-error" id="flash-error" >
			<?php echo Yii::app()->user->getFlash('failure');?>
		</div>
	<?php }?>

	<?php echo $content; ?>
	</div>

</div><!-- #wrapper -->
</body>
</html>