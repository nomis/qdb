<?
/*
	Copyright ©2008-2009  Simon Arlott

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU Affero General Public License v3
	as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

function qdb_secure($values) {
	global $user, $config;

	$verifying = TRUE;
	$str = "";

	if (!isset($values["now"])) { $values["now"] = microtime(TRUE); $verifying = FALSE; }
	$values["ip"] = $_SERVER["REMOTE_ADDR"];
	if ($user !== FALSE) {
		$values["user"] = $user->id;
	}
	ksort($values);

	foreach ($values as $name => $value) {
		if ($str != "") { $str .= "&amp;"; }
		if ($value == "" && isset($_REQUEST[$name])) {
			$str .= $name."=".urlencode($_REQUEST[$name]);
		} else {
			$str .= $name."=".urlencode($value);
		}
	}

	$hash = sha1($config['secret'].$str);
	$str .= "&amp;hash=$hash";

	return $verifying ? (isset($_REQUEST["hash"]) && $hash == $_REQUEST["hash"]) : $str;
}


$qdb_qs = array();

function qdb_qs_preserve($name) {
	global $qdb_qs;

	$qdb_qs[] = $name;
}

function qdb_qs() {
	global $qdb_qs;

	$str = "";
	foreach ($qdb_qs as $name) {
		if (isset($_GET[$name])) {
			$str .= $name."=".urlencode($_GET[$name])."&amp;";
		}
	}
	return $str;
}
?>
