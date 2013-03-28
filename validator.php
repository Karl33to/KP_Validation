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




class validator {

	protected $arrayXpathFactory;
	protected $errors = array();
	protected $validationTypes = array(); // array of validation objects

	public function __construct() {

		// add the validation rules
		$this->addValidationType( new validatonTypes_alphanumeric() );
		$this->addValidationType( new validatonTypes_boolean() );
		$this->addValidationType( new validatonTypes_date() );
		$this->addValidationType( new validatonTypes_email() );
		$this->addValidationType( new validatonTypes_int() );
		$this->addValidationType( new validatonTypes_ip() );
		$this->addValidationType( new validatonTypes_maxlength() );
		$this->addValidationType( new validatonTypes_minlength() );
		$this->addValidationType( new validatonTypes_numeric() );
		$this->addValidationType( new validatonTypes_required() );
		$this->addValidationType( new validatonTypes_strBoolean() );
		$this->addValidationType( new validatonTypes_strInt() );
		$this->addValidationType( new validatonTypes_strNoHtml() );
		$this->addValidationType( new validatonTypes_url() );
		$this->addValidationType( new validatonTypes_postcode() );

	}


	private function addValidationType( iValidationType $valType ){

		$valId = $valType->getId();
		$valId = strtoupper($valId);
		$this->validationTypes[$valId] = $valType;

	}


	public function listValidationTypes(){

		return array_keys( $this->validationTypes );

	}

}

?>