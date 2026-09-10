<?php
/**
 * カスタマイザー。サイト全体で使い回す会社情報と、トップの見せ方をここで設定する。
 *
 * 値の取り出しは kanayama_opt( 'company_tel' ) のように行う（template-tags.php）。
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 設定項目の定義。既定値はプロトタイプの内容に合わせてある。
 *
 * @return array
 */
function kanayama_settings_schema() {
	return array(
		'kanayama_company' => array(
			'title'    => __( '会社情報', 'kanayama' ),
			'priority' => 30,
			'desc'     => __( 'ヘッダー・フッター・お問い合わせ帯・会社概要で共通して使います。', 'kanayama' ),
			'fields'   => array(
				'company_name'        => array( 'label' => '会社名', 'default' => '有限会社 金山製作所' ),
				'company_biz'         => array( 'label' => 'ロゴ上の業種表記', 'default' => '太陽光発電・蓄電池工事／電気・水道工事' ),
				'company_reg'         => array( 'label' => '登録番号', 'default' => '東京都知事登録 第290199号' ),
				'company_zip'         => array( 'label' => '郵便番号', 'default' => '〒131-0042' ),
				'company_addr'        => array( 'label' => '住所', 'default' => '東京都墨田区東墨田 2-12-20' ),
				'company_tel'         => array( 'label' => '電話番号', 'default' => '03-6670-5540' ),
				'company_fax'         => array( 'label' => 'FAX番号', 'default' => '03-6323-8861' ),
				'company_hours'       => array( 'label' => '営業時間（正式表記）', 'default' => '9:00〜18:00（日曜・祝日定休）' ),
				'company_hours_short' => array( 'label' => '営業時間（ヘッダー用の短い表記）', 'default' => '9:00〜18:00' ),
			),
		),

		'kanayama_fv' => array(
			'title'    => __( 'トップ：ファーストビュー', 'kanayama' ),
			'priority' => 31,
			'desc'     => __( 'スライドの画像は「FVスライド」から追加します。ここでは文字と表示のしかたを決めます。', 'kanayama' ),
			'fields'   => array(
				'fv_ratio'     => array(
					'label'   => '高さの比率',
					'default' => '1360 / 540',
					'desc'    => '1枚目の画像の「横 / 縦」を入れます。すべてのスライドがこの高さに揃い、上下に余白が出ません。',
				),
				'fv_interval'  => array( 'label' => '自動切り替えの間隔（ミリ秒）', 'default' => '6000' ),
				'fv_eyebrow_1' => array( 'label' => '見出し上の英字（左）', 'default' => 'Solar & Storage' ),
				'fv_eyebrow_2' => array( 'label' => '見出し上の英字（右）', 'default' => 'Electric & Plumbing' ),
				'fv_title'     => array(
					'label'   => 'キャッチコピー',
					'default' => '屋根の上に、|*確かな仕事*を。',
					'desc'    => '「|」で改行、「*〜*」で囲むとその部分が緑色になります。',
					'type'    => 'textarea',
				),
				'fv_sub'       => array(
					'label'   => 'リード文',
					'type'    => 'textarea',
					'default' => '2011年から住宅の屋根に上がり続けてきました。太陽光発電システムの設置と、電気・水道工事。その両方を自社で手がけられることが、私たちの強みです。',
				),
			),
		),

		'kanayama_stats' => array(
			'title'    => __( 'トップ：実績の数字', 'kanayama' ),
			'priority' => 32,
			'fields'   => array(
				'stat1_num'   => array( 'label' => '1つめ：数字', 'default' => '25,000' ),
				'stat1_unit'  => array( 'label' => '1つめ：単位', 'default' => '棟以上' ),
				'stat1_label' => array( 'label' => '1つめ：説明', 'default' => 'これまでに上がった屋根' ),
				'stat2_num'   => array( 'label' => '2つめ：数字', 'default' => '2011' ),
				'stat2_unit'  => array( 'label' => '2つめ：単位', 'default' => '年 –' ),
				'stat2_label' => array( 'label' => '2つめ：説明', 'default' => '太陽光施工に取り組んで' ),
				'stat3_num'   => array( 'label' => '3つめ：数字', 'default' => '2' ),
				'stat3_unit'  => array( 'label' => '3つめ：単位', 'default' => '事業' ),
				'stat3_label' => array( 'label' => '3つめ：説明', 'default' => '太陽光 ＋ 電気・水道' ),
			),
		),

		'kanayama_cta' => array(
			'title'    => __( '全ページ共通：お問い合わせ帯', 'kanayama' ),
			'priority' => 33,
			'fields'   => array(
				'cta_title' => array(
					'label'   => '見出し',
					'type'    => 'textarea',
					'default' => '太陽光も、電気も水道も。|まとめてご相談ください。',
					'desc'    => '「|」で改行します。',
				),
				'cta_text'  => array(
					'label'   => '本文',
					'type'    => 'textarea',
					'default' => '現地調査・お見積りは無料です。他社でお見積り済みの内容へのセカンドオピニオンや、販売店様・元請け様からの施工のご依頼も承っています。',
				),
			),
		),
	);
}

/**
 * カスタマイザーに項目を登録する。
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザー。
 */
function kanayama_customize_register( $wp_customize ) {

	// 標準項目もライブプレビューで動くようにする。
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	foreach ( kanayama_settings_schema() as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => $section['title'],
				'priority'    => $section['priority'],
				'description' => isset( $section['desc'] ) ? $section['desc'] : '',
			)
		);

		foreach ( $section['fields'] as $key => $field ) {
			$type = isset( $field['type'] ) ? $field['type'] : 'text';

			$wp_customize->add_setting(
				$key,
				array(
					'default'           => $field['default'],
					'sanitize_callback' => 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				$key,
				array(
					'label'       => $field['label'],
					'section'     => $section_id,
					'type'        => 'textarea' === $type ? 'textarea' : 'text',
					'description' => isset( $field['desc'] ) ? $field['desc'] : '',
				)
			);
		}
	}

	// お問い合わせページの指定。ボタンのリンク先に使う。
	$wp_customize->add_setting(
		'contact_page',
		array( 'default' => 0, 'sanitize_callback' => 'absint' )
	);
	$wp_customize->add_control(
		'contact_page',
		array(
			'label'       => __( 'お問い合わせページ', 'kanayama' ),
			'section'     => 'kanayama_cta',
			'type'        => 'dropdown-pages',
			'description' => __( 'ヘッダーとお問い合わせ帯のボタンのリンク先になります。', 'kanayama' ),
		)
	);

	// 一覧ページのヘッダー画像。固定ページが無いので、ここで指定する。
	$archive_headers = array(
		'works_header_image'     => __( '工事実績一覧のヘッダー画像', 'kanayama' ),
		'interview_header_image' => __( '社員紹介一覧のヘッダー画像', 'kanayama' ),
	);
	foreach ( $archive_headers as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => '', 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$key,
				array( 'label' => $label, 'section' => 'kanayama_company', 'mime_type' => 'image' )
			)
		);
	}

	// 既定のページヘッダー画像。ページごとのアイキャッチが無いときに使う。
	$wp_customize->add_setting(
		'default_header_image',
		array( 'default' => '', 'sanitize_callback' => 'absint' )
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'default_header_image',
			array(
				'label'       => __( '下層ページの既定ヘッダー画像', 'kanayama' ),
				'section'     => 'kanayama_company',
				'mime_type'   => 'image',
				'description' => __( 'ページにアイキャッチが設定されていないときに使います。', 'kanayama' ),
			)
		)
	);
}
add_action( 'customize_register', 'kanayama_customize_register' );
