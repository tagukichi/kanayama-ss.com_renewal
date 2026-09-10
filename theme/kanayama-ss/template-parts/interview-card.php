<?php
/**
 * 社員紹介カード1枚。
 * $args['open'] が true のときはアコーディオンを開いた状態で出す（詳細ページ用）。
 *
 * @package Kanayama
 */

$kanayama_open  = ! empty( $args['open'] );
$kanayama_id    = get_the_ID();
$kanayama_style = get_post_meta( $kanayama_id, '_kn_iv_style', true );
$kanayama_role  = get_post_meta( $kanayama_id, '_kn_iv_role', true );
$kanayama_year  = get_post_meta( $kanayama_id, '_kn_iv_year', true );
$kanayama_no    = get_post_meta( $kanayama_id, '_kn_iv_number', true );
$kanayama_kind  = get_post_meta( $kanayama_id, '_kn_iv_kind', true );
$kanayama_is_pet = 'dog' === $kanayama_style;
?>
    <article class="iv reveal<?php echo $kanayama_is_pet ? ' iv--dog' : ''; ?>" id="interview-<?php echo esc_attr( $kanayama_id ); ?>">
      <div class="iv__fig">
	<?php
	kanayama_media(
		get_post_thumbnail_id( $kanayama_id ),
		'ph--1x1',
		array(
			'size'        => 'kanayama-square',
			/* translators: %s: 名前 */
			'placeholder' => sprintf( __( '［写真］%s ／ 900×900px', 'kanayama' ), get_the_title() ),
			'alt'         => get_the_title(),
		)
	);
	?>
      </div>
      <div class="iv__body">
        <div class="iv__head">
	<?php if ( $kanayama_role ) : ?>
          <span class="iv__role"><?php echo esc_html( $kanayama_role ); ?></span>
	<?php endif; ?>
	<?php if ( $kanayama_no ) : ?>
          <span class="iv__no"><i><?php esc_html_e( 'Interview', 'kanayama' ); ?></i><b><?php echo esc_html( $kanayama_no ); ?></b></span>
	<?php endif; ?>
        </div>

	<?php if ( $kanayama_kind ) : ?>
        <p class="iv__kind"><?php echo esc_html( $kanayama_kind ); ?></p>
	<?php endif; ?>

        <h2 class="iv__name"><?php the_title(); ?>
	<?php if ( $kanayama_year ) : ?>
          <small><?php echo esc_html( $kanayama_year ); ?></small>
	<?php endif; ?>
        </h2>

	<?php
	if ( $kanayama_is_pet ) :
		$kanayama_free = get_post_meta( $kanayama_id, '_kn_iv_free', true );
		if ( $kanayama_free ) :
			?>
        <p class="p"><?php echo wp_kses( nl2br( esc_html( $kanayama_free ) ), array( 'br' => array() ) ); ?></p>
			<?php
		endif;
	else :
		$kanayama_lead_q = get_post_meta( $kanayama_id, '_kn_iv_lead_q', true );
		$kanayama_lead_a = get_post_meta( $kanayama_id, '_kn_iv_lead_a', true );
		if ( $kanayama_lead_q || $kanayama_lead_a ) :
			?>
        <dl class="qa">
          <dt><?php echo esc_html( $kanayama_lead_q ); ?></dt>
          <dd><?php echo wp_kses( nl2br( esc_html( $kanayama_lead_a ) ), array( 'br' => array() ) ); ?></dd>
        </dl>
			<?php
		endif;

		$kanayama_qa       = kanayama_qa_html( get_post_meta( $kanayama_id, '_kn_iv_qa', true ) );
		$kanayama_sched    = kanayama_schedule_html( get_post_meta( $kanayama_id, '_kn_iv_sched', true ) );
		$kanayama_note     = get_post_meta( $kanayama_id, '_kn_iv_sched_note', true );
		$kanayama_tool_txt = get_post_meta( $kanayama_id, '_kn_iv_tool_text', true );
		$kanayama_tool_img = (int) get_post_meta( $kanayama_id, '_kn_iv_tool_img', true );
		$kanayama_has_tool = $kanayama_tool_txt || $kanayama_tool_img;

		if ( $kanayama_qa || $kanayama_sched || $kanayama_has_tool ) :
			?>
        <details class="more"<?php echo $kanayama_open ? ' open' : ''; ?>>
          <summary><span class="o"><?php esc_html_e( '続きを読む', 'kanayama' ); ?></span><span class="c"><?php esc_html_e( '閉じる', 'kanayama' ); ?></span></summary>
          <div class="more__in">
			<?php echo wp_kses_post( $kanayama_qa ); ?>
			<?php if ( $kanayama_sched || $kanayama_has_tool ) : ?>
            <div class="cards">
				<?php if ( $kanayama_sched ) : ?>
              <div class="card">
                <h3 class="card__t"><?php esc_html_e( '1日のスケジュール', 'kanayama' ); ?></h3>
					<?php echo wp_kses_post( $kanayama_sched ); ?>
					<?php if ( $kanayama_note ) : ?>
                <p class="card__note"><?php echo esc_html( $kanayama_note ); ?></p>
					<?php endif; ?>
              </div>
				<?php endif; ?>
				<?php if ( $kanayama_has_tool ) : ?>
              <div class="card card--tool">
                <h3 class="card__t"><?php esc_html_e( '私の仕事道具', 'kanayama' ); ?></h3>
					<?php if ( $kanayama_tool_txt ) : ?>
                <p class="card__d"><?php echo wp_kses( nl2br( esc_html( $kanayama_tool_txt ) ), array( 'br' => array() ) ); ?></p>
					<?php endif; ?>
					<?php if ( $kanayama_tool_img ) : ?>
						<?php kanayama_media( $kanayama_tool_img, 'ph--3x2', array( 'alt' => __( '仕事道具', 'kanayama' ) ) ); ?>
					<?php endif; ?>
              </div>
				<?php endif; ?>
            </div>
			<?php endif; ?>
          </div>
        </details>
			<?php
		endif;
	endif;
	?>
      </div>
    </article>
