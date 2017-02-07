<?php
class Formatter extends CFormatter {	
	public function formatCurrency($value){
		return Yii::app()->numberFormatter->formatCurrency($value, 'USD');
	}

	public function formatPercentage($value){
		return Yii::app()->numberFormatter->formatPercentage($value, 'USD');
	}
	
	public function formatLookup($value){
		return Lookup::getText($value);
	}

	public function formatDate($value){
		return Yii::app()->dateFormatter->format('EEE, MM/dd/yy', strtotime($value));
	}
}