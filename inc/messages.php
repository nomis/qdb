<?
$msgs = array();

function qdb_die($msg) {
	qdb_err($msg);
	qdb_messages();
	die();
}

function qdb_ok($msg) {
	global $msgs;
	$msgs[] = array(type => 'ok', text => $msg);
}

function qdb_err($msg) {
	global $msgs;
	$msgs[] = array(type => 'err', text => $msg);
}

function qdb_messages() {
	global $msgs;

	if (count($msgs) == 0) { return; }
	echo '<p><ul class="msgs">';
	foreach ($msgs as $msg) {
		echo '<li class="'.$msg['type'].'">'.$msg['text'].'</li>';
	}
	echo '</ul></p>';
	$msgs = array();
}
?>
