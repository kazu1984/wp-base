<?php

// ------------------------------------------------
// グローバル変数
// ------------------------------------------------

// パス
$WP_ROOT_PATH = get_stylesheet_directory_uri();
$WP_IMG_PATH = esc_html($WP_ROOT_PATH . '/assets/images');
$WP_CSS_PATH = esc_html($WP_ROOT_PATH . '/assets/css');
$WP_JS_PATH = esc_html($WP_ROOT_PATH . '/assets/js');

// OGP用
$FACEBOOK_APP_ID = '';
$TWITTER_ACCOUNT_ID = '';


// ------------------------------------------------
// テーマのセットアップ
// ------------------------------------------------
function my_theme_setup()
{
  add_theme_support('post-thumbnails');
  add_theme_support('title-tag');
  add_theme_support('wp-block-styles');
  add_theme_support(
    'html5',
    array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
    )
  );
}
add_action('after_setup_theme', 'my_theme_setup');


// ------------------------------------------------
// css、js、fontの読み込み
// ------------------------------------------------
function my_script_init()
{
  wp_deregister_script('jquery');
  wp_enqueue_script('jquery', '//code.jquery.com/jquery-3.6.0.min.js', "", "1.0.1");

  // google font
  wp_enqueue_style('NotoSans', '//fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap"');

  // fontawsome
  wp_enqueue_style('font-awesome', 'https://use.fontawesome.com/releases/v5.6.1/css/all.css');

  // js
  wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/common.js?v=' . date_i18n('Ymd_His'), array('jquery'), '1.0.1', true);

  // css
  wp_enqueue_style('style-name', get_template_directory_uri() . '/assets/css/styles.css?v=' . date_i18n('Ymd_His'), array(), '1.0.1', false);
}
add_action('wp_enqueue_scripts', 'my_script_init');


// ------------------------------------------------
// wordpressエディタ自動整形の無効化
// ------------------------------------------------
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
add_filter(
  'tiny_mce_before_init',
  function ($init_array) {
    global $allowedposttags;
    $init_array['valid_elements']          = '*[*]';
    $init_array['extended_valid_elements'] = '*[*]';
    $init_array['valid_children']          = '+a[' . implode('|', array_keys($allowedposttags)) . ']';
    $init_array['indent']                  = true;
    $init_array['wpautop']                 = false;
    $init_array['force_p_newlines']        = false;
    return $init_array;
  }
);

// ------------------------------------------------
// 投稿の自動整形を無効（ダブルクオーテーションなど）
// ------------------------------------------------
add_filter('run_wptexturize', '__return_false');


// ------------------------------------------------
// wordpress更新通知を管理者権限のみに表示
// ------------------------------------------------
function update_nag_admin_only()
{
  if (!current_user_can('administrator')) {
    remove_action('admin_notices', 'update_nag', 3);
  }
}
add_action('admin_init', 'update_nag_admin_only');


/**
 * 不要なメニューを非表示
 * （コメントアウトした行のメニューは表示される）
 */
add_action('admin_menu', 'my_add_remove_admin_menus');
function my_add_remove_admin_menus()
{
  global $menu;
  unset($menu[2]);  // ダッシュボード
  unset($menu[4]);  // メニューの線1
  // unset($menu[5]);  // 投稿
  // unset($menu[10]); // メディア
  // unset($menu[15]); // リンク
  // unset($menu[20]); // ページ
  unset($menu[25]); // コメント
  unset($menu[59]); // メニューの線2
  // unset($menu[60]); // テーマ
  // unset($menu[65]); // プラグイン
  // unset($menu[70]); // プロフィール
  // unset($menu[75]); // ツール
  // unset($menu[80]); // 設定
  unset($menu[90]); // メニューの線3
}


// ------------------------------------------------
// OGP設定
// ------------------------------------------------
add_action('wp_head', 'my_add_meta_ogp');
function my_add_meta_ogp()
{
  if (is_front_page() || is_singular()) {
    global $WP_IMG_PATH;
    global $FACEBOOK_APP_ID;
    global $TWITTER_ACCOUNT_ID;
    global $post;
    $ogp_title = '';
    $ogp_descr = '';
    $ogp_url = '';
    $ogp_img = '';
    $insert = '';

    if (is_singular() && !is_page()) {
      setup_postdata($post);
      $ogp_title = $post->post_title;
      $ogp_descr = mb_substr(get_the_excerpt(), 0, 100);
      $ogp_url = get_permalink();
      wp_reset_postdata();
    } else {
      $ogp_title = get_bloginfo('name');
      $ogp_descr = get_bloginfo('description');
      $ogp_url = home_url();
    }

    // og:type
    $ogp_type = (is_front_page() || is_home()) ? 'website' : 'article';

    // og:image
    if (is_singular() && has_post_thumbnail()) {
      $ps_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
      $ogp_img = $ps_thumb[0];
    } else {
      $ogp_img = $WP_IMG_PATH . '/common/ogp.jpg';
    }

    // タグ出力
    $insert .= '<meta property="og:title" content="' . esc_attr($ogp_title) . '">' . "\n";
    $insert .= '<meta property="og:description" content="' . esc_attr($ogp_descr) . '">' . "\n";
    $insert .= '<meta property="og:type" content="' . $ogp_type . '">' . "\n";
    $insert .= '<meta property="og:url" content="' . esc_url($ogp_url) . '">' . "\n";
    $insert .= '<meta property="og:image" content="' . esc_url($ogp_img) . '">' . "\n";
    $insert .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    $insert .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $insert .= '<meta name="twitter:site" content="' . $TWITTER_ACCOUNT_ID . '">' . "\n";
    $insert .= '<meta property="og:locale" content="ja_JP">' . "\n";
    $insert .= '<meta property="fb:app_id" content="' . $FACEBOOK_APP_ID . '">' . "\n";
    echo $insert;
  }
}


// ------------------------------------------------
// メインループの制御
// ------------------------------------------------
function my_query($query)
{
  if (is_admin() || !$query->is_main_query()) {
    return;
  }

  $post_per_page = 3;
  $query->set('posts_per_page', $post_per_page);
  $query->set('ignore_sticky_posts', 1);

}
add_filter('pre_get_posts', 'my_query');


// ------------------------------------------------
// デフォルトアイキャッチの設定
// ------------------------------------------------
function get_eyecatch_with_default()
{
  if (has_post_thumbnail()) {
    $id = get_post_thumbnail_id();
    $img = wp_get_attachment_image_src($id, 'full');
  } else {
    $img = array(get_template_directory_uri() . '/assets/images/common/no_image.png');
  }

  return $img;
}

