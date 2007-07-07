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

if (isset($_GET["id"]) && qdb_digit($_GET["id"])) {
	if (isset($_GET["rate"])) {
		if ($_GET["rate"] < 0) { $_GET["rate"] = -1; } else { $_GET["rate"] = 1; }

		qdb_header("Rate #".$_GET["id"]);
		if (!qdb_secure(array("id","rate","now"))) {
			qdb_err("Invalid URL parameters.");
		} else {
			try {
				$db->exec("DELETE FROM votes WHERE ts < CURRENT_DATE");

				$stmt = $db->prepare("SELECT * FROM votes WHERE quotes_id=:quoteid AND vote=:vote AND (users_id=:userid OR ip=:ip)");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->bindParam(":vote", $_GET["rate"]);
				if ($user === FALSE) {
					$stmt->bindParam(":userid", NULL);
				} else {
					$stmt->bindParam(":userid", $user->id);
				}
				$stmt->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);

				$stmt->execute();
				if ($stmt->fetch(PDO::FETCH_OBJ)) {
					$stmt->closeCursor();

					qdb_err('You\'ve already rated quote <a href="./?'.$_GET["id"].'" title="quote #'.$_GET["id"].'">#'.$_GET["id"].'</a> today!');
				} else {
					$stmt->closeCursor();

					$stmt = $db->prepare("UPDATE quotes SET rating=rating+:vote WHERE id=:quoteid");
					$stmt2 = $db->prepare("INSERT INTO votes (quotes_id, vote, users_id, ip)"
						." VALUES(:quoteid, :vote, :userid, :ip)");

					$stmt->bindParam(":quoteid", $_GET["id"]);
					$stmt->bindParam(":vote", $_GET["rate"]);

					$stmt2->bindParam(":quoteid", $_GET["id"]);
					$stmt2->bindParam(":vote", $_GET["rate"]);
					if ($user === FALSE) {
						$stmt2->bindParam(":userid", NULL);
					} else {
						$stmt2->bindParam(":userid", $user->id);
					}
					$stmt2->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);

					$stmt->execute();
					$stmt2->execute();
					if ($stmt->rowCount() > 0) {
						qdb_ok('Quote <a href="./?'.$_GET["id"].'" title="quote #'.$_GET["id"].'">#'.$_GET["id"].'</a> rated.');
					} else {
						qdb_err("Quote ".$_GET["id"]." does not exist.");
					}
					$stmt->closeCursor();
					$stmt2->closeCursor();
				}

				$db->commit();
			} catch (PDOException $e) {
				qdb_die("Error rating quote: ".htmlentities($e->getMessage()).".");
			}
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_GET["flag"])) {
		if ($_GET["flag"] > 0) { $_GET["flag"] = 1; } else { $_GET["flag"] = 0; }

		qdb_header(($_GET["flag"] == 0 ? "Unflag" : "Flag")." #".$_GET["id"]);
		if (!qdb_secure(array("id","flag","now"))) {
			qdb_err("Invalid URL parameters.");
		} else if ($_GET["flag"] == 0 && ($user === FALSE || !$user->admin)) {
			?><p>You are not an admin!</p><?
		} else {
			try {
				$stmt = $db->prepare("UPDATE quotes SET flag=:flag WHERE id=:quoteid");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->bindParam(":flag", $_GET["flag"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok('Quote <a href="./?'.$_GET["id"].'" title="quote #'.$_GET["id"].'">#'.$_GET["id"].'</a> '.($_GET["flag"] == 0 ? "un" : "").'flagged.');
				} else {
					qdb_err("Quote ".$_GET["id"]." does not exist.");
				}
				$stmt->closeCursor();
				$db->commit();
			} catch (PDOException $e) {
				qdb_die("Error ".($_GET["flag"] == 0 ? "un" : "")."flagging quote: ".htmlentities($e->getMessage()).".");
			}
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_GET["hide"])) {
		if ($_GET["hide"] > 0) { $_GET["hide"] = 1; } else { $_GET["hide"] = 0; }

		qdb_header(($_GET["hide"] == 0 ? "show" : "Hide")." #".$_GET["id"]);
		if (!qdb_secure(array("id","hide","now"))) {
			qdb_err("Invalid URL parameters.");
		} else if ($user === FALSE || !$user->admin) {
			?><p>You are not an admin!</p><?
		} else {
			try {
				$stmt = $db->prepare("UPDATE quotes SET hide=:hide WHERE id=:quoteid");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->bindParam(":hide", $_GET["hide"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok('Quote <a href="./?'.$_GET["id"].'" title="quote #'.$_GET["id"].'">#'.$_GET["id"].'</a> '.($_GET["hide"] == 0 ? "shown" : "hidden").'.');
				} else {
					qdb_err("Quote ".$_GET["id"]." does not exist.");
				}
				$stmt->closeCursor();
				$db->commit();
			} catch (PDOException $e) {
				qdb_die("Error ".($_GET["hide"] == 0 ? "showing" : "hiding")." quote: ".htmlentities($e->getMessage()).".");
			}
		}
		qdb_messages();
		qdb_footer();
	} else if (isset($_GET["del"])) {
		qdb_header("Delete #".$_GET["id"]);
		if ($user === FALSE || !$user->admin) {
			?><p>You are not an admin!</p><?
		} else {
			try {
				$stmt = $db->prepare("DELETE FROM quotes WHERE id=:quoteid");
				$stmt->bindParam(":quoteid", $_GET["id"]);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					qdb_ok("Quote #".$_GET["id"]." deleted.");
				} else {
					qdb_err("Quote #".$_GET["id"]." does not exist.");
				}
				$stmt->closeCursor();
				$db->commit();
			} catch (PDOException $e) {
				qdb_die("Error ".($_GET["flag"] == 0 ? "un" : "")."flagging quote: ".htmlentities($e->getMessage()).".");
			}
		}
		qdb_messages();
		qdb_footer();
	}
}
?>
