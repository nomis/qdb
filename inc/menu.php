<?
/*
	Copyright ©2008-2012  Simon Arlott

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
?><ul class="menu"><?
	?><li><a href="./">Home</a></li><?
	?><li><a href="latest">Latest</a></li><?
	?><li><a href="browse">Browse</a></li><?
	?><li><a href="random">Random</a> <a href="random?minrating=1" title="Random &gt;0">&gt;0</a></li><?
	?><li><a href="top">Top</a></li><?
	?><li><a href="bottom">Bottom</a></li><?
	?><li><strong><a href="addquote">Add Quote</a></strong></li><?
	?><li><a href="search">Search</a></li><?

	if ($user === FALSE) {
		?><li><a href="login">Login</a></li><?
	}

	?><li><form class="quick" method="post" action="index"><label for="text">#</label><input type="text" name="id" size="3"></form></li><?
?></ul><?

if ($user !== FALSE) {
	?><ul class="menu"><?
		?><li>User: <?
			?><ul class="menu"><?
				?><li><a href="password">Change Password</a></li><?
			?></ul><?
		?></li><?

	if ($user->admin) {
			?><li>Admin: <?
				?><ul class="menu"><?
					?><li><a href="pending">Pending</a><?=$pending ? " ($pending->count)" : ""?></li><?
					?><li><a href="flagged">Flagged</a><?=$flagged ? " ($flagged->count)" : ""?></li><?
					?><li><a href="users">Users</a></li><?
					?><li><a href="tags">Tags</a></li><?
				?></ul><?
			?></li><?
	}

	?></ul><?
}
?>
