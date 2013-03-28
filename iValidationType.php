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




interface iValidationType {

	// returns the correct validationConfig array key
	public function getId();

	// default validation failed message
	public function getMessage();

	/*
	 * CLIENT
	 */

	// means you can call a jQuery method that uses a different name, e.g. STR-INT calls digits()
	// return FALSE for no client side validation
	public function getMethodName();

	// custom jQuery validation method
	// return FALSE for no custom method
	public function getCustomMethod();

	/*
	 * SERVER
	 */

	// PHP validation
	public function run($var, $option = true);

}


?>