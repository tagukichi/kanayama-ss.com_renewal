<?php
/**
 * 有限会社金山製作所テーマ — 読み込み口
 *
 * テーマの中身は inc/ に分けている。
 *   setup.php         テーマサポート・アセット読み込み・メニュー登録
 *   nav-walker.php    グローバルナビの階層出力
 *   post-types.php    カスタム投稿タイプとタクソノミー
 *   meta-boxes.php    カスタム投稿タイプの入力欄（プラグイン不要）
 *   customizer.php    会社情報・ファーストビューの設定
 *   template-tags.php テンプレートから呼ぶ表示用の関数
 *   starter-content.php 初期コンテンツの一括投入
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KANAYAMA_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/nav-walker.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/starter-content.php';
