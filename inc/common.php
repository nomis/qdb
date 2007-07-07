<?
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
