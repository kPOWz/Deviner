<?php
/**
 * CheckedCheckBoxColumn class file.
 *
 * @author Katherine Zeman Bach <karrie.zeman@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2012 Fluff Enterprises LLC
 * @license http://www.yiiframework.com/license/
 */

Yii::import('zii.widgets.grid.CCheckBoxColumn');

/**
 * CheckedCheckBoxColumn represents a grid view column of checkboxes.
 *
 * CCheckBoxColumn supports no selection (read-only), single selection and multiple selection.
 * The mode is determined according to {@link selectableRows}. When in multiple selection mode, the header cell will display
 * an additional checkbox, clicking on which will check or uncheck all of the checkboxes in the data cells.
 *
 * Additionally selecting a checkbox can select a grid view row (depending on {@link CGridView::selectableRows} value) if
 * {@link selectableRows} is null (default).
 *
 * By default, the checkboxes rendered in data cells will have the values that are the same as
 * the key values of the data model. One may change this by setting either {@link name} or
 * {@link value}.
 *
 * @author Katherine Zeman Bach <karrie.zeman@gmail.com>
 * @version $Id: CheckedCheckBoxColumn.php
 * @package zii.widgets.grid
 * @since 1.1
 */


class CheckedCheckBoxColumn extends CCheckBoxColumn
    {
       
    	/**
    	 * Renders the header cell content.
    	 * This method will render a checked checkbox in the header when {@link selectableRows} is greater than 1
    	 * or in case {@link selectableRows} is null when {@link CGridView::selectableRows} is greater than 1.
    	 */
    	protected function renderHeaderCellContent()
    	{
    		
    		if($this->selectableRows===null && $this->grid->selectableRows>1)
    			echo CHtml::checkBox($this->id.'_all',true,array('class'=>'select-on-check-all'));
    		else if($this->selectableRows>1)
    			echo CHtml::checkBox($this->id.'_all',true);
    		else
    			parent::renderHeaderCellContent();
    	}
    }