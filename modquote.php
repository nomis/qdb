<?
/*
	Copyright ©2008-2012,2021  Simon Arlott

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
if (isset($_REQUEST["async"])) {
	define("QDB_ASYNC", TRUE);
	header("Content-Type: application/xml; charset=UTF-8");
	echo '<?xml version="1.0"?>';
	echo '<qdb>';
}

include("inc/common.php");

if ($config["disabled"] && ($user === FALSE || !$user->admin)) {
	qdb_header("Modify");
	qdb_err("Quote modification disabled.");
	qdb_messages();
	qdb_footer();
	exit;
}

function qdb_modquote_tags($quoteid, $tags) {
	global $db, $user, $config;

	$stmt_ins = $db->prepare("INSERT INTO tags (name, "
		.($user === FALSE ? "" : "users_id, ")."ip) VALUES(:name, "
		.($user === FALSE ? "" : ":userid, ").":ip)");
	$stmt_get = $db->prepare("SELECT * FROM quotes_tags WHERE quotes_id=:quoteid AND tags_id=:tagid");
	$stmt_add = $db->prepare("INSERT INTO quotes_tags (quotes_id, tags_id, "
		.($user === FALSE ? "" : "users_id, ")."ip) VALUES(:quoteid, :tagid, "
		.($user === FALSE ? "" : ":userid, ").":ip)");
	$stmt_del = $db->prepare("DELETE FROM quotes_tags WHERE quotes_id=:quoteid AND tags_id=:tagid");
	$stmt_clr = $db->prepare("DELETE FROM tags WHERE id=:tagid AND NOT EXISTS"
		." (SELECT tags_id FROM quotes_tags WHERE tags_id=:tagid LIMIT 1)");

	$anything = FALSE;
	foreach (qdb_tag_explode($tags) as $tag) {
		if ($tag == "") { continue; }

		$add = TRUE;
		if (substr($tag, 0, 1) == "!") {
			if ($user === FALSE || !$user->admin) { continue; }
			$add = FALSE;
			$tag = substr($tag, 1);
		} else if (!preg_match($config['tags_regexp'], $tag)) {
			qdb_err("Tag '".qdb_htmlentities($tag)."' ignored.");
			$anything = TRUE;
			continue;
		}

		$tagid = qdb_get_tag($tag);
		if ($add) {
			if ($tagid == NULL) {
				$stmt_ins->bindParam(":name", $tag);
				if ($user !== FALSE) {
					$stmt_ins->bindParam(":userid", $user->id);
				}
				$stmt_ins->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);
				$stmt_ins->execute();
				if ($stmt_ins->rowCount() <= 0) {
					qdb_err("Error creating tag '".qdb_htmlentities($tag)."'.");
					$anything = TRUE;
					continue;
				}
				$tagid = $db->lastInsertId("tags_id_seq");
				$stmt_ins->closeCursor();
			}

			$stmt_get->bindParam(":quoteid", $quoteid);
			$stmt_get->bindParam(":tagid", $tagid);
			$stmt_get->execute();
			if ($stmt_get->rowCount() > 0) {
				qdb_ok("Tag '".qdb_htmlentities($tag)."' already set.");
				$anything = TRUE;
			} else {
				$stmt_add->bindParam(":quoteid", $quoteid);
				$stmt_add->bindParam(":tagid", $tagid);
				if ($user !== FALSE) {
					$stmt_add->bindParam(":userid", $user->id);
				}
				$stmt_add->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);
				$stmt_add->execute();
				if ($stmt_add->rowCount() > 0) {
					qdb_ok("Tag '".qdb_htmlentities($tag)."' added.");
					$anything = TRUE;
				}
				$stmt_add->closeCursor();
			}
			$stmt_get->closeCursor();
		} else {
			if ($tagid == NULL) {
				qdb_err("Tag '".qdb_htmlentities($tag)."' does not exist.");
				$anything = TRUE;
				continue;
			}

			$stmt_del->bindParam(":quoteid", $quoteid);
			$stmt_del->bindParam(":tagid", $tagid);
			$stmt_del->execute();
			if ($stmt_del->rowCount() > 0) {
				qdb_ok("Tag '".qdb_htmlentities($tag)."' removed.");
				$anything = TRUE;
			} else {
				qdb_err("Tag '".qdb_htmlentities($tag)."' not set.");
				$anything = TRUE;
			}
			$stmt_del->closeCursor();

			$stmt_clr->bindParam(":tagid", $tagid);
			$stmt_clr->execute();
			if ($stmt_clr->rowCount() > 0) { qdb_del_tag($tag); }
				$stmt_clr->closeCursor();
			}
		}
	return $anything;
}

if (isset($_GET["id"]) && qdb_digit($_GET["id"])) {
	if (isset($_GET["rate"])) {
		qdb_header("Rate #".$_GET["id"]);
		if (!qdb_verify(array("id","rate","now")) || $_GET["rate"] == 0) {
			qdb_err("Invalid URL parameters.");
		} else {
			$_GET["rate"] = $_GET["rate"] > 0 ? "t" : "f";

			try {
				$stmt = $db->prepare("SELECT * FROM votes WHERE quotes_id=:quoteid AND ("
					.($user === FALSE ? "" : "users_id=:userid OR ")."ip=:ip) AND ts >= CURRENT_DATE");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				if ($user !== FALSE) {
					$stmt->bindParam(":userid", $user->id);
				}
				$stmt->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);

				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$vote = $stmt->fetch(PDO::FETCH_OBJ);
					$stmt->closeCursor();

					if ($vote->good == TRUE && $_GET["rate"] == "t") {
						qdb_err('You\'ve already rated that quote up today!');
					} else if ($vote->good == FALSE && $_GET["rate"] == "f") {
						qdb_err('You\'ve already rated that quote down today!');
					} else {
						$stmt = $db->prepare("DELETE FROM votes WHERE quotes_id=:quoteid AND ("
							.($user === FALSE ? "" : "users_id=:userid OR ")."ip=:ip) AND ts >= CURRENT_DATE");
						$stmt->bindParam(":quoteid", $_GET["id"]);
						if ($user !== FALSE) {
							$stmt->bindParam(":userid", $user->id);
						}
						$stmt->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);

						$stmt->execute();
						qdb_ok("Previous rating for today cancelled.");
						$stmt->closeCursor();
					}
				} else {
					$stmt->closeCursor();

					$stmt = $db->prepare("SELECT id FROM quotes WHERE id=:quoteid");

					$stmt->bindParam(":quoteid", $_GET["id"]);

					$stmt2 = $db->prepare("INSERT INTO votes (quotes_id, good, "
						.($user === FALSE ? "" : "users_id, ")."ip)"
						." VALUES(:quoteid, :vote, "
						.($user === FALSE ? "" : ":userid, ").":ip)");

					$stmt2->bindParam(":quoteid", $_GET["id"]);
					$stmt2->bindParam(":vote", $_GET["rate"]);
					if ($user !== FALSE) {
						$stmt2->bindParam(":userid", $user->id);
					}
					$stmt2->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);

					$stmt->execute();
					if ($stmt->rowCount() > 0) {
						$stmt2->execute();
						qdb_ok("Quote rated.");
					} else {
						qdb_err("Quote #".qdb_htmlentities($_GET["id"])." does not exist.");
					}
					$stmt->closeCursor();
					$stmt2->closeCursor();
				}

				$db->commit();
				$db->beginTransaction();
			} catch (PDOException $e) {
				qdb_die($e);
			}

			qdb_messages();
			qdb_get_show($_GET["id"]);
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_GET["flag"])) {
		if ($_GET["flag"] > 0) { $_GET["flag"] = 1; } else { $_GET["flag"] = 0; }

		qdb_header(($_GET["flag"] == 0 ? "Unflag" : "Flag")." #".$_GET["id"]);
		if (!qdb_verify(array("id","flag","now"))) {
			qdb_err("Invalid URL parameters.");
		} else if ($_GET["flag"] == 0 && ($user === FALSE || !$user->admin)) {
			qdb_not_admin();
		} else {
			try {
				$stmt = $db->prepare("UPDATE quotes SET flag=:flag WHERE id=:quoteid");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->bindParam(":flag", $_GET["flag"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok("Quote ".($_GET["flag"] == 0 ? "un" : "")."flagged.");
				} else {
					qdb_err("Quote #".qdb_htmlentities($_GET["id"])." does not exist.");
				}
				$stmt->closeCursor();

				$db->commit();
				$db->beginTransaction();
			} catch (PDOException $e) {
				qdb_die($e);
			}

			qdb_messages();
			qdb_get_show($_GET["id"]);
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_GET["hide"])) {
		if ($_GET["hide"] > 0) { $_GET["hide"] = 1; } else { $_GET["hide"] = 0; }

		qdb_header(($_GET["hide"] == 0 ? "show" : "Hide")." #".$_GET["id"]);
		if (!qdb_verify(array("id","hide","now"))) {
			qdb_err("Invalid URL parameters.");
		} else if ($user === FALSE || !$user->admin) {
			qdb_not_admin();
		} else {
			try {
				$stmt = $db->prepare("UPDATE quotes SET hide=:hide WHERE id=:quoteid");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->bindParam(":hide", $_GET["hide"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok("Quote ".($_GET["hide"] == 0 ? "shown" : "hidden").".");
				} else {
					qdb_err("Quote #".qdb_htmlentities($_GET["id"])." does not exist.");
				}
				$stmt->closeCursor();

				$db->commit();
				$db->beginTransaction();
			} catch (PDOException $e) {
				qdb_die($e);
			}

			qdb_messages();
			qdb_get_show($_GET["id"]);
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_GET["del"])) {
		qdb_header("Delete #".$_GET["id"]);
		if (!qdb_verify(array("id","del","now"))) {
			qdb_err("Invalid URL parameters.");
		} else if ($user === FALSE || !$user->admin) {
			qdb_not_admin();
		} else {
			try {
				$stmt2 = $db->prepare("DELETE FROM tags WHERE "
					." id IN (SELECT tags_id FROM quotes_tags WHERE quotes_id=:quoteid)"
					." AND NOT EXISTS (SELECT tags_id FROM quotes_tags WHERE tags_id=tags.id"
					." AND quotes_id!=:quoteid LIMIT 1)");
				$stmt2->bindParam(":quoteid", $_GET["id"]);
				$stmt2->execute();
				$stmt2->closeCursor();

				$stmt = $db->prepare("DELETE FROM quotes WHERE id=:quoteid");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$db->commit();

					qdb_ok("Quote #".qdb_htmlentities($_GET["id"])." deleted.");
				} else {
					qdb_err("Quote #".qdb_htmlentities($_GET["id"])." does not exist.");
				}
				$stmt->closeCursor();
			} catch (PDOException $e) {
				qdb_die($e);
			}
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_GET["edit"])) {
		qdb_header("Edit #".$_GET["id"]);
		if (!qdb_verify(array("id","edit","now"))) {
			qdb_err("Invalid URL parameters.");
		} else if ($user === FALSE || !$user->admin) {
			qdb_not_admin();
		} else {
			try {
				$stmt = $db->prepare("SELECT * FROM quotes WHERE id=:quoteid");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->execute();
				$quote = $stmt->fetch(PDO::FETCH_OBJ);
				$stmt->closeCursor();

				if ($quote === FALSE) {
					qdb_err("Quote #".qdb_htmlentities($_GET["id"])." does not exist.");
				} else {
					$stmt = $db->prepare("SELECT tags.* FROM tags"
						." JOIN quotes_tags ON tags.id=quotes_tags.tags_id"
						." WHERE quotes_tags.quotes_id=:quoteid ORDER BY tags.name ASC");
					$stmt->bindParam(":quoteid", $_GET["id"]);

					$stmt->execute();
					$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
					$stmt->closeCursor();

					?><p>Editing quote #<?=$_GET["id"]?>:</p><?

					?><form method="post" action="modquote"><?
						?><input type="hidden" name="id" value="<?=$_GET["id"]?>"><?
						?><input type="hidden" name="edit" value="1"><?
						?><textarea name="quote" rows="15" cols="80"><?=qdb_htmlentities($quote->quote)?></textarea><br><?
						?><label for="tags">Tags</label>: <input name="tags" size="50" value="<?

						foreach ($tags as $i => $tag) {
							if ($i > 0) { echo " "; }
							echo qdb_htmlentities($tag->name);
						}

						?>"><?
						?><input class="cancel" type="reset"><?
						?><input class="ok" type="submit" value="Modify Quote"><?
					?></form><?
				}
			} catch (PDOException $e) {
				qdb_die($e);
			}
		}
		qdb_messages();
		qdb_footer();
	}
} else if (isset($_POST["id"]) && qdb_digit($_POST["id"])) {
	if (isset($_POST["tagset"])) {
		qdb_header("Tags #".$_POST["id"]);
		if ($user === FALSE && $config['tags_useronly']) {
			qdb_not_tags();
		} else {
			try {
				if (!qdb_modquote_tags($_POST["id"], $_POST["tagset"])) {
					qdb_err("No tags specified.");
				}

				$db->commit();
				$db->beginTransaction();
			} catch (PDOException $e) {
				qdb_die($e);
			}

			qdb_messages();
			qdb_get_show($_POST["id"]);
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_POST["edit"]) && isset($_POST["quote"]) && isset($_POST["tags"])) {
		$_POST["quote"] = qdb_sanitise($_POST["quote"]);

		qdb_header("Edit #".$_POST["id"]);
		if ($user === FALSE || !$user->admin) {
			qdb_not_admin();
		} else if ($_POST["quote"] == "") {
			qdb_err("Cannot make quotes empty, use the delete function.");
		} else {
			try {
				$stmt = $db->prepare("SELECT * FROM quotes WHERE quote=:text AND id!=:quoteid");
				$stmt->bindParam(":quoteid", $_POST["id"]);
				$stmt->bindParam(":text", $_POST["quote"]);
				$stmt->execute();
				$quote = $stmt->fetch(PDO::FETCH_OBJ);
				$stmt->closeCursor();

				$stmt = $db->prepare("SELECT tags.* FROM tags"
					." JOIN quotes_tags ON tags.id=quotes_tags.tags_id"
					." WHERE quotes_tags.quotes_id=:quoteid");
				$stmt->bindParam(":quoteid", $_POST["id"]);

				$stmt->execute();
				$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
				$stmt->closeCursor();

				if ($quote !== FALSE) {
					qdb_err("That quote already exists.");
				} else {
					$stmt = $db->prepare("UPDATE quotes SET quote=:text WHERE id=:quoteid AND quote!=:text");
					$stmt->bindParam(":quoteid", $_POST["id"]);
					$stmt->bindParam(":text", $_POST["quote"]);
					$stmt->execute();
					if ($stmt->rowCount() > 0) {
						qdb_ok("Quote modified.");
					}
					$stmt->closeCursor();
				}

				$oldtags = array();
				$newtags = qdb_tag_explode($_POST["tags"]);
				foreach ($tags as $tag) {
					$oldtags[] = $tag->name;
					if (!in_array($tag->name, $newtags)) {
						$newtags[] = "!".$tag->name;
					}
				}
				$newtags = array_diff($newtags, $oldtags);

				qdb_modquote_tags($_POST["id"], implode(" ", $newtags));

				$db->commit();
				$db->beginTransaction();
			} catch (PDOException $e) {
				qdb_die($e);
			}

			foreach ($config['email_full'] as $email) {
				mail($email, "Quote #".$_POST["id"]." (".($user === FALSE ? "" : qdb_htmlentities($user->name)."/").$_SERVER["REMOTE_ADDR"].")",
					$config['base_url'].'?'.$_POST["id"]."\r\n\r\n"
					."Editor: ".($user === FALSE ? "" : qdb_htmlentities($user->name)."/").$_SERVER["REMOTE_ADDR"]."\r\n"
					."\r\n".str_replace("\n","\r\n",$_POST["quote"])
					."\r\n\r\n-- \r\n".$config['name']."\r\n",
				"Content-Transfer-Encoding: 8bit\r\n"
				."Content-Type: text/plain; charset=UTF-8");
			}

			qdb_messages();
			qdb_get_show($_POST["id"]);
		}
		qdb_messages();
		qdb_footer();
	}
}

if (defined("QDB_ASYNC")) {
	qdb_async_messages();
	echo '</qdb>';
}
?>
