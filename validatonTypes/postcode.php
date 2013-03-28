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




class validatonTypes_postcode implements iValidationType {

	public function getId(){
		return 'POSTCODE';
	}
	public function getMessage(){
		return 'That was not a valid UK Postcode format';
	}

	// CLIENT
	public function getMethodName(){
		return 'postcode';
	}
	public function getCustomMethod(){
		$str = 'jQuery.validator.addMethod("postcode", function(value, element){ '."\n";
		$str .= '	return this.optional(element) || /^[a-zA-Z]{1,2}[0-9]{1,2}\s[0-9]{1}[a-zA-Z]{2}$/.test(value);'."\n";
		$str .= '});'."\n";
		return $str;
	}

	// SERVER
	public function run($var, $option = null){
		// uk postcode format (AA1 1AA, A1 1AA, AA11 1AA)
		$re = '/^[A-Z]{1,2}[0-9]{1,2}\s[0-9]{1}[A-Z]{3}$/i';
		// TODO add in northern ireland format
		return preg_match($re, $var);
	}

}

?>