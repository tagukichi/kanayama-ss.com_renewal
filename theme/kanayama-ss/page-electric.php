<?php
/**
 * Template Name: 事業案内（電気・水道工事）
 *
 * 本文＋「対応する工事」＋「まとめて頼めるということ」。
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
$kanayama_has_service = get_posts(
	array(
		'post_type'      => 'kn_service',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'tax_query'      => array( array( 'taxonomy' => 'kn_service_cat', 'field' => 'slug', 'terms' => 'electric' ) ),
	)
);
if ( $kanayama_has_service ) :
	?>
<section class="sec sec--soft">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Service</p>
        <h2 class="h-sec"><?php esc_html_e( '対応する工事', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( '小さな工事もお気軽にご相談ください。', 'kanayama' ); ?></p>
    </div>
	<?php kanayama_services( 'electric' ); ?>
  </div>
</section>
<?php endif; ?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Merit</p>
      <h2 class="h-sec"><?php esc_html_e( 'まとめて頼める、', 'kanayama' ); ?><br><?php esc_html_e( 'ということ', 'kanayama' ); ?></h2>
    </div>
	<?php kanayama_features( 'merit', '', 'grid-template-columns:1fr' ); ?>
  </div>
</section>

	<?php
endwhile;

get_footer();
