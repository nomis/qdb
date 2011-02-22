<?
/*
	Copyright ©2008-2011  Simon Arlott

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU Affero General Public License v3
	as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
include("inc/common.php");

qdb_auth();
qdb_header("Tags");
if ($user === FALSE || !$user->admin) {
	qdb_not_admin();
} else {
	try {
		if (isset($_POST["id"]) && qdb_digit($_POST["id"]) && isset($_POST["name"]) && isset($_POST["action_"])) {
			if ($_POST["action_"] == "Rename") {
				$tagid = qdb_get_tag($_POST["name"]);

				if ($tagid != $_POST["id"]) {
					if ($tagid != NULL) {
						$stmt = $db->prepare("UPDATE quotes_tags SET tags_id=:newtagid WHERE tags_id=:oldtagid"
							." AND quotes_id NOT IN (SELECT quotes_id FROM quotes_tags WHERE tags_id=:newtagid)");
						$stmt->bindParam(":oldtagid", $_POST["id"]);
						$stmt->bindParam(":newtagid", $tagid);
						$stmt->execute();
						$stmt->closeCursor();

						$stmt = $db->prepare("DELETE FROM tags WHERE id=:tagid");
						$stmt->bindParam(":tagid", $_POST["id"]);
						$stmt->execute();
						if ($stmt->rowCount() > 0) {
							qdb_ok("Renamed tag.");
						}
						$stmt->closeCursor();
					} else if (!preg_match($config['tags_regexp'], $_POST["name"])) {
						qdb_err("Invalid tag name.");
					} else {
						$stmt = $db->prepare("UPDATE tags SET name=:name WHERE id=:tagid");
						$stmt->bindParam(":tagid", $_POST["id"]);
						$stmt->bindParam(":name", $_POST["name"]);
						$stmt->execute();
						if ($stmt->rowCount() > 0) {
							qdb_ok("Renamed tag.");
						}
						$stmt->closeCursor();
					}
				}
			} else if ($_POST["action_"] == "Delete") {
				$stmt = $db->prepare("DELETE FROM tags WHERE id=:tagid");
				$stmt->bindParam(":tagid", $_POST["id"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok("Deleted tag.");
				}
				$stmt->closeCursor();
			}
		}

		qdb_messages();

		?><table class="tags" width="100%"><?
			?><tr><?
				?><th align="left">Tag</th><?
				?><th align="left">Created</th><?
				?><th align="left">By</th><?
				?><th align="left">Quotes</th><?
				?><th align="right">Actions</th><?
			?></tr><?

		$stmt = $db->prepare("SELECT *,"
			." (SELECT COUNT(quotes_tags.quotes_id) FROM quotes_tags WHERE quotes_tags.tags_id=tags.id) AS count,"
			." (SELECT users.name FROM users WHERE tags.users_id=users.id) AS users_name"
			." FROM tags ORDER BY name ASC");
		$stmt->execute();
		$i = 0;
		while ($tag = $stmt->fetch(PDO::FETCH_OBJ)) {
			?><tr<?=$i++ % 2 == 0 ? ' class="moo"' : ''?>><?
				?><td><a href="browse.php?tags=<?=$tag->id?>" title="view quotes with tag '<?=qdb_htmlentities($tag->name)?>'"><?=qdb_htmlentities($tag->name)?></a></td><?
				?><td class="small"><?=str_replace(" ", "&nbsp;", date("Y-m-d H:i:s", strtotime($tag->ts)))?></td><?
				?><td class="verysmall"><?=($tag->users_name != NULL ? qdb_htmlentities($tag->users_name)."/" : "").$tag->ip?></td><?
				?><td align="center"><?=$tag->count?></td><?
				?><td align="right"><?
					?><form class="tags" method="post" action="tags.php"><?
						?><input type="hidden" name="id" value="<?=$tag->id?>"><?
						?><input type="text" name="name" value="<?=qdb_htmlentities($tag->name)?>"><?
						?><input type="submit" name="action_" value="Rename"><?
						?><input type="submit" name="action_" value="Delete"><?
					?></form><?
				?></td><?
			?></tr><?
		}
		$stmt->closeCursor();

		$db->commit();
	} catch (PDOException $e) {
		?></table><?
		qdb_die($e);
	}
	?></table><?
}
qdb_footer();
?>
