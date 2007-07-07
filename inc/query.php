<?
/*
	Copyright ©2007 Simon Arlott

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License v2
	as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
	Or, point your browser to http://www.gnu.org/copyleft/gpl.html

	http://svn.lp0.eu/simon/qdb/
	$Id: auth.php 68 2007-07-07 11:19:33Z byte $
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

	return $verifying ? ($hash == $_REQUEST["hash"]) : $str;
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
