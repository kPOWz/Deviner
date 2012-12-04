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
	const PRINTING_ACCNT = "5010-Sales:Custom Screenprinting Sales:Printing";
	const SETUP_ACCNT = "5010-Sales:Custom Screenprinting Sales:Setup Time";
	const RUSH_ACCNT = "5010-Sales:Custom Screenprinting Sales:Rush Charges";
	const ART_ACCNT = "5010-Sales:Custom Screenprinting Sales:Artwork";
	const TAX_ACCNT = "Sales Tax Payable";
	
	//Quickbooks Invoice/DOCNUM numbering prefix
	const QB_DOCNUM_PREFIX = "GUS-I-";
	
	//Quickbooks version constants
	const QB_PROD = 'QuickBooks Pro 6.0 for Macintosh';
	const QB_VERSION = 'Version 6.0';
	const QB_REL = 'Release R2';
	const QB_IIFVER = '1';
	const QB_DATE = '12/3/12';
	const QB_TIME = '1354535565';
}