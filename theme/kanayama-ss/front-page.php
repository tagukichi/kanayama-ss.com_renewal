<?php
/**
 * トップページ。
 *
 * @package Kanayama
 */

get_header();

$kanayama_solar    = get_page_by_path( 'solar' );
$kanayama_electric = get_page_by_path( 'electric' );
$kanayama_concept  = get_page_by_path( 'concept' );
$kanayama_recruit  = get_page_by_path( 'recruit' );
$kanayama_works    = get_post_type_archive_link( 'kn_work' );
$kanayama_iv       = get_post_type_archive_link( 'kn_interview' );
$kanayama_news     = (int) get_option( 'page_for_posts' );
$kanayama_news_url = $kanayama_news ? get_permalink( $kanayama_news ) : '';
?>

<section class="fv" style="--slide-ratio:<?php echo esc_attr( kanayama_opt( 'fv_ratio' ) ); ?>">
  <?php kanayama_fv_slider(); ?>

  <div class="wrap fv__in">
    <p class="fv__eyebrow"><span><?php echo esc_html( kanayama_opt( 'fv_eyebrow_1' ) ); ?></span><i></i><span><?php echo esc_html( kanayama_opt( 'fv_eyebrow_2' ) ); ?></span></p>
    <h1 class="fv__ttl"><?php echo wp_kses( kanayama_fv_title( kanayama_opt( 'fv_title' ) ), array( 'span' => array( 'class' => array() ), 'em' => array() ) ); ?></h1>
    <p class="fv__sub"><?php echo esc_html( kanayama_opt( 'fv_sub' ) ); ?></p>
  </div>
</section>

<div class="fvfoot">
  <div class="wrap fvfoot__in">
    <dl class="fv__stats">
      <div class="fv__stat"><dd><b data-count><?php echo esc_html( kanayama_opt( 'stat1_num' ) ); ?></b><i><?php echo esc_html( kanayama_opt( 'stat1_unit' ) ); ?></i></dd><dt><?php echo esc_html( kanayama_opt( 'stat1_label' ) ); ?></dt></div>
      <div class="fv__stat"><dd><b><?php echo esc_html( kanayama_opt( 'stat2_num' ) ); ?></b><i><?php echo esc_html( kanayama_opt( 'stat2_unit' ) ); ?></i></dd><dt><?php echo esc_html( kanayama_opt( 'stat2_label' ) ); ?></dt></div>
      <div class="fv__stat"><dd><b><?php echo esc_html( kanayama_opt( 'stat3_num' ) ); ?></b><i><?php echo esc_html( kanayama_opt( 'stat3_unit' ) ); ?></i></dd><dt><?php echo esc_html( kanayama_opt( 'stat3_label' ) ); ?></dt></div>
    </dl>
  </div>
</div>

<?php if ( $kanayama_concept ) : ?>
<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Concept</p>
      <h2 class="h-sec"><?php echo esc_html( get_the_title( $kanayama_concept ) ); ?></h2>
    </div>
    <div>
      <?php
      // 「私たちの想い」ページの抜粋を要約として使う。
      $kanayama_concept_lead = get_post_meta( $kanayama_concept->ID, '_kn_front_lead', true );
      $kanayama_concept_text = has_excerpt( $kanayama_concept ) ? get_the_excerpt( $kanayama_concept ) : '';
      ?>
      <?php if ( $kanayama_concept_lead ) : ?>
      <p class="lead mincho"><?php echo wp_kses( kanayama_br( $kanayama_concept_lead ), array( 'br' => array() ) ); ?></p>
      <?php endif; ?>
      <?php if ( $kanayama_concept_text ) : ?>
      <p class="p mt-m"><?php echo esc_html( $kanayama_concept_text ); ?></p>
      <?php endif; ?>
      <p class="mt-m"><a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $kanayama_concept ) ); ?>"><?php esc_html_e( '私たちの想いを読む', 'kanayama' ); ?></a></p>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ( $kanayama_solar || $kanayama_electric ) : ?>
<section class="sec sec--soft">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Business</p>
        <h2 class="h-sec"><?php esc_html_e( '事業案内', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( '太陽光発電システムの設置と、電気工事・水道工事。この両方ができるからこそ、屋根から分電盤、給湯まで一貫してお任せいただけます。', 'kanayama' ); ?></p>
    </div>
    <div class="biz mt-l reveal">
      <?php
      $kanayama_biz = array(
          array( 'page' => $kanayama_solar, 'no' => '01', 'en' => 'Solar &amp; Storage', 'tax' => 'solar' ),
          array( 'page' => $kanayama_electric, 'no' => '02', 'en' => 'Electric &amp; Plumbing', 'tax' => 'electric' ),
      );
      foreach ( $kanayama_biz as $kanayama_card ) :
          if ( ! $kanayama_card['page'] ) {
              continue;
          }
          $kanayama_tags = get_terms(
              array(
                  'taxonomy'   => 'kn_service_cat',
                  'slug'       => $kanayama_card['tax'],
                  'hide_empty' => false,
              )
          );
          $kanayama_services = get_posts(
              array(
                  'post_type'      => 'kn_service',
                  'posts_per_page' => -1,
                  'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
                  'no_found_rows'  => true,
                  'tax_query'      => array(
                      array( 'taxonomy' => 'kn_service_cat', 'field' => 'slug', 'terms' => $kanayama_card['tax'] ),
                  ),
              )
          );
          ?>
      <a class="biz__card" href="<?php echo esc_url( get_permalink( $kanayama_card['page'] ) ); ?>">
        <div class="biz__fig">
          <span class="biz__no"><?php echo esc_html( $kanayama_card['no'] ); ?></span>
          <?php
          kanayama_media(
              get_post_thumbnail_id( $kanayama_card['page'] ),
              'ph--fill',
              array( 'size' => 'kanayama-wide', 'alt' => '', 'placeholder' => __( '［写真］事業イメージ ／ 1600×1000px', 'kanayama' ) )
          );
          ?>
        </div>
        <div class="biz__body">
          <h3 class="biz__ttl"><span class="en"><?php echo wp_kses( $kanayama_card['en'], array() ); ?></span><?php echo esc_html( get_the_title( $kanayama_card['page'] ) ); ?></h3>
          <p class="p" style="font-size:14.5px"><?php echo esc_html( get_the_excerpt( $kanayama_card['page'] ) ); ?></p>
          <?php if ( $kanayama_services ) : ?>
          <ul class="biz__tags">
            <?php foreach ( $kanayama_services as $kanayama_service ) : ?>
            <li><?php echo esc_html( get_the_title( $kanayama_service ) ); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <span class="biz__more"><?php esc_html_e( 'くわしく見る', 'kanayama' ); ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="sec">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Reasons</p>
        <h2 class="h-sec"><?php esc_html_e( '選ばれる理由', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( '販売店様・元請け会社様からのご依頼も、施主様からの直接のご相談も。同じ基準で施工しています。', 'kanayama' ); ?></p>
    </div>
    <div class="mt-l reveal"><?php kanayama_features( 'reason' ); ?></div>
  </div>
</section>

<?php
$kanayama_latest_works = get_posts(
    array(
        'post_type'      => 'kn_work',
        'posts_per_page' => 3,
        'no_found_rows'  => true,
    )
);
if ( $kanayama_latest_works ) :
    ?>
<section class="sec sec--soft">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">Works</p>
        <h2 class="h-sec"><?php esc_html_e( '工事実績', 'kanayama' ); ?></h2>
      </div>
      <p class="lead"><?php esc_html_e( '東京・神奈川を中心に施工しています。条件の近い事例が、ご検討の参考になれば幸いです。', 'kanayama' ); ?></p>
    </div>
    <div class="works mt-l reveal">
      <?php foreach ( $kanayama_latest_works as $kanayama_work ) : ?>
        <?php kanayama_work_card( $kanayama_work ); ?>
      <?php endforeach; ?>
    </div>
    <?php if ( $kanayama_works ) : ?>
    <p class="mt-l center"><a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_works ); ?>"><?php esc_html_e( '工事実績をすべて見る', 'kanayama' ); ?></a></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ( $kanayama_recruit ) : ?>
<section class="sec">
  <div class="wrap rail reveal">
    <div>
      <p class="eyebrow">Recruit</p>
      <h2 class="h-sec"><?php esc_html_e( '採用情報', 'kanayama' ); ?></h2>
    </div>
    <div>
      <?php
      kanayama_media(
          get_post_thumbnail_id( $kanayama_recruit ),
          'ph--16x9',
          array( 'size' => 'kanayama-wide', 'placeholder' => __( '［写真］スタッフ集合写真 ／ 1600×900px', 'kanayama' ), 'alt' => __( '金山製作所のスタッフ', 'kanayama' ) )
      );
      ?>
      <p class="lead mt-m"><?php esc_html_e( '未経験からでも、屋根の上で一人前になれます。', 'kanayama' ); ?></p>
      <p class="p"><?php esc_html_e( '現在、施工スタッフを募集しています。資格取得の支援制度あり。まずは職場の雰囲気を、社員紹介のページからご覧ください。', 'kanayama' ); ?></p>
      <p class="acts" style="margin-top:26px">
        <a class="btn" href="<?php echo esc_url( get_permalink( $kanayama_recruit ) ); ?>"><?php esc_html_e( '募集要項を見る', 'kanayama' ); ?></a>
        <?php if ( $kanayama_iv ) : ?>
        <a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_iv ); ?>"><?php esc_html_e( '社員紹介', 'kanayama' ); ?></a>
        <?php endif; ?>
      </p>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="sec sec--tight sec--soft">
  <div class="wrap">
    <div class="rail reveal">
      <div>
        <p class="eyebrow">News</p>
        <h2 class="h-sec"><?php esc_html_e( 'お知らせ', 'kanayama' ); ?></h2>
      </div>
      <div>
        <?php kanayama_news_list( 3 ); ?>
        <?php if ( $kanayama_news_url ) : ?>
        <p class="mt-m"><a class="btn btn--ghost" href="<?php echo esc_url( $kanayama_news_url ); ?>"><?php esc_html_e( 'お知らせ一覧', 'kanayama' ); ?></a></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php
get_footer();
