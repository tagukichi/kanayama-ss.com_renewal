<?php
/**
 * 最後の受け皿。ほかのテンプレートが当たらなかったときに使われる。
 *
 * @package Kanayama
 */

get_header();

kanayama_page_head(
	array(
		'en'    => 'Archive',
		'title' => wp_strip_all_tags( get_the_archive_title() ),
		'style' => 'light',
	)
);
?>

<section class="sec">
  <div class="wrap">
	<?php if ( have_posts() ) : ?>
    <div class="news">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
      <a class="news__item" href="<?php the_permalink(); ?>">
        <span class="news__date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
        <span class="news__ttl"><?php the_title(); ?></span>
      </a>
	<?php endwhile; ?>
    </div>
	<?php
		the_posts_pagination( array( 'class' => 'pager', 'mid_size' => 2 ) );
	else :
		?>
    <p class="empty"><?php esc_html_e( '記事がまだありません。', 'kanayama' ); ?></p>
	<?php endif; ?>
  </div>
</section>

<?php
get_footer();
