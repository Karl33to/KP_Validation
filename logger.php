<?php
// FAUX logging class
class logger {
	public static function addMessage($message) {
		echo '<p class="log">LOG: ' . $message . '</p>';
	}
}
?>