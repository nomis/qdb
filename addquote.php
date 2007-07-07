<?
include("inc/common.php");

qdb_header("Add Quote");

$msg = NULL;

if (isset($_POST["quote"])) { $_POST["quote"] = trim($_POST["quote"]); }

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
			qdb_ok('Added quote <a href="./?'.$id.'">'.$id.'</a>');
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
<label>Tags</label>: <input name="tags" size="50">
<input class="cancel" type="reset">
<input class="ok" type="submit" value="Add Quote">
</form>
<? qdb_footer(); ?>
