<?php
/*QBTransactionLine_Invoice wraps a invoice and provides records for export to QuickBooks SPLs in a records property.*/
class QBTransactionLine_Invoice extends QBTransactionLine {
	protected function createLine($id, $amount, $price, $quantity, $invitem, $accnt, $taxable='Y'){
		$params = $this->initItem();
		$params['SPLID'] = $id; //rush, artcharge, setup fee, additionals, sales tax
		$params['TRNSTYPE'] = 'INVOICE';
		$params['DATE'] = date('n/j/Y', strtotime($this->owner->DATE)); //may need to format this
		$params['NAME'] = $this->owner->CUSTOMER->summary;
		$params['AMOUNT'] = $amount;
		$params['DOCNUM'] = QBConstants::QB_DOCNUM_PREFIX . $this->owner->ID;
		$params['CLEAR'] = 'N';
		$params['PRICE'] = $price;
		$params['QNTY'] = $quantity;
		$params['INVITEM'] = $invitem;
		$params['TAXABLE'] = $taxable;
		//ACCT (Required) The income or expense account to which you assigned the amount on the distribution line.
		$params['ACCNT'] = $accnt;
		return $params;		
	}

	protected function createSalesTax(){		
		
		$taxRate = $this->owner->TAX_RATE;
		$taxRateAsFloat = strrpos($taxRate, '.' ) ? $taxRate : $taxRate . '.00';
		return $this->createLine(
			'0',
			$this->owner->total * $taxRate / 100,
			substr($taxRateAsFloat, 0, strrpos($taxRateAsFloat,'.') + 3). '%',
			null,
			QBConstants::DESCRIPTION_SALES_TAX,			
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