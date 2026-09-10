<?php
/**
 * 工事実績の一覧。
 *
 * @package Kanayama
 */

get_header();

$kanayama_term = is_tax( 'kn_work_cat' ) ? get_queried_object() : null;

kanayama_page_head(
	array(
		'en'       => 'Works',
		'title'    => $kanayama_term ? $kanayama_term->name : __( '工事実績', 'kanayama' ),
		'image_id' => (int) get_theme_mod( 'works_header_image', 0 ),
		'crumbs'   => $kanayama_term
			? array( array( 'label' => __( '工事実績', 'kanayama' ), 'url' => get_post_type_archive_link( 'kn_work' ) ) )
			: array(),
	)
);
?>

<section class="sec">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Works</p>
        <h2 class="h-sec"><?php esc_html_e( 'これまでの工事', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( '条件の近い事例が、ご検討の参考になれば幸いです。掲載は施主様の許諾をいただいたものに限っています。', 'kanayama' ); ?></p>
    </div>

	<?php if ( have_posts() ) : ?>
    <div class="works mt-l reveal">
	<?php
	while ( have_posts() ) :
		the_post();
		kanayama_work_card( get_post() );
	endwhile;
	?>
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
    <p class="empty"><?php esc_html_e( '工事実績はまだありません。', 'kanayama' ); ?></p>
	<?php endif; ?>
  </div>
</section>

<?php
get_footer();
