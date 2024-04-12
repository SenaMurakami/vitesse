<?php 
if ( !defined( 'ABSPATH' ) ) exit;

//develop mode config
define( "IS_VITE_DEVELOPMENT", true );

/**
 * define
 */
define( 'THEME_VERSION', '0.0.1');
define( 'DIST_DEF', 'assets' );
define( 'DIST_URI',  get_template_directory_uri() . '/' . DIST_DEF );
define( 'DIST_PATH', get_template_directory()     . '/' . DIST_DEF );


/*
 * CSS 読み込み
* ----------------------------------------------*/
function page_style_script() {

  if(!IS_VITE_DEVELOPMENT) {
    // -------------------------
    // 全ページ共通
    // -------------------------
    wp_enqueue_style('main-style', get_template_directory_uri() . "/assets/css/main.css", array(), THEME_VERSION);

    // WordPress本体のjquery.jsを読み込まない
	  //wp_deregister_script('jquery');
    wp_enqueue_script( 'main-script', get_template_directory_uri() .'/assets/js/main.js', '', THEME_VERSION, false);

  } else {
    // ローカル開発環境
    $manifest = json_decode( file_get_contents( DIST_PATH . '/.vite/manifest.json'), true );
    if ( is_array( $manifest ) ) {
      foreach($manifest as $manifestItem) {
        // add css files
        if($manifestItem["css"]) {
          foreach($manifestItem["css"] as $css) {
            $cssFileName = explode("/", $css)[1];
            $cssName = explode(".", $cssFileName)[0];
            wp_enqueue_style($cssName.'-style', DIST_URI .'/'.$css, array(), '');
          }
        }
        // add javascript
        if($manifestItem["isEntry"] && $manifestItem["file"]) {
          $js = $manifestItem["file"];
          $jsFileName = explode("/", $js)[1];
          $jsName = explode(".", $jsFileName)[0];
          wp_enqueue_script( $jsName.'-script', DIST_URI .'/'.$js, '', THEME_VERSION, false);
        }
      }
    }
  }
}
add_action( 'wp_enqueue_scripts', 'page_style_script' );

/*
 * bodyタグ直下に出力
* ----------------------------------------------*/
function add_tag_body_open() {
	//echo 'wp_body_open action hook';
}
add_action('wp_body_open', add_tag_body_open);

/*
 * ダッシュボードウィジェット削除
* ----------------------------------------------*/
function remove_dashboard_meta() {
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );// WordPress ニュース
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // クイックドラフト
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal'); // アクティビティ
}
add_action( 'admin_init', 'remove_dashboard_meta' );

/*
 * 管理画面整理
* ----------------------------------------------*/
add_action( 'admin_menu', 'remove_menus' );
function remove_menus(){
    //remove_menu_page( 'index.php' ); //ダッシュボード
    //remove_menu_page( 'edit.php' ); //投稿メニュー
    //remove_menu_page( 'upload.php' ); //メディア
    //remove_menu_page( 'edit.php?post_type=page' ); //ページ追加
    remove_menu_page( 'edit-comments.php' ); //コメントメニュー
    //remove_menu_page( 'themes.php' ); //外観メニュー
    //remove_menu_page( 'plugins.php' ); //プラグインメニュー
    //remove_menu_page( 'tools.php' ); //ツールメニュー
    //remove_menu_page( 'options-general.php' ); //設定メニュー
}

/*
 * 更新通知を管理者権限のみに表示
* ----------------------------------------------*/
function update_nag_admin_only() {
  if ( ! current_user_can( 'administrator' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag', 10 );
  }
}
add_action( 'admin_init', 'update_nag_admin_only' );

/*
 * wp_headから削除
* ----------------------------------------------*/
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head','wp_oembed_add_host_js');
remove_action('wp_head', 'print_emoji_detection_script', 7 );
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head','wp_resource_hints',2);
add_filter( 'show_admin_bar', '__return_false' );


/*
 * アイキャッチ画像を有効にする。
* ----------------------------------------------*/
add_theme_support('post-thumbnails');

/*
 * WPログイン時、記事ページから
 * 編集画面に遷移できるボタンを追加
* ----------------------------------------------*/
function edit($the_content) {
  if (is_single() && is_user_logged_in()) {
      $return = '<div style="text-align: center; padding: 2rem; background: #eee;"><a target="_blank" href="'.get_edit_post_link().'" style="display: inline-block; padding: .5rem 1rem; background: #fff;">記事を編集</a></div>';
      $return .= $the_content;
      return $return;
  } else {
      return $the_content;
  }
}
add_filter('the_content','edit');

/*
* Authorアーカイブページを
* トップページにリダイレクト
* ----------------------------------*/

function author_custom_redirection() {
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
  $wp_rewrite->author_base = '';
  $wp_rewrite->author_structure = '/';
  if (isset($_REQUEST['author']) && !empty($_REQUEST['author'])) {
    wp_redirect(home_url());
    exit;
  }
}
add_action('init', 'author_custom_redirection');

