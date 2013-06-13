<?php Yii::app()->clientScript->registerScript('add-art', "" .
		"function addArt(sender, namePrefix, fileType){
			$.ajax({
				'url': '".CHtml::normalizeUrl(array('job/addArt'))."'," .
				"'data': {
					'print_id':  $(sender).parent().parent().parent().children('.print_id').val()," .
					"'namePrefix': namePrefix," .
					"'fileCount': $('.art_count').val()," .
					"'fileType': fileType,
				}," .
				"'success': function(data){
					$(sender).parent().prev('div.form').append(data);" .
					"$('.art_count').val((1 * $('.art_count').val()) + 1);" .
					"document.getElementById(namePrefix + '_FILE').addEventListener('change', submitForm, false);
				}
			})
		}", CClientScript::POS_BEGIN);?>
	
<!--  submit form will do ajax post of form data or XMLHttpRequest of forms data to form the
	php $_FILES / store temorarily & update progress bar
--><!-- PrintJob_files_0_FILE -->
<?php Yii::app()->clientScript->registerScript('submit-form', "" .
		"function submitForm(e){
			e.stopPropagation(); e.preventDefault();
			self._startUpload();

			//disable all submit buttons on page

			//check that xhr will work, file types, file size OK

			//xhr event handlers

			//do xhr

			//what should the xhr method hander do ?
				//just check if is a file,
				//check if is instance of CUploadedFile (or whatever that class is)
				//check for error on $_FILES
				//should be it, don't actually want to move to perm location yet

			//event listener on xhr to re-enable buttons disabled for upload

		}", CClientScript::POS_BEGIN);
?>

<?php Yii::app()->clientScript->registerScript('art-delete', "" .
		"$('.art_delete').live('click', function(event){
			var div = $(event.target).parent();" .
			"$.ajax({
				'url': '".CHtml::normalizeUrl(array('job/deleteArt'))."'," .
				"'type': 'POST'," .
				"'data': {
					'id': $(div).children('.art_id').val()
				}," .
				"'success': function(data){
					$(div).remove();
				}
			});
		})",
CClientScript::POS_END);?>

<div id="print" class="form">

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo CHtml::errorSummary($model); ?>

	<div class="row">

		<?php //echo CHtml::activeLabelEx($model,'FRONT_PASS'); ?>

		<label>Ink Colors (Front/Back/Sleve)</label>
		<?php echo CHtml::activeDropDownList($model,'FRONT_PASS', $passes, array('class'=>'pass_part front_pass')); ?>
		<?php echo CHtml::error($model,'FRONT_PASS'); ?>

		<?php //echo CHtml::activeLabelEx($model,'BACK_PASS'); ?>
		<?php echo CHtml::activeDropDownList($model,'BACK_PASS', $passes, array('class'=>'pass_part back_pass')); ?>
		<?php echo CHtml::error($model,'BACK_PASS'); ?>
		<?php //echo CHtml::activeLabelEx($model,'SLEEVE_PASS'); ?>
		<?php echo CHtml::activeDropDownList($model,'SLEEVE_PASS', $passes, array('class'=>'pass_part sleeve_pass')); ?>
		<?php echo CHtml::error($model,'SLEEVE_PASS'); ?>
	</div>

	<?php echo CHtml::hiddenField('score_pass',$model->pass, array('class'=>'score_pass')); ?>

	<div class="row art">
		<?php echo CHtml::hiddenField('PrintJob_fileCount', count($model->files) - 1, array(
			'class'=>'art_count',
		));?>
		<?php $index = -1;
		$fileField = 'files';
		$namePrefix = CHtml::resolveName($model, $fileField);
		foreach($model->files as $art){?>
			<?php $index++;
			$this->renderPartial('//print/_artForm', array(
				'model'=>$art,
				'print_id'=>$model->ID,
				'fileType'=>$art->FILE_TYPE,
				'namePrefix'=>$namePrefix . '['.$index.']',
				'fileCount'=>$index,
				'artLink'=>isset($art->FILE) && is_string($art->FILE) ? CHtml::normalizeUrl(array('job/art', 'art_id'=>$art->ID)) : null,
			));?>
		<?php }?>
		<div id='uploader' class='form'>

		</div>
		<div class="row buttons">
			<?php foreach($fileTypes as $fileType){
				echo CHtml::button('Add '.$fileType->TEXT . ' File', array(
					'onclick'=>"addArt(this, '".$namePrefix."', ".$fileType->ID.")",
				));
			}?>
		</div>
	</div>

</div><!-- form -->

<?php Yii::app()->clientScript->registerScript('pass-update', "" .
		"$('.pass_part').live('change keyup', function(event){
			var passes = 0;" .
			"$('.pass_part').each(function(){
				passes += 1 * $(this).val();
			});" .
			"$('.score_pass').val(passes).change();
		});",
CClientScript::POS_END);?>
