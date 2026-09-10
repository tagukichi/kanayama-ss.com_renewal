<?php
/**
 * グローバルナビをプロトタイプと同じマークアップで出力するウォーカー。
 *
 *   <ul class="nav__list">
 *     <li class="nav__item"><a class="nav__link" href="">私たちの想い</a></li>
 *     <li class="nav__item"><a class="nav__link" href="">事業案内</a>
 *       <ul class="nav__sub"><li><a href="">太陽光発電・蓄電</a></li></ul>
 *     </li>
 *   </ul>
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 2階層までのナビゲーション。
 */
class Kanayama_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * 子階層の <ul>。
	 *
	 * @param string   $output 出力バッファ。
	 * @param int      $depth  階層。
	 * @param stdClass $args   引数。
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= "\n" . str_repeat( "\t", $depth ) . '<ul class="nav__sub">' . "\n";
	}
}

/**
 * メニュー項目の li に付くクラスを整える。
 *
 * @param array    $classes 既定のクラス。
 * @param WP_Post  $item    メニュー項目。
 * @param stdClass $args    引数。
 * @param int      $depth   階層。
 * @return array
 */
function kanayama_nav_item_class( $classes, $item, $args, $depth ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $classes;
	}
	// 第1階層だけに nav__item を付ける。第2階層は素の li。
	$keep = array_filter(
		$classes,
		static function ( $c ) {
			return in_array( $c, array( 'current-menu-item', 'current-menu-ancestor', 'current-menu-parent' ), true );
		}
	);
	if ( 0 === $depth ) {
		$keep[] = 'nav__item';
	}
	return $keep;
}
add_filter( 'nav_menu_css_class', 'kanayama_nav_item_class', 10, 4 );

/**
 * メニューのリンクに nav__link と aria-current を付ける。
 *
 * @param array    $atts  リンク属性。
 * @param WP_Post  $item  メニュー項目。
 * @param stdClass $args  引数。
 * @param int      $depth 階層。
 * @return array
 */
function kanayama_nav_link_atts( $atts, $item, $args, $depth ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $atts;
	}
	if ( 0 === $depth ) {
		$atts['class'] = 'nav__link';
	}
	if ( ! empty( $item->current ) ) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'kanayama_nav_link_atts', 10, 4 );

/**
 * グローバルナビが未設定のときの代替表示。
 * 管理画面でメニューを作るまでの間、主要なページへのリンクを自動で並べる。
 */
function kanayama_primary_menu_fallback() {
	$candidates = array(
		array( 'path' => 'concept', 'label' => __( '私たちの想い', 'kanayama' ), 'children' => array() ),
		array(
			'path'     => 'solar',
			'label'    => __( '事業案内', 'kanayama' ),
			'children' => array(
				array( 'path' => 'solar', 'label' => __( '太陽光発電・蓄電', 'kanayama' ) ),
				array( 'path' => 'electric', 'label' => __( '電気・水道工事', 'kanayama' ) ),
			),
		),
		array( 'archive' => 'kn_work', 'label' => __( '工事実績', 'kanayama' ), 'children' => array() ),
		array( 'path' => 'about', 'label' => __( '会社案内', 'kanayama' ), 'children' => array() ),
		array(
			'path'     => 'recruit',
			'label'    => __( '採用情報', 'kanayama' ),
			'children' => array(
				array( 'path' => 'recruit', 'label' => __( '採用情報', 'kanayama' ) ),
				array( 'path' => 'message', 'label' => __( '代表メッセージ', 'kanayama' ) ),
				array( 'archive' => 'kn_interview', 'label' => __( '社員紹介', 'kanayama' ) ),
			),
		),
		array( 'posts' => true, 'label' => __( 'お知らせ', 'kanayama' ), 'children' => array() ),
	);

	echo '<ul class="nav__list">';
	foreach ( $candidates as $item ) {
		$url = kanayama_fallback_url( $item );
		if ( ! $url ) {
			continue;
		}
		echo '<li class="nav__item">';
		printf( '<a class="nav__link" href="%s">%s</a>', esc_url( $url ), esc_html( $item['label'] ) );

		$children = array();
		foreach ( $item['children'] as $child ) {
			$child_url = kanayama_fallback_url( $child );
			if ( $child_url ) {
				$children[] = sprintf( '<li><a href="%s">%s</a></li>', esc_url( $child_url ), esc_html( $child['label'] ) );
			}
		}
		if ( count( $children ) > 1 ) {
			echo '<ul class="nav__sub">' . implode( '', $children ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 各要素で個別にエスケープ済み。
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * 代替メニュー1項目のURLを求める。対象が無ければ空文字。
 *
 * @param array $item 項目の定義。
 * @return string
 */
function kanayama_fallback_url( $item ) {
	if ( ! empty( $item['posts'] ) ) {
		$blog = (int) get_option( 'page_for_posts' );
		return $blog ? get_permalink( $blog ) : '';
	}
	if ( ! empty( $item['archive'] ) ) {
		$url = get_post_type_archive_link( $item['archive'] );
		return $url ? $url : '';
	}
	$page = get_page_by_path( $item['path'] );
	return $page ? get_permalink( $page ) : '';
}
