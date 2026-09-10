<?php
/**
 * 固定ページの既定レイアウト。
 * プライバシーポリシーのように、本文がそのまま読み物になるページで使う。
 *
 * @package Kanayama
 */

get_header();

while ( have_posts() ) :
	the_post();
	kanayama_page_head_for( get_post() );
	get_template_part( 'template-parts/page-hero' );
	?>

<section class="sec">
  <div class="wrap">
    <div class="article reveal">
	<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="pager">',
				'after'  => '</nav>',
			)
		);
	?>
    </div>
  </div>
</section>

	<?php
endwhile;

get_footer();
