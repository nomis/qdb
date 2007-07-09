<?
/*
	Copyright ©2007 Simon Arlott

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License v3
	as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.


	$Id$
*/

// http://uk.php.net/manual/en/security.magicquotes.disabling.php
if (get_magic_quotes_gpc()) {
	function undoMagicQuotes($array, $topLevel=true) {
		$newArray = array();
		foreach($array as $key => $value) {
			if (!$topLevel) {
				$key = stripslashes($key);
			}
			if (is_array($value)) {
				$newArray[$key] = undoMagicQuotes($value, false);
			} else {
				$newArray[$key] = stripslashes($value);
			}
		}
		return $newArray;
	 }
	 $_GET = undoMagicQuotes($_GET);
	 $_POST = undoMagicQuotes($_POST);
	 $_COOKIE = undoMagicQuotes($_COOKIE);
	 $_REQUEST = undoMagicQuotes($_REQUEST);
}
?>
