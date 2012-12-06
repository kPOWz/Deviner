<?php 
/*QBInventoryLine_Job wraps a job  and provides records for export to QuickBooks INVITEMS in a records property.*/
class QBInventoryLine_Job extends QBInventoryLine {
	/*records which need to be translated:
	rush, art charge, setup time, additional charges, sales tax*/
	protected function createRush(){		
		return $this->createLine(
			CHtml::encode($this->owner->getAttributeLabel('RUSH')),
			'OTHC',
			'Fee for accelerated handling', 
			$this->owner->RUSH,
			QBConstants::RUSH_ACCNT
		);
	}

	protected function createArtCharge(){
		return $this->createLine(
			'Artwork Charge',
			'SERV',
			'Fee for design work',
			40, //hourly rate
			QBConstants::ART_ACCNT
		);
	}

	protected function createSetupFee(){
		return $this->createLine(
			'Setup Time',
			'SERV',
			'Fee for setup (waived for larger orders)',
			30, //hourly rate
			QBConstants::SETUP_ACCNT
		);
	}

	protected function createSalesTax(){
		$taxRate = $this->owner->additionalFees[Job::FEE_TAX_RATE]['VALUE'];
		return $this->createLine(
			'Sales Tax',
			'COMPTAX',
			'Sales Tax',
			substr(string(floatval($taxRate . '0')), 0, strrpos(string(floatval($taxRate . '0')),'.') + 1). '%',
			QBConstants::TAX_ACCNT
		);
	}

	protected function createAdditional($additional, $index){
		return $this->createLine(
			'Additional_'.$index,
			'OTHC',
			$additional['TEXT'],			
			null,
			strpos($additional['TEXT'],'Shipping') ? QBConstants::SHIPPING_ACCNT : QBConstants::TRNS_ACCNT
		);
	}

	protected function createRecords(){
		$lines = array();
		$lines[] = $this->createRush();
		$lines[] = $this->createArtCharge();
		$lines[] = $this->createSetupFee();			
		$index = 0;
		foreach ($this->owner->additionalFees as $fee) {
			if($fee['CONSTRAINTS']['part'] !== false){
				$lines[] = $this->createAdditional($fee, $index);
			}
		}
		$lines[] = $this->createSalesTax();
		return $lines;
	}
}