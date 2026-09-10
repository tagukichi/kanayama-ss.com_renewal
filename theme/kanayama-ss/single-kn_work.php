<?php
/**
 * 工事実績の詳細。
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();

	$kanayama_area  = get_post_meta( get_the_ID(), '_kn_work_area', true );
	$kanayama_scope = get_post_meta( get_the_ID(), '_kn_work_scope', true );
	$kanayama_roof  = get_post_meta( get_the_ID(), '_kn_work_roof', true );
	$kanayama_term  = get_post_meta( get_the_ID(), '_kn_work_term', true );

	kanayama_page_head(
		array(
			'en'       => 'Works',
			'title'    => get_the_title(),
			'image_id' => get_post_thumbnail_id(),
			'crumbs'   => array(
				array( 'label' => __( '工事実績', 'kanayama' ), 'url' => get_post_type_archive_link( 'kn_work' ) ),
			),
		)
	);
	?>

<section class="sec">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Data</p>
        <h2 class="h-sec" style="font-size:var(--fs-h3)"><?php esc_html_e( '工事概要', 'kanayama' ); ?></h2>
        <table class="tbl tbl--compact mt-m">
	<?php if ( $kanayama_area ) : ?>
          <tr><th><?php esc_html_e( '施工エリア', 'kanayama' ); ?></th><td><?php echo esc_html( $kanayama_area ); ?></td></tr>
	<?php endif; ?>
	<?php if ( $kanayama_scope ) : ?>
          <tr><th><?php esc_html_e( '工事内容', 'kanayama' ); ?></th><td><?php echo esc_html( $kanayama_scope ); ?></td></tr>
	<?php endif; ?>
	<?php if ( $kanayama_roof ) : ?>
          <tr><th><?php esc_html_e( '屋根材', 'kanayama' ); ?></th><td><?php echo esc_html( $kanayama_roof ); ?></td></tr>
	<?php endif; ?>
	<?php if ( $kanayama_term ) : ?>
          <tr><th><?php esc_html_e( '工期', 'kanayama' ); ?></th><td><?php echo esc_html( $kanayama_term ); ?></td></tr>
	<?php endif; ?>
          <tr><th><?php esc_html_e( '施工時期', 'kanayama' ); ?></th><td><?php echo esc_html( get_the_date( 'Y年n月' ) ); ?></td></tr>
        </table>
      </div>
      <div class="article"><?php the_content(); ?></div>
    </div>

	<?php
	// 施工写真。設定されているものだけを並べる。
	$kanayama_photos = array();
	for ( $kanayama_i = 1; $kanayama_i <= 3; $kanayama_i++ ) {
		$kanayama_id = (int) get_post_meta( get_the_ID(), '_kn_work_photo_' . $kanayama_i, true );
		if ( $kanayama_id ) {
			$kanayama_photos[] = array(
				'id'      => $kanayama_id,
				'caption' => get_post_meta( get_the_ID(), '_kn_work_caption_' . $kanayama_i, true ),
			);
		}
	}
	if ( $kanayama_photos ) :
		?>
    <div class="works mt-l reveal">
		<?php foreach ( $kanayama_photos as $kanayama_photo ) : ?>
      <div class="work">
			<?php kanayama_media( $kanayama_photo['id'], 'ph--4x3', array( 'alt' => $kanayama_photo['caption'] ) ); ?>
			<?php if ( $kanayama_photo['caption'] ) : ?>
        <h3 class="work__ttl"><?php echo esc_html( $kanayama_photo['caption'] ); ?></h3>
			<?php endif; ?>
      </div>
		<?php endforeach; ?>
    </div>
	<?php endif; ?>

    <p class="mt-l center"><a class="btn btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'kn_work' ) ); ?>"><?php esc_html_e( '工事実績一覧へ戻る', 'kanayama' ); ?></a></p>
  </div>
</section>

	<?php
endwhile;

get_footer();
