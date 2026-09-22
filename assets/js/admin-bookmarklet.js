/**
 * Zsoogi Clipper — bookmarklet installation page.
 *
 * Assigns the generated bookmarklet payload to the draggable link. The payload
 * is passed from PHP via wp_localize_script() rather than an inline <script>
 * tag, per the WordPress.org plugin guidelines.
 */
( function () {
	'use strict';

	if ( 'undefined' === typeof zsoogiClipperBookmarklet ) {
		return;
	}

	var link = document.getElementById( 'zsoogi-clipper-bookmarklet-link' );

	if ( ! link || ! zsoogiClipperBookmarklet.code ) {
		return;
	}

	link.href = 'javascript:' + zsoogiClipperBookmarklet.code;
}() );
