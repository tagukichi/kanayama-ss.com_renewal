<?php
/**
 * テーマサポート、アセット、メニューの登録。
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * テーマがWordPressに対応を宣言する機能。
 */
function kanayama_setup() {
	load_theme_textdomain( 'kanayama', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	// 画像サイズ。工事実績のサムネイルは4:3、社員紹介は正方形で切り抜く。
	set_post_thumbnail_size( 1200, 900, true );
	add_image_size( 'kanayama-card', 1200, 900, true );      // 工事実績・対応する工事
	add_image_size( 'kanayama-square', 900, 900, true );     // 社員紹介
	add_image_size( 'kanayama-wide', 1920, 760, true );      // ページヘッダー・FV
	add_image_size( 'kanayama-portrait', 900, 1200, true );  // 代表メッセージ

	register_nav_menus(
		array(
			'primary'         => __( 'グローバルナビゲーション', 'kanayama' ),
			'footer_business' => __( 'フッター：事業', 'kanayama' ),
			'footer_company'  => __( 'フッター：会社', 'kanayama' ),
			'footer_recruit'  => __( 'フッター：採用', 'kanayama' ),
		)
	);
}
add_action( 'after_setup_theme', 'kanayama_setup' );

/**
 * 抜粋の設定。工事実績カードなどで使う。
 */
function kanayama_excerpt_length() {
	return 70;
}
add_filter( 'excerpt_length', 'kanayama_excerpt_length' );

function kanayama_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'kanayama_excerpt_more' );

/**
 * スタイルとスクリプトの読み込み。
 */
function kanayama_assets() {
	// Google Fonts。表示用の3書体（英字見出し・本文ゴシック・明朝）。
	wp_enqueue_style(
		'kanayama-fonts',
		'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Noto+Sans+JP:wght@400;500;700&family=Noto+Serif+JP:wght@500;600&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'kanayama-style', get_stylesheet_uri(), array( 'kanayama-fonts' ), KANAYAMA_VERSION );

	wp_enqueue_script(
		'kanayama-app',
		get_template_directory_uri() . '/assets/js/app.js',
		array(),
		KANAYAMA_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kanayama_assets' );

/**
 * Google Fontsの事前接続。読み込みの待ち時間を短くする。
 *
 * @param array  $urls           リソースのURL。
 * @param string $relation_type  関係の種類。
 * @return array
 */
function kanayama_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'kanayama-fonts', 'queue' ) ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'kanayama_resource_hints', 10, 2 );

/**
 * body要素にページ種別のクラスを足す。プロトタイプの data-page 相当。
 *
 * @param array $classes bodyクラス。
 * @return array
 */
function kanayama_body_class( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'is-front';
	}
	if ( is_singular() && ! is_front_page() ) {
		$post = get_post();
		if ( $post instanceof WP_Post ) {
			$classes[] = 'page-' . sanitize_html_class( $post->post_name );
		}
	}
	return $classes;
}
add_filter( 'body_class', 'kanayama_body_class' );

/**
 * 一覧の表示件数。工事実績と社員紹介は一覧を1ページに収めたいので多めにする。
 *
 * @param WP_Query $query メインクエリ。
 */
function kanayama_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'kn_work' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
	if ( $query->is_post_type_archive( 'kn_interview' ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', 'menu_order date' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'kanayama_pre_get_posts' );

/**
 * 添付ファイルページは使わないので個別ページへ寄せる。
 */
function kanayama_redirect_attachment() {
	if ( is_attachment() ) {
		$parent = wp_get_post_parent_id( get_queried_object_id() );
		wp_safe_redirect( $parent ? get_permalink( $parent ) : home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'kanayama_redirect_attachment' );
