<?php
/**
 * Template Name: お問い合わせ
 *
 * フォーム本体はページ本文に置く。Contact Form 7 などのショートコードを
 * 貼れば、そのまま .form の体裁で表示される。
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
      <p class="eyebrow">Contact</p>
      <h2 class="h-sec"><?php the_title(); ?></h2>
      <div class="cta__tel mt-m" style="background:var(--soft);border-color:var(--line-strong);color:var(--ink)">
        <small style="color:var(--muted)"><?php esc_html_e( 'お電話でのご相談', 'kanayama' ); ?></small>
        <b style="color:var(--green)"><?php echo esc_html( kanayama_opt( 'company_tel' ) ); ?></b>
        <span style="color:var(--muted)"><?php echo esc_html( kanayama_opt( 'company_hours' ) ); ?></span>
      </div>
	<?php if ( has_excerpt() ) : ?>
      <p class="p mt-m" style="font-size:13.5px"><?php echo esc_html( get_the_excerpt() ); ?></p>
	<?php endif; ?>
    </div>

    <div>
	<?php if ( trim( get_the_content() ) ) : ?>
      <div class="form"><?php the_content(); ?></div>
	<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
      <p class="form__note"><?php esc_html_e( 'このページの本文にお問い合わせフォームのショートコード（Contact Form 7 など）を貼ると、ここに表示されます。この案内は編集権限のある人にだけ見えています。', 'kanayama' ); ?></p>
	<?php endif; ?>
    </div>
  </div>
</section>

	<?php
endwhile;

get_footer();
