<?
include("inc/common.php");

if ($_SERVER["QUERY_STRING"] != "" && qdb_digit($_SERVER["QUERY_STRING"])) {
	qdb_header("#".$_SERVER["QUERY_STRING"]);
	qdb_get_show($_SERVER["QUERY_STRING"]);
} else {
	qdb_header();
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
qdb_footer();
?>
