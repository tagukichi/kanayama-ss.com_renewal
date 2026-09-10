<?php
/**
 * Template Name: 会社案内
 *
 * 会社概要はカスタマイザーの会社情報から自動で組み立てる。
 * 沿革とアクセスはページ本文とカスタムフィールドで管理する。
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();
	kanayama_page_head_for( get_post() );
	get_template_part( 'template-parts/page-hero' );

	$kanayama_business = get_post_meta( get_the_ID(), '_kn_about_business', true );
	$kanayama_ceo      = get_post_meta( get_the_ID(), '_kn_about_ceo', true );
	$kanayama_map      = get_post_meta( get_the_ID(), '_kn_about_map', true );
	?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Profile</p>
      <h2 class="h-sec"><?php esc_html_e( '会社概要', 'kanayama' ); ?></h2>
    </div>
    <div>
      <table class="tbl">
        <tr><th><?php esc_html_e( '商号', 'kanayama' ); ?></th><td><?php echo esc_html( str_replace( ' ', '', kanayama_opt( 'company_name' ) ) ); ?></td></tr>
        <tr><th><?php esc_html_e( '所在地', 'kanayama' ); ?></th><td><?php echo esc_html( trim( kanayama_opt( 'company_zip' ) . ' ' . kanayama_opt( 'company_addr' ) ) ); ?></td></tr>
        <tr><th><?php esc_html_e( '電話番号', 'kanayama' ); ?></th><td><?php echo esc_html( kanayama_opt( 'company_tel' ) ); ?></td></tr>
        <tr><th><?php esc_html_e( 'FAX', 'kanayama' ); ?></th><td><?php echo esc_html( kanayama_opt( 'company_fax' ) ); ?></td></tr>
	<?php if ( $kanayama_ceo ) : ?>
        <tr><th><?php esc_html_e( '代表者', 'kanayama' ); ?></th><td><?php echo esc_html( $kanayama_ceo ); ?></td></tr>
	<?php endif; ?>
	<?php if ( $kanayama_business ) : ?>
        <tr><th><?php esc_html_e( '事業内容', 'kanayama' ); ?></th><td><?php echo wp_kses( nl2br( esc_html( $kanayama_business ) ), array( 'br' => array() ) ); ?></td></tr>
	<?php endif; ?>
        <tr><th><?php esc_html_e( '許可・登録', 'kanayama' ); ?></th><td><?php echo esc_html( kanayama_opt( 'company_reg' ) ); ?></td></tr>
        <tr><th><?php esc_html_e( '営業時間', 'kanayama' ); ?></th><td><?php echo esc_html( kanayama_opt( 'company_hours' ) ); ?></td></tr>
      </table>
    </div>
  </div>
</section>

<?php if ( trim( get_the_content() ) ) : ?>
<section class="sec sec--soft">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">History</p>
      <h2 class="h-sec"><?php esc_html_e( '沿革', 'kanayama' ); ?></h2>
    </div>
    <div class="article"><?php the_content(); ?></div>
  </div>
</section>
<?php endif; ?>

<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Access</p>
      <h2 class="h-sec"><?php esc_html_e( 'アクセス', 'kanayama' ); ?></h2>
    </div>
    <div>
	<?php
	// 地図。埋め込みURLの指定が無ければ住所から組み立てる。
	$kanayama_map_src = $kanayama_map
		? $kanayama_map
		: add_query_arg(
			array(
				'q'      => rawurlencode( trim( kanayama_opt( 'company_zip' ) . ' ' . kanayama_opt( 'company_addr' ) . ' ' . kanayama_opt( 'company_name' ) ) ),
				'output' => 'embed',
			),
			'https://www.google.com/maps'
		);
	?>
      <div class="map">
        <iframe src="<?php echo esc_url( $kanayama_map_src ); ?>"
                title="<?php echo esc_attr( sprintf( '%sの所在地（Googleマップ）', kanayama_opt( 'company_name' ) ) ); ?>"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
      <p class="p mt-m"><?php echo esc_html( trim( kanayama_opt( 'company_zip' ) . ' ' . kanayama_opt( 'company_addr' ) ) ); ?></p>
    </div>
  </div>
</section>

	<?php
endwhile;

get_footer();
