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




class validatonTypes_strInt implements iValidationType {

	public function getId(){
		return 'STR-INT';
	}
	public function getMessage(){
		return 'That was not a valid Integer String';
	}

	// CLIENT
	public function getMethodName(){
		return 'digits';
	}
	public function getCustomMethod(){
		return FALSE;
	}

	// SERVER
	public function run($var, $option = null){
		// numeric string
		$re = '/^[0-9]+$/';
		return preg_match($re, $var);
		// can't use filter_var as it will return "0" 
		// which gets translated as False when used as a logic test
		// if it is passed "0"
		// also can't use is_numeric() as it allow signs and decimals
	}

}




?>