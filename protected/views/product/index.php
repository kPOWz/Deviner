<?php
$this->breadcrumbs=array(
	'Products',
);

$this->menu=array(
	array('label'=>'Create Product', 'url'=>array('create')),
	array('label'=>'Manage Product', 'url'=>array('admin')),
);
?>

<h1>Products</h1>

<?php function listSizes($model){
	$sizes = Product::getAllowedSizes($model->VENDOR_ITEM_ID);
	if(count($sizes) == 0){
		return 'None';
	} else {
		ob_start();
		
		foreach($sizes as $size){
			echo CHtml::encode($size->TEXT);
			echo '<br/>';
		}
		
		return ob_get_clean();
	}
}

function listColors($model){
	$colors = Product::getAllowedColors($model->VENDOR_ITEM_ID);
	if(count($colors) == 0){
		return 'None';
	} else {
		ob_start();
		
		foreach($colors as $color){
			echo CHtml::encode($color->TEXT);
			echo '<br/>';
		}
		
		return ob_get_clean();
	}
}?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'formatter'=>new Formatter,
	'columns'=>array(
		array(
			'class'=>'CLinkColumn',
			'header'=>'Product ID',
			'urlExpression'=>"array('product/update', 'id'=>\$data->ID)",
			'labelExpression'=>"\$data->VENDOR_ITEM_ID",
		),
		array(
			'header'=>'Description',
			'value'=>"\$data->VENDOR_ITEM_DESC",
		),
		array(
			'header'=>'Cost',
			'value'=>"\$data->COST",
		),
		array(
				'header'=>'Vendor',
				'value'=>"\$data->VENDOR->NAME",
		),
		array(
			'class'=>'CButtonColumn',
			'viewButtonImageUrl'=>false,
			'viewButtonLabel'=>'',
			'viewButtonUrl'=>'',
			'updateButtonImageUrl'=>false,
			'updateButtonLabel'=>'',
			'updateButtonUrl'=>'',
			'deleteButtonUrl'=>"CHtml::normalizeUrl(array('product/delete', 'v'=>\$data->VENDOR_ID, 'i'=>\$data->VENDOR_ITEM_ID))",
		),
	),
));?>
