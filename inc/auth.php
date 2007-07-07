<?
$user = FALSE;

if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
	try {
		$stmt = $db->prepare("SELECT * FROM users WHERE name=:name AND pass=:pass");
		$stmt->bindParam(":name", $_SERVER["PHP_AUTH_USER"]);
		$stmt->bindParam(":pass", sha1($_SERVER["PHP_AUTH_PW"]));

		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		$stmt->closeCursor();
	} catch (PDOException $e) {
		qdb_die("Error checking username and password: ".htmlentities($e->getMessage()).".");
	}
}

function qdb_auth() {
	global $user, $name;
	if ($user === FALSE) {
		header('WWW-Authenticate: Basic realm="'.$name.'"');
		header('HTTP/1.0 401 Unauthorized');
		exit;
	}
}
?>
