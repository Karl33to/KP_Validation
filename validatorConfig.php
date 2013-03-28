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




/*
 * Validation configs need to be seperate from the model so that 
 * they can be used by the controller and the view in order 
 * to generate the javascript validation
 */
abstract class validatorConfig {

	/* The id of the form (<form id="xx">), 
	 * used for attaching the javascript validation function to the form
	 * enter without a hash
	 */
	public $formId;



	/* 
		sets of rules, used for various validation routines
		will commonly be used for seperating the rules for 
		CREATE, READ, UPDATE and DELETE operations e.g.

		$this->sets = array(
			'CREATE' = array('actualUrl', 'displayUrl', 'preferred'),
			'READ' = array('actualUrl', 'displayUrl', 'preferred'),
			'UPDATE' = array('urlId', 'displayUrl', 'preferred'),
			'DELETE' = array('displayUrl'),
			'someOtherOperation' = array('displayUrl'),
		);
		the array keys are used as the $operation argument
		when calling the config via validator::validateConfig()

		The array values need to refer to the keys of the $rules property
		For ease of readability they are usually the same as the fieldName 
		although there's no requirement to have them the same as form field 
	 */
	public $sets = array();



	/* NOTE: the javascript validation types are case sensetive
	 * PHP types aren't as they get converted to uppercase

		mulitdimensional array as per example:
		$this->rules = array(
			array(
				'fieldName' => 'id',
				'validateOnRead' => true,
				'validateOnUpdate' => true,
				'validateOnDelete' => true,
				'rules' => array(
					'required' => true,
					'STR-INT' => true,
				),
				'messages' => array(
					'required' => 'id is required',
					'STR-INT' => 'id must be numeric',
				),
			),
		);
	 */
	public $rules = array();

}

?>