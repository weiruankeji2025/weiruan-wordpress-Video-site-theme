<?php
/**
 * AJAX处理函数
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 点赞功能
 */
function weiruan_video_like_handler() {
    check_ajax_referer('weiruan_video_nonce', 'nonce');

    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;

    if (!$video_id) {
        wp_send_json_error(array('message' => __('无效的视频ID', 'weiruan-video')));
    }

    $cookie_name = 'video_liked_' . $video_id;
    $likes = get_post_meta($video_id, '_video_likes', true);
    $likes = $likes ? intval($likes) : 0;

    if (isset($_COOKIE[$cookie_name])) {
        // 取消点赞
        $likes = max(0, $likes - 1);
        setcookie($cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        $liked = false;
    } else {
        // 点赞
        $likes++;
        setcookie($cookie_name, '1', time() + (365 * 24 * 60 * 60), COOKIEPATH, COOKIE_DOMAIN);
        $liked = true;
    }

    update_post_meta($video_id, '_video_likes', $likes);

    wp_send_json_success(array(
        'likes'  => weiruan_video_format_number($likes),
        'liked'  => $liked,
    ));
}
add_action('wp_ajax_weiruan_video_like', 'weiruan_video_like_handler');
add_action('wp_ajax_nopriv_weiruan_video_like', 'weiruan_video_like_handler');

/**
 * 加载更多视频
 */
function weiruan_video_load_more_handler() {
    check_ajax_referer('weiruan_video_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $tag = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';
    $per_page = weiruan_video_get_option('videos_per_page', 12);

    $args = array(
        'post_type'      => 'video',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    );

    if ($category) {
        $args['tax_query'][] = array(
            'taxonomy' => 'video_category',
            'field'    => 'slug',
            'terms'    => $category,
        );
    }

    if ($tag) {
        $args['tax_query'][] = array(
            'taxonomy' => 'video_tag',
            'field'    => 'slug',
            'terms'    => $tag,
        );
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        wp_send_json_error(array('message' => __('没有更多视频了', 'weiruan-video')));
    }

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        weiruan_video_card();
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html'     => $html,
        'has_more' => $page < $query->max_num_pages,
    ));
}
add_action('wp_ajax_weiruan_video_load_more', 'weiruan_video_load_more_handler');
add_action('wp_ajax_nopriv_weiruan_video_load_more', 'weiruan_video_load_more_handler');

/**
 * 搜索视频
 */
function weiruan_video_search_handler() {
    check_ajax_referer('weiruan_video_nonce', 'nonce');

    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

    if (empty($keyword)) {
        wp_send_json_error(array('message' => __('请输入搜索关键词', 'weiruan-video')));
    }

    $args = array(
        'post_type'      => 'video',
        'posts_per_page' => 12,
        'paged'          => $page,
        's'              => $keyword,
        'post_status'    => 'publish',
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        wp_send_json_error(array('message' => __('未找到相关视频', 'weiruan-video')));
    }

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        weiruan_video_card();
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html'       => $html,
        'has_more'   => $page < $query->max_num_pages,
        'total'      => $query->found_posts,
    ));
}
add_action('wp_ajax_weiruan_video_search', 'weiruan_video_search_handler');
add_action('wp_ajax_nopriv_weiruan_video_search', 'weiruan_video_search_handler');

/**
 * 记录下载
 */
function weiruan_video_record_download_handler() {
    check_ajax_referer('weiruan_video_nonce', 'nonce');

    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    $resolution = isset($_POST['resolution']) ? sanitize_text_field($_POST['resolution']) : '';

    if (!$video_id) {
        wp_send_json_error(array('message' => __('无效的视频ID', 'weiruan-video')));
    }

    $downloads = get_post_meta($video_id, '_video_downloads', true);
    $downloads = $downloads ? intval($downloads) + 1 : 1;
    update_post_meta($video_id, '_video_downloads', $downloads);

    // 记录分辨率下载统计
    $resolution_downloads = get_post_meta($video_id, '_video_resolution_downloads', true);
    if (!is_array($resolution_downloads)) {
        $resolution_downloads = array();
    }
    if (!isset($resolution_downloads[$resolution])) {
        $resolution_downloads[$resolution] = 0;
    }
    $resolution_downloads[$resolution]++;
    update_post_meta($video_id, '_video_resolution_downloads', $resolution_downloads);

    wp_send_json_success(array(
        'downloads' => weiruan_video_format_number($downloads),
    ));
}
add_action('wp_ajax_weiruan_video_record_download', 'weiruan_video_record_download_handler');
add_action('wp_ajax_nopriv_weiruan_video_record_download', 'weiruan_video_record_download_handler');

/**
 * 获取视频信息
 */
function weiruan_video_get_info_handler() {
    check_ajax_referer('weiruan_video_nonce', 'nonce');

    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;

    if (!$video_id) {
        wp_send_json_error(array('message' => __('无效的视频ID', 'weiruan-video')));
    }

    $video = get_post($video_id);

    if (!$video || $video->post_type !== 'video') {
        wp_send_json_error(array('message' => __('视频不存在', 'weiruan-video')));
    }

    $data = array(
        'id'        => $video_id,
        'title'     => $video->post_title,
        'excerpt'   => $video->post_excerpt,
        'thumbnail' => get_the_post_thumbnail_url($video_id, 'video-thumb-large'),
        'duration'  => weiruan_video_get_duration($video_id),
        'views'     => weiruan_video_get_views($video_id),
        'downloads' => weiruan_video_get_downloads($video_id),
        'likes'     => weiruan_video_get_likes($video_id),
        'files'     => weiruan_video_get_files($video_id),
        'preview'   => weiruan_video_get_preview_url($video_id),
        'permalink' => get_permalink($video_id),
    );

    wp_send_json_success($data);
}
add_action('wp_ajax_weiruan_video_get_info', 'weiruan_video_get_info_handler');
add_action('wp_ajax_nopriv_weiruan_video_get_info', 'weiruan_video_get_info_handler');

/**
 * 收藏功能
 */
function weiruan_video_collect_handler() {
    check_ajax_referer('weiruan_video_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array(
            'message'       => __('请先登录', 'weiruan-video'),
            'require_login' => true,
        ));
    }

    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    $user_id = get_current_user_id();

    if (!$video_id) {
        wp_send_json_error(array('message' => __('无效的视频ID', 'weiruan-video')));
    }

    $collections = get_user_meta($user_id, '_video_collections', true);
    if (!is_array($collections)) {
        $collections = array();
    }

    $collected = false;

    if (in_array($video_id, $collections)) {
        // 取消收藏
        $collections = array_diff($collections, array($video_id));
        $collected = false;
    } else {
        // 添加收藏
        $collections[] = $video_id;
        $collected = true;
    }

    update_user_meta($user_id, '_video_collections', $collections);

    wp_send_json_success(array(
        'collected' => $collected,
        'message'   => $collected ? __('已添加到收藏', 'weiruan-video') : __('已取消收藏', 'weiruan-video'),
    ));
}
add_action('wp_ajax_weiruan_video_collect', 'weiruan_video_collect_handler');

/**
 * 筛选视频
 */
function weiruan_video_filter_handler() {
    check_ajax_referer('weiruan_video_nonce', 'nonce');

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $quality = isset($_POST['quality']) ? sanitize_text_field($_POST['quality']) : '';
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'date';
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

    $args = array(
        'post_type'      => 'video',
        'posts_per_page' => 12,
        'paged'          => $page,
        'post_status'    => 'publish',
    );

    // 分类筛选
    if ($category) {
        $args['tax_query'][] = array(
            'taxonomy' => 'video_category',
            'field'    => 'slug',
            'terms'    => $category,
        );
    }

    // 质量筛选
    if ($quality) {
        $args['tax_query'][] = array(
            'taxonomy' => 'video_quality',
            'field'    => 'slug',
            'terms'    => $quality,
        );
    }

    // 排序
    switch ($sort) {
        case 'views':
            $args['meta_key'] = '_video_views';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'downloads':
            $args['meta_key'] = '_video_downloads';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'likes':
            $args['meta_key'] = '_video_likes';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'title':
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        wp_send_json_error(array('message' => __('没有找到符合条件的视频', 'weiruan-video')));
    }

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        weiruan_video_card();
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html'     => $html,
        'has_more' => $page < $query->max_num_pages,
        'total'    => $query->found_posts,
    ));
}
add_action('wp_ajax_weiruan_video_filter', 'weiruan_video_filter_handler');
add_action('wp_ajax_nopriv_weiruan_video_filter', 'weiruan_video_filter_handler');
