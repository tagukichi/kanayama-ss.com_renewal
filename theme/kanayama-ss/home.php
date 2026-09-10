<?php
/**
 * お知らせ一覧（投稿の一覧ページ）。
 *
 * @package Kanayama
 */

get_header();

$kanayama_blog_id = (int) get_option( 'page_for_posts' );

kanayama_page_head(
	array(
		'en'       => $kanayama_blog_id ? get_post_meta( $kanayama_blog_id, '_kn_page_en', true ) : 'News',
		'title'    => $kanayama_blog_id ? get_the_title( $kanayama_blog_id ) : __( 'お知らせ', 'kanayama' ),
		'image_id' => $kanayama_blog_id ? get_post_thumbnail_id( $kanayama_blog_id ) : 0,
	)
);
?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">News</p>
      <h2 class="h-sec"><?php esc_html_e( 'お知らせ', 'kanayama' ); ?></h2>
    </div>
    <div>
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
		the_posts_pagination(
			array(
				'class'     => 'pager',
				'mid_size'  => 2,
				'prev_text' => __( '前へ', 'kanayama' ),
				'next_text' => __( '次へ', 'kanayama' ),
			)
		);
	else :
		?>
      <p class="empty"><?php esc_html_e( 'お知らせはまだありません。', 'kanayama' ); ?></p>
	<?php endif; ?>
    </div>
  </div>
</section>

<?php
get_footer();
