<?php
/**
 * 検索フォーム。
 *
 * @package Kanayama
 */

?>
<form class="form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <div class="field">
    <label class="screen-reader-text" for="s"><?php esc_html_e( 'サイト内を検索', 'kanayama' ); ?></label>
    <input type="search" id="s" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'サイト内を検索', 'kanayama' ); ?>">
  </div>
  <button class="btn btn--ghost" type="submit"><?php esc_html_e( '検索', 'kanayama' ); ?></button>
</form>
