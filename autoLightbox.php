<?php
/*
Plugin Name: Hello World
Description: Starter plugin 
Version: 1.0
Author: GetSimple CE
Author URI: https://www.getsimple-ce.ovh
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, //Plugin id
	'autoLightbox', 	//Plugin name
	'1.0', 		//Plugin version
	'GetSimple CE',  //Plugin author
	'https://www.getsimple-ce.ovh/', //author website
	'Automatically adds Lightbox to all images in content areas, gallery plugins not needed.', //Plugin description
	'plugins', //page type - on which admin tab to display
	''  //main function (administration)
);

# activate filter 
add_action('theme-header', 'headerLightbox');
add_action('theme-footer', 'footerLightbox');


# functions
function headerLightbox()
{
	global $SITEURL;
	echo '<link rel="stylesheet" href="' . $SITEURL . 'plugins/autoLightbox/glightbox/glightbox.min.css">';


	global $content;
	$string = html_entity_decode($content);
	function wrap_images_in_link($matches)
	{
		$img_tag = $matches[0]; // Cały tag <img>
		preg_match('/src="([^"]+)"/', $img_tag, $src_match); // Wyszukaj src
		$src_url = $src_match[1]; // Pobierz url z src
		return '<a href="' . $src_url . '" class="glightbox">' . $img_tag . '</a>'; // Zwróć nowy kod z <a> wokół <img>
	}
	$content = preg_replace_callback('/<img[^>]*src="[^"]+"[^>]*>/', 'wrap_images_in_link', $string);
}

function footerLightbox()
{
	global $GSPLUGINPATH;
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