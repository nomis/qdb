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
	$Id: latest.php 72 2007-07-07 17:17:48Z byte $
*/
include("inc/common.php");

qdb_header("Search");
qdb_qs_preserve("q");
?>
<p>Enter search string, using _ to match one char and % to match any chars:</p>
<form method="get">
<input type="text" name="q" value="<?=isset($_GET["q"]) ? htmlentities($_GET["q"]) : ""?>" size="50">
<input type="submit">
</form><br>
<?
if (isset($_GET["q"])) {
	qdb_getall_show("quotes.hide=FALSE AND quotes.quote LIKE :search", array(":search" => $_GET["q"]), "id ASC");
}
qdb_footer();
?>
