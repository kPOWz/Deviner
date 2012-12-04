<?php 
/*QBInventoryLine acts as an abstract base class for behaviors which export a certain record type to a QuickBooks IIF INVITEM format.*/
abstract class QBInventoryLine extends CActiveRecordBehavior {
	private $lines = null;

	/**
	Creates an array with all fields in the INVITEM record type of QuickBooks IIF.
	@return array The array, with all elements set to null.
	*/
	private function createInvItem(){
		return array(
			'INVITEM'=>'INVITEM',
			'NAME'=>null,
			'TIMESTAMP'=>null,  // QB EXPORT FILES ONLY & GUS MAKES IMPORT FILES
			'REFNUM'=>null, // QB EXPORT FILES ONLY & GUS MAKES IMPORT FILES
			'INVITEMTYPE'=>null, //NEEDS FIX
			'DESC'=>null,
			'PURCHASEDESC'=>null,
			'ACCNT'=>null,
			'ASSETACCNT'=>null, //Inventory part INVITEMTYPE items only
			'COGSACCNT'=>null,  //Inventory part INVITEMTYPE items only
			'PRICE'=>null, //MAYBE NEEDS FIX
			'COST'=>null,
			'TAXABLE'=>null //EXTRA TAB BEING CREATED BETWEEN COST & TAXABLE
			
			/*, NOT REQUIRED FOR IIF & NOT USED BY GUS
			'PAYMETH'=>null,
			'TAXVEND'=>null,
			'TAXDIST'=>null,
			'TOPRINT'=>null, //Inventoy Group INVITEMTYPE items only
			'PREFVEND'=>null,
			'REORDERPOINT'=>null,
			'EXTRA'=>null,
			'CUSTFLD1'=>null,
			'CUSTFLD2'=>null,
			'CUSTFLD3'=>null,
			'CUSTFLD4'=>null,
			'CUSTFLD5'=>null,
			'DEP_TYPE'=>null,
			'ISPASSEDTHRU'=>null, //May need to add this one back in as it applies to service INVITEMTYPE
			*/
		);
	}

	/**
	Initializes the inventory item array with values that are common to all record types.
	The following values are initialized, while the remaining elements are set to null:
	ACCNT, ASSETACCNT, COGSACCNT, TAXABLE(Y), TOPRINT (Y), ISPASSEDTHRU (Y)
	@return array The INVITEM array.
	*/
	protected function initInvItem(){
		$params = $this->createInvItem();
		//ACCT (Required) The name of the income account you use to track sales of the item. The type of this account should be INC.
		$params['ACCNT'] = QBConstants::TRNS_ACCNT;//null; //need a setting for this
		$params['TAXABLE'] = 'Y';
		return $params;
	}

	/**
	Creates an inventory line with the given name, description, price, and type.
	@param string $name The name to associate with the line.
	@param string $itemType One of the item types provided in the QuickBooks IIF documentation.
	@param string $description The description to associate with the line.
	@param float $price The unit price of the item.	
	@return array THe resultant array object.
	*/
	protected function createLine($name, $itemType, $description, $price,  $accnt){
		$params = $this->initInvItem();
		$params['NAME'] = $name;
		$params['INVITEMTYPE'] = $itemType;
		$params['DESC'] = $description;
		$params['PURCHASEDESC'] = $description;
		$params['PRICE'] = $price;
		$params['ACCNT'] = $accnt;
		return $params;
	}

	/**
	@return array An array containing all INVITEM records associated with the decorated class.
	*/
	public function getRecords(){
		if($this->lines === null){
			$this->lines = $this->createRecords();
		}
		return $this->lines;
	}

	/**
	Constructs the array of INVITEM records associated with the decorated class. This function need not handle caching.
	@return array An array of INVITEM records, with field names as returned by initInvItem.
	*/
	protected abstract function createRecords();	
}