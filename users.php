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


	$Id$
*/
include("inc/common.php");

qdb_auth();
qdb_header("Users");
if ($user === FALSE || !$user->admin) {
	qdb_not_admin();
} else {
	try {
		if (isset($_POST["id"]) && qdb_digit($_POST["id"]) && isset($_POST["name"]) && isset($_POST["pass"]) && isset($_POST["action"])) {
			if ($_POST["action"] == "Rename") {
				$stmt = $db->prepare("SELECT * FROM users WHERE name=:name AND id!=:userid");
				$stmt->bindParam(":userid", $_POST["id"]);
				$stmt->bindParam(":name", $_POST["name"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$stmt->closeCursor();
					qdb_err("Cannot rename, new username already exists.");
				} else if (!preg_match($config['users_regexp'], $_POST["name"])) {
					$stmt->closeCursor();
					qdb_err("Invalid username.");
				} else {
					$stmt->closeCursor();

					$stmt = $db->prepare("UPDATE users SET name=:name WHERE id=:userid AND name!=:name AND nodelete=FALSE");
					$stmt->bindParam(":userid", $_POST["id"]);
					$stmt->bindParam(":name", $_POST["name"]);
					$stmt->execute();
					if ($stmt->rowCount() > 0) {
						qdb_ok("Renamed user.");
					}
					$stmt->closeCursor();
				}
			} else if ($_POST["action"] == "Make User") {
				$stmt = $db->prepare("UPDATE users SET admin=FALSE WHERE id=:userid AND admin=TRUE AND nodelete=FALSE");
				$stmt->bindParam(":userid", $_POST["id"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok("Removed admin privileges from user.");
				}
				$stmt->closeCursor();
			} else if ($_POST["action"] == "Make Admin") {
				$stmt = $db->prepare("UPDATE users SET admin=TRUE WHERE id=:userid AND admin=FALSE AND nodelete=FALSE");
				$stmt->bindParam(":userid", $_POST["id"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok("Gave admin privileges to user.");
				}
				$stmt->closeCursor();
			} else if ($_POST["action"] == "Change Password") {
				if (trim($_POST["pass"]) == "") {
					qdb_err("Password is blank.");
				} else {
					$stmt = $db->prepare("UPDATE users SET pass=:pass WHERE id=:userid AND nodelete=FALSE");
					$stmt->bindParam(":userid", $_POST["id"]);
					$stmt->bindParam(":pass", sha1($_POST["pass"]));
					$stmt->execute();
					if ($stmt->rowCount() > 0) {
						qdb_ok("Changed user's password.");
					}
					$stmt->closeCursor();
				}
			} else if ($_POST["action"] == "Delete") {
				$stmt = $db->prepare("DELETE FROM users WHERE id=:userid AND nodelete=FALSE");
				$stmt->bindParam(":userid", $_POST["id"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok("Deleted user.");
				}
				$stmt->closeCursor();
			} else if ($_POST["action"] == "Create User") {
				$stmt = $db->prepare("SELECT * FROM users WHERE name=:name");
				$stmt->bindParam(":name", $_POST["name"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$stmt->closeCursor();
					qdb_err("Cannot create, new username already exists.");
				} else if (!preg_match($config['users_regexp'], $_POST["name"])) {
					$stmt->closeCursor();
					qdb_err("Invalid username.");
				} else if (trim($_POST["pass"]) == "") {
					qdb_err("Password is blank.");
				} else {
					$stmt->closeCursor();

					$stmt = $db->prepare("INSERT INTO users (name, pass, admin)"
						." VALUES(:name, :pass, :admin)");
					$stmt->bindParam(":name", $_POST["name"]);
					$stmt->bindParam(":pass", sha1($_POST["pass"]));
					$admin = isset($_POST["admin"]) ? "TRUE" : "FALSE";
					$stmt->bindParam(":admin", $admin);
					$stmt->execute();
					if ($stmt->rowCount() > 0) {
						qdb_ok("Created user '".qdb_htmlentities($_POST["name"], ENT_COMPAT, "UTF-8", ENT_COMPAT, "UTF-8")."'.", ENT_COMPAT, "UTF-8");
					}
					$stmt->closeCursor();
				}
			}
		}

		qdb_messages();

		?><table class="users" width="100%"><?
			?><tr><?
				?><th align="left">Name</th><?
				?><th>Quotes</th><?
				?><th>Tags</th><?
				?><th>Admin</th><?
				?><th align="right">Actions</th><?
			?></tr><?
	
		$stmt = $db->prepare("SELECT *,"
			." (SELECT COUNT(id) FROM quotes WHERE quotes.users_id=users.id) AS count_quotes,"
			." (SELECT COUNT(id) FROM tags WHERE tags.users_id=users.id) AS count_tags"
			." FROM users ORDER BY admin, name ASC");
		$stmt->execute();
		$i = 0;
		while ($auser = $stmt->fetch(PDO::FETCH_OBJ)) {
			?><tr<?=$i++ % 2 == 0 ? ' class="moo"' : ''?>><?
				?><td><?=qdb_htmlentities($auser->name, ENT_COMPAT, "UTF-8", ENT_COMPAT, "UTF-8", ENT_COMPAT, "UTF-8")?></td><?
				?><td align="center"><?=$auser->count_quotes?></td><?
				?><td align="center"><?=$auser->count_tags?></td><?
				?><td align="center"><?=$auser->admin ? "Yes" : "No"?></td><?
				?><td align="right"><?
					?><form class="users" method="post" action="users.php"><?
						?><input type="hidden" name="id" value="<?=$auser->id?>"><?
						?><input type="password" name="pass" value=""><?
						?><input type="submit" name="action" value="Change Password"><?
						?><input type="submit" name="action" value="Make User"><?
						?><br><?
						?><input type="text" name="name" value="<?=qdb_htmlentities($auser->name, ENT_COMPAT, "UTF-8", ENT_COMPAT, "UTF-8", ENT_COMPAT, "UTF-8")?>"><?
						?><input type="submit" name="action" value="Rename"><?
						?><input type="submit" name="action" value="Delete"><?
						?><input type="submit" name="action" value="Make Admin"><?
						?><br><?
					?></form><?
				?></td><?
			?></tr><?
		}
		$stmt->closeCursor();

		$db->commit();
	} catch (PDOException $e) {
		?></table><?;
		qdb_die($e);
	}
	?></table><?
	?><hr><?
	?><form method="post" action="users.php"><?
		?><input type="hidden" name="id" value="0"><?
		?><label for="name">Name</label>: <input type="text" name="name"><br><?
		?><label for="pass">Pass</label>: <input type="password" name="pass"><br><?
		?><input type="checkbox" name="admin"><label for="admin">Admin</label><?
		?><input type="submit" name="action" value="Create User"><?
	?></form><?
}
qdb_footer();
?>
