/**
 * Zsoogi Clipper Bookmarklet
 *
 * Captures content from any webpage and creates a new Zsoogi Clip.
 * This vanilla JavaScript bookmarklet captures:
 * - Page URL and title
 * - Selected text
 * - First meaningful image (or YouTube thumbnail for YouTube videos)
 *
 * @package Zsoogi_Clipper
 * @version 2.4.2
 */

(function () {
	// Capture the current page URL.
	const u = encodeURIComponent( location.href );

	// Clean up YouTube titles if needed.
	// Removes view count prefix like "(153) " and "- YouTube" suffix.
	const rawTitle = location.href.includes( 'youtube' )
		? document.title.replace( /^\(\d+\)\s*/, '' ).replace( /\s*-\s*YouTube\s*$/, '' )
		: document.title;
	const t        = encodeURIComponent( rawTitle );

	// Capture any selected text on the page.
	const s = encodeURIComponent( window.getSelection().toString() );

	// Plugin version (will be replaced by PHP).
	const v = '__VERSION__';

	// Capture image - prioritize YouTube thumbnails for YouTube videos.
	let img = '';

	// YouTube video ID regex pattern.
	const ytRegex = /(?:youtube(?:-nocookie)?\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|live)\/|.*[?&]v=)|youtu\.be\/)([^&?\/\s]{11})/i;
	const match   = location.href.match( ytRegex );

	if (match && match[1]) {
		// If it's a YouTube video, get the high-quality thumbnail.
		img = encodeURIComponent( 'https://i.ytimg.com/vi/' + match[1] + '/maxresdefault.jpg' );
	} else {
		// For non-YouTube pages, find the first meaningful image.
		// Filter out small images, icons, logos, and avatars.
		const images = Array.from( document.querySelectorAll( 'img' ) ).filter(
			i =>
			i.naturalWidth > 200 &&
			i.naturalHeight > 200 &&
			! i.src.includes( 'icon' ) &&
			! i.src.includes( 'logo' ) &&
			! i.src.includes( 'avatar' )
		);

		if (images.length > 0) {
			img = encodeURIComponent( images[0].src );
		}
	}

	// Build the WordPress post-new URL with all captured data.
	const postUrl = '__SITE_URL__/wp-admin/post-new.php?post_type=zsoogiclips' +
		'&title=' + t +
		'&url=' + u +
		'&selection=' + s +
		(img ? '&image=' + img : '') +
		'&clipper_version=' + v;

	// Open the new post window.
	const w = window.open(
		postUrl,
		'_blank',
		'width=900,height=700,menubar=no,toolbar=no,location=no,status=no'
	);

	// Alert if popup was blocked.
	if ( ! w) {
		alert( 'Please allow popups for this site to use Zsoogi Clipper' );
	}
})();
