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




class validatorValidate extends validator {

	protected $arrayXpathFactory;

	public function __construct($arrayXpathFactory) {

		parent::__construct();
		$this->arrayXpathFactory = $arrayXpathFactory;

	}


	private function addError($field, $msg) {

		$this->errors[$field] = $msg;

	}


	public function getErrors() {

		return $this->errors;

	}


	/*
	 * makes sure there is a matching $rules
	 * for every item in the $array
	 */
	private function checkRules($array, $rules) {

		$passed = TRUE;

		foreach ($array as $key => $value) {
			if (!array_key_exists($key, $rules)) {
				$passed = FALSE;
				$this->addError($key, 'No rule found for this var.');
			}
		}

		return $passed;

	}


	/*
	 * formats the error message, by replacing a placeholder with the option
	 */
	private function parseMessage($message, $option = true) {

		if (is_string($message) && is_bool($option)) {
			return $message;
		}
		return str_replace('{0}', $option, $message);

	}


	/*
	 * Validates from a validation object
	 * (validation objects are also used for client side javascript validation)
	 */
	public function validateConfig(validatorConfig $valConfig, array $data = array(), $ruleSetId) {

		if( !is_array($data) && !is_string($ruleSetId) ){
			logger::addMessage('Wrong argument types passed to validateConfig()');
			return FALSE;
		}

		if( !$ruleSet = $valConfig->sets[$ruleSetId] ){
			logger::addMessage('Rule set ['.$ruleSetId.'] does not exist.');
			return FALSE;
		}

		$retVal = TRUE; // fallback


		// TODO - is it better to loop through the validations first, or the values?
		foreach( $ruleSet as $ruleId ) {

			if( !$rule = $valConfig->rules[$ruleId] ){
				logger::addMessage('The rule ['.$ruleId.'] was not defined in ['.$ruleSet.']');
				return FALSE;
			}

			// for easier referencing
			$fieldName = $rule['fieldName'];
			// uppercased so that the rule keys dont have to match the message keys, or even the rule->getId()'s
			$fieldRules = array_change_key_case($rule['rules'], CASE_UPPER); 

			// should be optional, falback onto the type message
			if( isset($rule['messages']) ){
				$fieldMessages = array_change_key_case($rule['messages'], CASE_UPPER);
			} else {
				$fieldMessages = array();
			}

			// put the data into an array stucture for easy loops
			if(strpos($fieldName, '[')!== FALSE){

				// field names with square brackets in them refer to 
				// sub-dimensions of the $data array
				// so convert them from a string: alternativeUrls[current][url][]

				// to a xpath: alternativeUrls/current/url
				$xpath = str_replace(array('[', ']'), array('/', ''), $fieldName);

				// and look up their values
				$arrayXpath = $this->arrayXpathFactory->createInstance($data);
				$values = $arrayXpath->filter($xpath);

			} else {
				// pop single values into an array so we can do a foreach loop on them

				// if the field was missing, it may be an un-checked checkbox
				// so don't try and reference it as it will error
				if( isset($data[$fieldName]) ){
					$values = array( $data[$fieldName] );
				} else {
					// assume missing items were un-checked checkboxes
					logger::addMessage('The field ['.$fieldName.'] was missing from the data - using NULL as its value.');
					$values = array( NULL );
				}

			}

			// loop through the data for this field
			foreach($values as $value){

				// convert simple rules to array format, with the default option of [true]
				if (!is_array($fieldRules)) {
					$fieldRules = array($fieldRules => true);
				}

				// skip certain non-required fields
				// non-required empty strings (aka optional)
				if( $value === '' && !array_key_exists('REQUIRED', $fieldRules) ){
					logger::addMessage('Skipped validation of ['.$fieldName.'] as it was an un-required empty string.');
					continue;
				}
				// non-required and null (aka unchecked checkbox)
				// NOTE: this could also be a text input that is missing from the form - but we'll take that risk
				if( is_null($value) && !array_key_exists('REQUIRED', $fieldRules) ){
					logger::addMessage('Skipped validation of ['.$fieldName.'] as its value was NULL and it wasnt required.');
					continue;
				}

				// run all the applicable rules against this field
				// TODO: the rules will be in alphabetical order!
				foreach ($fieldRules as $valType => $valOption) {

					$valType = strtoupper($valType);

					// check its a valid server side validation function
					if( !isset($this->validationTypes[$valType]) || !method_exists($this->validationTypes[$valType], 'run') ){
						logger::addMessage('Skipped validation of ['.$fieldName.'] as a ['.$valType.'] as this is not a valid server side validation method.');
						continue;
					}

					if (!$this->isValid($value, $valType, $valOption)) {
						$retVal = FALSE;
						if (array_key_exists($valType, $fieldMessages)) {
							// custom error as specified in the config
							$msg = $this->parseMessage($fieldMessages[$valType], $valOption);
							$this->addError($fieldName, $msg);
						} else {
							// generic type specific error
							$msg = $this->validationTypes[$valType]->getMessage();
							$this->addError($fieldName, $msg);
						}
					}

				}

			}

		}

		return $retVal;

	}


	/**
	 *
	 * sanitizes an array of items according to the $rules
	 * sanitations will be standard of type string, but can also be specified.
	 * returns an array of the sanitized items
	 *
	 * careful, cos if there are items which arent selected for sanitizing
	 * they will still be returned in the sanitized array
	 */
	public function sanitizeArray($array, $rules) {

		foreach ($array as $key => $val) {
			$array[$key] = $this->sanitizeItem($val, $rules[$key]);
		}

		return($items);

	}


	/*
	 * sanitize a single var according to $type.
	 * and return the sanitised variable
	 * 
	 * sanitations can be useful when we want to accept anything 
	 * the user enters but only store whats safe/required
	 * 
	 * for example a telephone number might get entered with hypens 
	 * and brackets, but it would be converted to an int
	 *
	 * it would be vary rare to do both a validation and a sanitization
	 * as either routine will leave the data in a valid format
	 */
	public function sanitizeItem($var, $type) {

		$flags = NULL;
		switch ($type) {
			case 'URL':
				$filter = FILTER_SANITIZE_URL;
				break;
			case 'INT':
				$filter = FILTER_SANITIZE_NUMBER_INT;
				break;
			case 'FLOAT':
				$filter = FILTER_SANITIZE_NUMBER_FLOAT;
				$flags = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
				break;
			case 'EMAIL':
				$var = substr($var, 0, 254);
				$filter = FILTER_SANITIZE_EMAIL;
				break;
			case 'STRING':
			default:
				$filter = FILTER_SANITIZE_STRING;
				$flags = FILTER_FLAG_NO_ENCODE_QUOTES;
				break;
		}

		$output = filter_var($var, $filter, $flags);

		return($output);

	}

	/*
	 * Validates a single var according to $type.
	 *
	 * always returns true or false
	 * so that it can be used in the validate() fn
	 */
	public function isValid($var, $type, $option = true) {

		$pass = FALSE;
		$type = strtoupper($type);

		if( array_key_exists($type, $this->validationTypes) ){
			return $this->validationTypes[$type]->run($var, $option);
		} else {
			logger::addMessage('Validation type not found');
			return FALSE;
		}

	}

}

?>