<?
/*
	Copyright ©2008-2010  Simon Arlott

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

qdb_header("Search");
qdb_qs_preserve("q1");
qdb_qs_preserve("q2");

if (isset($_GET["submit"]) && isset($_GET["tags"])) {
	$_GET["tags"] = "";
}

if (isset($_GET["q1"])) {
	$_GET["q1"] = trim($_GET["q1"]);
	$_GET["q1"] = preg_replace('/ +/', '&', $_GET["q1"]);
	$_GET["q1"] = preg_replace('/&+/', '&', $_GET["q1"]);
	$_GET["q1"] = preg_replace('/\|+/', '|', $_GET["q1"]);
	$_GET["q1"] = preg_replace('/!+/', '!', $_GET["q1"]);
}

?><p>Tsearch2 &ndash; use &amp; for AND, | for OR, and ! for NOT, etc. &ndash; <strong>no spaces and no wildcards</strong>.<br><?
?>Regexp &ndash; <a href="http://www.postgresql.org/docs/8.2/static/functions-matching.html">SQL regular expression</a> (% for wildcard *, _ for wildcard ?).</p><?
?><form method="get" action="search.php"><?
	?><label for="q1">Tsearch2</label>:<?
	?><input type="hidden" name="tags" value="<?=isset($_GET["tags"]) ? qdb_htmlentities($_GET["tags"]) : ""?>"><?
	?><input type="text" name="q1" value="<?=isset($_GET["q1"]) ? qdb_htmlentities($_GET["q1"]) : ""?>" size="50"><?
	?><input type="submit" value="Submit Query"><br><?
	?><label for="q2">Regexp</label>:<?
	?><input type="text" name="q2" value="<?=isset($_GET["q2"]) ? qdb_htmlentities($_GET["q2"]) : ""?>" size="50"><?

	if (isset($_GET["tags"]) && $_GET["tags"] != "") {
		?><input type="submit" name="submit" title="Submit Query without current tag filter" value="(without tags)"><?
	}
?></form><br><?

$ok = TRUE;

if (isset($_GET["q1"]) && $_GET["q1"] != "") {
	try {
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

		$stmt = $db->prepare("SELECT to_tsquery(:search)");
		$stmt->bindParam(":search", $_GET["q1"]);

		$stmt->execute();
		if ($stmt->rowCount() <= 0) {
			$err = $stmt->errorInfo();
			$stmt->closeCursor();
			qdb_err(qdb_htmlentities('Tsearch2: '.$err[2]));
			$ok = FALSE;
		}
		$stmt->closeCursor();

		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		qdb_die($e);
	}
}

if ($ok && isset($_GET["q2"]) && $_GET["q2"] != "") {
	try {
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

		$stmt = $db->prepare("SELECT '' SIMILAR TO '".pg_escape_string($_GET["q2"])."'");
		$stmt->execute();
		if ($stmt->rowCount() <= 0) {
			$err = $stmt->errorInfo();
			$stmt->closeCursor();
			qdb_err(qdb_htmlentities(preg_replace('/^.*?invalid regular expression: /', 'Regexp: ', $err[2])));
			$ok = FALSE;
		}
		$stmt->closeCursor();

		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		qdb_die($e);
	}
}
qdb_messages();

if ($ok && ((isset($_GET["q1"]) && $_GET["q1"] != "") || (isset($_GET["q2"]) && $_GET["q2"] != ""))) {
	$sql = "quotes.hide=FALSE";
	$bind = array();
	if (isset($_GET["q1"]) && $_GET["q1"] != "") {
		$sql .= " AND (quotes.quote_idx @@ to_tsquery(:tsearch))";
		$bind[":tsearch"] = $_GET["q1"];
	}
	if (isset($_GET["q2"]) && $_GET["q2"] != "") {
// !?
//		$sql .= " AND (quotes.quote SIMILAR TO :regexp)";
//		$bind[":regexp"] = $_GET["q2"];
		$sql .= " AND (quotes.quote SIMILAR TO '".pg_escape_string($_GET["q2"])."')";
	}
	qdb_getall_show($sql, $bind, "id ASC");
}

qdb_footer();
?>
