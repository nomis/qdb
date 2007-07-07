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
	$Id$
*/
include("config.php");
include("messages.php");

function qdb_digit($str) { return preg_match('/^[0-9]+$/', $str); }

if (!defined("QDB_CONFIG")) { qdb_die("Invalid or missing configuration."); }
$config = array(
	'db'             => "PDO DSN",
	'name'           => "Site name",
	'top'            => "Top N Quotes",
	'perpage'        => "Quotes displayed per page",
	'autohide_anon'  => "Hide all new anonymous quotes automatically",
	'autohide_user'  => "Hide all new user quotes automatically",
	'email_notify'   => "Notification email",
	'email_full'     => "Full email"
);
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
	$db->beginTransaction();
} catch (PDOException $e) {
	qdb_die("Error connecting to database: ".htmlentities($e->getMessage()).".");
}

function qdb_header($title = NULL) {
	global $config, $user;
	include("header.php");
}

function qdb_footer() {
	global $config, $user;
	include("footer.php");
}

include("auth.php");
include("show.php");
?>
