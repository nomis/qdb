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
include("inc/common.php");

qdb_header("Top ".$config['top']);

try {
	$stmt = $db->prepare("SELECT * FROM quotes WHERE hide=FALSE ORDER BY rating DESC, id DESC LIMIT ".$config['top']);
	$stmt2 = $db->prepare("SELECT * FROM tags WHERE quotes_id=:id");

	$stmt->execute();
	while ($quote = $stmt->fetch(PDO::FETCH_OBJ)) {
		$stmt2->bindParam(":id", $quote->id);
		$stmt2->execute();
		$tags = $stmt2->fetch(PDO::FETCH_OBJ);
		$stmt2->closeCursor();

		qdb_show($quote, tags);
	}
	$stmt->closeCursor();
} catch (PDOException $e) {
	qdb_die("Error retrieving quote: ".htmlentities($e->getMessage()).".");
}

qdb_messages();
qdb_footer();
?>
