<?
/*
	Copyright ©2008 Simon Arlott

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU Affero General Public License v3
	as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.


	$Id$
*/
?></div><?
include("menu.php")
?><div id="bottom"><?
	?><p><?
		?><a href="qdb-2_3.tar.bz2" title="QDB 2.3 source">QDB 2.3</a><?
		?> | <?=$quotes_count->count?> quote<?=$quotes_count->count == 1 ? "" : "s"?>,<?
		?> <?=$tags_count->count?> tag<?=$tags_count->count == 1 ? "" : "s"?><?
	?></p><?
?></div><?
?></body></html>
