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




class validatonTypes_ip implements iValidationType {

	public function getId(){
		return 'IP';
	}
	public function getMessage(){
		return 'That was not a valid IP address';
	}

	// CLIENT
	public function getMethodName(){
		return FALSE;
	}
	public function getCustomMethod(){
		return FALSE;
	}

	// SERVER
	public function run($var, $option = null){
		$filtered = filter_var($var, FILTER_VALIDATE_IP);
		if($var === $filtered){
			return true;
		} else {
			return false;
		}
	}

}

?>