<?php
/**
 * カスタム投稿タイプの入力欄。プラグインを使わずに実装している。
 *
 * 欄の定義は kanayama_field_schema() にまとめてある。
 * 追加したいときはこの配列に1行足すだけでよい。
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 投稿タイプごとの入力欄の定義。
 *
 * type は text / textarea / select / checkbox / image のいずれか。
 *
 * @return array
 */
function kanayama_field_schema() {
	return array(
		'page' => array(
			'title'  => __( 'ページヘッダーの設定', 'kanayama' ),
			'fields' => array(
				'_kn_page_en' => array(
					'type'  => 'text',
					'label' => __( '英字の見出し', 'kanayama' ),
					'desc'  => __( 'ページ上部に大きく出る英語。例：Concept／Company／Recruit', 'kanayama' ),
				),
				'_kn_page_head' => array(
					'type'    => 'select',
					'label'   => __( 'ヘッダーの見た目', 'kanayama' ),
					'options' => array(
						'photo' => __( '写真を敷く（アイキャッチ画像を使用）', 'kanayama' ),
						'light' => __( '写真なし（白地）', 'kanayama' ),
					),
					'default' => 'photo',
				),
				'_kn_page_header_img' => array(
					'type'  => 'image',
					'label' => __( 'ページヘッダーの背景写真', 'kanayama' ),
					'desc'  => __( '空欄のときはアイキャッチ画像、それも無ければカスタマイザーの既定画像を使います。', 'kanayama' ),
				),
				'_kn_page_hero' => array(
					'type'  => 'image',
					'label' => __( 'パンくずの下に置く大きな画像', 'kanayama' ),
					'desc'  => __( '採用情報ページなどで使います。不要なら空のままにしてください。', 'kanayama' ),
				),
				'_kn_front_lead' => array(
					'type'  => 'text',
					'label' => __( '大きなリード文', 'kanayama' ),
					'desc'  => __( '「私たちの想い」の見出しと、トップページの要約に使います。「|」で改行します。', 'kanayama' ),
				),
				'_kn_page_heading' => array(
					'type'  => 'text',
					'label' => __( '本文いちばん上の見出し', 'kanayama' ),
					'desc'  => __( '事業案内・会社案内などで使います。「|」で改行します。例：つくる、貯める、|使いきる。', 'kanayama' ),
				),
				'_kn_page_caption' => array(
					'type'  => 'text',
					'label' => __( '写真のキャプション', 'kanayama' ),
					'desc'  => __( '「私たちの想い」の写真の下に出ます。例：代表取締役　金山　準', 'kanayama' ),
				),
				'_kn_message_extra' => array(
					'type'  => 'textarea',
					'label' => __( '代表メッセージ：これから仲間になる方へ', 'kanayama' ),
					'rows'  => 6,
					'desc'  => __( '空行で段落を分けます。空欄ならこの節ごと表示しません。', 'kanayama' ),
				),
				'_kn_about_ceo' => array(
					'type'  => 'text',
					'label' => __( '会社概要：代表者', 'kanayama' ),
					'desc'  => __( '例：代表取締役　金山 準', 'kanayama' ),
				),
				'_kn_about_business' => array(
					'type'  => 'textarea',
					'label' => __( '会社概要：事業内容', 'kanayama' ),
					'rows'  => 6,
					'desc'  => __( '1行につき1項目。改行がそのまま表示されます。', 'kanayama' ),
				),
				'_kn_about_map' => array(
					'type'  => 'text',
					'label' => __( '会社概要：地図の埋め込みURL', 'kanayama' ),
					'desc'  => __( '空欄なら住所から自動で表示します。差し替えたいときだけGoogleマップの埋め込みURLを入れてください。', 'kanayama' ),
				),
				'_kn_recruit_table' => array(
					'type'  => 'textarea',
					'label' => __( '募集要項の表', 'kanayama' ),
					'rows'  => 8,
					'desc'  => __( '「項目 | 内容」の形で1行ずつ。内容の中の「/」は改行になります。例：応募資格 | 未経験可/要普通自動車免許', 'kanayama' ),
				),
				'_kn_page_faq' => array(
					'type'  => 'textarea',
					'label' => __( 'よくあるご質問', 'kanayama' ),
					'rows'  => 10,
					'desc'  => __( '1行目に「Q. 質問」、次の行に「A. 回答」。1組ごとに空行で区切ります。空欄ならFAQの節ごと表示しません。', 'kanayama' ),
				),
			),
		),

		'kn_slide' => array(
			'title'  => __( 'スライドの設定', 'kanayama' ),
			'fields' => array(
				'_kn_slide_overlay' => array(
					'type'  => 'checkbox',
					'label' => __( 'キャッチコピーを重ねる', 'kanayama' ),
					'desc'  => __( 'バナー画像のように絵だけを見せたいときはチェックを外します。', 'kanayama' ),
					'default' => '1',
				),
				'_kn_slide_fit' => array(
					'type'    => 'select',
					'label'   => __( '画像の収め方', 'kanayama' ),
					'options' => array(
						'cover'   => __( '枠いっぱいに敷く（写真向き）', 'kanayama' ),
						'contain' => __( '全体を収める（バナー・ロゴ向き）', 'kanayama' ),
					),
					'default' => 'cover',
				),
				'_kn_slide_bg' => array(
					'type'  => 'text',
					'label' => __( '余白の色', 'kanayama' ),
					'desc'  => __( '「全体を収める」を選んだときの背景色。例：#F2F2F2', 'kanayama' ),
				),
				'_kn_slide_pos' => array(
					'type'  => 'text',
					'label' => __( '切り取り位置', 'kanayama' ),
					'desc'  => __( 'CSSのobject-position。左寄りに切りたいときは「8% 50%」など。空欄なら中央。', 'kanayama' ),
				),
			),
		),

		'kn_work' => array(
			'title'  => __( '工事の概要', 'kanayama' ),
			'fields' => array(
				'_kn_work_area' => array(
					'type'  => 'text',
					'label' => __( '施工エリア', 'kanayama' ),
					'desc'  => __( '例：東京都墨田区', 'kanayama' ),
				),
				'_kn_work_scope' => array(
					'type'  => 'text',
					'label' => __( '工事内容', 'kanayama' ),
					'desc'  => __( '例：太陽光パネル設置／蓄電池設置', 'kanayama' ),
				),
				'_kn_work_roof' => array(
					'type'  => 'text',
					'label' => __( '屋根材', 'kanayama' ),
					'desc'  => __( '例：スレート', 'kanayama' ),
				),
				'_kn_work_term' => array(
					'type'  => 'text',
					'label' => __( '工期', 'kanayama' ),
					'desc'  => __( '例：2日間', 'kanayama' ),
				),
				'_kn_work_photo_1' => array( 'type' => 'image', 'label' => __( '施工写真 1', 'kanayama' ) ),
				'_kn_work_caption_1' => array( 'type' => 'text', 'label' => __( '写真1のキャプション', 'kanayama' ), 'default' => '施工前' ),
				'_kn_work_photo_2' => array( 'type' => 'image', 'label' => __( '施工写真 2', 'kanayama' ) ),
				'_kn_work_caption_2' => array( 'type' => 'text', 'label' => __( '写真2のキャプション', 'kanayama' ), 'default' => '施工中' ),
				'_kn_work_photo_3' => array( 'type' => 'image', 'label' => __( '施工写真 3', 'kanayama' ) ),
				'_kn_work_caption_3' => array( 'type' => 'text', 'label' => __( '写真3のキャプション', 'kanayama' ), 'default' => '施工後' ),
			),
		),

		'kn_interview' => array(
			'title'  => __( 'インタビューの内容', 'kanayama' ),
			'fields' => array(
				'_kn_iv_style' => array(
					'type'    => 'select',
					'label'   => __( '種類', 'kanayama' ),
					'options' => array(
						'staff' => __( '社員インタビュー', 'kanayama' ),
						'dog'   => __( '看板犬など（自由文）', 'kanayama' ),
					),
					'default' => 'staff',
				),
				'_kn_iv_role' => array(
					'type'  => 'text',
					'label' => __( '職種・肩書', 'kanayama' ),
					'desc'  => __( '例：統括部長／施工／事務／看板犬', 'kanayama' ),
				),
				'_kn_iv_year' => array(
					'type'  => 'text',
					'label' => __( '名前の横に出す文字', 'kanayama' ),
					'desc'  => __( '例：2011年入社／ジャムテ', 'kanayama' ),
				),
				'_kn_iv_number' => array(
					'type'  => 'text',
					'label' => __( '通し番号', 'kanayama' ),
					'desc'  => __( '例：01。空欄にすると番号を表示しません。', 'kanayama' ),
				),
				'_kn_iv_kind' => array(
					'type'  => 'text',
					'label' => __( '犬種など（看板犬のとき）', 'kanayama' ),
				),
				'_kn_iv_lead_q' => array(
					'type'  => 'text',
					'label' => __( '折りたたむ前の質問', 'kanayama' ),
					'default' => '現在の仕事内容を教えてください。',
				),
				'_kn_iv_lead_a' => array(
					'type'  => 'textarea',
					'label' => __( '折りたたむ前の回答', 'kanayama' ),
					'rows'  => 3,
				),
				'_kn_iv_qa' => array(
					'type'  => 'textarea',
					'label' => __( '「続きを読む」の中の質問と回答', 'kanayama' ),
					'rows'  => 12,
					'desc'  => __( '1行目に「Q. 質問」、次の行に「A. 回答」。1組ごとに空行で区切ります。回答内の改行はそのまま表示されます。', 'kanayama' ),
				),
				'_kn_iv_sched' => array(
					'type'  => 'textarea',
					'label' => __( '1日のスケジュール', 'kanayama' ),
					'rows'  => 8,
					'desc'  => __( '「6:30　出社」のように、時刻と内容を空白で区切って1行ずつ。時刻のない文章を書いた場合はそのまま文章として表示します。', 'kanayama' ),
				),
				'_kn_iv_sched_note' => array(
					'type'  => 'text',
					'label' => __( 'スケジュールの注記', 'kanayama' ),
					'desc'  => __( '例：※早起きは三文以上の得だと思っております。', 'kanayama' ),
				),
				'_kn_iv_tool_text' => array(
					'type'  => 'textarea',
					'label' => __( '私の仕事道具（説明）', 'kanayama' ),
					'rows'  => 4,
				),
				'_kn_iv_tool_img' => array(
					'type'  => 'image',
					'label' => __( '私の仕事道具（写真）', 'kanayama' ),
				),
				'_kn_iv_free' => array(
					'type'  => 'textarea',
					'label' => __( '自由文（看板犬のとき）', 'kanayama' ),
					'rows'  => 5,
				),
			),
		),

		'kn_feature' => array(
			'title'  => __( 'カードの設定', 'kanayama' ),
			'fields' => array(
				'_kn_feature_label' => array(
					'type'  => 'text',
					'label' => __( '左上のラベル', 'kanayama' ),
					'desc'  => __( '例：Reason 01／Step 01／Point 01／01', 'kanayama' ),
				),
			),
		),
	);
}

/**
 * メタボックスを追加する。
 */
function kanayama_add_meta_boxes() {
	foreach ( kanayama_field_schema() as $post_type => $box ) {
		add_meta_box(
			'kanayama-' . $post_type,
			$box['title'],
			'kanayama_render_meta_box',
			$post_type,
			'normal',
			'high',
			array( 'post_type' => $post_type )
		);
	}
}
add_action( 'add_meta_boxes', 'kanayama_add_meta_boxes' );

/**
 * メタボックスの中身を描く。
 *
 * @param WP_Post $post 編集中の投稿。
 * @param array   $box  add_meta_box に渡した情報。
 */
function kanayama_render_meta_box( $post, $box ) {
	$schema = kanayama_field_schema();
	$fields = $schema[ $box['args']['post_type'] ]['fields'];

	wp_nonce_field( 'kanayama_save_meta', 'kanayama_meta_nonce' );

	// 一度も保存していない投稿にだけ既定値を出す。
	$is_new = ! get_post_meta( $post->ID, '_kanayama_saved', true );

	echo '<div class="kanayama-fields">';
	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		if ( '' === $value && $is_new && isset( $field['default'] ) ) {
			$value = $field['default'];
		}
		$id = esc_attr( $key );

		echo '<p class="kanayama-field" style="margin:0 0 18px">';
		if ( 'checkbox' !== $field['type'] ) {
			printf( '<label for="%s"><strong>%s</strong></label><br>', $id, esc_html( $field['label'] ) );
		}

		switch ( $field['type'] ) {
			case 'textarea':
				printf(
					'<textarea id="%s" name="%s" rows="%d" class="widefat" style="font-family:inherit">%s</textarea>',
					$id,
					$id,
					isset( $field['rows'] ) ? (int) $field['rows'] : 4,
					esc_textarea( $value )
				);
				break;

			case 'select':
				printf( '<select id="%s" name="%s">', $id, $id );
				foreach ( $field['options'] as $opt_value => $opt_label ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $opt_value ),
						selected( $value, $opt_value, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'checkbox':
				printf(
					'<label for="%s"><input type="checkbox" id="%s" name="%s" value="1"%s> <strong>%s</strong></label>',
					$id,
					$id,
					$id,
					checked( $value, '1', false ),
					esc_html( $field['label'] )
				);
				break;

			case 'image':
				$attachment_id = (int) $value;
				$thumb         = $attachment_id ? wp_get_attachment_image( $attachment_id, 'medium', false, array( 'style' => 'max-width:240px;height:auto;display:block;margin-bottom:8px' ) ) : '';
				printf(
					'<span class="kanayama-image" data-target="%s"><span class="kanayama-image__preview">%s</span>'
					. '<input type="hidden" id="%s" name="%s" value="%s">'
					. '<button type="button" class="button kanayama-image__pick">%s</button> '
					. '<button type="button" class="button-link kanayama-image__clear" style="margin-left:10px">%s</button></span>',
					$id,
					$thumb, // wp_get_attachment_image の出力なのでそのまま。
					$id,
					$id,
					esc_attr( $attachment_id ),
					esc_html__( '画像を選ぶ', 'kanayama' ),
					esc_html__( '削除', 'kanayama' )
				);
				break;

			default:
				printf(
					'<input type="text" id="%s" name="%s" value="%s" class="widefat">',
					$id,
					$id,
					esc_attr( $value )
				);
		}

		if ( ! empty( $field['desc'] ) ) {
			printf( '<br><span class="description">%s</span>', esc_html( $field['desc'] ) );
		}
		echo '</p>';
	}
	echo '</div>';
}

/**
 * 入力欄を保存する。
 *
 * @param int $post_id 投稿ID。
 */
function kanayama_save_meta( $post_id ) {
	if ( ! isset( $_POST['kanayama_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['kanayama_meta_nonce'] ) ), 'kanayama_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$schema    = kanayama_field_schema();
	$post_type = get_post_type( $post_id );
	if ( ! isset( $schema[ $post_type ] ) ) {
		return;
	}

	foreach ( $schema[ $post_type ]['fields'] as $key => $field ) {
		if ( 'checkbox' === $field['type'] ) {
			// 未チェックは '0'。空文字だと「未保存」と区別できなくなる。
			update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? '1' : '0' );
			continue;
		}
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );

		switch ( $field['type'] ) {
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			case 'image':
				$value = (int) $raw ? (string) (int) $raw : '';
				break;
			case 'select':
				$value = array_key_exists( $raw, $field['options'] ) ? $raw : '';
				break;
			default:
				$value = sanitize_text_field( $raw );
		}
		update_post_meta( $post_id, $key, $value );
	}

	// 2回目以降は既定値を出さないための印。
	update_post_meta( $post_id, '_kanayama_saved', '1' );
}
add_action( 'save_post', 'kanayama_save_meta' );

/**
 * 画像選択に使うメディアライブラリとスクリプトを読み込む。
 *
 * @param string $hook 管理画面のフック名。
 */
function kanayama_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! array_key_exists( $screen->post_type, kanayama_field_schema() ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'kanayama-admin',
		get_template_directory_uri() . '/assets/js/admin.js',
		array( 'jquery' ),
		KANAYAMA_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'kanayama_admin_assets' );
