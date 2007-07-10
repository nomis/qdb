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


	$Id$
*/

function modquote_op($op) {
	if (!$op
			|| !$op.parentNode
			|| !$op.parentNode.parentNode
			|| !$op.parentNode.parentNode.parentNode
			|| !XMLHttpRequest) {
		return true;
	}

	var $parent = $op.parentNode.parentNode;
	var $xhr = new XMLHttpRequest();
	$xhr.open("GET", $op.href + "&async=1", true);
	$xhr.onreadystatechange = function() {
		if ($xhr.readyState == 4) {
			if ($xhr.status == 200 && $xhr.responseXML) {
				$page = $xhr.responseXML;

				if ($page.childNodes.length == 1
						&& $page.childNodes[0]
						&& $page.childNodes[0].tagName == "qdb") {
					var $nodes = $page.childNodes[0].childNodes;
					var $msgs = null;
					var $quote = null;
					var $len = $nodes.length;
					var $i, $n_msgs, $p_msgs, $n_quote;

					for ($i = 0; $i < $len; $i++) {
						if ($nodes[$i].tagName == "msgs") {
							$msgs = $nodes[$i];
						} else if ($nodes[$i].tagName == "quote") {
							$quote = $nodes[$i];
						}
					}

					if ($quote) {
						$n_quote = document.createElement("div");
						$n_quote.setAttribute("class", "quote asyncquote");
						$n_quote.innerHTML = $quote.textContent;

						$parent.parentNode.replaceChild($n_quote, $parent);
						$parent = $n_quote;
					}

					if ($msgs) {
						$n_msgs = document.createElement("div");
						$n_msgs.setAttribute("class", "asyncmsgs");
						$n_msgs.setAttribute("onclick", "void(this.parentNode.removeChild(this))");
						$n_msgs.innerHTML = "<p>" + $msgs.textContent + "</p>";

						$p_msgs = $parent.childNodes[3];

						if ($p_msgs) {
							if ($p_msgs.getAttribute("class").match("\\basyncmsgs\\b")) {
								$parent.replaceChild($n_msgs, $p_msgs);
							} else {
								$parent.insertBefore($n_msgs, $p_msgs);
							}
						}
					}
				}
 			} else {
				if (confirm("The background request to: " + $op.title + " failed... try again directly?")) {
					document.location.replace($op.href);
				}
			}
		}
	}
	$xhr.send("");

	return false;
}

function modquote_tags($submit) {
	if (!$submit
			|| !$submit.parentNode
			|| !$submit.parentNode.parentNode
			|| !$submit.parentNode.parentNode.parentNode
			|| !$submit.parentNode.parentNode.parentNode.parentNode
			|| !XMLHttpRequest) {
		return true;
	}

	var $parent = $op.parentNode.parentNode;

	// inimplemented

	return true;
}

function modquote_edit($op) {
	if (!$op
			|| !$op.parentNode
			|| !$op.parentNode.parentNode
			|| !$op.parentNode.parentNode.parentNode
			|| !XMLHttpRequest) {
		return true;
	}

	var $parent = $op.parentNode.parentNode;

	// inimplemented

	return true;
}
