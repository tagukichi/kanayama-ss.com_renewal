<?php
/**
 * フッター。お問い合わせ帯とフッター本体。
 *
 * @package Kanayama
 */

?>
</main>

<section class="cta">
  <div class="wrap cta__in">
    <div>
      <h2 class="cta__ttl"><span class="en"><?php esc_html_e( 'Contact', 'kanayama' ); ?></span><?php echo wp_kses( kanayama_br( kanayama_opt( 'cta_title' ) ), array( 'br' => array() ) ); ?></h2>
      <p class="cta__d"><?php echo esc_html( kanayama_opt( 'cta_text' ) ); ?></p>
    </div>
    <div class="cta__acts">
      <a class="cta__tel" href="<?php echo esc_url( kanayama_tel_href() ); ?>">
        <small><?php esc_html_e( 'お電話でのご相談', 'kanayama' ); ?></small>
        <b><?php echo esc_html( kanayama_opt( 'company_tel' ) ); ?></b>
        <span><?php echo esc_html( kanayama_opt( 'company_hours' ) ); ?></span>
      </a>
      <a class="btn btn--onDark" href="<?php echo esc_url( kanayama_contact_url() ); ?>"><?php esc_html_e( 'フォームでお問い合わせ', 'kanayama' ); ?></a>
    </div>
  </div>
</section>

<footer class="ft">
  <div class="wrap">
    <div class="ft__top">
      <div>
        <div class="logo">
          <?php get_template_part( 'template-parts/logo-mark' ); ?>
          <span class="logo__txt">
            <span class="logo__biz"><?php echo esc_html( kanayama_opt( 'company_biz' ) ); ?></span>
            <span class="logo__ja"><?php echo esc_html( kanayama_opt( 'company_name' ) ); ?></span>
          </span>
        </div>
        <p class="ft__reg"><?php echo esc_html( kanayama_opt( 'company_reg' ) ); ?></p>
        <address class="ft__addr">
          <?php echo esc_html( trim( kanayama_opt( 'company_zip' ) . ' ' . kanayama_opt( 'company_addr' ) ) ); ?><br>
          <?php
          printf(
              /* translators: 1: 電話番号, 2: FAX番号 */
              esc_html__( 'TEL %1$s ／ FAX %2$s', 'kanayama' ),
              esc_html( kanayama_opt( 'company_tel' ) ),
              esc_html( kanayama_opt( 'company_fax' ) )
          );
          ?><br>
          <?php echo esc_html( sprintf( '営業時間 %s', kanayama_opt( 'company_hours' ) ) ); ?>
        </address>
      </div>
      <nav class="ft__nav" aria-label="<?php esc_attr_e( 'フッターナビゲーション', 'kanayama' ); ?>">
        <?php
        $kanayama_footer_columns = array(
            'footer_business' => 'Business',
            'footer_company'  => 'Company',
            'footer_recruit'  => 'Recruit',
        );
        foreach ( $kanayama_footer_columns as $kanayama_location => $kanayama_heading ) :
            if ( ! has_nav_menu( $kanayama_location ) ) {
                continue;
            }
            ?>
        <div>
          <p class="ft__h"><?php echo esc_html( $kanayama_heading ); ?></p>
          <?php
            wp_nav_menu(
                array(
                    'theme_location' => $kanayama_location,
                    'container'      => false,
                    'menu_class'     => '',
                    'depth'          => 1,
                )
            );
            ?>
        </div>
            <?php
        endforeach;
        ?>
      </nav>
    </div>
    <div class="ft__btm">
      <small>&copy; <?php echo esc_html( kanayama_opt( 'company_name' ) ); ?></small>
      <?php
      $kanayama_privacy = get_privacy_policy_url();
      if ( ! $kanayama_privacy ) {
          $kanayama_privacy_page = get_page_by_path( 'privacy' );
          $kanayama_privacy      = $kanayama_privacy_page ? get_permalink( $kanayama_privacy_page ) : '';
      }
      if ( $kanayama_privacy ) :
          ?>
      <a href="<?php echo esc_url( $kanayama_privacy ); ?>"><?php esc_html_e( 'プライバシーポリシー', 'kanayama' ); ?></a>
      <?php endif; ?>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
