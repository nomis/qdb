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

qdb_header("Add Quote");

$msg = NULL;

if (isset($_POST["quote"])) {
	$_POST["quote"] = qdb_sanitise($_POST["quote"]);
}

if ($config["disabled"] && ($user === FALSE || !$user->admin)) {
	qdb_err("Quote adding disabled.");
} else if (isset($_POST["quote"]) && $_POST["quote"] != "") {
	try {
		$exists = FALSE;
		$stmt = $db->prepare("SELECT * FROM quotes WHERE quote=:text");
		$stmt->bindParam(":text", $_POST["quote"]);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			qdb_err("Quote already exists.");
			$exists = TRUE;
		}
		$stmt->closeCursor();

		if (!$exists) {
			$stmt = $db->prepare("INSERT INTO quotes (quote, hide, users_id, ip) VALUES(:text, :hide, :userid, :ip)");
			$stmt->bindParam(":text", $_POST["quote"]);
			if ($user === FALSE) {
				$stmt->bindParam(":userid", NULL);
				$stmt->bindParam(":hide", $config['autohide_anon']);
			} else {
				$stmt->bindParam(":userid", $user->id);
				$hide = $user->admin ? "FALSE" : $config['autohide_user'];
				$stmt->bindParam(":hide", $hide);
			}
			$stmt->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);

			$stmt->execute();
		}

		if (!$exists && $stmt->rowCount() > 0) {
			$id = $db->lastInsertId("quotes_id_seq");
			qdb_ok('Added quote <a href="./?'.$id.'" title="quote #'.$id.'">#'.$id.'</a>.');

			$oktags = array();
			if (!$config['tags_useronly'] || $user !== FALSE) {
				$stmt_ins = $db->prepare("INSERT INTO tags (name, users_id, ip) VALUES(:name, :userid, :ip)");
				$stmt_get = $db->prepare("SELECT * FROM quotes_tags WHERE quotes_id=:quoteid AND tags_id=:tagid");
				$stmt_add = $db->prepare("INSERT INTO quotes_tags (quotes_id, tags_id, users_id, ip) VALUES(:quoteid, :tagid, :userid, :ip)");

				foreach (explode(" ", $_POST["tags"]) as $tag) {
					if ($tag == "") { continue; }

					if (substr($tag, 0, 1) == "!") {
						continue;
					} else if (!preg_match($config['tags_regexp'], $tag)) {
						qdb_err("Tag '".qdb_htmlentities($tag)."' ignored.");
						continue;
					}

					$tagid = qdb_get_tag($tag);
					if ($tagid == NULL) {
						$stmt_ins->bindParam(":name", $tag);
						if ($user === NULL) {
							$stmt_ins->bindParam(":userid", NULL);
						} else {
							$stmt_ins->bindParam(":userid", $user->id);
						}
						$stmt_ins->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);
						$stmt_ins->execute();
						if ($stmt_ins->rowCount() <= 0) {
							qdb_err("Error creating tag '".qdb_htmlentities($tag)."'.");
							continue;
						}
						$tagid = $db->lastInsertId("tags_id_seq");
						$stmt_ins->closeCursor();
					}

					$stmt_get->bindParam(":quoteid", $_POST["id"]);
					$stmt_get->bindParam(":tagid", $tagid);
					$stmt_get->execute();
					if ($stmt_get->rowCount() > 0) {
						qdb_ok("Tag '".qdb_htmlentities($tag)."' already set.");
					} else {
						$stmt_add->bindParam(":quoteid", $id);
						$stmt_add->bindParam(":tagid", $tagid);
						if ($user === NULL) {
							$stmt_add->bindParam(":userid", NULL);
						} else {
							$stmt_add->bindParam(":userid", $user->id);
						}
						$stmt_add->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);
						$stmt_add->execute();
						if ($stmt_add->rowCount() > 0) {
							qdb_ok("Tag '".qdb_htmlentities($tag)."' added.");
							$oktags[] = $tag;
						}
						$stmt_add->closeCursor();
					}
					$stmt_get->closeCursor();
				}
			}
			$db->commit();

			foreach ($config['email_notify'] as $email) {
				mail($email, $config['email_url'].'?'.$id, count($oktags) == 0 ? "" : "(".implode(" ", $oktags).")");
			}

			foreach ($config['email_full'] as $email) {
				mail($email, "Quote #".$id,
					$config['email_url'].'?'.$id."\r\n\r\n"
					."From: ".($user === FALSE ? "" : qdb_htmlentities($user->name)."/").$_SERVER["REMOTE_ADDR"]."\r\n"
					.(count($oktags) == 0 ? "" : "Tags: ".implode(" ", $oktags)."\r\n")
					."\r\n".str_replace("\n","\r\n",$_POST["quote"])
					."\r\n\r\n-- \r\n".$config['name']."\r\n",
				"Content-Transfer-Encoding: 8bit\r\n"
				."Content-Type: text/plain; charset=UTF-8");
			}

			unset($_POST["quote"]);
		}
		$stmt->closeCursor();
	} catch (PDOException $e) {
		qdb_die($e);
	}
} else if (isset($_POST["quote"])) {
	qdb_err("Quotes cannot be empty.");
}

qdb_messages();
if (!$config["disabled"] || ($user !== FALSE && $user->admin)) {
?>
<p>Please remove timestamps unless necessary.</p>
<form method="post" action="addquote.php">
<textarea name="quote" rows="5" cols="80">
<?=isset($_POST["quote"]) ? qdb_htmlentities($_POST["quote"]) : ""?>
</textarea><br>
<? if (!$config['tags_useronly'] || $user !== FALSE) {?>
<label>Tags</label>: <input name="tags" size="50">
<? } ?>
<input class="cancel" type="reset">
<input class="ok" type="submit" value="Add Quote">
</form>
<?
}
qdb_footer();
?>
