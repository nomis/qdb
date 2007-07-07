<?
include("inc/common.php");

qdb_header("Bottom");

try {
	$stmt = $db->prepare("SELECT * FROM quotes WHERE hide=FALSE ORDER BY rating, id DESC LIMIT ".$config['perpage']);
	$stmt2 = $db->prepare("SELECT * FROM tags WHERE quotes_id=:id");

	$stmt->execute();
	while ($quote = $stmt->fetch(PDO::FETCH_OBJ)) {
		$stmt2->bindParam(":id", $quote->id);
		$stmt2->execute();
		$tags = $stmt2->fetch(PDO::FETCH_OBJ);
		$stmt2->closeCursor();

		qdb_show($quote, tags);
	}
	$stmt->closeCursor();
} catch (PDOException $e) {
	qdb_die("Error retrieving quote: ".htmlentities($e->getMessage()).".");
}

qdb_messages();
qdb_footer();
?>
