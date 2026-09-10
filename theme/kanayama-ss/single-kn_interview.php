<?php
/**
 * 社員紹介の個別ページ。一覧と同じカードを開いた状態で出す。
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();

	kanayama_page_head(
		array(
			'en'       => 'Interview',
		'image_id' => (int) get_theme_mod( 'interview_header_image', 0 ),
			'title'  => get_the_title(),
			'crumbs' => array(
				array( 'label' => __( '社員紹介', 'kanayama' ), 'url' => get_post_type_archive_link( 'kn_interview' ) ),
			),
		)
	);
	?>

<section class="sec">
  <div class="wrap ivs">
	<?php get_template_part( 'template-parts/interview-card', null, array( 'open' => true ) ); ?>
  </div>
  <div class="wrap">
    <p class="mt-l center"><a class="btn btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'kn_interview' ) ); ?>"><?php esc_html_e( '社員紹介一覧へ戻る', 'kanayama' ); ?></a></p>
  </div>
</section>

	<?php
endwhile;

get_footer();
