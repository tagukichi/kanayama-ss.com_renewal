<?php
/**
 * Template Name: 代表メッセージ
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();
	kanayama_page_head_for( get_post() );
	get_template_part( 'template-parts/page-hero' );

	$kanayama_role = get_post_meta( get_the_ID(), '_kn_page_caption', true );
	$kanayama_lead = get_post_meta( get_the_ID(), '_kn_front_lead', true );
	?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
	<?php
	kanayama_media(
		get_post_thumbnail_id(),
		'ph--3x4',
		array( 'size' => 'kanayama-portrait', 'eager' => true, 'placeholder' => __( '［写真］代表 ／ 900×1200px', 'kanayama' ), 'alt' => __( '代表取締役', 'kanayama' ) )
	);
	?>
	<?php if ( $kanayama_role ) : ?>
      <p class="member__role mt-m"><?php echo esc_html( $kanayama_role ); ?></p>
	<?php endif; ?>
    </div>
    <div class="article">
	<?php if ( $kanayama_lead ) : ?>
      <p class="lead mincho"><?php echo wp_kses( kanayama_br( $kanayama_lead ), array( 'br' => array() ) ); ?></p>
	<?php endif; ?>
	<?php the_content(); ?>
    </div>
  </div>
</section>

<?php
$kanayama_to_applicants = get_post_meta( get_the_ID(), '_kn_message_extra', true );
if ( $kanayama_to_applicants ) :
	$kanayama_recruit = get_page_by_path( 'recruit' );
	$kanayama_iv      = get_post_type_archive_link( 'kn_interview' );
	?>
<section class="sec sec--soft">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">To Applicants</p>
      <h2 class="h-sec"><?php esc_html_e( 'これから', 'kanayama' ); ?><br><?php esc_html_e( '仲間になる方へ', 'kanayama' ); ?></h2>
    </div>
    <div>
	<?php foreach ( preg_split( '/\R{2,}/u', trim( $kanayama_to_applicants ) ) as $kanayama_para ) : ?>
      <p class="p"><?php echo wp_kses( nl2br( esc_html( trim( $kanayama_para ) ) ), array( 'br' => array() ) ); ?></p>
	<?php endforeach; ?>
      <p class="acts" style="margin-top:28px">
	<?php if ( $kanayama_recruit ) : ?>
        <a class="btn" href="<?php echo esc_url( get_permalink( $kanayama_recruit ) ); ?>"><?php esc_html_e( '募集要項を見る', 'kanayama' ); ?></a>
	<?php endif; ?>
	<?php if ( $kanayama_iv ) : ?>
        <a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_iv ); ?>"><?php esc_html_e( '社員紹介を読む', 'kanayama' ); ?></a>
	<?php endif; ?>
      </p>
    </div>
  </div>
</section>
	<?php
endif;

endwhile;

get_footer();
