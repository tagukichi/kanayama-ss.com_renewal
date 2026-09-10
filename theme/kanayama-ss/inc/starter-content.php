<?php
/**
 * 初期コンテンツの一括投入。
 *
 * テーマを有効化しただけでは中身が空なので、管理画面の
 * 「外観 → 初期コンテンツ」から1回だけ実行してもらう。
 * すでに同じスラッグのものがあれば作り直さないので、何度実行しても安全。
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/starter-content-data.php';

/**
 * 管理メニューに追加する。
 */
function kanayama_starter_menu() {
	add_theme_page(
		__( '初期コンテンツ', 'kanayama' ),
		__( '初期コンテンツ', 'kanayama' ),
		'edit_theme_options',
		'kanayama-starter',
		'kanayama_starter_screen'
	);
}
add_action( 'admin_menu', 'kanayama_starter_menu' );

/**
 * 未実行のあいだ、管理画面に案内を出す。
 */
function kanayama_starter_notice() {
	if ( get_option( 'kanayama_starter_done' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_kanayama-starter' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( '金山製作所テーマ：ページ・工事実績・社員紹介などの初期コンテンツをまとめて作成できます。', 'kanayama' ),
		esc_url( admin_url( 'themes.php?page=kanayama-starter' ) ),
		esc_html__( '初期コンテンツを入れる', 'kanayama' )
	);
}
add_action( 'admin_notices', 'kanayama_starter_notice' );

/**
 * 設定画面。
 */
function kanayama_starter_screen() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( '権限がありません。', 'kanayama' ) );
	}

	$log = array();
	if ( isset( $_POST['kanayama_starter_run'] ) ) {
		check_admin_referer( 'kanayama_starter' );
		$log = kanayama_starter_import( ! empty( $_POST['kanayama_starter_images'] ) );
		update_option( 'kanayama_starter_done', 1 );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( '初期コンテンツ', 'kanayama' ); ?></h1>

		<?php if ( $log ) : ?>
		<div class="notice notice-success"><p><?php esc_html_e( '作成が完了しました。', 'kanayama' ); ?></p></div>
		<ul style="margin:20px 0;padding:16px 20px;background:#fff;border:1px solid #dcdcde;max-height:420px;overflow:auto">
			<?php foreach ( $log as $line ) : ?>
			<li><?php echo esc_html( $line ); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<p><?php esc_html_e( '下のボタンを押すと、次のものをまとめて作成します。すでに同じスラッグのものがある場合はそのまま残し、作り直しません。', 'kanayama' ); ?></p>
		<ul style="list-style:disc;margin-left:22px">
			<li><?php esc_html_e( '固定ページ 10件（ホーム／私たちの想い／太陽光発電・蓄電／電気・水道工事／会社案内／採用情報／代表メッセージ／お問い合わせ／プライバシーポリシー／お知らせ）', 'kanayama' ); ?></li>
			<li><?php esc_html_e( 'FVスライド 2件、工事実績 6件、社員紹介 6件、対応する工事 10件、特長・ステップ 15件、お知らせ 5件', 'kanayama' ); ?></li>
			<li><?php esc_html_e( 'グローバルナビとフッターの3つのメニュー', 'kanayama' ); ?></li>
			<li><?php esc_html_e( 'フロントページと投稿ページの割り当て', 'kanayama' ); ?></li>
		</ul>

		<form method="post">
			<?php wp_nonce_field( 'kanayama_starter' ); ?>
			<p>
				<label>
					<input type="checkbox" name="kanayama_starter_images" value="1" checked>
					<?php esc_html_e( 'テーマに同梱している写真をメディアライブラリに取り込む', 'kanayama' ); ?>
				</label><br>
				<span class="description"><?php esc_html_e( 'すでに同じファイル名の画像がある場合は取り込まず、それを使います。現行サイトのWordPressに入れる場合は、チェックを外して既存のメディアを手で割り当てることもできます。', 'kanayama' ); ?></span>
			</p>
			<?php submit_button( __( '初期コンテンツを作成する', 'kanayama' ), 'primary', 'kanayama_starter_run' ); ?>
		</form>
	</div>
	<?php
}

/**
 * テーマ同梱の画像をメディアライブラリに取り込む。
 * 同じファイル名の添付がすでにあればそれを使い回す。
 *
 * @param string $file  assets/img/starter/ の中のファイル名。
 * @param string $title 添付のタイトル。
 * @return int 添付ID。取り込めなければ0。
 */
function kanayama_starter_image( $file, $title = '' ) {
	static $cache = array();
	if ( isset( $cache[ $file ] ) ) {
		return $cache[ $file ];
	}

	// 同名の添付がすでにあるか。
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'name'           => sanitize_title( pathinfo( $file, PATHINFO_FILENAME ) ),
		)
	);
	if ( $existing ) {
		$cache[ $file ] = (int) $existing[0];
		return $cache[ $file ];
	}

	$source = get_template_directory() . '/assets/img/starter/' . $file;
	if ( ! file_exists( $source ) ) {
		$cache[ $file ] = 0;
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( $file, null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- テーマ内のファイルを読むだけ。
	if ( ! empty( $upload['error'] ) ) {
		$cache[ $file ] = 0;
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $upload['type'],
			'post_title'     => $title ? $title : pathinfo( $file, PATHINFO_FILENAME ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		$cache[ $file ] = 0;
		return 0;
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );

	$cache[ $file ] = (int) $attachment_id;
	return $cache[ $file ];
}

/**
 * 投稿を1件つくる。同じスラッグのものがあればそのIDを返す。
 *
 * @param array $args post_type / post_name / post_title / post_content ほか。
 * @return int 投稿ID。
 */
function kanayama_starter_post( $args ) {
	$existing = get_posts(
		array(
			'post_type'      => $args['post_type'],
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'name'           => $args['post_name'],
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$id = wp_insert_post( wp_parse_args( $args, array( 'post_status' => 'publish' ) ), true );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * タクソノミーの語をまとめてつくる。
 *
 * @param string $taxonomy タクソノミー名。
 * @param array  $terms    スラッグ => 表示名。
 */
function kanayama_starter_terms( $taxonomy, $terms ) {
	foreach ( $terms as $slug => $name ) {
		if ( ! term_exists( $slug, $taxonomy ) ) {
			wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
		}
	}
}

/**
 * 初期コンテンツを作成する本体。
 *
 * @param bool $with_images 画像も取り込むか。
 * @return array 実行ログ。
 */
function kanayama_starter_import( $with_images = true ) {
	$data = kanayama_starter_data();
	$log  = array();
	$ids  = array();

	/* --- 固定ページ --- */
	$front_id = 0;
	$blog_id  = 0;
	foreach ( $data['pages'] as $page ) {
		$id = kanayama_starter_post(
			array(
				'post_type'    => 'page',
				'post_name'    => $page['slug'],
				'post_title'   => $page['title'],
				'post_content' => isset( $page['content'] ) ? $page['content'] : '',
				'post_excerpt' => isset( $page['excerpt'] ) ? $page['excerpt'] : '',
			)
		);
		if ( ! $id ) {
			continue;
		}
		$ids[ $page['slug'] ] = $id;

		if ( ! empty( $page['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', $page['template'] );
		}
		foreach ( ( isset( $page['meta'] ) ? $page['meta'] : array() ) as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
		update_post_meta( $id, '_kanayama_saved', '1' );

		if ( $with_images && ! empty( $page['thumb'] ) ) {
			$thumb_id = kanayama_starter_image( $page['thumb'], $page['title'] );
			if ( $thumb_id ) {
				set_post_thumbnail( $id, $thumb_id );
			}
		}
		if ( $with_images && ! empty( $page['header'] ) ) {
			$header_img = kanayama_starter_image( $page['header'], __( 'ページヘッダー', 'kanayama' ) );
			if ( $header_img ) {
				update_post_meta( $id, '_kn_page_header_img', $header_img );
			}
		}
		if ( $with_images && ! empty( $page['hero'] ) ) {
			$hero_id = kanayama_starter_image( $page['hero'], $page['title'] );
			if ( $hero_id ) {
				update_post_meta( $id, '_kn_page_hero', $hero_id );
			}
		}
		if ( ! empty( $page['front'] ) ) {
			$front_id = $id;
		}
		if ( ! empty( $page['blog'] ) ) {
			$blog_id = $id;
		}

		/* translators: %s: ページ名 */
		$log[] = sprintf( __( '固定ページ「%s」', 'kanayama' ), $page['title'] );
	}

	// 親子関係は全ページ作成後に設定する。
	foreach ( $data['pages'] as $page ) {
		if ( empty( $page['parent'] ) || empty( $ids[ $page['slug'] ] ) || empty( $ids[ $page['parent'] ] ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'          => $ids[ $page['slug'] ],
				'post_parent' => $ids[ $page['parent'] ],
			)
		);
	}

	/* --- 表示設定 --- */
	if ( $front_id && $blog_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_id );
		update_option( 'page_for_posts', $blog_id );
		$log[] = __( 'フロントページと投稿ページを割り当て', 'kanayama' );
	}
	if ( ! empty( $ids['contact'] ) ) {
		set_theme_mod( 'contact_page', $ids['contact'] );
	}
	if ( ! empty( $ids['privacy'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $ids['privacy'] );
	}
	if ( $with_images ) {
		$archive_headers = array(
			'default_header_image'   => 'bg_01.jpg',
			'works_header_image'     => 'bg_02.jpg',
			'interview_header_image' => 'recruit_01.jpg',
		);
		foreach ( $archive_headers as $mod => $file ) {
			$header_id = kanayama_starter_image( $file, __( 'ページヘッダー', 'kanayama' ) );
			if ( $header_id ) {
				set_theme_mod( $mod, $header_id );
			}
		}
	}

	/* --- FVスライド --- */
	foreach ( $data['slides'] as $order => $slide ) {
		$id = kanayama_starter_post(
			array(
				'post_type'  => 'kn_slide',
				'post_name'  => $slide['slug'],
				'post_title' => $slide['title'],
				'menu_order' => $order + 1,
			)
		);
		if ( ! $id ) {
			continue;
		}
		foreach ( $slide['meta'] as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
		update_post_meta( $id, '_kanayama_saved', '1' );
		if ( $with_images ) {
			$image_id = kanayama_starter_image( $slide['image'], $slide['title'] );
			if ( $image_id ) {
				set_post_thumbnail( $id, $image_id );
			}
		}
		/* translators: %s: スライド名 */
		$log[] = sprintf( __( 'FVスライド「%s」', 'kanayama' ), $slide['title'] );
	}

	/* --- 対応する工事 --- */
	kanayama_starter_terms( 'kn_service_cat', $data['service_terms'] );
	foreach ( $data['services'] as $order => $service ) {
		$id = kanayama_starter_post(
			array(
				'post_type'    => 'kn_service',
				'post_name'    => sanitize_title( $service['term'] . '-' . ( $order + 1 ) ),
				'post_title'   => $service['title'],
				'post_content' => $service['text'],
				'menu_order'   => $order + 1,
			)
		);
		if ( ! $id ) {
			continue;
		}
		wp_set_object_terms( $id, $service['term'], 'kn_service_cat' );
		update_post_meta( $id, '_kanayama_saved', '1' );
		if ( $with_images && ! empty( $service['image'] ) ) {
			$image_id = kanayama_starter_image( $service['image'], $service['title'] );
			if ( $image_id ) {
				set_post_thumbnail( $id, $image_id );
			}
		}
	}
	/* translators: %d: 件数 */
	$log[] = sprintf( __( '対応する工事 %d件', 'kanayama' ), count( $data['services'] ) );

	/* --- 特長・ステップ --- */
	kanayama_starter_terms( 'kn_feature_group', $data['feature_terms'] );
	foreach ( $data['features'] as $order => $feature ) {
		$id = kanayama_starter_post(
			array(
				'post_type'    => 'kn_feature',
				'post_name'    => sanitize_title( $feature['term'] . '-' . ( $order + 1 ) ),
				'post_title'   => $feature['title'],
				'post_content' => $feature['text'],
				'menu_order'   => $order + 1,
			)
		);
		if ( ! $id ) {
			continue;
		}
		wp_set_object_terms( $id, $feature['term'], 'kn_feature_group' );
		update_post_meta( $id, '_kn_feature_label', $feature['label'] );
		update_post_meta( $id, '_kanayama_saved', '1' );
	}
	/* translators: %d: 件数 */
	$log[] = sprintf( __( '特長・ステップ %d件', 'kanayama' ), count( $data['features'] ) );

	/* --- 工事実績 --- */
	kanayama_starter_terms( 'kn_work_cat', $data['work_terms'] );
	foreach ( $data['works'] as $work ) {
		$id = kanayama_starter_post(
			array(
				'post_type'    => 'kn_work',
				'post_name'    => $work['slug'],
				'post_title'   => $work['title'],
				'post_content' => isset( $work['content'] ) ? $work['content'] : '',
				'post_date'    => $work['date'],
			)
		);
		if ( ! $id ) {
			continue;
		}
		wp_set_object_terms( $id, $work['term'], 'kn_work_cat' );
		foreach ( $work['meta'] as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
		update_post_meta( $id, '_kanayama_saved', '1' );
		if ( $with_images && ! empty( $work['image'] ) ) {
			$image_id = kanayama_starter_image( $work['image'], $work['title'] );
			if ( $image_id ) {
				set_post_thumbnail( $id, $image_id );
			}
		}
	}
	/* translators: %d: 件数 */
	$log[] = sprintf( __( '工事実績 %d件', 'kanayama' ), count( $data['works'] ) );

	/* --- 社員紹介 --- */
	foreach ( $data['interviews'] as $order => $person ) {
		$id = kanayama_starter_post(
			array(
				'post_type'  => 'kn_interview',
				'post_name'  => $person['slug'],
				'post_title' => $person['title'],
				'menu_order' => $order + 1,
			)
		);
		if ( ! $id ) {
			continue;
		}
		foreach ( $person['meta'] as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
		update_post_meta( $id, '_kanayama_saved', '1' );
		if ( $with_images ) {
			$image_id = kanayama_starter_image( $person['image'], $person['title'] );
			if ( $image_id ) {
				set_post_thumbnail( $id, $image_id );
			}
			if ( ! empty( $person['tool'] ) ) {
				$tool_id = kanayama_starter_image( $person['tool'], $person['title'] . ' の仕事道具' );
				if ( $tool_id ) {
					update_post_meta( $id, '_kn_iv_tool_img', $tool_id );
				}
			}
		}
	}
	/* translators: %d: 件数 */
	$log[] = sprintf( __( '社員紹介 %d件', 'kanayama' ), count( $data['interviews'] ) );

	/* --- お知らせ --- */
	$category_ids = array();
	foreach ( $data['news_categories'] as $slug => $name ) {
		$term = term_exists( $slug, 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
		}
		if ( ! is_wp_error( $term ) ) {
			$category_ids[ $slug ] = (int) $term['term_id'];
		}
	}
	foreach ( $data['news'] as $news ) {
		$id = kanayama_starter_post(
			array(
				'post_type'    => 'post',
				'post_name'    => $news['slug'],
				'post_title'   => $news['title'],
				'post_content' => $news['content'],
				'post_date'    => $news['date'],
			)
		);
		if ( $id && isset( $category_ids[ $news['cat'] ] ) ) {
			wp_set_post_categories( $id, array( $category_ids[ $news['cat'] ] ) );
		}
	}
	/* translators: %d: 件数 */
	$log[] = sprintf( __( 'お知らせ %d件', 'kanayama' ), count( $data['news'] ) );

	/* --- メニュー --- */
	kanayama_starter_menus( $ids );
	$log[] = __( 'メニュー 4件', 'kanayama' );

	flush_rewrite_rules();

	return $log;
}

/**
 * メニューを作って表示位置に割り当てる。
 *
 * @param array $ids スラッグ => 固定ページID。
 */
function kanayama_starter_menus( $ids ) {
	$works_url = get_post_type_archive_link( 'kn_work' );
	$iv_url    = get_post_type_archive_link( 'kn_interview' );

	$page_item = static function ( $id, $label = '' ) {
		return array( 'type' => 'post_type', 'object' => 'page', 'object_id' => $id, 'title' => $label );
	};
	$url_item = static function ( $url, $label ) {
		return array( 'type' => 'custom', 'url' => $url, 'title' => $label );
	};

	$menus = array(
		'primary' => array(
			'name'  => __( 'グローバルナビ', 'kanayama' ),
			'items' => array(
				array( 'item' => $page_item( $ids['concept'] ?? 0 ), 'children' => array() ),
				array(
					'item'     => $page_item( $ids['solar'] ?? 0, __( '事業案内', 'kanayama' ) ),
					'children' => array(
						$page_item( $ids['solar'] ?? 0 ),
						$page_item( $ids['electric'] ?? 0 ),
					),
				),
				array( 'item' => $url_item( $works_url, __( '工事実績', 'kanayama' ) ), 'children' => array() ),
				array( 'item' => $page_item( $ids['about'] ?? 0 ), 'children' => array() ),
				array(
					'item'     => $page_item( $ids['recruit'] ?? 0 ),
					'children' => array(
						$page_item( $ids['recruit'] ?? 0 ),
						$page_item( $ids['message'] ?? 0 ),
						$url_item( $iv_url, __( '社員紹介', 'kanayama' ) ),
					),
				),
				array( 'item' => $page_item( $ids['news'] ?? 0 ), 'children' => array() ),
			),
		),
		'footer_business' => array(
			'name'  => __( 'フッター：事業', 'kanayama' ),
			'items' => array(
				array( 'item' => $page_item( $ids['solar'] ?? 0 ), 'children' => array() ),
				array( 'item' => $page_item( $ids['electric'] ?? 0 ), 'children' => array() ),
				array( 'item' => $url_item( $works_url, __( '工事実績', 'kanayama' ) ), 'children' => array() ),
			),
		),
		'footer_company' => array(
			'name'  => __( 'フッター：会社', 'kanayama' ),
			'items' => array(
				array( 'item' => $page_item( $ids['concept'] ?? 0 ), 'children' => array() ),
				array( 'item' => $page_item( $ids['about'] ?? 0 ), 'children' => array() ),
				array( 'item' => $page_item( $ids['message'] ?? 0 ), 'children' => array() ),
				array( 'item' => $page_item( $ids['news'] ?? 0 ), 'children' => array() ),
			),
		),
		'footer_recruit' => array(
			'name'  => __( 'フッター：採用', 'kanayama' ),
			'items' => array(
				array( 'item' => $page_item( $ids['recruit'] ?? 0 ), 'children' => array() ),
				array( 'item' => $url_item( $iv_url, __( '社員紹介', 'kanayama' ) ), 'children' => array() ),
				array( 'item' => $page_item( $ids['contact'] ?? 0 ), 'children' => array() ),
			),
		),
	);

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	foreach ( $menus as $location => $menu ) {
		$existing = wp_get_nav_menu_object( $menu['name'] );
		if ( $existing ) {
			$locations[ $location ] = (int) $existing->term_id;
			continue;
		}

		$menu_id = wp_create_nav_menu( $menu['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}

		foreach ( $menu['items'] as $entry ) {
			$parent_id = kanayama_starter_menu_item( $menu_id, $entry['item'], 0 );
			foreach ( $entry['children'] as $child ) {
				kanayama_starter_menu_item( $menu_id, $child, $parent_id );
			}
		}

		$locations[ $location ] = (int) $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * メニュー項目を1件つくる。
 *
 * @param int   $menu_id   メニューID。
 * @param array $item      項目の定義。
 * @param int   $parent_id 親項目のID。
 * @return int 作成した項目のID。
 */
function kanayama_starter_menu_item( $menu_id, $item, $parent_id = 0 ) {
	if ( 'post_type' === $item['type'] && empty( $item['object_id'] ) ) {
		return 0;
	}
	if ( 'custom' === $item['type'] && empty( $item['url'] ) ) {
		return 0;
	}

	$args = array(
		'menu-item-type'      => $item['type'],
		'menu-item-title'     => $item['title'],
		'menu-item-parent-id' => $parent_id,
		'menu-item-status'    => 'publish',
	);
	if ( 'post_type' === $item['type'] ) {
		$args['menu-item-object']    = $item['object'];
		$args['menu-item-object-id'] = $item['object_id'];
	} else {
		$args['menu-item-url'] = $item['url'];
	}

	$id = wp_update_nav_menu_item( $menu_id, 0, $args );
	return is_wp_error( $id ) ? 0 : (int) $id;
}
