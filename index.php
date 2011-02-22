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
include("inc/common.php");

if (isset($_POST["id"]) && qdb_digit($_POST["id"])) {
	header("Location: ".$config['base_url']."?".$_POST["id"]);
	exit;
}

if ($_SERVER["QUERY_STRING"] != "" && qdb_digit($_SERVER["QUERY_STRING"])) {
	qdb_header("#".$_SERVER["QUERY_STRING"]);
	qdb_get_show($_SERVER["QUERY_STRING"]);
} else {
	qdb_header();
	if (is_readable("home.php")) {
		include("home.php");
	} else {
?>
<p>
Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
Pellentesque dolor. Cras et elit. Donec porttitor eros in
turpis. Vivamus dapibus, mauris a placerat fermentum, nunc
turpis egestas felis, in tempus odio mi sit amet arcu. In
hac habitasse platea dictumst. Suspendisse lacinia tortor
non sem. In quam. Etiam sed sem. Aliquam erat volutpat.
Vivamus quis pede. Vestibulum magna pede, euismod aliquam,
scelerisque vitae, vestibulum ultricies, massa. Nullam
vitae nunc. Cras vehicula pulvinar ante. In volutpat
rhoncus est. Sed quis lacus. Cras pellentesque commodo mi.
Etiam semper aliquam tellus.
</p>

<p>
Cras ante ipsum, gravida nec, pellentesque sed, dignissim
at, nunc. Maecenas quam. Curabitur tellus dolor, dictum eu,
semper a, tincidunt non, tellus. Suspendisse mollis magna
eu purus. Vestibulum porta. Mauris faucibus convallis lectus.
Proin felis leo, facilisis eget, sodales sed, mollis eget,
est. Praesent eu metus. Suspendisse a tellus. Integer sed
purus et massa malesuada ultrices. Cras aliquet diam ac diam.
Maecenas eget nibh et eros pharetra accumsan. Sed bibendum.
Nullam viverra diam ac massa. Phasellus varius risus eget
purus. Sed aliquet mi a dui. Aenean sit amet neque sit amet
metus semper ornare.
</p>
<?
	}
}
qdb_footer();
?>
