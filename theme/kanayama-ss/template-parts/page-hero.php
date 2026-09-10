<?php
/**
 * パンくずの下に置く大きな画像。設定されているページだけに出る。
 *
 * @package Kanayama
 */

$kanayama_hero = (int) get_post_meta( get_the_ID(), '_kn_page_hero', true );
if ( ! $kanayama_hero ) {
	return;
}
?>
<div class="wrap">
  <div class="pageHero reveal">
	<?php
	kanayama_media(
		$kanayama_hero,
		'ph--16x9 ph--top',
		array( 'size' => 'kanayama-wide', 'alt' => '', 'eager' => true )
	);
	?>
  </div>
</div>
