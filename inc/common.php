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

	http://svn.lp0.eu/simon/qdb/
	$Id$
*/
include("magic_quotes.php");
include("messages.php");

function qdb_digit($str) { return preg_match('/^[0-9]+$/', $str); }

$config = array(
	'db'             => "PDO DSN",
	'name'           => "Site name",
	'perpage'        => "Quotes displayed per page",
	'autohide_anon'  => "Hide all new anonymous quotes automatically",
	'autohide_user'  => "Hide all new user quotes automatically",
	'email_notify'   => "Notification email",
	'email_full'     => "Full email",
	'tags_useronly'  => "Only allow users to create new tags",
	'tags_regexp'    => "Regular expression of valid tags",
	'secret'         => "Secret string used to validate urls"
);
foreach ($config as $option => $msg) {
	if (isset($$option)) { unset($$option); }
}
include("config.php");
foreach ($config as $option => $msg) {
	if (!isset($$option)) {
		qdb_die("Missing configuration option '$option' ($msg).");
	} else {
		$config[$option] = $$option;
		unset($$option);
	}
}

try {
	$db = new PDO($config['db'], $user, $pass);
	$user = NULL;
	$pass = NULL;
	$db->exec("SELECT set_curcfg('default')");
	$db->beginTransaction();
} catch (PDOException $e) {
	qdb_die("Error connecting to database: ".htmlentities($e->getMessage()).".");
}

function qdb_header($title = NULL) {
	global $config, $user, $pending, $flagged;
	include("header.php");
}

function qdb_footer() {
	global $config, $user, $pending, $flagged;
	include("footer.php");
}

include("auth.php");
include("show.php");
include("query.php");
?>
