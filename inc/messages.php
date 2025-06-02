<?
/*
	Copyright ©2008-2011,2021,2025  Simon Arlott

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
$msgs = array();

$header = FALSE;
function qdb_die($e) {
	global $header, $config, $user;

	@header("HTTP/1.0 500 Server Error");

	ob_start();
	echo '[_SERVER] => ';
	$_SERVER2=$_SERVER;
	unset($_SERVER2["UNIQUE_ID"]);
	unset($_SERVER2["PATH"]);
	unset($_SERVER2["SERVER_SIGNATURE"]);
	unset($_SERVER2["SERVER_SOFTWARE"]);
	unset($_SERVER2["SERVER_NAME"]);
	unset($_SERVER2["SERVER_ADDR"]);
	unset($_SERVER2["SERVER_PORT"]);
	unset($_SERVER2["DOCUMENT_ROOT"]);
	unset($_SERVER2["SERVER_ADMIN"]);
	unset($_SERVER2["SCRIPT_FILENAME"]);
	unset($_SERVER2["SCRIPT_NAME"]);
	unset($_SERVER2["PATH_INFO"]);
	unset($_SERVER2["PATH_TRANSLATED"]);
	unset($_SERVER2["PHP_SELF"]);
	unset($_SERVER2["PHP_AUTH_USER"]);
	unset($_SERVER2["PHP_AUTH_PW"]);
	unset($_SERVER2["argv"]);
	unset($_SERVER2["argc"]);
	print_r($_SERVER2);
	echo "\n";
	echo '[_GET] => ';
	print_r($_GET);
	echo "\n";
	echo '[_POST] => ';
	print_r($_POST);
	$debug = ob_get_clean();

	foreach ($config['email_admin'] as $email) {
		mail($email, "[".$_SERVER["SERVER_NAME"]."] Exception: ".$e->getMessage()
				." (".$_SERVER["REMOTE_ADDR"].")",
			"IP: ".$_SERVER["REMOTE_ADDR"]."\r\n"
			.($user === FALSE ? "" : "User: ".$user->name."\r\n")
			."\r\n"
			."Message:\r\n\t".$e->getMessage()."\r\n\r\n"
			."Backtrace:\r\n".str_replace("\n", "\r\n", $e->getTraceAsString())."\r\n\r\n"
			.str_replace("\n", "\r\n", $debug)
			."\r\n-- \r\n".$config['name']."\r\n",
			"Content-Transfer-Encoding: 8bit\r\n"
			."Content-Type: text/plain; charset=UTF-8");
	}

	if (!$header) { qdb_header(); }
	qdb_err($e->getMessage());
	qdb_messages();
	qdb_async_messages();
	qdb_footer();
	die();
}

function qdb_ok($html_msg) {
	global $msgs;
	$msgs[] = array("type" => 'ok', "html" => $html_msg);
}

function qdb_err($html_msg) {
	global $msgs;
	$msgs[] = array("type" => 'err', "html" => $html_msg);
}

function qdb_messages() {
	global $msgs;

	if (defined("QDB_ASYNC")) { return; }

	if (count($msgs) == 0) { return; }
	echo '<p><ul class="msgs">';
	foreach ($msgs as $msg) {
		echo '<li class="'.$msg['type'].'">'.$msg['html'].'</li>';
	}
	echo '</ul></p>';
	$msgs = array();
}

function qdb_async_messages() {
	global $msgs;

	if (count($msgs) == 0) { return; }
	echo '<msgs><![CDATA[<ul class="msgs">';
	foreach ($msgs as $msg) {
		echo '<li class="'.$msg['type'].'">'.$msg['html'].'</li>';
	}
	echo '</ul>]]></msgs>';
	$msgs = array();
}

function qdb_not_admin() {
	if (!defined("QDB_ASYNC")) {
		?><p>You are not an admin!</p><?
	} else {
		qdb_err("You are not an admin!");
	}
}

function qdb_not_tags() {
	if (!defined("QDB_ASYNC")) {
		?><p>You are not allowed to set tags!</p><?
	} else {
		qdb_err("You are not allowed to set tags!");
	}
}
?>
