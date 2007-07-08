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
	'email_url'      => "Base url for quotes",
	'email_notify'   => "Quote notification email addresses",
	'email_full'     => "Full quote notification email addresses",
	'email_admin'    => "Admin email addresses (for errors)",
	'tags_useronly'  => "Only allow users to create new tags",
	'tags_regexp'    => "Regular expression of valid tags",
	'tags_cloudsize' => "Number of tags to show in tag cloud",
	'secret'         => "Secret string used to validate urls"
);
foreach ($config as $option => $msg) {
	if (isset($$option)) { unset($$option); }
}
include("config.php");
foreach ($config as $option => $msg) {
	if (!isset($$option)) {
		die("Missing configuration option '$option' ($msg).");
	} else {
		$config[$option] = $$option;
		unset($$option);
	}
}

try {
	$db = new PDO($config['db'], $user, $pass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$user = NULL;
	$pass = NULL;
	$db->exec("SELECT set_curcfg('default')");

	$db->beginTransaction();
	$stmt = $db->prepare("SELECT COUNT(id) AS count FROM quotes");
	$stmt->execute();
	$quotes_count = $stmt->fetch(PDO::FETCH_OBJ);
	$stmt->closeCursor();

	$stmt = $db->prepare("SELECT COUNT(id) AS count FROM tags");
	$stmt->execute();
	$tags_count = $stmt->fetch(PDO::FETCH_OBJ);
	$stmt->closeCursor();
	unset($stmt);
} catch (PDOException $e) {
	qdb_die($e);
}

function qdb_header($title = NULL) {
	global $config, $user, $pending, $flagged, $quotes_count, $tags_count, $header;
	include("header.php");
	$header = TRUE;
}

function qdb_footer() {
	global $config, $user, $pending, $flagged, $quotes_count, $tags_count;
	include("footer.php");
}

function qdb_sanitise($str) {
	$str = trim($str);
	$str = preg_replace('/[\x00-\x02\x04-\x09\x0B-\x0C\x0E-\x19]/', '', $str);
	$str = preg_replace('/\x03[0-9]{0,2}(,[0-9]{0,2})?/', '', $str);
	$str = preg_replace('/\x0D\x0A/', "\n", $str);
	$str = preg_replace('/\x0D/', "\n", $str);
	return $str;
}

include("auth.php");
include("show.php");
include("query.php");
?>
