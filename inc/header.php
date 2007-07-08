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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en-GB"><head>
<title><?=htmlentities($config['name'])?><?=isset($title) ? ": ".htmlentities($title) : ""?></title>
<link rel="stylesheet" href="qdb.css" title="Stylesheet" type="text/css">
</head>
<body>
<div id="top">
<h1><?=htmlentities($config['name'])?></h1>
<? if (isset($title)) { ?><h2><?=htmlentities($title)?></h2><? } ?>
<div class="clr"></div>
</div>
<? include("menu.php") ?>
<div id="content">
