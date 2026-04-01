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
	console.log( '[ZsoogiClips] bookmarklet start' );

	const u = encodeURIComponent( location.href );
	console.log( '[ZsoogiClips] url:', location.href );

	const rawTitle = location.href.includes( 'youtube' )
		? document.title.replace( /^\(\d+\)\s*/, '' ).replace( /\s*-\s*YouTube\s*$/, '' )
		: document.title;
	const t = encodeURIComponent( rawTitle );
	console.log( '[ZsoogiClips] title:', rawTitle );

	const s = encodeURIComponent( window.getSelection().toString() );
	console.log( '[ZsoogiClips] selection length:', window.getSelection().toString().length );

	const v = '__VERSION__';
	let img = '';

	const ytRegex = /(?:youtube(?:-nocookie)?\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|live)\/|.*[?&]v=)|youtu\.be\/)([^&?\/\s]{11})/i;
	const match = location.href.match( ytRegex );

	if ( match && match[1] ) {
		console.log( '[ZsoogiClips] YouTube video id:', match[1] );
		img = encodeURIComponent( 'https://i.ytimg.com/vi/' + match[1] + '/maxresdefault.jpg' );
	} else {
		const images = Array.from( document.querySelectorAll( 'img' ) ).filter(
			function ( i ) {
				return i.naturalWidth > 200 &&
					i.naturalHeight > 200 &&
					i.src.indexOf( 'icon' ) === -1 &&
					i.src.indexOf( 'logo' ) === -1 &&
					i.src.indexOf( 'avatar' ) === -1;
			}
		);
		console.log( '[ZsoogiClips] images found:', images.length );
		if ( images.length > 0 ) {
			img = encodeURIComponent( images[0].src );
		}
	}

	const postUrl = '__SITE_URL__/wp-admin/post-new.php?post_type=zsoogiclips' +
		'&title=' + t +
		'&url=' + u +
		'&selection=' + s +
		( img ? '&image=' + img : '' ) +
		'&clipper_version=' + v;
	console.log( '[ZsoogiClips] opening:', postUrl );

	const w = window.open(
		postUrl,
		'_blank',
		'width=900,height=700,menubar=no,toolbar=no,location=no,status=no'
	);

	if ( !w ) {
		console.log( '[ZsoogiClips] popup blocked' );
		alert( 'Please allow popups for this site to use Zsoogi Clipper' );
	} else {
		console.log( '[ZsoogiClips] popup opened ok' );
	}
})();
