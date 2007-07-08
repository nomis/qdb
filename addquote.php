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
include("inc/common.php");

qdb_header("Add Quote");

$msg = NULL;

if (isset($_POST["quote"])) {
	$_POST["quote"] = trim($_POST["quote"]);
	$_POST["quote"] = preg_replace('/[\x00-\x02\x04-\x09\x0B-\x0C\x0E-\x19]/', '', $_POST["quote"]);
	$_POST["quote"] = preg_replace('/[\x03][0-9]{0,2}(,[0-9]{0,2})?/', '', $_POST["quote"]);
	$_POST["quote"] = preg_replace('/[\x0D\x0A]/', '\n', $_POST["quote"]);
	$_POST["quote"] = preg_replace('/[\x0D]/', '\n', $_POST["quote"]);
}

if (isset($_POST["quote"]) && $_POST["quote"] != "") {
	try {
		$stmt = $db->prepare("INSERT INTO quotes (quote, hide, users_id, ip) VALUES(:quote, :hide, :userid, :ip)");
		$stmt->bindParam(":quote", $_POST["quote"]);
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
		if ($stmt->rowCount() > 0) {
			$id = $db->lastInsertId("quotes_id_seq");
			qdb_ok('Added quote <a href="./?'.$id.'" title="quote #'.$id.'">#'.$id.'</a>.');
			unset($_POST["quote"]);
		} else {
			qdb_err("Quote already exists.");
		}
		$stmt->closeCursor();
		$db->commit();
	} catch (PDOException $e) {
		qdb_err("Error adding quote: ".$e->getMessage());
	}
}

qdb_messages();
?>
<p>Please remove timestamps unless necessary.</p>
<form method="post">
<textarea name="quote" rows="5" cols="80">
<?=isset($_POST["quote"]) ? htmlentities($_POST["quote"]) : ""?>
</textarea><br>
<? if (!$config['tags_useronly'] || $user !== FALSE) {?>
<label>Tags</label>: <input name="tags" size="50">
<? } ?>
<input class="cancel" type="reset">
<input class="ok" type="submit" value="Add Quote">
</form>
<? qdb_footer(); ?>
