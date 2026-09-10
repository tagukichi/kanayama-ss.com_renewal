<?php
/**
 * Template Name: 私たちの想い
 *
 * 代表の写真を右上に大きく置き、左に縦組みの本文を流す専用レイアウト。
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();
	kanayama_page_head_for( get_post() );

	$kanayama_caption = get_post_meta( get_the_ID(), '_kn_page_caption', true );
	$kanayama_lead    = get_post_meta( get_the_ID(), '_kn_front_lead', true );
	?>

<section class="cnc">
  <span class="cnc__diag" aria-hidden="true"></span>

  <figure class="cnc__media">
	<?php
	kanayama_media(
		get_post_thumbnail_id(),
		'ph--2x1 ph--right',
		array( 'size' => 'kanayama-wide', 'eager' => true, 'alt' => '', 'placeholder' => __( '［写真］代表 ／ 1600×800px', 'kanayama' ) )
	);
	?>
	<?php if ( $kanayama_caption ) : ?>
    <figcaption class="cnc__cap"><?php echo esc_html( $kanayama_caption ); ?></figcaption>
	<?php endif; ?>
  </figure>

  <div class="wrap cnc__in">
    <div class="cnc__col">
	<?php if ( $kanayama_lead ) : ?>
      <h2 class="cnc__ttl reveal"><?php echo wp_kses( kanayama_br( $kanayama_lead ), array( 'br' => array() ) ); ?></h2>
	<?php endif; ?>

      <div class="cnc__body reveal">
	<?php the_content(); ?>
        <p class="acts cnc__acts">
	<?php
	$kanayama_about = get_page_by_path( 'about' );
	$kanayama_works = get_post_type_archive_link( 'kn_work' );
	if ( $kanayama_about ) :
		?>
          <a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $kanayama_about ) ); ?>"><?php esc_html_e( '会社案内', 'kanayama' ); ?></a>
	<?php endif; ?>
	<?php if ( $kanayama_works ) : ?>
          <a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_works ); ?>"><?php esc_html_e( '工事実績を見る', 'kanayama' ); ?></a>
	<?php endif; ?>
        </p>
      </div>
    </div>
  </div>
</section>

	<?php
endwhile;

get_footer();
