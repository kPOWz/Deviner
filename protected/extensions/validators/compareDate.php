<?php
/**
 * ACompareDateValidator class file.
 *
 * @author Katherine Zeman <karrie.zeman@gmail.com>
 * @link http://www.github.com/kpowz
 * @copyright Copyright &copy; 2013 Fluff Enterprises LLC
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 */

/**
 * ACompareDateValidator compares the specified date attribute value 
 * with another date value and validates if they are equal by default (=)
 * or returns the result of the specified operator.
 *
 * The date to compare with can be another attribute value
 * (specified via {@link compareAttribute}) or a constant (specified via
 * {@link compareValue}. When both are specified, the latter takes
 * precedence. If neither is specified, the attribute will be compared
 * with another attribute whose name is by appending "_repeat" to the source
 * attribute name.
 *
 * The date comparison can be either {@link strict} or not. (false)
 *
 * The date comparison can allow empty or null {@link allowEmpty} attribute value. (false)
 * 
 * The date comparison can specify a CDateTimeParser-friendly {@link CDateTimeParser}
 * date format reflecting the date attribute and date comparison value format.
 *
 * @author Katherine Zeman <karrie.zeman@gmail.com>
 * @package system.validators
 * @since 1.0
 */
class compareDate extends CValidator
{
	/**
	 * @var string the name of the date attribute to be compared with
	 */
	public $compareAttribute;
	/**
	 * @var string the constant date value to be compared with
	 */
	public $compareValue;
	/**
	 * @var boolean whether the comparison is strict (both value and type must be the same.)
	 * Defaults to false.
	 */
	public $strict=false;
	/**
	 * @var boolean whether the attribute value can be null or empty. Defaults to false.
	 * If this is true, it means the attribute is considered valid when it is empty.
	 */
	public $allowEmpty=false;
	/**
	 * @var string the operator for comparison. Defaults to '='.
	 * The followings are valid operators:
	 * <ul>
	 * <li>'=' or '==': validates to see if the two values are equal. If {@link strict} is true, the comparison
	 * will be done in strict mode (i.e. checking value type as well).</li>
	 * <li>'!=': validates to see if the two values are NOT equal. If {@link strict} is true, the comparison
	 * will be done in strict mode (i.e. checking value type as well).</li>
	 * <li>'>': validates to see if the value being validated is greater than the value being compared with.</li>
	 * <li>'>=': validates to see if the value being validated is greater than or equal to the value being compared with.</li>
	 * <li>'<': validates to see if the value being validated is less than the value being compared with.</li>
	 * <li>'<=': validates to see if the value being validated is less than or equal to the value being compared with.</li>
	 * </ul>
	 * @since 1.0.8
	 */
	public $operator='=';
	
	/**
	 * @var string CDateTimeParser-friendly date format expected to reflect the attribute value and comparison value. 
	 * Defaults to 'MM-dd-yyyy'.
	 */
	public $dateFormat='yyyy-MM-dd';

	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object,$attribute)
	{
		// extract the attribute value from it's model object & prep it for timestamp comparison
		// hang on to orginal value for error message purposes
		$originalValue=$object->$attribute;
		Yii::log('date value before timestamp cast :  '.strval($originalValue),
			CLogger::LEVEL_TRACE, 'application.extenstions.validators.aCompareDateValidator');
		$value=CDateTimeParser::parse($originalValue,$this->dateFormat);
		Yii::log('date value after timestamp cast :  '.strval($value), 
			CLogger::LEVEL_TRACE, 'application.extenstions.validators.aCompareDateValidator');
			
		if($this->allowEmpty && ($this->isEmpty($value) || $this->isEmpty($originalValue)))
			return;
		
		if($this->compareValue!==null)
			$compareTo=$compareValue=$this->compareValue;
		else
		{
			$compareAttribute=$this->compareAttribute===null ? $attribute.'_repeat' : $this->compareAttribute;
			$originalCompareValue=$object->$compareAttribute;
			$compareTo=$object->getAttributeLabel($compareAttribute);
		}
		
		// prep the comparisson value for timestamp comparison
		// hang on to orginal compare value for error message purposes
		Yii::log('compare date value before timestamp cast :  '.strval($originalCompareValue),
			CLogger::LEVEL_TRACE, 'application.extenstions.validators.aCompareDateValidator');
		$compareValue=CDateTimeParser::parse($originalCompareValue, $this->dateFormat);
		Yii::log('compare date value after timestamp cast :  '.strval($compareValue), 
			CLogger::LEVEL_TRACE, 'application.extensions.validators.aCompareDateValidator');

		//TODO: When a compareAttribute was specified, messages including the orginalCompareValue 
		// may want to instead specify the object's current value of the compareAttribute rather than 
		// the unsaved/model's compareAttribute value. 
		// For example, when the compareAttribute's value has changed too in an update scenario the error message (changed value)
		// won't match the value in the compareAttribute's input (saved value) on the form that failed validation 
		switch($this->operator)
		{
			case '=':
			case '==':
				if(($this->strict && $value!==$compareValue) || (!$this->strict && $value!=$compareValue))
				{
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be repeated exactly.');
					$this->addError($object,$attribute,$message,array('{compareAttribute}'=>$compareTo));
				}
				break;
			case '!=':
				if(($this->strict && $value===$compareValue) || (!$this->strict && $value==$compareValue))
				{
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must not be equal to "{originalCompareValue}".');
					$this->addError($object,$attribute,$message,array('{compareAttribute}'=>$compareTo,'{originalCompareValue}'=>$originalCompareValue));
				}
				break;
			case '>':
				if($value<=$compareValue)
				{
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be greater than "{originalCompareValue}".');
					$this->addError($object,$attribute,$message,array('{compareAttribute}'=>$compareTo,'{originalCompareValue}'=>$originalCompareValue));
				}
				break;
			case '>=':
				if($value<$compareValue)
				{
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be greater than or equal to "{originalCompareValue}".');
					$this->addError($object,$attribute,$message,array('{compareAttribute}'=>$compareTo,'{originalCompareValue}'=>$originalCompareValue));
				}
				break;
			case '<':
				if($value>=$compareValue)
				{
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be less than "{originalCompareValue}".');
					$this->addError($object,$attribute,$message,array('{compareAttribute}'=>$compareTo,'{originalCompareValue}'=>$originalCompareValue));
				}
				break;
			case '<=':
				if($value>$compareValue)
				{
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be less than or equal to "{originalCompareValue}".');
					$this->addError($object,$attribute,$message,array('{compareAttribute}'=>$compareTo,'{originalCompareValue}'=>$originalCompareValue));
				}
				break;
			default:
				throw new CException(Yii::t('yii','Invalid operator "{operator}".',array('{operator}'=>$this->operator)));
		}
		
		if($this->message !== null){
			//$object->$attribute= '';
			//$object->$compareAttribute = 
		}
	}

	/**
	 * Returns the JavaScript needed for performing client-side validation.
	 * @param CModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 * @return string the client-side validation script.
	 * @see CActiveForm::enableClientValidation
	 * @since 1.1.7
	 */
	public function clientValidateAttribute($object,$attribute)
	{
		if($this->compareValue !== null)
		{
			$compareTo=$this->compareValue;
			$compareValue=CJSON::encode($this->compareValue);
		}
		else
		{
			$compareAttribute=$this->compareAttribute === null ? $attribute . '_repeat' : $this->compareAttribute;
			$compareValue="\$('#" . (CHtml::activeId($object, $compareAttribute)) . "').val()";
			$compareTo=$object->getAttributeLabel($compareAttribute);
		}

		$message=$this->message;
		switch($this->operator)
		{
			case '=':
			case '==':
				if($message===null)
					$message=Yii::t('yii','{attribute} must be repeated exactly.');
				$condition='value!='.$compareValue;
				break;
			case '!=':
				if($message===null)
					$message=Yii::t('yii','{attribute} must not be equal to "{compareValue}".');
				$condition='value=='.$compareValue;
				break;
			case '>':
				if($message===null)
					$message=Yii::t('yii','{attribute} must be greater than "{compareValue}".');
				$condition='value<='.$compareValue;
				break;
			case '>=':
				if($message===null)
					$message=Yii::t('yii','{attribute} must be greater than or equal to "{compareValue}".');
				$condition='value<'.$compareValue;
				break;
			case '<':
				if($message===null)
					$message=Yii::t('yii','{attribute} must be less than "{compareValue}".');
				$condition='value>='.$compareValue;
				break;
			case '<=':
				if($message===null)
					$message=Yii::t('yii','{attribute} must be less than or equal to "{compareValue}".');
				$condition='value>'.$compareValue;
				break;
			default:
				throw new CException(Yii::t('yii','Invalid operator "{operator}".',array('{operator}'=>$this->operator)));
		}

		$message=strtr($message,array(
			'{attribute}'=>$object->getAttributeLabel($attribute),
			'{compareValue}'=>$compareTo,
		));

		return "
if(".($this->allowEmpty ? "$.trim(value)!='' && " : '').$condition.") {
	messages.push(".CJSON::encode($message).");
}
";
	}
}
