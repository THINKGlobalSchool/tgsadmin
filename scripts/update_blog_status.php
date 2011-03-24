<?

/*

After migration from 1.7 to 1.8, all blogs default to draft status. This script
updates all blog posts to published or draft.

*/

require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

$change_status = get_input('change_status');

$area = elgg_view_title("Update Blog Posts")."<br/>";

//list all blog guids, titles and statuses
$blogs = elgg_get_entities(array('type'=>'object','subtype'=>'blog','limit'=>10000));
//$area .= "<p>Blog count: {$blogs}</p>";

$area .= "<table class='update_blog_table'>";
$area .= "<tr><th>Title</th><th>Publish Status</th></tr>";
foreach($blogs as $blog) {
	if ($change_status == "draft") {
		$blog->status = "draft";
	} elseif ($change_status == "published") {
		$blog->status = "published";
	}
	$area .= "<tr><td>{$blog->title}</td><td>{$blog->status}</td></tr>";
}
$area .= "</table><br/>";

if ($change_status) {
	$area .= "<p><strong>Status updated.</strong></p><br/>";
}

$url = $CONFIG->url . "mod/tgstweaks/update_blog_status.php";
$area .= "<p>Update all blogs to status: <a href='{$url}?change_status=draft'>Draft</a> <a href='{$url}?change_status=published'>Published</a></p>";

$body = elgg_view_layout("one_column", $area);
page_draw("Update Blog Status",$body);
?>