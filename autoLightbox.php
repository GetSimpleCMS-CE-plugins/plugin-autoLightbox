<?php
/*
Plugin Name: autoLightbox
Description: Automatically adds Lightbox to all images
Version: 2.2
Author: CE Team
Author URI: https://www.getsimple-ce.ovh
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");
# register plugin
register_plugin(
	$thisfile, //Plugin id
	'autoLightbox', 	//Plugin name
	'2.2', 		//Plugin version
	'CE Team',  //Plugin author
	'https://www.getsimple-ce.ovh/', //author website
	'Automatically adds Lightbox to all images in content areas, gallery plugins not needed.', //Plugin description
	'plugins', //page type - on which admin tab to display
	''  // display main function (admin)
);

# activate filter 
add_filter('content', 'processLightbox');
add_action('theme-header', 'headerLightbox');
add_action('theme-footer', 'footerLightbox');

# functions
function processLightbox($content){
	
	// Simple regex replacement - no decoding at all
	function wrap_images_in_link($matches) {
		$img_tag = $matches[0];
		
		// Check if has excluded class
		if (preg_match('/class=["\']([^"\']*)\b(nolightbox|btn)\b/i', $img_tag)) {
			return $img_tag;
		}
		
		// Extract src
		if (preg_match('/src=["\']([^"\']+)["\']/i', $img_tag, $src_match)) {
			$src_url = $src_match[1];
			return '<a href="' . $src_url . '" class="glightbox">' . $img_tag . '</a>';
		}
		
		return $img_tag;
	}
	
	// Only wrap images that are NOT already inside <a> or <button> tags
	// First, wrap all eligible images
	$content = preg_replace_callback('/<img[^>]+>/i', 'wrap_images_in_link', $content);
	
	// Then unwrap those inside <a> tags
	$content = preg_replace('/<a([^>]*)>\s*<a href="[^"]*" class="glightbox">(<img[^>]*>)<\/a>\s*<\/a>/i', '<a$1>$2</a>', $content);
	
	// Unwrap those inside <button> tags
	$content = preg_replace('/<button([^>]*)>\s*<a href="[^"]*" class="glightbox">(<img[^>]*>)<\/a>\s*<\/button>/i', '<button$1>$2</button>', $content);
	
	return $content;
}

function headerLightbox()
{
	global $SITEURL;
	echo '<link rel="stylesheet" href="' . $SITEURL . 'plugins/autoLightbox/glightbox/glightbox.min.css">';
}

function footerLightbox()
{
	global $SITEURL;
	echo '<script src="' . $SITEURL . 'plugins/autoLightbox/glightbox/glightbox.min.js"></script>';
	echo "
<script type='text/javascript'>
const lightbox = GLightbox({
    touchNavigation: true,
    loop: true,
    autoplayVideos: true
});
</script>
";
}
?>
