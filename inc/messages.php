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
