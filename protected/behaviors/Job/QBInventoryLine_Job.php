<?php 
/*QBInventoryLine_Job wraps a job  and provides records for export to QuickBooks INVITEMS in a records property.*/
class QBInventoryLine_Job extends QBInventoryLine {
	/*records which need to be translated:
	rush, art charge, setup time, additional charges, sales tax*/
	protected function createRush(){		
		return $this->createLine(
			CHtml::encode($this->owner->getAttributeLabel('RUSH')),
			'OTHC',
			QBConstants::DESCRIPTION_RUSH, 
			$this->owner->RUSH,
			QBConstants::RUSH_ACCNT
		);
	}

	protected function createArtCharge(){
		return $this->createLine(
			QBConstants::NAME_ART_CHARGE,
			'SERV',
			QBConstants::DESCRIPTION_ART_CHARGE,
			40, //hourly rate
			QBConstants::ART_ACCNT
		);
	}

	protected function createSetupFee(){
		return $this->createLine(
			QBConstants::NAME_SETUP_TIME,
			'SERV',
			QBConstants::DESCRIPTION_SETUP_TIME,
			30, //hourly rate
			QBConstants::SETUP_ACCNT
		);
	}

	protected function createSalesTax(){
		$taxRate = $this->owner->additionalFees[Job::FEE_TAX_RATE]['VALUE'];
		$taxRateAsFloat = strrpos($taxRate, '.' ) ? $taxRate : $taxRate . '.00';
		return $this->createLine(
			QBConstants::NAME_SALES_TAX,
			'COMPTAX',
			QBConstants::DESCRIPTION_SALES_TAX,
			substr($taxRateAsFloat, 0, strrpos($taxRateAsFloat,'.') + 3). '%',
			QBConstants::TAX_ACCNT,
			'N'
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