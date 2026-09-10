<?php
/**
 * Template Name: 採用情報
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();
	kanayama_page_head_for( get_post() );
	get_template_part( 'template-parts/page-hero' );

	$kanayama_requirements = get_post_meta( get_the_ID(), '_kn_recruit_table', true );
	$kanayama_iv           = get_post_type_archive_link( 'kn_interview' );
	?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Recruit</p>
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
$kanayama_env = get_posts(
	array(
		'post_type'      => 'kn_feature',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'tax_query'      => array( array( 'taxonomy' => 'kn_feature_group', 'field' => 'slug', 'terms' => 'environment' ) ),
	)
);
if ( $kanayama_env ) :
	?>
<section class="sec sec--soft">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Environment</p>
        <h2 class="h-sec"><?php esc_html_e( '働く環境', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( '長く続けられることを大事にしています。', 'kanayama' ); ?></p>
    </div>
    <div class="mt-l reveal"><?php kanayama_features( 'environment' ); ?></div>
  </div>
</section>
<?php endif; ?>

<?php if ( $kanayama_requirements ) : ?>
<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Requirements</p>
      <h2 class="h-sec"><?php esc_html_e( '募集要項', 'kanayama' ); ?></h2>
    </div>
    <div>
      <table class="tbl">
	<?php
	// 「項目｜内容」を1行ずつ。内容の中の「/」は改行にする。
	foreach ( preg_split( '/\R/u', trim( $kanayama_requirements ) ) as $kanayama_row ) {
		$kanayama_row = trim( $kanayama_row );
		if ( '' === $kanayama_row || false === strpos( $kanayama_row, '|' ) ) {
			continue;
		}
		list( $kanayama_th, $kanayama_td ) = array_map( 'trim', explode( '|', $kanayama_row, 2 ) );
		printf(
			'<tr><th>%s</th><td>%s</td></tr>',
			esc_html( $kanayama_th ),
			wp_kses( str_replace( '/', '<br>', esc_html( $kanayama_td ) ), array( 'br' => array() ) )
		);
	}
	?>
      </table>
      <p class="mt-m"><a class="btn btn--accent" href="<?php echo esc_url( kanayama_contact_url() ); ?>"><?php esc_html_e( 'この求人に応募する', 'kanayama' ); ?></a></p>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ( $kanayama_iv ) : ?>
<section class="sec sec--tight sec--soft">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Interview</p>
      <h2 class="h-sec"><?php esc_html_e( '先輩の声', 'kanayama' ); ?></h2>
    </div>
    <div>
      <p class="p mt-m"><?php esc_html_e( '実際に働くスタッフに、仕事のやりがいや一日の流れを聞きました。', 'kanayama' ); ?></p>
      <p class="mt-m"><a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_iv ); ?>"><?php esc_html_e( '社員紹介を読む', 'kanayama' ); ?></a></p>
    </div>
  </div>
</section>
	<?php
endif;

endwhile;

get_footer();
