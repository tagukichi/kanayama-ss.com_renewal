<?php
/**
 * テーマのテンプレートを WordPress 無しで描画して、HTMLを書き出す。
 * 出力先: <出力ディレクトリ>/*.html（既定は theme-preview/）
 *
 * 目的はデザインの目視確認と、テンプレートの取りこぼし検出。
 * WordPress の実挙動（フック・クエリ・書き換え）は再現しないので、
 * これが通ったことは本番で動くことの保証にはならない。
 *
 * 使い方: php tools/wp-stub/render.php [出力ディレクトリ]
 */

$root = dirname( __DIR__, 2 );
$out  = $argv[1] ?? ( $root . '/theme-preview' );
$out  = rtrim( $out, '/' );

$GLOBALS['kn_base'] = '.';

require __DIR__ . '/wp-stub.php';
require $root . '/theme/kanayama-ss/functions.php';

/* --------------------------------------------------------------
   初期コンテンツの定義から、描画用のダミーデータを組み立てる
   -------------------------------------------------------------- */
$data     = kanayama_starter_data();
$next_id  = 100;
$image_id = 1000;
$images   = array();
$ids      = array();

/** 画像を登録してIDを返す */
$img = function ( $file ) use ( &$image_id, &$images ) {
	if ( ! $file ) { return 0; }
	foreach ( $images as $id => $f ) { if ( $f === $file ) { return $id; } }
	$images[ ++$image_id ] = $file;
	return $image_id;
};

/** 投稿を登録してIDを返す */
$add = function ( $type, $slug, $title, $extra = array() ) use ( &$next_id ) {
	$id = ++$next_id;
	$GLOBALS['kn_posts'][ $id ] = array_merge(
		array(
			'ID'           => $id,
			'post_type'    => $type,
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => '',
			'post_excerpt' => '',
			'post_date'    => '2025-01-01 00:00:00',
			'post_parent'  => 0,
			'menu_order'   => 0,
			'thumb'        => 0,
			'url'          => $slug,
		),
		$extra
	);
	return $id;
};

// 固定ページ
foreach ( $data['pages'] as $page ) {
	$id = $add(
		'page',
		$page['slug'],
		$page['title'],
		array(
			'post_content' => $page['content'] ?? '',
			'post_excerpt' => $page['excerpt'] ?? '',
			'thumb'        => $img( $page['thumb'] ?? '' ),
		)
	);
	$ids[ $page['slug'] ] = $id;
	foreach ( ( $page['meta'] ?? array() ) as $k => $v ) { $GLOBALS['kn_meta'][ $id ][ $k ] = $v; }
	if ( ! empty( $page['header'] ) ) { $GLOBALS['kn_meta'][ $id ]['_kn_page_header_img'] = $img( $page['header'] ); }
	if ( ! empty( $page['hero'] ) ) { $GLOBALS['kn_meta'][ $id ]['_kn_page_hero'] = $img( $page['hero'] ); }
	if ( ! empty( $page['template'] ) ) { $GLOBALS['kn_meta'][ $id ]['_wp_page_template'] = $page['template']; }
}
$GLOBALS['kn_posts'][ $ids['message'] ]['post_parent'] = $ids['recruit'];
$GLOBALS['kn_options']['page_for_posts'] = $ids['news'];
$GLOBALS['kn_options']['page_on_front']  = $ids['home'];
$GLOBALS['kn_mods']['contact_page']      = $ids['contact'];
$GLOBALS['kn_mods']['default_header_image']   = $img( 'bg_01.jpg' );
$GLOBALS['kn_mods']['works_header_image']     = $img( 'bg_02.jpg' );
$GLOBALS['kn_mods']['interview_header_image'] = $img( 'recruit_01.jpg' );

// FVスライド
foreach ( $data['slides'] as $order => $slide ) {
	$id = $add( 'kn_slide', $slide['slug'], $slide['title'], array( 'menu_order' => $order + 1, 'thumb' => $img( $slide['image'] ) ) );
	foreach ( $slide['meta'] as $k => $v ) { $GLOBALS['kn_meta'][ $id ][ $k ] = $v; }
}

// 対応する工事
$GLOBALS['kn_terms']['kn_service_cat'] = $data['service_terms'];
foreach ( $data['services'] as $order => $service ) {
	$id = $add( 'kn_service', $service['term'] . '-' . ( $order + 1 ), $service['title'], array( 'post_content' => $service['text'], 'menu_order' => $order + 1, 'thumb' => $img( $service['image'] ?? '' ) ) );
	$GLOBALS['kn_rel'][ $id ]['kn_service_cat'] = array( $service['term'] );
}

// 特長・ステップ
$GLOBALS['kn_terms']['kn_feature_group'] = $data['feature_terms'];
foreach ( $data['features'] as $order => $feature ) {
	$id = $add( 'kn_feature', $feature['term'] . '-' . ( $order + 1 ), $feature['title'], array( 'post_content' => $feature['text'], 'menu_order' => $order + 1 ) );
	$GLOBALS['kn_rel'][ $id ]['kn_feature_group'] = array( $feature['term'] );
	$GLOBALS['kn_meta'][ $id ]['_kn_feature_label'] = $feature['label'];
}

// 工事実績
$GLOBALS['kn_terms']['kn_work_cat'] = $data['work_terms'];
$work_ids = array();
foreach ( $data['works'] as $work ) {
	$id = $add(
		'kn_work',
		$work['slug'],
		$work['title'],
		array( 'post_content' => $work['content'] ?? '', 'post_date' => $work['date'], 'thumb' => $img( $work['image'] ?? '' ), 'url' => 'works-detail' )
	);
	$GLOBALS['kn_rel'][ $id ]['kn_work_cat'] = array( $work['term'] );
	foreach ( $work['meta'] as $k => $v ) { $GLOBALS['kn_meta'][ $id ][ $k ] = $v; }
	$work_ids[] = $id;
}
// 詳細ページの施工写真
$GLOBALS['kn_meta'][ $work_ids[0] ]['_kn_work_photo_1']   = $img( 'service_01.jpg' );
$GLOBALS['kn_meta'][ $work_ids[0] ]['_kn_work_caption_1'] = '施工前';
$GLOBALS['kn_meta'][ $work_ids[0] ]['_kn_work_photo_2']   = $img( 'service_02.jpg' );
$GLOBALS['kn_meta'][ $work_ids[0] ]['_kn_work_caption_2'] = '施工中';
$GLOBALS['kn_meta'][ $work_ids[0] ]['_kn_work_photo_3']   = $img( 'DSC01045-1-760x530.jpg' );
$GLOBALS['kn_meta'][ $work_ids[0] ]['_kn_work_caption_3'] = '施工後';

// 社員紹介
$iv_ids = array();
foreach ( $data['interviews'] as $order => $person ) {
	$id = $add( 'kn_interview', $person['slug'], $person['title'], array( 'menu_order' => $order + 1, 'thumb' => $img( $person['image'] ), 'url' => 'interview' ) );
	foreach ( $person['meta'] as $k => $v ) { $GLOBALS['kn_meta'][ $id ][ $k ] = $v; }
	if ( ! empty( $person['tool'] ) ) { $GLOBALS['kn_meta'][ $id ]['_kn_iv_tool_img'] = $img( $person['tool'] ); }
	$iv_ids[] = $id;
}

// お知らせ
$GLOBALS['kn_terms']['category'] = $data['news_categories'];
$news_ids = array();
foreach ( $data['news'] as $news ) {
	$id = $add( 'post', $news['slug'], $news['title'], array( 'post_content' => $news['content'], 'post_date' => $news['date'], 'url' => 'news-detail' ) );
	$GLOBALS['kn_rel'][ $id ]['category'] = array( $news['cat'] );
	$news_ids[] = $id;
}

$GLOBALS['kn_images'] = $images;

/* --------------------------------------------------------------
   描画
   -------------------------------------------------------------- */
@mkdir( $out, 0777, true );
@mkdir( $out . '/img', 0777, true );
foreach ( $images as $file ) {
	@copy( $root . '/theme/kanayama-ss/assets/img/starter/' . $file, $out . '/img/' . $file );
}
@mkdir( $out . '/theme/assets/js', 0777, true );
@copy( $root . '/theme/kanayama-ss/style.css', $out . '/theme/style.css' );
@copy( $root . '/theme/kanayama-ss/assets/js/app.js', $out . '/theme/assets/js/app.js' );
@mkdir( $out . '/theme/assets/img', 0777, true );
foreach ( array( 'default-header.jpg', 'default-fv.jpg' ) as $file ) {
	@copy( $root . '/theme/kanayama-ss/assets/img/' . $file, $out . '/theme/assets/img/' . $file );
}

/**
 * テンプレートを1本描画してファイルに書く。
 *
 * @param string $file  テンプレートのファイル名。
 * @param string $name  出力するHTMLの名前。
 * @param array  $posts ループに流す投稿ID。
 * @param array  $ctx   is_front_page などの状況。
 */
function kn_render( $file, $name, $posts, $ctx = array() ) {
	global $out;
	$GLOBALS['kn_query'] = array( 'posts' => $posts, 'i' => -1, 'current' => $posts[0] ?? null );
	$GLOBALS['kn_ctx']   = array_merge( array( 'title' => $name, 'body_class' => '' ), $ctx );

	ob_start();
	include dirname( __DIR__, 2 ) . '/theme/kanayama-ss/' . $file;
	$html = ob_get_clean();

	file_put_contents( $out . '/' . $name . '.html', $html );
	printf( "  %-26s → %s.html (%d バイト)\n", $file, $name, strlen( $html ) );
}

echo "テーマのテンプレートを描画:\n";
kn_render( 'front-page.php', 'index', array( $ids['home'] ), array( 'front' => true, 'body_class' => 'home is-front' ) );
kn_render( 'page-concept.php', 'concept', array( $ids['concept'] ), array( 'singular' => true, 'body_class' => 'page page-concept' ) );
kn_render( 'page-message.php', 'message', array( $ids['message'] ), array( 'singular' => true, 'body_class' => 'page page-message' ) );
kn_render( 'page-solar.php', 'solar', array( $ids['solar'] ), array( 'singular' => true, 'body_class' => 'page page-solar' ) );
kn_render( 'page-electric.php', 'electric', array( $ids['electric'] ), array( 'singular' => true, 'body_class' => 'page page-electric' ) );
kn_render( 'page-about.php', 'about', array( $ids['about'] ), array( 'singular' => true, 'body_class' => 'page page-about' ) );
kn_render( 'page-recruit.php', 'recruit', array( $ids['recruit'] ), array( 'singular' => true, 'body_class' => 'page page-recruit' ) );
kn_render( 'page-contact.php', 'contact', array( $ids['contact'] ), array( 'singular' => true, 'body_class' => 'page page-contact' ) );
kn_render( 'page.php', 'privacy', array( $ids['privacy'] ), array( 'singular' => true, 'body_class' => 'page page-privacy' ) );
kn_render( 'archive-kn_work.php', 'works', $work_ids, array( 'pt_archive' => true, 'body_class' => 'archive' ) );
kn_render( 'single-kn_work.php', 'works-detail', array( $work_ids[0] ), array( 'singular' => true, 'body_class' => 'single' ) );
kn_render( 'archive-kn_interview.php', 'interview', $iv_ids, array( 'pt_archive' => true, 'body_class' => 'archive' ) );
kn_render( 'single-kn_interview.php', 'interview-single', array( $iv_ids[1] ), array( 'singular' => true, 'body_class' => 'single' ) );
kn_render( 'home.php', 'news', $news_ids, array( 'body_class' => 'blog' ) );
kn_render( 'single.php', 'news-detail', array( $news_ids[0] ), array( 'singular' => true, 'body_class' => 'single' ) );
kn_render( '404.php', '404', array(), array( 'body_class' => 'error404' ) );
kn_render( 'search.php', 'search', $news_ids, array( 'body_class' => 'search' ) );

echo "完了: $out\n";
