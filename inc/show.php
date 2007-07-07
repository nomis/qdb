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
function qdb_get_show($id) {
	global $db;

	try {
		$stmt = $db->prepare("SELECT * FROM quotes WHERE id=:id AND hide=FALSE");
		$stmt->bindParam(":id", $id);

		$stmt->execute();
		$quote = $stmt->fetch(PDO::FETCH_OBJ);
		$stmt->closeCursor();

		if ($quote === FALSE) {
			qdb_err("Quote ".$id." does not exist.");
		} else {
			$stmt = $db->prepare("SELECT * FROM tags WHERE quotes_id=:id");
			$stmt->bindParam(":id", $id);

			$stmt->execute();
			$tags = $stmt->fetch(PDO::FETCH_OBJ);
			$stmt->closeCursor();

			qdb_show($quote, $tags);
		}
	} catch (PDOException $e) {
		qdb_die("Error retrieving quote: ".htmlentities($e->getMessage()).".");
	}

	qdb_messages();
}

function qdb_show($quote, $tags) {
	?>
<p class="quote"><small>
<a href="./?id=<?=$quote->id?>" title="quote <?=$quote->id?>"><strong class="id">#<?=$quote->id?></strong></a>:
<a class="rateup" href="rate.php?id=<?=$quote->id?>&amp;rate=1" title="rate <?=$quote->id?> up">+</a>
<em class="rating"><?=$quote->rating?></em>
<a class="ratedown" href="rate.php?id=<?=$quote->id?>&amp;rate=-1" title="rate <?=$quote->id?> down">-</a>
<a class="flag" href="rate.php?id=<?=$quote->id?>&amp;rate=0" title="flag <?=$quote->id?>">X</a>
</small><br>
<tt><?=nl2br(htmlentities($quote->quote));?></tt>
</p>
	<?
}
?>
