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
