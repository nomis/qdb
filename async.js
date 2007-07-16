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

function create_xhr() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null;
}

function modquote_replace($page, $current) {
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

			$current.parentNode.replaceChild($n_quote, $current);
			$current = $n_quote;
		}

		if ($msgs) {
			$n_msgs = document.createElement("div");
			$n_msgs.setAttribute("class", "asyncmsgs");
			$n_msgs.setAttribute("onclick", "void(this.parentNode.removeChild(this))");
			$n_msgs.innerHTML = "<p>" + $msgs.textContent + "</p>";

			$p_msgs = $current.childNodes[3];

			if ($p_msgs) {
				if ($p_msgs.getAttribute("class").match("\\basyncmsgs\\b")) {
					$current.replaceChild($n_msgs, $p_msgs);
				} else {
					$current.insertBefore($n_msgs, $p_msgs);
				}
			} else {
				return false;
			}
		}

		return ($quote || $msgs);
	}
	return false;
}

function modquote_xhr($xhr, $title, $href, $form, $parent) {
		var $failed = false;

		try {
			if ($xhr.readyState == 4) {
				if (!($xhr.status == 200 && $xhr.responseXML && modquote_replace($xhr.responseXML, $parent))) {
					$failed = true;
				}
			}
		} catch(e) {
			$failed = true;
		}

		if ($failed) {
			if (confirm("The background request to " + $title + " failed... try again directly?")) {
				if ($href) {
					document.location.replace($href);
				} else if ($form) {
					$form.var_asyncdisabled = 1;
					$form.submit();
				}
			}
		}
}

function modquote_op($op) {
	if (!$op
			|| !$op.parentNode
			|| !$op.parentNode.parentNode
			|| !$op.parentNode.parentNode.parentNode) {
		return true;
	}

	var $parent = $op.parentNode.parentNode;
	var $title = $op.title;
	var $href = $op.href;

	var $xhr = create_xhr();
	if (!$xhr) return true;

	try {
		$xhr.open("GET", $op.href + "&async=1", true);
		$xhr.onreadystatechange = function() {
			modquote_xhr($xhr, $title, $href, null, $parent);
		}
		$xhr.send(null);
	} catch(e) {
		return true;
	}

	return false;
}

function modquote_tags($form) {
	if (!$form
			|| !$form.parentNode
			|| !$form.parentNode.parentNode) {
		return true;
	}

	var $parent = $form.parentNode;
	var $title = $form.submit.title;

	if ($form.var_asyncdisabled.value == 1) return true;

	var $xhr = create_xhr();
	if (!$xhr) return true;

	try {
		$xhr.open("POST", $form.action, true);
		$xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		$xhr.onreadystatechange = function() {
			modquote_xhr($xhr, $title, null, $form, $parent);
		}
		$xhr.send("async=1&id=" + escape($form.id.value) + "&tagset=" + escape($form.tagset.value));
	} catch(e) {
		return true;
	}

	return false;
}


function modquote_edit($op) {
	// unimplemented
	return true;
}
