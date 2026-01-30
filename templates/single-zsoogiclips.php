<?php
/**
 * Template for displaying single Zsoogi Clips
 *
 * This template provides a custom display for Zsoogi Clips that is compatible
 * with both classic and block themes (like Twenty Twenty-Five).
 *
 * .wp-block-navigation.items-justified-right
 *
 * @package Zsoogi\Zsoogi_Clips
 * @since 2.3.0
 */

// Enqueue custom styles for Zsoogi Clips.
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_singular( 'zsoogiclips' ) ) {
			// Only enqueue Twenty Twenty-Five theme styles if registered.
			if ( wp_style_is( 'twentytwentyfive', 'registered' ) ) {
				wp_enqueue_style( 'twentytwentyfive' );
			}

			// Add inline styles to wp-block-library (guaranteed to exist in block themes).
			if ( wp_style_is( 'wp-block-library', 'registered' ) ) {
				wp_add_inline_style(
					'wp-block-library',
					'
				.zsoogi-content-wrapper { max-width: 800px; margin: 2rem auto; padding: 0 2rem; }
				.zsoogi-post-content .entry-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0; }
				.zsoogi-post-content .entry-title { margin-bottom: 1rem; font-size: 2.5rem; line-height: 1.2; }
				.zsoogi-post-content .entry-meta { color: #666; font-size: 0.9rem; }
				.zsoogi-post-content .entry-meta a { color: inherit; text-decoration: none; }
				.zsoogi-post-content .entry-meta a:hover { text-decoration: underline; }
				.zsoogi-post-content .entry-featured-image { margin-bottom: 2rem; }
				.zsoogi-post-content .entry-featured-image img { width: 100%; height: auto; border-radius: 8px; }
				.zsoogi-post-content .entry-content { line-height: 1.8; font-size: 1.1rem; }
				.zsoogi-post-content .entry-footer { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e0e0e0; }
				.comments-area { margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e0e0e0; }
				div.wp-site-blocks > div.wp-block-group > div > div.wp-block-group.is-content-justification-space-between,
				div.wp-site-blocks > header > div > div > div { justify-content: space-between; }
				.comments-title { margin-bottom: 1.5rem; font-size: 1.5rem; }
				.comment-list { list-style: none; margin: 0; padding: 0; }
				.comment-list .comment { margin-bottom: 1.5rem; padding: 1rem; background: #f9f9f9; border-radius: 4px; }
				.comment-list .children { list-style: none; margin-left: 2rem; margin-top: 1rem; }
				.comment-author img { border-radius: 50%; margin-right: 0.5rem; vertical-align: middle; }
				.comment-metadata { font-size: 0.85rem; color: #666; margin-bottom: 0.5rem; }
				.comment-form-comment textarea, .comment-form-author input, .comment-form-email input, .comment-form-url input { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
				@media (max-width: 768px) {
					.zsoogi-content-wrapper { padding: 0 1rem; }
					.zsoogi-post-content .entry-title { font-size: 2rem; }
					.comment-list .children { margin-left: 1rem; }
				}
				'
				);
			}
		}
	}
);

// Check if this is a block theme.
$is_block_theme = function_exists( 'wp_is_block_theme' ) && wp_is_block_theme();

if ( $is_block_theme ) {
	// For block themes, manually construct HTML structure with proper WordPress hooks.
	/**
	 * Create a block-backed shim for classic header templates.
	 */
	?>
	<!DOCTYPE html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div class="wp-site-blocks">
	<header class="wp-block-template-part">
		<?php
		block_template_part( 'header' );
		?>
	</header>
		<div class="wp-block-group">
			<div class="wp-block-group has-global-padding is-layout-constrained">
				<main class="zsoogi-content-wrapper">
	<?php
} else {
	// For classic themes, use get_header().
	get_header();
	?>
	<div class="zsoogi-content-wrapper">
		<main class="site-main">
	<?php
}

// Main content loop (same for both theme types).
while ( have_posts() ) :
	the_post();
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zsoogi-post-content' ); ?>>

		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<div class="entry-meta">
				<?php
				// Author.
				printf(
					'<span class="byline">%s <a href="%s">%s</a></span>',
					esc_html__( 'By', 'zsoogi-clips' ),
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_html( get_the_author() )
				);

				// Date.
				printf(
					'<span class="posted-on"> &bull; %s</span>',
					esc_html( get_the_date() )
				);

				// Zsoogi Type taxonomy.
				$terms = get_the_terms( get_the_ID(), 'zsoogi_type' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					echo '<span class="zsoogi-types"> &bull; ';
					$term_links = array();
					foreach ( $terms as $term ) {
						$term_links[] = sprintf(
							'<a href="%s">%s</a>',
							esc_url( get_term_link( $term ) ),
							esc_html( $term->name )
						);
					}
					echo implode( ', ', $term_links );
					echo '</span>';
				}
				?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="entry-featured-image">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php endif; ?>

		<div class="entry-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'zsoogi-clips' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<footer class="entry-footer">
			<?php
			// echo '<pre>$terms ' . print_r($terms, true) . '</pre>';
			// Edit link.
			edit_post_link(
				sprintf(
					/* translators: %s: Post title. Only visible to screen readers. */
					esc_html__( 'Edit %s', 'zsoogi-clips' ),
					'<span class="screen-reader-text">' . get_the_title() . '</span>'
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer>

	</article>

	<?php
	// Custom comments section (compatible with block themes).
	if ( comments_open() || get_comments_number() ) :
		?>
		<div id="comments" class="comments-area">
			<?php if ( have_comments() ) : ?>
				<h2 class="comments-title">
					<?php
					$comments_number = get_comments_number();
					if ( 1 === $comments_number ) {
						printf( esc_html__( 'One comment on &ldquo;%s&rdquo;', 'zsoogi-clips' ), esc_html( get_the_title() ) );
					} else {
						printf(
							/* translators: 1: number of comments, 2: post title */
							esc_html( _n( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', $comments_number, 'zsoogi-clips' ) ),
							esc_html( number_format_i18n( $comments_number ) ),
							esc_html( get_the_title() )
						);
					}
					?>
				</h2>

				<ol class="comment-list">
					<?php
					wp_list_comments(
						array(
							'style'       => 'ol',
							'short_ping'  => true,
							'avatar_size' => 50,
						)
					);
					?>
				</ol>

				<?php
				the_comments_navigation();
				?>

			<?php endif; ?>

			<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
				<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'zsoogi-clips' ); ?></p>
			<?php endif; ?>

			<?php
			comment_form(
				array(
					'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
					'title_reply_after'  => '</h2>',
				)
			);
			?>
		</div>
		<?php
	endif;

endwhile;

if ( $is_block_theme ) {
	?>
				</main>
			</div>
		</div>
		<?php block_template_part( 'footer' ); ?>
	</div>
	<?php wp_footer(); ?>
	</body>
	</html>
	<?php
} else {
	?>
		</main>
	</div>
	<?php
	get_footer();
}
