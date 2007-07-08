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
$user = FALSE;
$pending = FALSE;
$flagged = FALSE;

if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
	try {
		$stmt = $db->prepare("SELECT * FROM users WHERE name=:name AND pass=:pass");
		$stmt->bindParam(":name", $_SERVER["PHP_AUTH_USER"]);
		$stmt->bindParam(":pass", sha1($_SERVER["PHP_AUTH_PW"]));

		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		$stmt->closeCursor();

		if ($user !== FALSE && $user->admin) {
			$stmt = $db->prepare("SELECT COUNT(hide) AS count FROM quotes WHERE hide");
			$stmt->execute();
			$pending = $stmt->fetch(PDO::FETCH_OBJ);
			$stmt->closeCursor();

			$stmt = $db->prepare("SELECT COUNT(flag) AS count FROM quotes WHERE flag");
			$stmt->execute();
			$flagged = $stmt->fetch(PDO::FETCH_OBJ);
			$stmt->closeCursor();
			unset($stmt);
		}
	} catch (PDOException $e) {
		qdb_die($e);
	}

	if ($user === FALSE) { qdb_auth(); }
}

function qdb_auth() {
	global $user, $name;
	if ($user === FALSE) {
		header('WWW-Authenticate: Basic realm="'.$name.'"');
		header('HTTP/1.0 401 Unauthorized');
		qdb_header("Login");
		echo "<p>Login failed.</p>";
		qdb_footer();
		exit;
	}
}
?>
