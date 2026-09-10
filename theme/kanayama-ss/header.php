<?php
/**
 * ヘッダー。
 *
 * @package Kanayama
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#content"><?php esc_html_e( '本文へスキップ', 'kanayama' ); ?></a>

<header class="hd">
  <div class="wrap hd__in">
    <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( '%s トップページ', kanayama_opt( 'company_name' ) ) ); ?>">
      <?php get_template_part( 'template-parts/logo-mark' ); ?>
      <span class="logo__txt">
        <span class="logo__biz"><?php echo esc_html( kanayama_opt( 'company_biz' ) ); ?></span>
        <span class="logo__ja"><?php echo esc_html( kanayama_opt( 'company_name' ) ); ?></span>
      </span>
    </a>

    <button class="burger" type="button" aria-label="<?php esc_attr_e( 'メニューを開く', 'kanayama' ); ?>" aria-expanded="false" aria-controls="gnav">
      <span></span><span></span><span></span>
    </button>

    <nav class="nav" id="gnav" aria-label="<?php esc_attr_e( 'グローバルナビゲーション', 'kanayama' ); ?>">
      <?php
      wp_nav_menu(
          array(
              'theme_location' => 'primary',
              'container'      => false,
              'menu_class'     => 'nav__list',
              'depth'          => 2,
              'walker'         => new Kanayama_Nav_Walker(),
              'fallback_cb'    => 'kanayama_primary_menu_fallback',
          )
      );
      ?>
      <div class="nav__mobileCta">
        <a class="btn btn--accent" href="<?php echo esc_url( kanayama_contact_url() ); ?>"><?php esc_html_e( 'お問い合わせ', 'kanayama' ); ?></a>
        <a class="btn btn--ghost" href="<?php echo esc_url( kanayama_tel_href() ); ?>"><?php echo esc_html( sprintf( '%s に電話する', kanayama_opt( 'company_tel' ) ) ); ?></a>
      </div>
    </nav>

    <div class="hd__cta">
      <span class="hd__tel">
        <b><?php echo esc_html( kanayama_opt( 'company_tel' ) ); ?></b>
        <small><?php echo esc_html( kanayama_opt( 'company_hours_short' ) ); ?></small>
      </span>
      <a class="btn btn--accent hd__btn" href="<?php echo esc_url( kanayama_contact_url() ); ?>"><?php esc_html_e( 'お問い合わせ', 'kanayama' ); ?></a>
    </div>
  </div>
</header>

<main id="content">
