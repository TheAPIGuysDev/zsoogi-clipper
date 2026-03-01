/**
 * Zsoogi Clipper Service Worker
 *
 * Minimal service worker enabling PWA installability and offline resilience.
 *
 * Strategy:
 *   - /wp-admin/* and /wp-login.php → network-first (auth state must be current)
 *   - Everything else              → cache-first (static assets)
 *
 * The __VERSION__ placeholder is replaced at serve-time by class-pwa.php
 * so the cache name bumps automatically with each plugin release.
 *
 * @package Zsoogi_Clipper
 * @version __VERSION__
 */

const CACHE_NAME = 'zsoogi-clipper-__VERSION__';

// ── Install ───────────────────────────────────────────────────────────────────
// Skip waiting so the new SW activates immediately without waiting for
// all open tabs to close.

self.addEventListener( 'install', function( event ) {
	self.skipWaiting();
} );

// ── Activate ──────────────────────────────────────────────────────────────────
// Delete caches belonging to older versions, then claim all open clients
// so this SW controls them without a page reload.

self.addEventListener( 'activate', function( event ) {
	event.waitUntil(
		caches.keys().then( function( keys ) {
			return Promise.all(
				keys
					.filter( function( key ) { return key !== CACHE_NAME; } )
					.map( function( key ) { return caches.delete( key ); } )
			);
		} ).then( function() {
			return clients.claim();
		} )
	);
} );

// ── Fetch ─────────────────────────────────────────────────────────────────────

self.addEventListener( 'fetch', function( event ) {
	// Only handle GET; skip non-http(s) (chrome-extension://, etc.).
	if ( event.request.method !== 'GET' ) return;
	if ( ! event.request.url.startsWith( 'http' ) ) return;

	const url     = event.request.url;
	const isAdmin = url.includes( '/wp-admin/' ) || url.includes( '/wp-login.php' );

	if ( isAdmin ) {
		// Network-first: always fetch fresh admin pages so auth state,
		// nonces, and post content are never served stale from cache.
		event.respondWith(
			fetch( event.request ).catch( function() {
				return caches.match( event.request );
			} )
		);
	} else {
		// Cache-first: serve static assets (CSS, JS, images) from cache,
		// populating the cache on first fetch.
		event.respondWith(
			caches.match( event.request ).then( function( cached ) {
				if ( cached ) {
					return cached;
				}
				return fetch( event.request ).then( function( response ) {
					if ( response.ok ) {
						const clone = response.clone();
						caches.open( CACHE_NAME ).then( function( cache ) {
							cache.put( event.request, clone );
						} );
					}
					return response;
				} );
			} )
		);
	}
} );
