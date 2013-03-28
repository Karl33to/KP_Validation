<?php
/*
 * Copyright 2012 Karl Payne (www.karlpayne.co.uk)
 *
 * This file is part of KP-ARRAY-XPATH
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



class arrayXpath {

	protected $pathStack = array();
	protected $arrData;
	public $arrPaths;

	public function __construct(array $arrData) {
		$this->arrData = $arrData;
		$this->flatten($arrData);
	}

	protected function findValue($path){

		$arrPath = explode('/', $path);
		$retVal = $this->arrData;
		foreach($arrPath as $key){
			if(isset($retVal[$key])){
				$retVal = $retVal[$key];
			} else {
				return FALSE;
			}
		}
		return $retVal;

	}

	public function filter($xPath = ''){

		// convert the xpath into a regex
		$pattern = $xPath;
		$pattern = str_replace('/', '\/', $pattern);
		$pattern = str_replace('*', '(.*?)', $pattern);
		$pattern = '/'.$pattern.'/';

		$filteredPaths = array();

		foreach($this->arrPaths as $value){
			if( preg_match($pattern, $value) ){
				$filteredPaths[$value] = $this->findValue($value);
			}
		}

		return $filteredPaths;

	}

	protected function flatten($arrData, $realStack = array()){

		// take the first item in the stack
		$currentKey = array_shift($this->pathStack);

		foreach($arrData as $key => $value){

			// put the real key into the realStack
			array_push($realStack, $key);
			// and build a real path from it
			$realPath = implode('/',$realStack);

			if(is_array($value)){
				// recurse
				$this->arrPaths[] = $realPath;

				// recurse into the new array
				$this->flatten($value, $realStack);

			} else {
				// do nothing, we want to keep this value
				$this->arrPaths[] = $realPath;
			}

			// take the current key off the stack
			array_pop($realStack);

		}
		return $arrData;
	}

}

?>