<?php
/**
 * Blank Template for Documentation Pages
 *
 * This template is automatically used for any page containing the [zsoogi_docs] shortcode.
 * No theme modifications needed!
 *
 * @package Zsoogi_Clipper
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		html, body {
			width: 100%;
			height: 100%;
			overflow: hidden;
		}
		iframe {
			width: 100vw;
			height: 100vh;
			border: none;
			display: block;
		}
	</style>
</head>
<body>
	<?php
	// Output the page content (which contains the shortcode).
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</body>
</html>
