<?php
/**
 * カスタム投稿タイプとタクソノミー。
 *
 *   kn_slide     FVスライド        管理画面のみ（トップに出る）
 *   kn_work      工事実績          /works/ に一覧と詳細
 *   kn_interview 社員紹介          /interview/ に一覧と詳細
 *   kn_service   対応する工事      管理画面のみ（事業案内の各ページに出る）
 *   kn_feature   特長・ステップ    管理画面のみ（各ページのカードに出る）
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 投稿タイプの登録。
 */
function kanayama_register_post_types() {

	register_post_type(
		'kn_slide',
		array(
			'label'         => __( 'FVスライド', 'kanayama' ),
			'labels'        => kanayama_pt_labels( 'FVスライド', 'スライド' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => false,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-images-alt2',
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'description'   => __( 'トップページのファーストビューで切り替わる画像。並び順で表示順が決まります。', 'kanayama' ),
		)
	);

	register_post_type(
		'kn_work',
		array(
			'label'         => __( '工事実績', 'kanayama' ),
			'labels'        => kanayama_pt_labels( '工事実績', '実績' ),
			'public'        => true,
			'has_archive'   => 'works',
			'rewrite'       => array( 'slug' => 'works', 'with_front' => false ),
			'menu_position' => 22,
			'menu_icon'     => 'dashicons-hammer',
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		)
	);

	register_post_type(
		'kn_interview',
		array(
			'label'         => __( '社員紹介', 'kanayama' ),
			'labels'        => kanayama_pt_labels( '社員紹介', '社員' ),
			'public'        => true,
			'has_archive'   => 'interview',
			'rewrite'       => array( 'slug' => 'interview', 'with_front' => false ),
			'menu_position' => 23,
			'menu_icon'     => 'dashicons-groups',
			'show_in_rest'  => false,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'kn_service',
		array(
			'label'         => __( '対応する工事', 'kanayama' ),
			'labels'        => kanayama_pt_labels( '対応する工事', '工事' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => false,
			'menu_position' => 24,
			'menu_icon'     => 'dashicons-screenoptions',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'description'   => __( '事業案内の各ページに並ぶカード。「事業区分」で太陽光か電気・水道かを選びます。', 'kanayama' ),
		)
	);

	register_post_type(
		'kn_feature',
		array(
			'label'         => __( '特長・ステップ', 'kanayama' ),
			'labels'        => kanayama_pt_labels( '特長・ステップ', '項目' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => false,
			'menu_position' => 25,
			'menu_icon'     => 'dashicons-list-view',
			'supports'      => array( 'title', 'editor', 'page-attributes' ),
			'description'   => __( '「選ばれる理由」「工事の流れ」などの箇条カード。「グループ」でどのページに出すかを選びます。', 'kanayama' ),
		)
	);
}
add_action( 'init', 'kanayama_register_post_types' );

/**
 * 投稿タイプのラベルをまとめて作る。
 *
 * @param string $name  表示名。
 * @param string $short 「新規追加」などに使う短い名前。
 * @return array
 */
function kanayama_pt_labels( $name, $short ) {
	return array(
		'name'               => $name,
		'singular_name'      => $name,
		'add_new'            => sprintf( '%sを追加', $short ),
		'add_new_item'       => sprintf( '%sを追加', $short ),
		'edit_item'          => sprintf( '%sを編集', $short ),
		'new_item'           => sprintf( '新しい%s', $short ),
		'view_item'          => sprintf( '%sを表示', $short ),
		'search_items'       => sprintf( '%sを検索', $name ),
		'not_found'          => sprintf( '%sはまだありません', $name ),
		'not_found_in_trash' => sprintf( 'ゴミ箱に%sはありません', $name ),
		'all_items'          => sprintf( '%s一覧', $name ),
		'menu_name'          => $name,
	);
}

/**
 * タクソノミーの登録。
 */
function kanayama_register_taxonomies() {

	register_taxonomy(
		'kn_work_cat',
		'kn_work',
		array(
			'label'             => __( '工事区分', 'kanayama' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'works-category', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'kn_service_cat',
		'kn_service',
		array(
			'label'             => __( '事業区分', 'kanayama' ),
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);

	register_taxonomy(
		'kn_feature_group',
		'kn_feature',
		array(
			'label'             => __( 'グループ', 'kanayama' ),
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);
}
add_action( 'init', 'kanayama_register_taxonomies', 5 );

/**
 * 有効化直後にパーマリンクを作り直す。/works/ などをすぐ使えるようにする。
 */
function kanayama_flush_rewrite() {
	kanayama_register_post_types();
	kanayama_register_taxonomies();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'kanayama_flush_rewrite' );
