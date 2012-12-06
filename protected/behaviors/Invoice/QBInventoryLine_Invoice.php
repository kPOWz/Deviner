<?php 
/*QBInventoryLine_Invoice wraps an invoice and provides records for export to QuickBooks INVITEMS in a records property.*/
class QBInventoryLine_Invoice extends QBInventoryLine {
	/*records which need to be translated:
	rush, art charge, setup time, additional charges, sales tax*/
	

	protected function createSalesTax(){
		$taxRate = $this->owner->TAX_RATE;
		$taxRateAsFloat = strrpos($taxRate, '.' ) ? $taxRate : $taxRate . '.00';
		return $this->createLine(
			'Sales Tax',
			'COMPTAX',
			'Sales Tax',
			substr($taxRateAsFloat, 0, strrpos($taxRateAsFloat,'.') + 3). '%',
			QBConstants::TAX_ACCNT,
			'N'
		);
	}

	protected function createRecords(){
		$lines = array();
		$lines[] = $this->createSalesTax();
		return $lines;
	}
}