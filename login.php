<?
include("inc/common.php");

qdb_auth();
qdb_header("Login");
?>
<p>You are logged in as <strong><?=htmlentities($user->name)?></strong>.</p>
<? qdb_footer(); ?>
