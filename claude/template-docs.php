<?php
/**
 * Template Name: Zsoogi Docs
 *
 * Serves MkDocs documentation with clean URLs
 */

// Get the requested doc page from URL
$doc_page = get_query_var('doc_page', 'index.html');

// Get docs directory
$docs_dir = plugin_dir_path(__FILE__) . 'docs-built/';

// Security: sanitize and validate path
$doc_page = str_replace(['..', '\\'], '', $doc_page);
if (empty($doc_page) || $doc_page === '/') {
    $doc_page = 'index.html';
}

// If requesting a directory, append index.html
if (is_dir($docs_dir . $doc_page)) {
    $doc_page = rtrim($doc_page, '/') . '/index.html';
}

$file_path = $docs_dir . $doc_page;

// Check if file exists and is within docs directory
$real_path = realpath($file_path);
$real_docs_dir = realpath($docs_dir);

if (!$real_path || strpos($real_path, $real_docs_dir) !== 0) {
    status_header(404);
    die('Documentation page not found.');
}

// Determine content type
$extension = pathinfo($file_path, PATHINFO_EXTENSION);
$mime_types = [
    'html' => 'text/html',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'gif' => 'image/gif',
    'svg' => 'image/svg+xml',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
];

$content_type = $mime_types[$extension] ?? 'text/html';

// If it's an HTML file, we need to fix asset paths
if ($extension === 'html') {
    $html = file_get_contents($file_path);

    // Replace relative asset paths with WordPress plugin URLs
    $plugin_url = plugins_url('claude/docs-built/', dirname(__FILE__));

    // Fix CSS links
    $html = preg_replace(
        '/(href=["\'])(?!http|\/\/)(.*?\.css["\'])/',
        '$1' . $plugin_url . '$2',
        $html
    );

    // Fix JS links
    $html = preg_replace(
        '/(src=["\'])(?!http|\/\/)(.*?\.js["\'])/',
        '$1' . $plugin_url . '$2',
        $html
    );

    // Fix image links
    $html = preg_replace(
        '/(src=["\'])(?!http|\/\/)(.*?\.(png|jpg|gif|svg)["\'])/',
        '$1' . $plugin_url . '$2',
        $html
    );

    // Output the modified HTML
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
    exit;
} else {
    // Serve other assets directly
    header('Content-Type: ' . $content_type);

    // Cache headers for assets
    if (in_array($extension, ['css', 'js', 'png', 'jpg', 'gif', 'svg', 'woff', 'woff2'])) {
        header('Cache-Control: public, max-age=2592000'); // 30 days
    }

    readfile($file_path);
    exit;
}
