<?
/*
	Copyright ©2007 Simon Arlott

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License v3
	as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.

	http://svn.lp0.eu/simon/qdb/
	$Id$
*/
?></div><?
include("menu.php")
?><div id="bottom"><?
	?><p><?
		?><a href="qdb-2_0.tar.bz2" title="QDB 2.0 source">QDB 2.0</a><!-- http://svn.lp0.eu/simon/qdb/ --><?
		?> | <?=$quotes_count->count?> quote<?=$quotes_count->count == 1 ? "" : "s"?>,<?
		?> <?=$tags_count->count?> tag<?=$tags_count->count == 1 ? "" : "s"?><?
	?></p><?
?></div><?
?></body></html>
