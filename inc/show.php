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
			$stmt = $db->prepare("SELECT tags.* FROM tags"
				." JOIN quotes_tags ON tags.id=quotes_tags.tags_id"
				." WHERE quotes_tags.quotes_id=:id");
			$stmt->bindParam(":id", $id);

			$stmt->execute();
			$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
			$stmt->closeCursor();

			qdb_show($quote, $tags);
		}
	} catch (PDOException $e) {
		qdb_die("Error retrieving quote: ".htmlentities($e->getMessage()).".");
	}

	qdb_messages();
}

function qdb_getall_show($where = "", $order = "", $limit = 0) {
	global $db, $config;
	try {
		$sql = "SELECT * FROM quotes";
		if ($where != "") { $sql .= " WHERE $where"; }
		$tags_sql = qdb_tags_sql();
		if ($tags_sql != "") {
			$sql .= " AND id IN (SELECT quotes_id FROM quotes_tags GROUP BY quotes_id"
				." HAVING BOOL_OR(tags_id IN ($tags_sql)))";
		}
		if ($order != "") { $sql .= " ORDER BY $order"; }
		$sql .= " LIMIT ".($limit > 0 ? $limit : $config['perpage']);

		$stmt = $db->prepare($sql);
		$stmt2 = $db->prepare("SELECT tags.* FROM tags"
			." JOIN quotes_tags ON tags.id=quotes_tags.tags_id"
			." WHERE quotes_tags.quotes_id=:id");

		$stmt->execute();
		while ($quote = $stmt->fetch(PDO::FETCH_OBJ)) {
			$stmt2->bindParam(":id", $quote->id);
			$stmt2->execute();
			$tags = $stmt2->fetchAll(PDO::FETCH_OBJ);
			$stmt2->closeCursor();

			qdb_show($quote, $tags);
		}
		$stmt->closeCursor();
	} catch (PDOException $e) {
		qdb_die("Error retrieving quote: ".htmlentities($e->getMessage()).".");
	}
}

function qdb_show($quote, $tags) {
	?>
<p class="quote"><span class="header">
<a href="./?<?=$quote->id?>" title="quote <?=$quote->id?>"><strong class="id">#<?=$quote->id?></strong></a>:
<a class="rateup" href="rate.php?id=<?=$quote->id?>&amp;rate=1" title="rate <?=$quote->id?> up">+</a>
<em class="rating"><?=$quote->rating?></em>
<a class="ratedown" href="rate.php?id=<?=$quote->id?>&amp;rate=-1" title="rate <?=$quote->id?> down">-</a>
<a class="flag" href="rate.php?id=<?=$quote->id?>&amp;rate=0" title="flag <?=$quote->id?>">X</a>
</span><br><tt><?=nl2br(htmlentities($quote->quote));?></tt><?
if ($tags !== FALSE) {
	?><ul class="tags"><?
	foreach ($tags as $tag) {
		?><li><a href="?tags=<?=qdb_tags_qs($tag->id)?>"><?=htmlentities($tag->name)?></a></li><?
	}
	?></ul><?
}
?></p><br>
	<?
}

function qdb_tags_sql() {
	$tags = array();
	if (isset($_GET["tags"])) {
		foreach (explode(" ", $_GET["tags"]) as $tag) {
			if (qdb_digit($tag)) { $tags[] = $tag; }
		}
	}
	if (count($tags) == 0) { return ""; }
	sort($tags);
	return implode(",", $tags);
}

function qdb_tags_qs($tagid) {
	$tags = array();
	if (isset($_GET["tags"])) {
		foreach (explode(" ", $_GET["tags"]) as $tag) {
			if (qdb_digit($tag)) { $tags[] = $tag; }
		}
	}
	if (!in_array($tagid, $tags)) { $tags[] = $tagid; }
	sort($tags);
	return implode("+", $tags);
}
?>
