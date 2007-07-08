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
qdb_header("Change Password");

$disp = TRUE;
if (isset($_POST["pass0"]) && isset($_POST["pass1"]) && isset($_POST["pass2"])) {
	if ($_POST["pass1"] != $_POST["pass2"]) {
		qdb_err("Passwords do not match.");
	} else if (trim($_POST["pass1"]) == "") {
		qdb_err("Password is blank.");
	} else {
		try {
			$stmt = $db->prepare("UPDATE users SET pass=:newpass WHERE id=:id AND pass=:oldpass");
			$stmt->bindParam(":id", $user->id);
			$stmt->bindParam(":oldpass", sha1($_POST["pass0"]));
			$stmt->bindParam(":newpass", sha1($_POST["pass1"]));

			$stmt->execute();
			$stmt->fetch(PDO::FETCH_OBJ);
			if ($stmt->rowCount() > 0) {
				qdb_ok("Password changed.");
				$ok = TRUE;
			} else {
				qdb_err("Failed to change password.");
			}
			$stmt->closeCursor();
			$db->commit();
		} catch (PDOException $e) {
			qdb_die($e);
		}
	}
	qdb_messages();
}

if ($disp) {
	?><p>Enter old password, and new password twice:</p>
	<form method="post" action="password.php">
	<label for="pass0">Old</label>: <input name="pass0" type="password"><br>
	<label for="pass1">New</label>: <input name="pass1" type="password"><br>
	<label for="pass2">Repeat</label>: <input name="pass2" type="password"><br>
	<input type="submit" value="Change Password">
	</form><?
}

qdb_footer();
?>
