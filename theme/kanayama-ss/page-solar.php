<?php
/**
 * Template Name: 事業案内（太陽光発電・蓄電）
 *
 * 本文＋「対応する工事」＋「工事の流れ」＋FAQ。
 * 対応する工事は「対応する工事」の事業区分 solar、工事の流れは「特長・ステップ」のグループ flow を並べる。
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();
	kanayama_page_head_for( get_post() );
	get_template_part( 'template-parts/page-hero' );
	?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">About</p>
      <h2 class="h-sec"><?php echo wp_kses( kanayama_br( get_post_meta( get_the_ID(), '_kn_page_heading', true ) ), array( 'br' => array() ) ); ?></h2>
    </div>
    <div class="article">
	<?php if ( has_excerpt() ) : ?>
      <p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
	<?php endif; ?>
	<?php the_content(); ?>
    </div>
  </div>
</section>

<?php
$kanayama_services = get_posts(
	array(
		'post_type'      => 'kn_service',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'tax_query'      => array( array( 'taxonomy' => 'kn_service_cat', 'field' => 'slug', 'terms' => 'solar' ) ),
	)
);
if ( $kanayama_services ) :
	?>
<section class="sec sec--soft">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Service</p>
        <h2 class="h-sec"><?php esc_html_e( '対応する工事', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( '太陽光発電まわりの工事を、まとめて承っています。', 'kanayama' ); ?></p>
    </div>
	<?php kanayama_services( 'solar' ); ?>
  </div>
</section>
<?php endif; ?>

<?php
$kanayama_flow = get_posts(
	array(
		'post_type'      => 'kn_feature',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'no_found_rows'  => true,
		'tax_query'      => array( array( 'taxonomy' => 'kn_feature_group', 'field' => 'slug', 'terms' => 'flow' ) ),
	)
);
if ( $kanayama_flow ) :
	?>
<section class="sec">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Flow</p>
        <h2 class="h-sec"><?php esc_html_e( '工事の流れ', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( 'お問い合わせから引き渡しまで、通常1〜2か月ほどです。', 'kanayama' ); ?></p>
    </div>
    <div class="stack mt-l reveal">
	<?php
	// 3件ずつの段に分けて並べる。
	foreach ( array_chunk( $kanayama_flow, 3 ) as $kanayama_row ) :
		echo '<div class="pts">';
		foreach ( $kanayama_row as $kanayama_step ) :
			$kanayama_label = get_post_meta( $kanayama_step->ID, '_kn_feature_label', true );
			?>
      <div class="pt">
		<?php if ( $kanayama_label ) : ?>
        <p class="pt__n"><?php echo esc_html( $kanayama_label ); ?></p>
		<?php endif; ?>
        <h3 class="pt__t"><?php echo wp_kses( kanayama_br( get_the_title( $kanayama_step ) ), array( 'br' => array() ) ); ?></h3>
        <p class="pt__d"><?php echo esc_html( wp_strip_all_tags( $kanayama_step->post_content ) ); ?></p>
      </div>
			<?php
		endforeach;
		echo '</div>';
	endforeach;
	?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
$kanayama_faq = get_post_meta( get_the_ID(), '_kn_page_faq', true );
if ( $kanayama_faq ) :
	?>
<section class="sec sec--soft">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">FAQ</p>
      <h2 class="h-sec"><?php esc_html_e( 'よくあるご質問', 'kanayama' ); ?></h2>
    </div>
    <div class="article">
	<?php
	// 「Q. 質問」「A. 回答」形式を見出し＋段落として出す。
	foreach ( preg_split( '/\R{2,}/u', trim( $kanayama_faq ) ) as $kanayama_block ) {
		$kanayama_lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', $kanayama_block ) ), 'strlen' ) );
		if ( ! $kanayama_lines ) {
			continue;
		}
		$kanayama_q = preg_replace( '/^[QqＱ][.．:：][\s　]*/u', '', array_shift( $kanayama_lines ) );
		printf( '<h2>%s</h2>', esc_html( $kanayama_q ) );
		foreach ( $kanayama_lines as $kanayama_a ) {
			printf( '<p>%s</p>', esc_html( preg_replace( '/^[AaＡ][.．:：][\s　]*/u', '', $kanayama_a ) ) );
		}
	}
	$kanayama_works = get_post_type_archive_link( 'kn_work' );
	if ( $kanayama_works ) :
		?>
      <p class="mt-m"><a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_works ); ?>"><?php esc_html_e( '工事実績を見る', 'kanayama' ); ?></a></p>
	<?php endif; ?>
    </div>
  </div>
</section>
	<?php
endif;

endwhile;

get_footer();
