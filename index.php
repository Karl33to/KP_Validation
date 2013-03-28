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




// a little bit of setup
error_reporting(E_ALL ^ E_NOTICE);
define('DS', DIRECTORY_SEPARATOR);
function __autoload($className) {
	$filePath = implode(DS, explode('_', $className));
	if (file_exists($filePath . '.php')) {
		require_once($filePath . '.php');
	} else if (file_exists('validationTypes' . DS . $filePath . '.php')) {
		require_once('validationTypes' . DS . $filePath . '.php');
	} else {
		die('Required include file not found: ' . $filePath);
	}
}


$valConfig = new valExampleForm();
?>
<!DOCTYPE html>
<html>
<head>
	<title>VALIDATION TEST</title>
	<link href="styles.css" rel="stylesheet" type="text/css">
	<?php
	if( $_GET['noJs'] !== 'true'){
		?>
		<script type="text/JavaScript" src="js/jquery/jquery-1.8.2.js"></script>
		<script type="text/JavaScript" src="js/jquery/jquery-validation-1.8.1/jquery.validate.min.js"></script>
		<?php
		$valWriter = new validatorJsWriter( );
		echo $valWriter->write($valConfig, 'TEST');
	}
	?>
</head>
<body>
	<?php
	if( isset($_POST['submit']) ){

		$arrayXpathFactory = new arrayXpathFactory();
		$validator = new validatorValidate( $arrayXpathFactory );
		$validationResult = $validator->validateConfig($valConfig, $_POST, 'TEST');
		if ( $validationResult ) {
			echo '<p style="background-color:green;color:#fff;">PHP Validation Passed</p>';
		} else {
			echo '<p style="background-color:red;color:#fff;">PHP Validation Failed</p>';

			$errors = $validator->getErrors();
			foreach( $errors as $field => $message){
				echo '<p class="error">'. $field .': '. $message .'</p>';
			}

		}

	}
	?>
	<p><b>Start Again:</b> <a href="index.php">With Javascript</a> || <a href="index.php?noJs=true">Without Javascript</a> </p>
	<form name="commentForm" id="commentForm" action="index.php<?php
		if( $_GET['noJs'] === 'true'){
			echo '?noJs=true';
		}
		?>" method="post">
		<fieldset>

			<h1>Give the form a test...</h1>

			<input type="hidden" name="id" id="formField_1" value="123">
			<p>
				<label for="formField_2">Name *</label><br />
				<input type="text" name="name" id="formField_2" value="<?php echo $_POST['name']; ?>"><br />
			</p>
			<p>
				<label for="formField_3">Email</label><br />
				<input type="text" name="email" id="formField_3" value="<?php echo $_POST['email']; ?>"><br />
			</p>
			<p>
				<label for="formField_4">Comment *</label><br />
				<textarea name="comment" id="formField_4"><?php echo $_POST['comment']; ?></textarea><br />
			</p>
			<p>
				<label for="formField_5">Postcode *</label><br />
				<input type="text" name="postcode" id="formField_5" value="<?php echo $_POST['postcode']; ?>"><br />
			</p>
			<p>
				<label for="formField_6">Subscribe to something</label><br />
				<input type="checkbox" value="true" name="subscribe" id="formField_6" <?php if( $_POST['subscribe'] === 'true' ){ echo 'checked'; } ?>><br />
			</p>
			<p>
				<label for="formField_7">Terms and Conditions *</label><br />
				<input type="checkbox" value="true" name="terms" id="formField_7" <?php if( $_POST['terms'] === 'true' ){ echo 'checked'; } ?>><br />
			</p>
			<p>
				<input type="submit" name="submit" value="Submit" /><br />
			</p>

			<p>Note: Fields marked with an asterisk (*) are required.</p>

		</fieldset>
	</form>

</body>
</html>