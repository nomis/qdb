<ul class="menu">
	<li><a href="./">Home</a></li>
	<li><a href="latest.php">Latest</a></li>
	<li>Browse</li>
	<li><a href="random.php">Random</a> <a href="random.php?minrating=1" title="Random &gt;0">&gt;0</a></li>
	<li><a href="top.php">Top <?=$config['top']?></a></li>
	<li><a href="bottom.php">Bottom</a></li>
	<li><strong><a href="addquote.php">Add Quote</a></strong></li>
	<li>Search</li>
	<? if ($user === FALSE) {
		?><li><a href="login.php">Login</a></li><?
	} else {
		if ($user->admin) {
			?><li>Admin</li><?
		}
	} ?>
</ul>
