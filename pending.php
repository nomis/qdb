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
include("inc/common.php");

qdb_auth();
qdb_header("Pending");
if ($user === FALSE || !$user->admin) {
	?><p>You are not an admin!</p><?
} else {
	qdb_getall_show("quotes.hide=TRUE", array(), "id ASC");
}
qdb_footer();
?>
