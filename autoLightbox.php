<?php
/*
Plugin Name: autoLightbox
Description: Automatically adds Lightbox to all images
Version: 2.0
Author: CE Team
Author URI: https://www.getsimple-ce.ovh
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, //Plugin id
	'autoLightbox', 	//Plugin name
	'2.0', 		//Plugin version
	'CE Team',  //Plugin author
	'https://www.getsimple-ce.ovh/', //author website
	'Automatically adds Lightbox to all images in content areas, gallery plugins not needed.', //Plugin description
	'plugins', //page type - on which admin tab to display
	''  //main function (administration)
);

# activate filter 
add_action('theme-header', 'headerLightbox');
add_action('theme-footer', 'footerLightbox');

# functions
function headerLightbox(){
	global $SITEURL, $content;
	echo '<link rel="stylesheet" href="' . $SITEURL . 'plugins/autoLightbox/glightbox/glightbox.min.css">';

	//$string = html_entity_decode($content);
	$string = $content;
	
	$dom = new DOMDocument();
	libxml_use_internal_errors(true); // Suppress HTML5 warnings
	$dom->loadHTML('<?xml encoding="utf-8" ?>' . $string, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	libxml_clear_errors();
	
	$xpath = new DOMXPath($dom);
	$images = $xpath->query('//img');
	
	foreach ($images as $img) {
		$classes = $img->getAttribute('class');
		$excludedClasses = ['nolightbox', 'btn']; // excluded classes
		$hasExcludedClass = false;
		
		foreach ($excludedClasses as $excludedClass) {
			if (strpos($classes, $excludedClass) !== false) {
				$hasExcludedClass = true;
				break;
			}
		}
		
		if ($hasExcludedClass) {
			continue; // Skip this image
		}
		
		// Check if image is already inside a clickable element
		$parent = $img->parentNode;
		$isInsideClickable = false;
		
		while ($parent && $parent->nodeName !== 'body' && $parent->nodeName !== '#document') {
			$parentTag = strtolower($parent->nodeName);
			$parentClass = '';
			
			if ($parent->hasAttribute('class')) {
				$parentClass = $parent->getAttribute('class');
			}
			
			// Check if parent is <a>, <button>, or has 'btn' class
			if ($parentTag === 'a' || $parentTag === 'button' || strpos($parentClass, 'btn') !== false) {
				$isInsideClickable = true;
				break;
			}
			
			$parent = $parent->parentNode;
		}
		
		if ($isInsideClickable) {
			continue; // Skip this image
		}
		
		// Wrap image with lightbox link
		$link = $dom->createElement('a');
		$link->setAttribute('href', $img->getAttribute('src'));
		$link->setAttribute('class', 'glightbox');
		
		$imgClone = $img->cloneNode(true);
		$img->parentNode->replaceChild($link, $img);
		$link->appendChild($imgClone);
	}
	
	// Save modified HTML
	$content = $dom->saveHTML();
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
