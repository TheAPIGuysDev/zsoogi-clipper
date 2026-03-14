/**
 * Zsoogi Clipper Bookmarklet
 *
 * Captures content from any webpage and creates a new Zsoogi Clip.
 * This vanilla JavaScript bookmarklet captures:
 * - Page URL and title
 * - Selected text
 * - First meaningful image (or YouTube thumbnail for YouTube videos)
 * - YouTube video transcripts (when transcript panel is open)
 *
 * @package Zsoogi_Clipper
 * @version 2.5.0
 */

(function() {
	// Capture the current page URL
	const rawUrl = location.href;

	// Clean up YouTube titles if needed
	// Removes view count prefix like "(153) " and "- YouTube" suffix
	const rawTitle = location.href.includes('youtube')
		? document.title.replace(/^\(\d+\)\s*/, '').replace(/\s*-\s*YouTube\s*$/, '')
		: document.title;

	// Capture any selected text on the page
	const rawSelection = window.getSelection().toString();

	// Plugin version (will be replaced by PHP)
	const v = '__VERSION__';

	// Capture image - prioritize YouTube thumbnails for YouTube videos
	let img = '';
	let videoId = '';
	let transcript = '';

	// YouTube video ID regex pattern
	const ytRegex = /(?:youtube(?:-nocookie)?\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|live)\/|.*[?&]v=)|youtu\.be\/)([^&?\/\s]{11})/i;
	const match = rawUrl.match(ytRegex);

	if (match && match[1]) {
		// If it's a YouTube video, get the video ID and thumbnail
		videoId = match[1];
		img = 'https://i.ytimg.com/vi/' + videoId + '/maxresdefault.jpg';

		// Try to capture YouTube transcript if available
		// Transcript is only available if the user has opened the transcript panel
		const segments = document.querySelectorAll('ytd-transcript-segment-renderer .segment-text');
		if (segments.length > 0) {
			// Combine all transcript segments and limit to 8000 characters
			transcript = Array.from(segments)
				.map(seg => seg.textContent.trim())
				.join(' ')
				.substring(0, 8000);
		}
	} else {
		// For non-YouTube pages, find the first meaningful image
		// Filter out small images, icons, logos, and avatars
		const images = Array.from(document.querySelectorAll('img')).filter(i =>
			i.naturalWidth > 200 &&
			i.naturalHeight > 200 &&
			!i.src.includes('icon') &&
			!i.src.includes('logo') &&
			!i.src.includes('avatar')
		);

		if (images.length > 0) {
			img = images[0].src;
		}
	}

	// Build the target URL
	const targetUrl = '__SITE_URL__/wp-admin/post-new.php?post_type=zsoogiclips&clipper_data=1';

	// Package all data as JSON for window.name bridge
	const clipperData = JSON.stringify({
		title: rawTitle,
		url: rawUrl,
		selection: rawSelection,
		image: img,
		clipper_version: v,
		youtube_video_id: videoId,
		youtube_transcript: transcript
	});

	// Open the new post window
	const w = window.open(
		'about:blank',
		'_blank',
		'width=900,height=700,menubar=no,toolbar=no,location=no,status=no'
	);

	// Alert if popup was blocked
	if (!w) {
		alert('Please allow popups for this site to use Zsoogi Clipper');
		return;
	}

	// Use window.name as a bridge to pass data (avoids GET URL length limits)
	w.name = clipperData;

	// After a brief delay, navigate to the actual post-new URL
	// The window.name persists across navigation
	setTimeout(function() {
		w.location.href = targetUrl;
	}, 100);
})();
