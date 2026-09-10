<?php
/**
 * カテゴリー・年月別などの一覧。お知らせ一覧と同じ体裁で出す。
 *
 * @package Kanayama
 */

get_header();

$kanayama_blog_id  = (int) get_option( 'page_for_posts' );
$kanayama_blog_url = $kanayama_blog_id ? get_permalink( $kanayama_blog_id ) : '';

kanayama_page_head(
	array(
		'en'     => 'News',
		'title'  => wp_strip_all_tags( get_the_archive_title() ),
		'style'  => 'light',
		'crumbs' => $kanayama_blog_url
			? array( array( 'label' => get_the_title( $kanayama_blog_id ), 'url' => $kanayama_blog_url ) )
			: array(),
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
		$kanayama_cats = get_the_category();
		?>
      <a class="news__item" href="<?php the_permalink(); ?>">
        <span class="news__date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
		<?php if ( $kanayama_cats ) : ?>
        <span class="news__cat"><?php echo esc_html( $kanayama_cats[0]->name ); ?></span>
		<?php endif; ?>
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
