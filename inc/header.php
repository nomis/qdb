<?
/*
	Copyright ©2008-2011  Simon Arlott

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
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en-GB"><?
	?><head><?
		?><title><?=qdb_htmlentities($config['name'])?><?=isset($title) ? ": ".qdb_htmlentities($title) : ""?></title><?
		?><meta name="generator" content="QDB 2.0"><!-- QDB 2.0 ©2008 Simon Arlott --><?
		?><link rel="stylesheet" href="default.css" title="Stylesheet" type="text/css"><?
		?><script language="JavaScript" type="application/javascript" src="async.js"></script><?
	?></head><?
	?><body><?
		?><div id="top"><?
			?><h1><?=qdb_htmlentities($config['name'])?></h1><?

if (isset($title)) {
			?><h2><?=qdb_htmlentities($title)?></h2><?
}

			?><div class="clr"></div><?
		?></div><?

include("menu.php");
?><div id="content"><?
?>
