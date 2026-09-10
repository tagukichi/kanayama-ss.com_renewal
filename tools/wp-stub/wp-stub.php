<?php
/**
 * WordPress の代わりに最低限の関数だけを用意して、テーマのテンプレートを
 * そのまま描画するための足場。デザイン確認と取りこぼしの検出に使う。
 *
 * これは WordPress の代用品ではない。フックもクエリも本物ではないので、
 * 「テンプレートが意図どおりのHTMLを吐くか」を見る目的だけに使うこと。
 *
 * 使い方: php tools/wp-stub/render.php
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'DOING_AUTOSAVE', false );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['kn_posts']   = array();   // ID => 投稿データ
$GLOBALS['kn_meta']    = array();   // ID => メタ
$GLOBALS['kn_terms']   = array();   // taxonomy => term
$GLOBALS['kn_rel']     = array();   // ID => taxonomy => term slug[]
$GLOBALS['kn_options'] = array();
$GLOBALS['kn_mods']    = array();
$GLOBALS['kn_query']   = array( 'posts' => array(), 'i' => -1, 'current' => null );
$GLOBALS['kn_ctx']     = array();   // is_front_page などの状況
$GLOBALS['kn_actions'] = array();

/* ---------- 文字列とエスケープ ---------- */
function __( $t, $d = null ) { return $t; }
function _e( $t, $d = null ) { echo $t; }
function esc_html( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $t ) { return esc_html( $t ); }
function esc_textarea( $t ) { return esc_html( $t ); }
function esc_html__( $t, $d = null ) { return esc_html( $t ); }
function esc_attr__( $t, $d = null ) { return esc_attr( $t ); }
function esc_html_e( $t, $d = null ) { echo esc_html( $t ); }
function esc_attr_e( $t, $d = null ) { echo esc_attr( $t ); }
function esc_url( $u ) { return esc_html( $u ); }
function esc_url_raw( $u ) { return $u; }
function wp_kses( $html, $allowed ) { return $html; }
function wp_kses_post( $html ) { return $html; }
function wp_strip_all_tags( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_text_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_textarea_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_key( $t ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $t ) ); }
function sanitize_html_class( $t ) { return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $t ); }
function sanitize_title( $t ) {
	$t = strtolower( trim( (string) $t ) );
	$t = preg_replace( '/[^a-z0-9\-_]+/u', '-', $t );
	return trim( $t, '-' );
}
function absint( $n ) { return abs( (int) $n ); }
function wp_parse_args( $args, $defaults = array() ) { return array_merge( $defaults, (array) $args ); }
function wp_unslash( $v ) { return $v; }
function is_wp_error( $v ) { return false; }
function nl2br_wp( $t ) { return nl2br( $t ); }
function checked( $a, $b = true, $echo = true ) { $r = ( $a == $b ) ? ' checked' : ''; if ( $echo ) { echo $r; } return $r; }
function selected( $a, $b = true, $echo = true ) { $r = ( $a == $b ) ? ' selected' : ''; if ( $echo ) { echo $r; } return $r; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }

/* ---------- フック（登録だけして実行しない） ---------- */
function add_action( $tag, $fn, $p = 10, $n = 1 ) { $GLOBALS['kn_actions'][ $tag ][] = $fn; }
function add_filter( $tag, $fn, $p = 10, $n = 1 ) { $GLOBALS['kn_actions'][ $tag ][] = $fn; }
function apply_filters( $tag, $value ) { return $value; }
function do_action( $tag ) {}
function add_theme_support() {}
function add_image_size() {}
function set_post_thumbnail_size() {}
function register_nav_menus() {}
function register_post_type( $type, $args = array() ) { $GLOBALS['kn_pt'][ $type ] = $args; }
function register_taxonomy() {}
function load_theme_textdomain() {}
function add_meta_box() {}
function add_theme_page() {}
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function wp_enqueue_media() {}
function wp_style_is() { return true; }
function get_current_screen() { return null; }
function current_user_can() { return false; }
function is_admin() { return false; }
function wp_nonce_field() {}
function wp_verify_nonce() { return true; }
function check_admin_referer() {}
function submit_button() {}
function wp_die( $m ) { die( $m ); }
function flush_rewrite_rules() {}
function wp_safe_redirect() {}
function wp_body_open() {}
function language_attributes() { echo 'lang="ja"'; }
function bloginfo( $k ) { echo 'utf-8' === $k || 'charset' === $k ? 'utf-8' : ''; }

/* ---------- URL・パス ---------- */
function get_template_directory() { return dirname( __DIR__, 2 ) . '/theme/kanayama-ss'; }
function get_template_directory_uri() { return $GLOBALS['kn_base'] . '/theme'; }
function get_stylesheet_uri() { return $GLOBALS['kn_base'] . '/theme/style.css'; }
function home_url( $p = '/' ) { return $GLOBALS['kn_base'] . '/index.html'; }
function admin_url( $p = '' ) { return '#'; }

/* ---------- オプション・テーマ設定 ---------- */
function get_option( $k, $d = false ) { return $GLOBALS['kn_options'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['kn_options'][ $k ] = $v; }
function get_theme_mod( $k, $d = false ) { return $GLOBALS['kn_mods'][ $k ] ?? $d; }
function set_theme_mod( $k, $v ) { $GLOBALS['kn_mods'][ $k ] = $v; }
function get_privacy_policy_url() { return ''; }
function has_nav_menu() { return false; }

/* ---------- 投稿 ---------- */
function kn_post( $id ) { return $GLOBALS['kn_posts'][ (int) $id ] ?? null; }

function kn_resolve( $p = null ) {
	if ( is_object( $p ) ) { return (int) $p->ID; }
	if ( is_numeric( $p ) && $p ) { return (int) $p; }
	return (int) ( $GLOBALS['kn_query']['current'] ?? 0 );
}
function get_post( $p = null ) {
	$id = kn_resolve( $p );
	return $id ? (object) $GLOBALS['kn_posts'][ $id ] : null;
}
function get_the_ID() { return kn_resolve(); }
function get_the_title( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return $x['post_title'] ?? ''; }
function the_title() { echo esc_html( get_the_title() ); }
function get_permalink( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return $GLOBALS['kn_base'] . '/' . ( $x['url'] ?? 'index' ) . '.html'; }
function the_permalink() { echo esc_html( get_permalink() ); }
function get_the_content( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return $x['post_content'] ?? ''; }
function the_content() { echo get_the_content(); }
function has_excerpt( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return ! empty( $x['post_excerpt'] ); }
function get_the_excerpt( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return $x['post_excerpt'] ?? ''; }
function get_the_date( $f = 'Y.m.d', $p = null ) { $x = kn_post( kn_resolve( $p ) ); return date( $f, strtotime( $x['post_date'] ?? 'now' ) ); }
function get_post_type( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return $x['post_type'] ?? 'post'; }
function get_post_type_object( $t ) { return (object) array( 'labels' => (object) array( 'singular_name' => $GLOBALS['kn_pt'][ $t ]['label'] ?? 'お知らせ' ) ); }
function wp_get_post_parent_id( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return (int) ( $x['post_parent'] ?? 0 ); }
function get_post_meta( $id, $k = '', $single = false ) {
	if ( '' === $k ) { return $GLOBALS['kn_meta'][ $id ] ?? array(); }
	return $GLOBALS['kn_meta'][ $id ][ $k ] ?? '';
}
function update_post_meta( $id, $k, $v ) { $GLOBALS['kn_meta'][ $id ][ $k ] = $v; }
function get_post_thumbnail_id( $p = null ) { $x = kn_post( kn_resolve( $p ) ); return (int) ( $x['thumb'] ?? 0 ); }
function set_post_thumbnail() {}
function get_page_by_path( $slug, $out = OBJECT, $type = 'page' ) {
	foreach ( $GLOBALS['kn_posts'] as $id => $p ) {
		if ( 'page' === $p['post_type'] && $p['post_name'] === $slug ) { return (object) $p; }
	}
	return null;
}
function get_post_type_archive_link( $t ) {
	$map = array( 'kn_work' => 'works', 'kn_interview' => 'interview' );
	return isset( $map[ $t ] ) ? $GLOBALS['kn_base'] . '/' . $map[ $t ] . '.html' : '';
}
function get_posts( $args = array() ) {
	$type  = $args['post_type'] ?? 'post';
	$limit = (int) ( $args['posts_per_page'] ?? 5 );
	$tax   = $args['tax_query'][0] ?? null;

	$out = array();
	foreach ( $GLOBALS['kn_posts'] as $id => $p ) {
		if ( $p['post_type'] !== $type ) { continue; }
		if ( isset( $args['name'] ) && $p['post_name'] !== $args['name'] ) { continue; }
		if ( $tax ) {
			$have = $GLOBALS['kn_rel'][ $id ][ $tax['taxonomy'] ] ?? array();
			if ( ! in_array( $tax['terms'], $have, true ) ) { continue; }
		}
		$out[] = (object) $p;
	}
	usort( $out, function ( $a, $b ) use ( $args ) {
		if ( isset( $args['orderby']['menu_order'] ) ) {
			return ( $a->menu_order ?? 0 ) <=> ( $b->menu_order ?? 0 );
		}
		return strtotime( $b->post_date ) <=> strtotime( $a->post_date );
	} );
	if ( $limit > 0 ) { $out = array_slice( $out, 0, $limit ); }
	if ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {
		return array_map( function ( $p ) { return $p->ID; }, $out );
	}
	return $out;
}

/* ---------- タクソノミー ---------- */
function get_the_terms( $p, $tax ) {
	$id    = kn_resolve( $p );
	$slugs = $GLOBALS['kn_rel'][ $id ][ $tax ] ?? array();
	$out   = array();
	foreach ( $slugs as $slug ) {
		$out[] = (object) array( 'slug' => $slug, 'name' => $GLOBALS['kn_terms'][ $tax ][ $slug ] ?? $slug );
	}
	return $out ? $out : false;
}
function get_the_category( $id = 0 ) {
	$terms = get_the_terms( $id ? $id : null, 'category' );
	return $terms ? $terms : array();
}
function get_terms( $args = array() ) { return array(); }
function term_exists() { return false; }
function is_tax() { return false; }

/* ---------- 画像 ---------- */
function wp_get_attachment_image_src( $id, $size = '' ) {
	$file = $GLOBALS['kn_images'][ $id ] ?? null;
	return $file ? array( $GLOBALS['kn_base'] . '/img/' . $file, 1200, 900, false ) : false;
}
function wp_get_attachment_image( $id, $size = '', $icon = false, $attr = array() ) {
	$src = wp_get_attachment_image_src( $id, $size );
	if ( ! $src ) { return ''; }
	$attr = array_merge( array( 'alt' => '' ), $attr );
	$out  = '<img src="' . esc_attr( $src[0] ) . '"';
	foreach ( $attr as $k => $v ) { $out .= ' ' . $k . '="' . esc_attr( $v ) . '"'; }
	return $out . '>';
}

/* ---------- ループ ---------- */
function have_posts() { return $GLOBALS['kn_query']['i'] + 1 < count( $GLOBALS['kn_query']['posts'] ); }
function the_post() {
	$GLOBALS['kn_query']['i']++;
	$GLOBALS['kn_query']['current'] = $GLOBALS['kn_query']['posts'][ $GLOBALS['kn_query']['i'] ];
}
function the_posts_pagination() {}
function wp_link_pages() {}
function get_search_form() { echo '<form class="form"><div class="field"><input type="search"></div></form>'; }
function get_search_query() { return ''; }
function get_the_archive_title() { return $GLOBALS['kn_ctx']['archive_title'] ?? 'アーカイブ'; }
function get_queried_object() { return null; }
function get_queried_object_id() { return 0; }

/* ---------- 状況判定 ---------- */
function is_front_page() { return ! empty( $GLOBALS['kn_ctx']['front'] ); }
function is_singular() { return ! empty( $GLOBALS['kn_ctx']['singular'] ); }
function is_post_type_archive() { return ! empty( $GLOBALS['kn_ctx']['pt_archive'] ); }
function is_attachment() { return false; }
function is_main_query() { return true; }
function body_class() { echo 'class="' . esc_attr( $GLOBALS['kn_ctx']['body_class'] ?? '' ) . '"'; }

/* ---------- テンプレート読み込み ---------- */
function get_header() { include get_template_directory() . '/header.php'; }
function get_footer() { include get_template_directory() . '/footer.php'; }
function get_template_part( $slug, $name = null, $args = array() ) {
	$file = get_template_directory() . '/' . $slug . ( $name ? '-' . $name : '' ) . '.php';
	if ( file_exists( $file ) ) { include $file; }
}
function wp_head() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
	echo '<title>' . esc_html( $GLOBALS['kn_ctx']['title'] ?? '' ) . ' | 有限会社金山製作所</title>' . "\n";
	echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Noto+Sans+JP:wght@400;500;700&family=Noto+Serif+JP:wght@500;600&display=swap">' . "\n";
	echo '<link rel="stylesheet" href="' . esc_attr( get_stylesheet_uri() ) . '">' . "\n";
}
function wp_footer() {
	echo '<script src="' . esc_attr( get_template_directory_uri() ) . '/assets/js/app.js"></script>' . "\n";
}

/* ---------- ナビ ---------- */
class Walker_Nav_Menu {}
function wp_nav_menu( $args ) {
	if ( ! empty( $args['fallback_cb'] ) && is_callable( $args['fallback_cb'] ) ) {
		call_user_func( $args['fallback_cb'] );
	}
}
