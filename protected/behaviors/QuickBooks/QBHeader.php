<?php
/*
 * QBHeader composes records for export to QuickBooks in a record property.
 * header records identify the version of QuickBooks in order for QuickBooks to understand the rest of the export data
 * !HDR	PROD	VER	REL	IIFVER	DATE	TIME
 * HDR	QuickBooks Pro 6.0 for Macintosh	Version 6.0	Release R2	1	12/3/12	1354535565
 * 
 * */
class QBHeader extends QBTransactionLine{
	
	/**
	 Creates an array describing all fields in the HDR record type of QuickBooks IIF.
	 This makes the IIF file more human readible & understandable.
	 @return array The array, with all HDR elements.
	 */
	protected function createDocumentation(){
		
		$record['HDR'] = '!HDR';
		$record['PROD']='PROD';
		$record['VER']='VER';
		$record['REL']='REL';
		$record['IIFVER']='IIFVER';
		$record['DATE'] ='DATE';
		$record['TIME']='TIME';
		return $record;
	}

	/**
	 Creates an array with all fields in the HDR record type of QuickBooks IIF.
	 @return array The array, with all elements set. Eventually this will pull from a company's individual settings.
	 */
	protected function createHeader(){
		
		$record['HDR'] = 'HDR';
		$record['PROD']= QBConstants::QB_PROD;
		$record['VER']= QBConstants::QB_VERSION;
		$record['REL']= QBConstants::QB_REL;
		$record['IIFVER']= QBConstants::QB_IIFVER;
		$record['DATE'] = QBConstants::QB_DATE;
		$record['TIME']=QBConstants::QB_TIME;
		return $record;
	}
	
	
	protected function createRecords(){

		$lines = array();
		$lines[] = $this->createDocumentation();
		$lines[] = $this->createHeader();
		return $lines;
	}

}