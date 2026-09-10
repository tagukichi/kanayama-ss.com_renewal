<?php
/**
 * テンプレートから呼ぶ表示用の関数。
 *
 * @package Kanayama
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -----------------------------------------------------------------
   設定値の取り出し
   ----------------------------------------------------------------- */

/**
 * カスタマイザーの値を取り出す。未設定なら定義側の既定値を返す。
 *
 * @param string $key 設定キー。
 * @return string
 */
function kanayama_opt( $key ) {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = array();
		foreach ( kanayama_settings_schema() as $section ) {
			foreach ( $section['fields'] as $field_key => $field ) {
				$defaults[ $field_key ] = $field['default'];
			}
		}
	}
	$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value   = get_theme_mod( $key, $default );
	return '' === $value ? $default : $value;
}

/**
 * 「|」を改行に、「*〜*」を強調に変換する。入力はエスケープしてから組み立てる。
 *
 * @param string $text 元の文字列。
 * @return string HTML。
 */
function kanayama_rich( $text ) {
	$html = esc_html( $text );
	$html = str_replace( '|', '<br>', $html );
	$html = preg_replace( '/\*([^*]+)\*/u', '<em>$1</em>', $html );
	return $html;
}

/**
 * ファーストビューのキャッチコピー。1行ずつ <span class="l"> で包む。
 * CSSがこの単位で順に出す動きを付けているため、行を要素に分けておく必要がある。
 *
 * @param string $text 「|」で改行、「*〜*」で強調を指定した文字列。
 * @return string HTML。
 */
function kanayama_fv_title( $text ) {
	$lines = explode( '|', (string) $text );
	$html  = '';
	foreach ( $lines as $line ) {
		$line = preg_replace( '/\*([^*]+)\*/u', '<em>$1</em>', esc_html( $line ) );
		$html .= '<span class="l">' . $line . '</span>';
	}
	return $html;
}

/**
 * 「|」だけを改行に変換する。
 *
 * @param string $text 元の文字列。
 * @return string HTML。
 */
function kanayama_br( $text ) {
	return str_replace( '|', '<br>', esc_html( $text ) );
}

/**
 * お問い合わせページのURL。指定が無ければスラッグ contact のページを探す。
 *
 * @return string
 */
function kanayama_contact_url() {
	$id = (int) get_theme_mod( 'contact_page', 0 );
	if ( ! $id ) {
		$page = get_page_by_path( 'contact' );
		$id   = $page ? $page->ID : 0;
	}
	return $id ? get_permalink( $id ) : home_url( '/' );
}

/**
 * 電話番号を tel: リンク用の文字列にする。
 *
 * @param string $tel 電話番号。
 * @return string
 */
function kanayama_tel_href( $tel = '' ) {
	$tel = $tel ? $tel : kanayama_opt( 'company_tel' );
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $tel );
}

/* -----------------------------------------------------------------
   画像
   ----------------------------------------------------------------- */

/**
 * 画像を出力する。未設定のときはプロトタイプと同じプレースホルダーを出す。
 *
 * @param int    $attachment_id 添付ファイルID。0なら枠だけ。
 * @param string $shape         ph--4x3 のような形の指定。複数可（スペース区切り）。
 * @param array  $args          size / alt / placeholder / eager / pos。
 */
function kanayama_media( $attachment_id, $shape = 'ph--4x3', $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'size'        => 'kanayama-card',
			'alt'         => null,
			'placeholder' => __( '［写真］未設定', 'kanayama' ),
			'eager'       => false,
			'pos'         => '',
		)
	);

	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id || ! wp_get_attachment_image_src( $attachment_id, $args['size'] ) ) {
		printf(
			'<div class="ph %s" data-ph="%s"></div>',
			esc_attr( $shape ),
			esc_attr( $args['placeholder'] )
		);
		return;
	}

	// ph--4x3 → media--4x3。ph--dark は色調整用なのでそのまま持たせない。
	$modifiers = array( 'media' );
	foreach ( preg_split( '/\s+/', trim( $shape ) ) as $class ) {
		if ( 0 === strpos( $class, 'ph--' ) && 'ph--dark' !== $class ) {
			$modifiers[] = 'media--' . substr( $class, 4 );
		}
	}

	$attr = array(
		'class'    => implode( ' ', $modifiers ),
		'decoding' => 'async',
		'loading'  => $args['eager'] ? 'eager' : 'lazy',
	);
	if ( $args['eager'] ) {
		$attr['fetchpriority'] = 'high';
	}
	if ( $args['pos'] ) {
		$attr['style'] = 'object-position:' . $args['pos'];
	}
	if ( null !== $args['alt'] ) {
		$attr['alt'] = $args['alt'];
	}

	echo wp_get_attachment_image( $attachment_id, $args['size'], false, $attr );
}

/* -----------------------------------------------------------------
   ページヘッダーとパンくず
   ----------------------------------------------------------------- */

/**
 * 下層ページ共通のページヘッダー。
 *
 * @param array $args en / title / image_id / style（photo|light）/ crumbs。
 */
function kanayama_page_head( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'en'       => '',
			'title'    => '',
			'image_id' => 0,
			'style'    => 'photo',
			'crumbs'   => array(),
		)
	);

	if ( 'light' === $args['style'] ) : ?>
<section class="phd phd--light">
  <span class="phd__diag" aria-hidden="true"></span>
  <div class="wrap phd__in">
    <h1 class="phd__en"><?php echo esc_html( $args['en'] ); ?></h1>
    <p class="phd__ja"><?php echo esc_html( $args['title'] ); ?></p>
  </div>
</section>
	<?php else :
		$image_id = $args['image_id'] ? $args['image_id'] : (int) get_theme_mod( 'default_header_image', 0 );
		?>
<section class="phd">
  <div class="phd__media">
		<?php
		if ( $image_id ) {
			kanayama_media(
				$image_id,
				'ph--fill ph--dark',
				array( 'size' => 'kanayama-wide', 'alt' => '', 'eager' => true, 'pos' => '50% 35%' )
			);
		} else {
			printf(
				'<img class="media media--fill" src="%s" alt="" fetchpriority="high" decoding="async" style="object-position:50%% 35%%">',
				esc_url( get_template_directory_uri() . '/assets/img/default-header.jpg' )
			);
		}
		?>
  </div>
  <div class="phd__veil"></div>
  <div class="wrap phd__in">
    <h1 class="phd__en"><?php echo esc_html( $args['en'] ); ?></h1>
    <p class="phd__ja"><?php echo esc_html( $args['title'] ); ?></p>
  </div>
</section>
	<?php endif;

	kanayama_breadcrumb( $args['crumbs'], $args['title'] );
}

/**
 * パンくずリスト。
 *
 * @param array  $crumbs  中間の階層。array( array( 'label' => '', 'url' => '' ) )。
 * @param string $current 現在地の表示名。
 */
function kanayama_breadcrumb( $crumbs, $current ) {
	?>
<nav class="bc" aria-label="<?php esc_attr_e( 'パンくずリスト', 'kanayama' ); ?>">
  <div class="wrap">
    <ol>
      <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'ホーム', 'kanayama' ); ?></a></li>
	<?php foreach ( $crumbs as $crumb ) : ?>
      <li><a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['label'] ); ?></a></li>
	<?php endforeach; ?>
      <li aria-current="page"><?php echo esc_html( $current ); ?></li>
    </ol>
  </div>
</nav>
	<?php
}

/**
 * 固定ページ用に、ページヘッダーの設定をまとめて取り出してから描画する。
 *
 * @param WP_Post $post 対象のページ。
 */
function kanayama_page_head_for( $post ) {
	$crumbs = array();
	$parent = $post->post_parent;
	if ( $parent ) {
		$crumbs[] = array( 'label' => get_the_title( $parent ), 'url' => get_permalink( $parent ) );
	}

	// ヘッダーの写真は専用欄が最優先。無ければアイキャッチ、それも無ければ既定画像。
	$header_id = (int) get_post_meta( $post->ID, '_kn_page_header_img', true );
	if ( ! $header_id ) {
		$header_id = (int) get_post_thumbnail_id( $post );
	}

	kanayama_page_head(
		array(
			'en'       => get_post_meta( $post->ID, '_kn_page_en', true ),
			'title'    => get_the_title( $post ),
			'image_id' => $header_id,
			'style'    => get_post_meta( $post->ID, '_kn_page_head', true ) === 'light' ? 'light' : 'photo',
			'crumbs'   => $crumbs,
		)
	);
}

/* -----------------------------------------------------------------
   ファーストビューのスライダー
   ----------------------------------------------------------------- */

/**
 * トップのスライドを出力する。DOMはプロトタイプと同じなので app.js がそのまま動く。
 */
function kanayama_fv_slider() {
	$slides = get_posts(
		array(
			'post_type'      => 'kn_slide',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);

	$interval = (int) kanayama_opt( 'fv_interval' );
	printf(
		'<div class="fv__media" data-slider data-interval="%d" role="group" aria-roledescription="%s" aria-label="%s">',
		$interval > 0 ? $interval : 6000,
		esc_attr__( 'カルーセル', 'kanayama' ),
		esc_attr__( 'メインビジュアル', 'kanayama' )
	);

	if ( ! $slides ) {
		// スライドが1枚も無いときは、テーマ同梱の画像を1枚だけ出す。
		printf(
			'<div class="fv__slide" data-overlay="true" data-active aria-hidden="false">'
			. '<img class="fv__photo" src="%s" alt="" fetchpriority="high" decoding="async" style="object-position:8%% 50%%">'
			. '<span class="fv__wash" aria-hidden="true"></span><span class="fv__flare" aria-hidden="true"></span></div>',
			esc_url( get_template_directory_uri() . '/assets/img/default-fv.jpg' )
		);
		echo '</div>';
		return;
	}

	foreach ( $slides as $index => $slide ) {
		// 未保存（空）なら重ねる。チェックを外して保存すると '0' が入る。
		$overlay = '0' !== get_post_meta( $slide->ID, '_kn_slide_overlay', true );
		$fit     = get_post_meta( $slide->ID, '_kn_slide_fit', true );
		$bg      = get_post_meta( $slide->ID, '_kn_slide_bg', true );
		$pos     = get_post_meta( $slide->ID, '_kn_slide_pos', true );

		$style = array();
		if ( $bg ) {
			$style[] = 'background:' . $bg;
		}
		if ( $fit ) {
			$style[] = '--fit:' . $fit;
		}

		printf(
			'<div class="fv__slide" data-overlay="%s"%s%s aria-hidden="%s">',
			$overlay ? 'true' : 'false',
			$style ? ' style="' . esc_attr( implode( ';', $style ) ) . '"' : '',
			0 === $index ? ' data-active' : '',
			0 === $index ? 'false' : 'true'
		);

		$image_id = get_post_thumbnail_id( $slide );
		if ( $image_id ) {
			$attr = array(
				'class'    => 'fv__photo',
				'decoding' => 'async',
				'style'    => 'object-position:' . ( $pos ? $pos : '50% 50%' ),
			);
			if ( 0 === $index ) {
				$attr['loading']       = 'eager';
				$attr['fetchpriority'] = 'high';
			} else {
				$attr['loading'] = 'lazy';
			}
			// バナーなど絵だけを見せるスライドは装飾扱い。
			if ( ! $overlay && ! get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) {
				$attr['alt'] = get_the_title( $slide );
			}
			echo wp_get_attachment_image( $image_id, 'full', false, $attr );
		} else {
			printf(
				'<div class="fv__ph"><span>%s<br><small>%s</small></span></div>',
				esc_html( get_the_title( $slide ) ),
				esc_html__( 'アイキャッチ画像を設定すると表示されます', 'kanayama' )
			);
		}

		echo '<span class="fv__wash" aria-hidden="true"></span><span class="fv__flare" aria-hidden="true"></span></div>';
	}

	// 何枚目かを示すインジケーター。スライダーの内側・下端中央。
	if ( count( $slides ) > 1 ) {
		printf( '<div class="fv__pager"><div class="fv__pins" role="tablist" aria-label="%s">', esc_attr__( 'スライドの選択', 'kanayama' ) );
		foreach ( $slides as $index => $slide ) {
			printf(
				'<button type="button" class="fv__pin" role="tab" data-go="%d" aria-label="%s"%s></button>',
				$index,
				/* translators: %d: スライドの番号 */
				esc_attr( sprintf( __( '%d枚目を表示', 'kanayama' ), $index + 1 ) ),
				0 === $index ? ' aria-current="true"' : ''
			);
		}
		echo '</div></div>';
	}

	echo '</div>';
}

/* -----------------------------------------------------------------
   一覧まわり
   ----------------------------------------------------------------- */

/**
 * 工事実績のカード1枚。
 *
 * @param WP_Post $post 工事実績。
 */
function kanayama_work_card( $post ) {
	$terms = get_the_terms( $post, 'kn_work_cat' );
	$cat   = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
	$area  = get_post_meta( $post->ID, '_kn_work_area', true );
	?>
      <a class="work" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
        <div class="work__fig">
	<?php if ( $cat ) : ?>
          <span class="work__cat"><?php echo esc_html( $cat ); ?></span>
	<?php endif; ?>
	<?php
		kanayama_media(
			get_post_thumbnail_id( $post ),
			'ph--4x3',
			array( 'placeholder' => __( '［写真］施工事例サムネイル ／ 1200×900px', 'kanayama' ) )
		);
	?>
        </div>
        <h3 class="work__ttl"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
        <p class="work__meta">
          <span class="en"><?php echo esc_html( get_the_date( 'Y.m', $post ) ); ?></span>
	<?php if ( $area ) : ?>
          <span><?php echo esc_html( $area ); ?></span>
	<?php endif; ?>
        </p>
      </a>
	<?php
}

/**
 * お知らせの一覧。
 *
 * @param int $limit 件数。-1 で全件。
 */
function kanayama_news_list( $limit = 5 ) {
	$posts = get_posts(
		array(
			'posts_per_page' => $limit,
			'no_found_rows'  => true,
		)
	);
	if ( ! $posts ) {
		printf( '<p class="empty">%s</p>', esc_html__( 'お知らせはまだありません。', 'kanayama' ) );
		return;
	}
	echo '<div class="news">';
	foreach ( $posts as $post ) {
		$cats = get_the_category( $post->ID );
		?>
          <a class="news__item" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
            <span class="news__date"><?php echo esc_html( get_the_date( 'Y.m.d', $post ) ); ?></span>
		<?php if ( $cats ) : ?>
            <span class="news__cat"><?php echo esc_html( $cats[0]->name ); ?></span>
		<?php endif; ?>
            <span class="news__ttl"><?php echo esc_html( get_the_title( $post ) ); ?></span>
          </a>
		<?php
	}
	echo '</div>';
}

/**
 * 「選ばれる理由」などの箇条カードを出す。
 *
 * @param string $group タクソノミー kn_feature_group のスラッグ。
 * @param string $class ラップ要素に足すクラス。
 * @param string $style ラップ要素のインラインスタイル。
 */
function kanayama_features( $group, $class = '', $style = '' ) {
	$items = get_posts(
		array(
			'post_type'      => 'kn_feature',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'kn_feature_group',
					'field'    => 'slug',
					'terms'    => $group,
				),
			),
		)
	);
	if ( ! $items ) {
		return;
	}

	printf(
		'<div class="pts%s"%s>',
		$class ? ' ' . esc_attr( $class ) : '',
		$style ? ' style="' . esc_attr( $style ) . '"' : ''
	);
	foreach ( $items as $item ) {
		$label = get_post_meta( $item->ID, '_kn_feature_label', true );
		?>
      <div class="pt">
		<?php if ( $label ) : ?>
        <p class="pt__n"><?php echo esc_html( $label ); ?></p>
		<?php endif; ?>
        <h3 class="pt__t"><?php echo wp_kses( kanayama_br( get_the_title( $item ) ), array( 'br' => array() ) ); ?></h3>
        <p class="pt__d"><?php echo esc_html( wp_strip_all_tags( $item->post_content ) ); ?></p>
      </div>
		<?php
	}
	echo '</div>';
}

/**
 * 「対応する工事」のカードを出す。
 *
 * @param string $category タクソノミー kn_service_cat のスラッグ。
 */
function kanayama_services( $category ) {
	$items = get_posts(
		array(
			'post_type'      => 'kn_service',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'kn_service_cat',
					'field'    => 'slug',
					'terms'    => $category,
				),
			),
		)
	);
	if ( ! $items ) {
		return;
	}

	echo '<div class="works mt-l reveal">';
	foreach ( $items as $index => $item ) {
		?>
      <div class="work">
        <div class="work__fig">
          <span class="work__cat"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
		<?php
			kanayama_media(
				get_post_thumbnail_id( $item ),
				'ph--4x3',
				array(
					/* translators: %s: 工事の名前 */
					'placeholder' => sprintf( __( '［写真］%s ／ 1200×900px', 'kanayama' ), get_the_title( $item ) ),
				)
			);
		?>
        </div>
        <h3 class="work__ttl"><?php echo esc_html( get_the_title( $item ) ); ?></h3>
        <p class="pt__d"><?php echo esc_html( wp_strip_all_tags( $item->post_content ) ); ?></p>
      </div>
		<?php
	}
	echo '</div>';
}

/* -----------------------------------------------------------------
   社員紹介
   ----------------------------------------------------------------- */

/**
 * 「Q. 質問」「A. 回答」の書式を <dl class="qa"> に変換する。
 *
 * @param string $text 入力テキスト。
 * @return string HTML。
 */
function kanayama_qa_html( $text ) {
	$text = trim( (string) $text );
	if ( '' === $text ) {
		return '';
	}

	$pairs = array();
	$index = -1;
	foreach ( preg_split( '/\R/u', $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		// 「Q.」「Q：」のように、印のうしろに区切り記号がある行だけを質問とみなす。
		if ( preg_match( '/^[QqＱ][.．:：][\s　]*(.*)$/u', $line, $m ) ) {
			$pairs[] = array( 'q' => $m[1], 'a' => array() );
			$index   = count( $pairs ) - 1;
			continue;
		}
		if ( $index < 0 ) {
			continue;
		}
		if ( preg_match( '/^[AaＡ][.．:：][\s　]*(.*)$/u', $line, $m ) ) {
			$pairs[ $index ]['a'][] = $m[1];
			continue;
		}
		// 印が無い行は直前の回答の続き。
		$pairs[ $index ]['a'][] = $line;
	}

	if ( ! $pairs ) {
		return '';
	}

	$html = '<dl class="qa">';
	foreach ( $pairs as $pair ) {
		$html .= '<dt>' . esc_html( $pair['q'] ) . '</dt>';
		$html .= '<dd>' . implode( '<br>', array_map( 'esc_html', $pair['a'] ) ) . '</dd>';
	}
	return $html . '</dl>';
}

/**
 * 1日のスケジュール。時刻付きの行が並んでいれば箇条書き、そうでなければ文章として出す。
 *
 * @param string $text 入力テキスト。
 * @return string HTML。
 */
function kanayama_schedule_html( $text ) {
	$text = trim( (string) $text );
	if ( '' === $text ) {
		return '';
	}

	$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', $text ) ), 'strlen' ) );
	$rows  = array();
	foreach ( $lines as $line ) {
		// 「6:30 出社」「15:00–17:00 現場終了」「16:00頃　現場退出」など。
		if ( preg_match( '/^([0-9]{1,2}[:：][0-9]{2}(?:\s*[–—\-〜~]\s*[0-9]{1,2}[:：][0-9]{2})?[^\s　]*)[\s　]+(.+)$/u', $line, $m ) ) {
			$rows[] = array( $m[1], $m[2] );
			continue;
		}
		$rows = array();
		break;
	}

	if ( $rows ) {
		$html = '<ul class="sched">';
		foreach ( $rows as $row ) {
			$html .= '<li><span class="en">' . esc_html( $row[0] ) . '</span>' . esc_html( $row[1] ) . '</li>';
		}
		return $html . '</ul>';
	}

	return '<p class="card__d">' . implode( '<br>', array_map( 'esc_html', $lines ) ) . '</p>';
}
