<?php
/*
 * Copyright 2012 Karl Payne (www.karlpayne.co.uk)
 *
 * This file is part of KP-Validation
 *
 * KP-Validation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * KP-Validation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KP-Validation.  If not, see <http://www.gnu.org/licenses/>.
 */




class validatorJsWriter extends validator implements iValidatorWriter {

	private function javascriptValueToString( $var ) {

		if (is_bool($var)) {
			// convert booleans to a string
			if ($var) {
				return 'true';
			} else {
				return 'false';
			}
		} else if (is_int($var)) {
			// leave integers as they are
			return $var;
		} else {
			// everything else needs to be quoted
			return "'" . $var . "'";
		}

	}


	private function formatJsObject( $var, $indent ) {

		$str = '{'."\n";
		foreach($var as $key => $value){
			$str .= str_repeat("\t", $indent) . $key . ': ';
			if( is_array($value) ){
				//$str .= '{'."\n";
				$str .= $this->formatJsObject( $value, $indent + 1 );
				//$str .= str_repeat("\t", $indent) . '}, '."\n";
			} else {
				$str .= $value;
			}
			$str .= ", \n";
		}
		$str = substr($str, 0, -3) . "\n";
		$str .= str_repeat("\t", $indent) . '}';
		// $str = substr($str, 0, -3) . "\n";
		// trim trailing comma
		return $str;

	}

	public function write( validatorConfig $valConfig, $ruleSetId ) {

		if( !is_string($ruleSetId) ){
			logger::addMessage('Wrong argument types passed to validatorJsWriter::write()');
			return FALSE;
		}

		if( !$ruleSet = $valConfig->sets[$ruleSetId] ){
			logger::addMessage('Rule set ['.$ruleSetId.'] does not exist.');
			return FALSE;
		}

		$str = '<script src="/js/jquery/jquery-validation-1.8.1/jquery.validate.min.js"></script>' . "\n";
		$str .= '<script type="text/javascript">' . "\n";
		$str .= '$(document).ready(function() {' . "\n";
		$str .= '	$("#' . $valConfig->formId . '").validate(';

		// used later on
		$strComplexFields = "\n";
		$strCustomMethods = "\n";

		foreach( $ruleSet as $ruleId ) {

			if( !$rule = $valConfig->rules[$ruleId] ){
				logger::addMessage('The rule: ['.$ruleId.'] was not defined in ruleSet: ['.$ruleSetId.']');
				return FALSE;
			}

			// for easier referencing
			$fieldName = $rule['fieldName'];
			// uppercased so that the rule keys dont have to match the message keys, or even the rule->getId()'s
			$fieldRules = array_change_key_case($rule['rules'], CASE_UPPER);
			// messages are optional - worst case we use the one from the validationType class
			if( isset($rule['messages']) ){
				$fieldMessages = array_change_key_case($rule['messages'], CASE_UPPER);
			} else {
				$fieldMessages = array();
			}

			$isWildcard = false;
			if( strpos($fieldName, '*') ){
				// wildcard
				$isWildcard = true;
				// convert the name to a regex
				$fieldRef = str_replace( array('[', ']', '*'), array('\[', '\]', '(.*?)'), $fieldName);
			} elseif( strpos($fieldName, '[') ){
				// complex field names (arrays) and those containing hypens need to be enclosed in quotes for Javascript to understand them
				$fieldRef = '"'.$fieldName.'"';
			} else {
				// normal
				$fieldRef = $fieldName;
			}

			$arrRules = array();
			$arrMessages = array();

			foreach ($fieldRules as $valType => $valOption) {

				// check it's a vaild validation type
				if( !isset($this->validationTypes[strtoupper($valType)]) ){
					logger::addMessage('Validation type ['.$valType.'] not found.');
					continue;
				}
				$thisValidationType = $this->validationTypes[strtoupper($valType)];

				// check its a server side validation function
				$methodName = $thisValidationType->getMethodName();
				if( $methodName == FALSE ){
					logger::addMessage('Skipped validation of ['.$fieldName.'] as a ['.$valType.'] as this is not a valid client side validation method.');
					continue;
				}

				$arrRules[$methodName] = $this->javascriptValueToString($valOption);

				if( isset($fieldMessages[$valType]) ){
					// custom error as specified in the config
					$msg = $fieldMessages[$valType];
				} else {
					// generic type specific error
					$msg = $thisValidationType->getMessage();
				}

				$arrMessages[$methodName] = '"' . $msg . '"';

				// if this is a special JS validation method, retreive the JS code 
				// and it will be added into the page later on
				if($customMethod = $thisValidationType->getCustomMethod() ){
					$strCustomMethods .= $customMethod ."\n";
				}

			}

			if( $isWildcard ){

				if( count($arrRules) > 0 ){

					// silly syntax
					$arrComplexFields = $arrRules;
					$arrComplexFields['messages'] = $arrMessages;

					$strComplexFields .= '	$("#' . $valConfig->formId . ' input").filter(function(){'."\n";
					$strComplexFields .= '			return $(this).attr("name").match(/' . $fieldRef . '/);'."\n";
					$strComplexFields .= '		})'."\n";
					$strComplexFields .= '		.each( function() {'."\n";
					$strComplexFields .= '			$(this).rules("add", ';
					$strComplexFields .= $this->formatJsObject( $arrComplexFields, 4 );
					$strComplexFields .= ');'."\n";
					$strComplexFields .= '		});'."\n";

				}

			} else {

				$arrNormalFields['rules'][$fieldRef] = $arrRules;
				$arrNormalFields['messages'][$fieldRef] = $arrMessages;

			}

		}

		// as of jQuery validation v1.9 - hidden fields arent validated by default, so override this setting
		$arrNormalFields['ignore'] = '[]';

		$str .= $this->formatJsObject( $arrNormalFields, 2 );
		$str .= ");\n"; // end of $("#abcdef").validate({

		$str .= $strComplexFields;

		$str .= "});\n"; // end of $(document).ready(function() {
		$str .= $strCustomMethods;

		$str .= '</script>';
		return $str;

	}

}

?>