<?php
/**
 * 自定义文章类型和分类法
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 注册视频文章类型
 */
function weiruan_video_register_post_types() {
    $labels = array(
        'name'                  => __('视频', 'weiruan-video'),
        'singular_name'         => __('视频', 'weiruan-video'),
        'menu_name'             => __('视频管理', 'weiruan-video'),
        'name_admin_bar'        => __('视频', 'weiruan-video'),
        'add_new'               => __('添加视频', 'weiruan-video'),
        'add_new_item'          => __('添加新视频', 'weiruan-video'),
        'new_item'              => __('新视频', 'weiruan-video'),
        'edit_item'             => __('编辑视频', 'weiruan-video'),
        'view_item'             => __('查看视频', 'weiruan-video'),
        'all_items'             => __('所有视频', 'weiruan-video'),
        'search_items'          => __('搜索视频', 'weiruan-video'),
        'parent_item_colon'     => __('父级视频:', 'weiruan-video'),
        'not_found'             => __('未找到视频', 'weiruan-video'),
        'not_found_in_trash'    => __('回收站中未找到视频', 'weiruan-video'),
        'featured_image'        => __('视频封面', 'weiruan-video'),
        'set_featured_image'    => __('设置视频封面', 'weiruan-video'),
        'remove_featured_image' => __('移除视频封面', 'weiruan-video'),
        'use_featured_image'    => __('使用此视频封面', 'weiruan-video'),
        'archives'              => __('视频归档', 'weiruan-video'),
        'insert_into_item'      => __('插入到视频', 'weiruan-video'),
        'uploaded_to_this_item' => __('上传到此视频', 'weiruan-video'),
        'filter_items_list'     => __('筛选视频列表', 'weiruan-video'),
        'items_list_navigation' => __('视频列表导航', 'weiruan-video'),
        'items_list'            => __('视频列表', 'weiruan-video'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'video', 'with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => 'videos',
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-video-alt3',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'),
        'show_in_rest'       => true,
        'rest_base'          => 'videos',
    );

    register_post_type('video', $args);
}
add_action('init', 'weiruan_video_register_post_types', 0);

/**
 * 注册视频分类法
 */
function weiruan_video_register_taxonomies() {
    // 视频分类
    $cat_labels = array(
        'name'                       => __('视频分类', 'weiruan-video'),
        'singular_name'              => __('视频分类', 'weiruan-video'),
        'search_items'               => __('搜索分类', 'weiruan-video'),
        'popular_items'              => __('热门分类', 'weiruan-video'),
        'all_items'                  => __('所有分类', 'weiruan-video'),
        'parent_item'                => __('父级分类', 'weiruan-video'),
        'parent_item_colon'          => __('父级分类:', 'weiruan-video'),
        'edit_item'                  => __('编辑分类', 'weiruan-video'),
        'update_item'                => __('更新分类', 'weiruan-video'),
        'add_new_item'               => __('添加新分类', 'weiruan-video'),
        'new_item_name'              => __('新分类名称', 'weiruan-video'),
        'separate_items_with_commas' => __('用逗号分隔分类', 'weiruan-video'),
        'add_or_remove_items'        => __('添加或删除分类', 'weiruan-video'),
        'choose_from_most_used'      => __('从常用分类中选择', 'weiruan-video'),
        'not_found'                  => __('未找到分类', 'weiruan-video'),
        'menu_name'                  => __('视频分类', 'weiruan-video'),
    );

    $cat_args = array(
        'hierarchical'          => true,
        'labels'                => $cat_labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'video-category', 'with_front' => false),
        'show_in_rest'          => true,
        'rest_base'             => 'video-categories',
    );

    register_taxonomy('video_category', array('video'), $cat_args);

    // 视频标签
    $tag_labels = array(
        'name'                       => __('视频标签', 'weiruan-video'),
        'singular_name'              => __('视频标签', 'weiruan-video'),
        'search_items'               => __('搜索标签', 'weiruan-video'),
        'popular_items'              => __('热门标签', 'weiruan-video'),
        'all_items'                  => __('所有标签', 'weiruan-video'),
        'edit_item'                  => __('编辑标签', 'weiruan-video'),
        'update_item'                => __('更新标签', 'weiruan-video'),
        'add_new_item'               => __('添加新标签', 'weiruan-video'),
        'new_item_name'              => __('新标签名称', 'weiruan-video'),
        'separate_items_with_commas' => __('用逗号分隔标签', 'weiruan-video'),
        'add_or_remove_items'        => __('添加或删除标签', 'weiruan-video'),
        'choose_from_most_used'      => __('从常用标签中选择', 'weiruan-video'),
        'not_found'                  => __('未找到标签', 'weiruan-video'),
        'menu_name'                  => __('视频标签', 'weiruan-video'),
    );

    $tag_args = array(
        'hierarchical'          => false,
        'labels'                => $tag_labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'video-tag', 'with_front' => false),
        'show_in_rest'          => true,
        'rest_base'             => 'video-tags',
    );

    register_taxonomy('video_tag', array('video'), $tag_args);

    // 视频质量/分辨率
    $quality_labels = array(
        'name'                       => __('视频质量', 'weiruan-video'),
        'singular_name'              => __('视频质量', 'weiruan-video'),
        'search_items'               => __('搜索质量', 'weiruan-video'),
        'all_items'                  => __('所有质量', 'weiruan-video'),
        'edit_item'                  => __('编辑质量', 'weiruan-video'),
        'update_item'                => __('更新质量', 'weiruan-video'),
        'add_new_item'               => __('添加新质量', 'weiruan-video'),
        'new_item_name'              => __('新质量名称', 'weiruan-video'),
        'menu_name'                  => __('视频质量', 'weiruan-video'),
    );

    $quality_args = array(
        'hierarchical'          => true,
        'labels'                => $quality_labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'video-quality'),
        'show_in_rest'          => true,
    );

    register_taxonomy('video_quality', array('video'), $quality_args);
}
add_action('init', 'weiruan_video_register_taxonomies', 0);

/**
 * 添加默认视频质量术语
 */
function weiruan_video_add_default_terms() {
    $default_qualities = array(
        '4k'   => array('name' => '4K Ultra HD', 'description' => '3840x2160 分辨率'),
        '1080p' => array('name' => '1080p Full HD', 'description' => '1920x1080 分辨率'),
        '720p'  => array('name' => '720p HD', 'description' => '1280x720 分辨率'),
        '480p'  => array('name' => '480p SD', 'description' => '854x480 分辨率'),
        '360p'  => array('name' => '360p', 'description' => '640x360 分辨率'),
    );

    foreach ($default_qualities as $slug => $quality) {
        if (!term_exists($slug, 'video_quality')) {
            wp_insert_term($quality['name'], 'video_quality', array(
                'slug'        => $slug,
                'description' => $quality['description'],
            ));
        }
    }
}
add_action('after_switch_theme', 'weiruan_video_add_default_terms');

/**
 * 自定义视频列表列
 */
function weiruan_video_custom_columns($columns) {
    $new_columns = array();

    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['video_thumbnail'] = __('封面', 'weiruan-video');
        } elseif ($key === 'date') {
            $new_columns['video_duration'] = __('时长', 'weiruan-video');
            $new_columns['video_views'] = __('观看', 'weiruan-video');
            $new_columns['video_downloads'] = __('下载', 'weiruan-video');
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }

    return $new_columns;
}
add_filter('manage_video_posts_columns', 'weiruan_video_custom_columns');

/**
 * 填充自定义列内容
 */
function weiruan_video_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'video_thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(80, 45), array('style' => 'border-radius: 4px;'));
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'video_duration':
            $duration = get_post_meta($post_id, '_video_duration', true);
            echo $duration ? esc_html($duration) : '<span style="color: #999;">—</span>';
            break;

        case 'video_views':
            $views = get_post_meta($post_id, '_video_views', true);
            echo $views ? number_format_i18n($views) : '0';
            break;

        case 'video_downloads':
            $downloads = get_post_meta($post_id, '_video_downloads', true);
            echo $downloads ? number_format_i18n($downloads) : '0';
            break;
    }
}
add_action('manage_video_posts_custom_column', 'weiruan_video_custom_column_content', 10, 2);

/**
 * 使自定义列可排序
 */
function weiruan_video_sortable_columns($columns) {
    $columns['video_views'] = 'video_views';
    $columns['video_downloads'] = 'video_downloads';
    return $columns;
}
add_filter('manage_edit-video_sortable_columns', 'weiruan_video_sortable_columns');

/**
 * 处理自定义列排序
 */
function weiruan_video_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ($orderby === 'video_views') {
        $query->set('meta_key', '_video_views');
        $query->set('orderby', 'meta_value_num');
    } elseif ($orderby === 'video_downloads') {
        $query->set('meta_key', '_video_downloads');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'weiruan_video_column_orderby');

/**
 * 添加视频文章类型到主查询
 */
function weiruan_video_add_to_query($query) {
    if (is_home() && $query->is_main_query() && !is_admin()) {
        $query->set('post_type', array('post', 'video'));
    }
}
// add_action('pre_get_posts', 'weiruan_video_add_to_query');

/**
 * 修改视频归档标题
 */
function weiruan_video_archive_title($title) {
    if (is_post_type_archive('video')) {
        $title = __('视频库', 'weiruan-video');
    } elseif (is_tax('video_category')) {
        $title = single_term_title('', false);
    } elseif (is_tax('video_tag')) {
        $title = sprintf(__('标签: %s', 'weiruan-video'), single_term_title('', false));
    } elseif (is_tax('video_quality')) {
        $title = sprintf(__('质量: %s', 'weiruan-video'), single_term_title('', false));
    }
    return $title;
}
add_filter('get_the_archive_title', 'weiruan_video_archive_title');
