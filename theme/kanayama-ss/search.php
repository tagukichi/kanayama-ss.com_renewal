<?php
/**
 * 検索結果。
 *
 * @package Kanayama
 */

get_header();

kanayama_page_head(
	array(
		'en'    => 'Search',
		/* translators: %s: 検索語 */
		'title' => sprintf( __( '「%s」の検索結果', 'kanayama' ), get_search_query() ),
		'style' => 'light',
	)
);
?>

<section class="sec">
  <div class="wrap">
    <div style="max-width:520px;margin-bottom:40px"><?php get_search_form(); ?></div>

	<?php if ( have_posts() ) : ?>
    <div class="news">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
      <a class="news__item" href="<?php the_permalink(); ?>">
        <span class="news__date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
        <span class="news__cat"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
        <span class="news__ttl"><?php the_title(); ?></span>
      </a>
	<?php endwhile; ?>
    </div>
	<?php
		the_posts_pagination( array( 'class' => 'pager', 'mid_size' => 2 ) );
	else :
		?>
    <p class="empty"><?php esc_html_e( '見つかりませんでした。別のことばでお試しください。', 'kanayama' ); ?></p>
	<?php endif; ?>
  </div>
</section>

<?php
get_footer();
