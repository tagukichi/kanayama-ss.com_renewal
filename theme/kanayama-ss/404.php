<?php
/**
 * ページが見つからないとき。
 *
 * @package Kanayama
 */

get_header();

kanayama_page_head(
	array(
		'en'    => '404',
		'title' => __( 'ページが見つかりません', 'kanayama' ),
		'style' => 'light',
	)
);
?>

<section class="sec">
  <div class="wrap">
    <div class="article reveal">
      <p class="lead"><?php esc_html_e( 'お探しのページは、移動または削除された可能性があります。', 'kanayama' ); ?></p>
      <p class="p"><?php esc_html_e( 'お手数ですが、下のリンクからお探しください。', 'kanayama' ); ?></p>
      <p class="acts" style="margin-top:28px">
        <a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'トップページへ', 'kanayama' ); ?></a>
        <a class="btn btn--ghost" href="<?php echo esc_url( kanayama_contact_url() ); ?>"><?php esc_html_e( 'お問い合わせ', 'kanayama' ); ?></a>
      </p>
      <div style="max-width:520px;margin-top:40px"><?php get_search_form(); ?></div>
    </div>
  </div>
</section>

<?php
get_footer();
