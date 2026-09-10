<?php
/**
 * 社員紹介の一覧。1ページに全員を並べる。
 *
 * @package Kanayama
 */

get_header();

$kanayama_recruit = get_page_by_path( 'recruit' );

kanayama_page_head(
	array(
		'en'       => 'Interview',
		'image_id' => (int) get_theme_mod( 'interview_header_image', 0 ),
		'title'  => __( '社員紹介', 'kanayama' ),
		'crumbs' => $kanayama_recruit
			? array( array( 'label' => get_the_title( $kanayama_recruit ), 'url' => get_permalink( $kanayama_recruit ) ) )
			: array(),
	)
);
?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Interview</p>
      <h2 class="h-sec"><?php esc_html_e( '現場の人たち', 'kanayama' ); ?></h2>
    </div>
    <p class="lead"><?php esc_html_e( '入社の年も、担当する仕事もそれぞれ。共通しているのは、最後まできちんとやるという姿勢です。', 'kanayama' ); ?></p>
  </div>

	<?php if ( have_posts() ) : ?>
  <div class="wrap ivs mt-l">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/interview-card' );
	endwhile;
	?>
  </div>
	<?php else : ?>
  <div class="wrap"><p class="empty"><?php esc_html_e( '社員紹介はまだありません。', 'kanayama' ); ?></p></div>
	<?php endif; ?>
</section>

<section class="sec sec--soft">
  <div class="wrap entry reveal">
    <p class="entry__en en"><?php esc_html_e( 'Entry', 'kanayama' ); ?></p>
    <h2 class="entry__ttl"><?php esc_html_e( '私たちのチームでプロフェッショナルを目指す', 'kanayama' ); ?><br><?php esc_html_e( '元気な仲間を待っています', 'kanayama' ); ?></h2>
    <p class="acts entry__acts">
      <a class="btn btn--accent" href="<?php echo esc_url( kanayama_contact_url() ); ?>"><?php esc_html_e( '応募フォーム', 'kanayama' ); ?></a>
	<?php if ( $kanayama_recruit ) : ?>
      <a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $kanayama_recruit ) ); ?>"><?php esc_html_e( '募集要項を見る', 'kanayama' ); ?></a>
	<?php endif; ?>
    </p>
  </div>
</section>

<?php
get_footer();
