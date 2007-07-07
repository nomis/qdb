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
?>
<ul class="menu">
	<li><a href="./">Home</a></li>
	<li><a href="latest.php">Latest</a></li>
	<li><a href="browse.php">Browse</a></li>
	<li><a href="random.php">Random</a> <a href="random.php?minrating=1" title="Random &gt;0">&gt;0</a></li>
	<li><a href="top.php">Top</a></li>
	<li><a href="bottom.php">Bottom</a></li>
	<li><strong><a href="addquote.php">Add Quote</a></strong></li>
	<li><a href="search.php">Search</a></li>
	<? if ($user === FALSE) {
		?><li><a href="login.php">Login</a></li><?
	} ?>
</ul>
<? if ($user !== FALSE) {
	?><ul class="menu">
		<li>User: <ul class="menu">
			<li><a xhref="password.php">Change Password</a></li>
		</ul></li><?
		if ($user->admin) {
			?><li>Admin: <ul class="menu">
				<li><a href="pending.php">Pending</a><?=$pending ? " ($pending->count)" : ""?></li>
				<li><a href="flagged.php">Flagged</a><?=$flagged ? " ($flagged->count)" : ""?></li>
				<li><a xhref="users.php">Users</a></li>
				<li><a xhref="tags.php">Tags</a></li>
			</ul></li><?
		} ?>
	</ul><?
} ?>
