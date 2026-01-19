<?php
/**
 * 威软视频下载站主题 - 核心功能文件
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// 定义主题常量
define('WEIRUAN_VIDEO_VERSION', '1.0.0');
define('WEIRUAN_VIDEO_DIR', get_template_directory());
define('WEIRUAN_VIDEO_URI', get_template_directory_uri());

/**
 * 主题初始化设置
 */
function weiruan_video_setup() {
    // 加载文本域
    load_theme_textdomain('weiruan-video', WEIRUAN_VIDEO_DIR . '/languages');

    // 添加主题支持
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');

    // 自定义Logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // 自定义背景
    add_theme_support('custom-background', array(
        'default-color' => '0f0f23',
    ));

    // 设置缩略图尺寸
    set_post_thumbnail_size(1280, 720, true);
    add_image_size('video-thumb-large', 640, 360, true);
    add_image_size('video-thumb-medium', 320, 180, true);
    add_image_size('video-thumb-small', 160, 90, true);

    // 注册导航菜单
    register_nav_menus(array(
        'primary'   => __('主导航菜单', 'weiruan-video'),
        'footer'    => __('底部菜单', 'weiruan-video'),
        'mobile'    => __('移动端菜单', 'weiruan-video'),
    ));
}
add_action('after_setup_theme', 'weiruan_video_setup');

/**
 * 设置内容宽度
 */
function weiruan_video_content_width() {
    $GLOBALS['content_width'] = apply_filters('weiruan_video_content_width', 1200);
}
add_action('after_setup_theme', 'weiruan_video_content_width', 0);

/**
 * 注册小工具区域
 */
function weiruan_video_widgets_init() {
    register_sidebar(array(
        'name'          => __('主侧边栏', 'weiruan-video'),
        'id'            => 'sidebar-main',
        'description'   => __('添加小工具到主侧边栏', 'weiruan-video'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('视频页侧边栏', 'weiruan-video'),
        'id'            => 'sidebar-video',
        'description'   => __('添加小工具到视频页侧边栏', 'weiruan-video'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('底部区域 1', 'weiruan-video'),
        'id'            => 'footer-1',
        'description'   => __('底部小工具区域 1', 'weiruan-video'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('底部区域 2', 'weiruan-video'),
        'id'            => 'footer-2',
        'description'   => __('底部小工具区域 2', 'weiruan-video'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('底部区域 3', 'weiruan-video'),
        'id'            => 'footer-3',
        'description'   => __('底部小工具区域 3', 'weiruan-video'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'weiruan_video_widgets_init');

/**
 * 加载前端资源
 */
function weiruan_video_scripts() {
    // 主样式
    wp_enqueue_style('weiruan-video-style', get_stylesheet_uri(), array(), WEIRUAN_VIDEO_VERSION);

    // 视频播放器样式
    wp_enqueue_style('weiruan-video-player', WEIRUAN_VIDEO_URI . '/assets/css/video-player.css', array(), WEIRUAN_VIDEO_VERSION);

    // 字体图标
    wp_enqueue_style('weiruan-video-icons', WEIRUAN_VIDEO_URI . '/assets/css/icons.css', array(), WEIRUAN_VIDEO_VERSION);

    // 主脚本
    wp_enqueue_script('weiruan-video-main', WEIRUAN_VIDEO_URI . '/assets/js/main.js', array('jquery'), WEIRUAN_VIDEO_VERSION, true);

    // 视频播放器脚本
    wp_enqueue_script('weiruan-video-player', WEIRUAN_VIDEO_URI . '/assets/js/video-player.js', array('jquery'), WEIRUAN_VIDEO_VERSION, true);

    // 下载管理脚本
    wp_enqueue_script('weiruan-video-download', WEIRUAN_VIDEO_URI . '/assets/js/download-manager.js', array('jquery'), WEIRUAN_VIDEO_VERSION, true);

    // 传递AJAX数据
    wp_localize_script('weiruan-video-main', 'weiruanVideo', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('weiruan_video_nonce'),
        'strings' => array(
            'loading'     => __('加载中...', 'weiruan-video'),
            'error'       => __('出错了，请重试', 'weiruan-video'),
            'downloaded'  => __('下载完成', 'weiruan-video'),
            'downloading' => __('下载中...', 'weiruan-video'),
        ),
    ));

    // 评论回复脚本
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'weiruan_video_scripts');

/**
 * 加载后台资源
 */
function weiruan_video_admin_scripts($hook) {
    global $post_type;

    if ($post_type === 'video' || $hook === 'toplevel_page_weiruan-video-settings') {
        wp_enqueue_style('weiruan-video-admin', WEIRUAN_VIDEO_URI . '/assets/css/admin.css', array(), WEIRUAN_VIDEO_VERSION);
        wp_enqueue_script('weiruan-video-admin', WEIRUAN_VIDEO_URI . '/assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), WEIRUAN_VIDEO_VERSION, true);
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'weiruan_video_admin_scripts');

/**
 * 加载主题功能模块
 */
require_once WEIRUAN_VIDEO_DIR . '/inc/custom-post-types.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/meta-boxes.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/theme-options.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/template-functions.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/template-tags.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/ajax-handlers.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/widgets.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/shortcodes.php';
require_once WEIRUAN_VIDEO_DIR . '/inc/visitor-stats.php';

/**
 * 自定义摘要长度
 */
function weiruan_video_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'weiruan_video_excerpt_length');

/**
 * 自定义摘要更多链接
 */
function weiruan_video_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'weiruan_video_excerpt_more');

/**
 * 为视频添加查看次数统计
 */
function weiruan_video_track_views() {
    if (is_singular('video')) {
        global $post;
        $views = get_post_meta($post->ID, '_video_views', true);
        $views = $views ? intval($views) + 1 : 1;
        update_post_meta($post->ID, '_video_views', $views);
    }
}
add_action('wp_head', 'weiruan_video_track_views');

/**
 * 添加主题自定义类到body
 */
function weiruan_video_body_classes($classes) {
    // 添加主题标识类
    $classes[] = 'weiruan-video-theme';

    // 侧边栏状态
    if (is_active_sidebar('sidebar-main')) {
        $classes[] = 'has-sidebar';
    }

    // 视频单页
    if (is_singular('video')) {
        $classes[] = 'single-video-page';
    }

    return $classes;
}
add_filter('body_class', 'weiruan_video_body_classes');

/**
 * 修改视频文章类型的归档查询
 */
function weiruan_video_archive_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('video') || is_tax('video_category') || is_tax('video_tag')) {
            $query->set('posts_per_page', 12);
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
    }
}
add_action('pre_get_posts', 'weiruan_video_archive_query');

/**
 * 添加自定义重写规则
 */
function weiruan_video_rewrite_rules() {
    add_rewrite_rule(
        'videos/category/([^/]+)/?$',
        'index.php?video_category=$matches[1]',
        'top'
    );

    add_rewrite_rule(
        'videos/tag/([^/]+)/?$',
        'index.php?video_tag=$matches[1]',
        'top'
    );
}
add_action('init', 'weiruan_video_rewrite_rules');

/**
 * 主题激活时刷新重写规则
 */
function weiruan_video_activation() {
    weiruan_video_register_post_types();
    weiruan_video_register_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'weiruan_video_activation');

/**
 * 主题停用时清理
 */
function weiruan_video_deactivation() {
    flush_rewrite_rules();
}
add_action('switch_theme', 'weiruan_video_deactivation');

/**
 * 安全的视频下载处理
 */
function weiruan_video_handle_download() {
    if (!isset($_GET['download_video']) || !isset($_GET['video_id']) || !isset($_GET['resolution'])) {
        return;
    }

    // 验证nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'video_download_' . $_GET['video_id'])) {
        wp_die(__('安全验证失败', 'weiruan-video'));
    }

    $video_id = intval($_GET['video_id']);
    $resolution = sanitize_text_field($_GET['resolution']);

    // 获取视频文件
    $video_files = get_post_meta($video_id, '_video_files', true);

    if (empty($video_files) || !isset($video_files[$resolution])) {
        wp_die(__('视频文件不存在', 'weiruan-video'));
    }

    $file_url = $video_files[$resolution]['url'];
    $file_name = basename($file_url);

    // 记录下载次数
    $downloads = get_post_meta($video_id, '_video_downloads', true);
    $downloads = $downloads ? intval($downloads) + 1 : 1;
    update_post_meta($video_id, '_video_downloads', $downloads);

    // 重定向到文件下载
    wp_redirect($file_url);
    exit;
}
add_action('template_redirect', 'weiruan_video_handle_download');

/**
 * 禁用Gutenberg编辑器用于视频文章类型（可选）
 */
function weiruan_video_disable_gutenberg($use_block_editor, $post_type) {
    if ($post_type === 'video') {
        return false;
    }
    return $use_block_editor;
}
// add_filter('use_block_editor_for_post_type', 'weiruan_video_disable_gutenberg', 10, 2);

/**
 * 添加管理栏快捷链接
 */
function weiruan_video_admin_bar_menu($wp_admin_bar) {
    if (!current_user_can('edit_posts')) {
        return;
    }

    $wp_admin_bar->add_node(array(
        'id'    => 'weiruan-video-add',
        'title' => __('添加视频', 'weiruan-video'),
        'href'  => admin_url('post-new.php?post_type=video'),
        'meta'  => array(
            'title' => __('添加新视频', 'weiruan-video'),
        ),
    ));
}
add_action('admin_bar_menu', 'weiruan_video_admin_bar_menu', 100);

/**
 * 自定义登录页面Logo
 */
function weiruan_video_login_logo() {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
    } else {
        $logo_url = WEIRUAN_VIDEO_URI . '/assets/images/logo.png';
    }
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: url(<?php echo esc_url($logo_url); ?>);
            background-size: contain;
            width: 200px;
            height: 80px;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'weiruan_video_login_logo');

/**
 * 自定义登录页面链接
 */
function weiruan_video_login_url() {
    return home_url();
}
add_filter('login_headerurl', 'weiruan_video_login_url');

/**
 * 自定义登录页面标题
 */
function weiruan_video_login_title() {
    return get_bloginfo('name') . ' - ' . __('威软视频下载站', 'weiruan-video');
}
add_filter('login_headertext', 'weiruan_video_login_title');
