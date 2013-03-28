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




final class valExampleForm extends validatorConfig {

	public function __construct() {

		$this->formId = 'commentForm';

		$this->sets = array(
			'TEST' => array('id', 'name', 'email', 'comment', 'postcode', 'subscribe', 'terms'),
		);

		$this->rules = array(
			'id' => array(
					'fieldName' => 'id',
					'rules' => array(
						'required' => true,
						'STR-INT' => true,
					),
					'messages' => array(
						'required' => 'id is required',
						'STR-INT' => 'id must be numeric',
					),
				),
			'name' => array(
					'fieldName' => 'name',
					'rules' => array(
						'required' => true,
						'STR-NOHTML' => true,
						'minlength' => 5,
						'maxlength' => 150,
					),
					'messages' => array(
						'required' => 'name is required',
						'STR-NOHTML' => 'HTML code is not accepted',
						'minlength' => 'Please enter a name LONGER than the minimum of {0} characters',
						'maxlength' => 'Please enter a name SHORTER than the maximum of {0} characters',
					),
				),
			'email' => array(
					'fieldName' => 'email',
					'rules' => array(
						'email' => true,
					),
					'messages' => array(
						'email' => 'email address not valid',
					),
				),
			'comment' => array(
					'fieldName' => 'comment',
					'rules' => array(
						'required' => true,
						'STR-NOHTML' => true,
					),
					'messages' => array(
						'required' => 'comment is required',
						'STR-NOHTML' => 'HTML code is not accepted',
					),
				),
			'postcode' => array(
					'fieldName' => 'postcode',
					'rules' => array(
						'required' => true,
						'POSTCODE' => true,
					),
					'messages' => array(
						'required' => 'postcode is required',
						'POSTCODE' => 'Ooh that dont look right, please enter a proper postcode.',
					),
				),
			'subscribe' => array(
					'fieldName' => 'subscribe',
					'rules' => array(
						'STR-BOOLEAN' => true,
					),
					'messages' => array(
						'STR-BOOLEAN' => 'must be a BOOL',
					),
				),
			'terms' => array(
					'fieldName' => 'terms',
					'rules' => array(
						'required' => true,
						'STR-BOOLEAN' => true,
					),
					'messages' => array(
						'required' => 'You must agree to the Terms and conditions.',
						'STR-BOOLEAN' => 'Terms must be a STR-BOOL',
					),
				),
		);

	}

}

?>