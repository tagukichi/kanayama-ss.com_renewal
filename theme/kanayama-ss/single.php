<?php
/**
 * お知らせの詳細。
 *
 * @package Kanayama
 */

get_header();

$kanayama_blog_id  = (int) get_option( 'page_for_posts' );
$kanayama_blog_url = $kanayama_blog_id ? get_permalink( $kanayama_blog_id ) : '';

while ( have_posts() ) :
	the_post();

	kanayama_page_head(
		array(
			'en'       => 'News',
			'title'    => get_the_title(),
			'image_id' => get_post_thumbnail_id(),
			'crumbs'   => $kanayama_blog_url
				? array( array( 'label' => get_the_title( $kanayama_blog_id ), 'url' => $kanayama_blog_url ) )
				: array(),
		)
	);
	?>

<section class="sec">
  <div class="wrap">
    <div class="article reveal">
      <p class="work__meta">
        <span class="en"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
	<?php
	$kanayama_cats = get_the_category();
	if ( $kanayama_cats ) :
		?>
        <span><?php echo esc_html( $kanayama_cats[0]->name ); ?></span>
	<?php endif; ?>
      </p>
	<?php
		the_content();
		wp_link_pages( array( 'before' => '<nav class="pager">', 'after' => '</nav>' ) );
	?>
    </div>

	<?php if ( $kanayama_blog_url ) : ?>
    <p class="mt-l center"><a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_blog_url ); ?>"><?php esc_html_e( 'お知らせ一覧へ戻る', 'kanayama' ); ?></a></p>
	<?php endif; ?>
  </div>
</section>

	<?php
endwhile;

get_footer();
