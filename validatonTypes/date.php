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




class validatonTypes_date implements iValidationType {

	public function getId(){
		return 'DATE';
	}
	public function getMessage(){
		return 'That was not a valid UK Date Format';
	}

	// CLIENT
	public function getMethodName(){
		return 'date';
	}
	public function getCustomMethod(){
		return FALSE;
	}

	// SERVER
	public function run($var, $option = null){
		// uk date format (dd/mm/yyyy)
		$re = '/^[0-3][0-9][-/][0-1][0-9][-/][1-2][0-9]{3}$/';
		return preg_match($re, $var);
	}

}
?>