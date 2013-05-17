<?php
/*Stores constants for all quickbooks operations.*/
class QBConstants {
	/* original
	const TRNS_ACCNT = "Accounts Receivable";
	const PRINTING_ACCNT = "Sales:Custom Screenprinting:Printing";
	const SETUP_ACCNT = "Sales:Custom Screenprinting:Setup Time";
	const RUSH_ACCNT = "Sales:Custom Screenprinting:Rush Charges";
	const ART_ACCNT = "Sales:Custom Screenprinting:Artwork";
	const TAX_ACCNT = "Sales Tax Payable";
	*/
	
	
	//<-----new------>
	
	//Quickbooks accounts constants
	
	//Master Income account linked to both the Invoice TRNS data & Inventory INVITEM data
	//TRNS  - (Required) The name of the account assigned to the transaction.
	//INVITEM -  (Required) The name of the income account you use to track sales of the item. The type of this account should be INC.
	const TRNS_ACCNT = "5010-Sales"; 
	
	//(Required) The income or expense account to which you assigned the amount on the distribution line.
	const SHIPPING_ACCNT = "5010-Sales:Shipping";
	const PRINTING_ACCNT = "5010-Sales:Custom Screenprinting Sales";
	const SETUP_ACCNT = "5010-Sales:Custom Screenprinting Sales:Setup Time";
	const RUSH_ACCNT = "5010-Sales:Custom Screenprinting Sales:Rush Charges";
	const ART_ACCNT = "5010-Sales:Custom Screenprinting Sales:Artwork";
	const TAX_ACCNT = "Sales Tax Payable";
	
	//Quickbooks Invoice/DOCNUM numbering prefix
	const QB_DOCNUM_PREFIX = "GUS-I-";
	
	//
	const DESCRIPTION_SALES_TAX = 'Sales Tax';
	const DESCRIPTION_RUSH = 'Fee for accelerated handling';
	const DESCRIPTION_ART_CHARGE = 'Fee for design work';
	const DESCRIPTION_SETUP_TIME = 'Fee for setup (waived for larger orders)';
	
	//
	const NAME_SALES_TAX = 'Sales Tax';
	//const NAME_RUSH = '';
	const NAME_ART_CHARGE = 'Artwork Charge';
	const NAME_SETUP_TIME = 'Setup Time';
	
	
	//Quickbooks version constants
	//TODO: make this configuraable in gus dashboard/settings
	//HDR	QuickBooks 2012 for Macintosh	Version 13.0.11	Release R12	1	12/7/12	1354915827
	const QB_PROD = 'QuickBooks 2012 for Macintosh';
	const QB_VERSION = 'Version 13.0.11';
	const QB_REL = 'Release R2';
	const QB_IIFVER = '1';
	const QB_DATE = '12/7/12';
	const QB_TIME = '1354915827';
	/*
	const QB_PROD = 'QuickBooks Pro 6.0 for Macintosh';
	const QB_VERSION = 'Version 6.0';
	const QB_REL = 'Release R2';
	const QB_IIFVER = '1';
	const QB_DATE = '12/3/12';
	const QB_TIME = '1354535565';*/
}