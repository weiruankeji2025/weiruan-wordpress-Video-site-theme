<?php
/**
 * 模板函数
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 获取视频观看次数
 */
function weiruan_video_get_views($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $views = get_post_meta($post_id, '_video_views', true);
    return $views ? intval($views) : 0;
}

/**
 * 格式化数字（K/M格式）
 */
function weiruan_video_format_number($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}

/**
 * 获取视频下载次数
 */
function weiruan_video_get_downloads($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $downloads = get_post_meta($post_id, '_video_downloads', true);
    return $downloads ? intval($downloads) : 0;
}

/**
 * 获取视频点赞数
 */
function weiruan_video_get_likes($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $likes = get_post_meta($post_id, '_video_likes', true);
    return $likes ? intval($likes) : 0;
}

/**
 * 获取视频时长
 */
function weiruan_video_get_duration($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_video_duration', true);
}

/**
 * 获取视频文件列表
 */
function weiruan_video_get_files($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $files = get_post_meta($post_id, '_video_files', true);
    return is_array($files) ? $files : array();
}

/**
 * 获取预览视频URL
 */
function weiruan_video_get_preview_url($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $preview_url = get_post_meta($post_id, '_video_preview_url', true);

    if (empty($preview_url)) {
        // 如果没有设置预览URL，尝试获取最高分辨率的文件
        $files = weiruan_video_get_files($post_id);
        $priority = array('1080p', '720p', '480p', '360p', '4k');

        foreach ($priority as $res) {
            if (isset($files[$res]['url']) && !empty($files[$res]['url'])) {
                $preview_url = $files[$res]['url'];
                break;
            }
        }
    }

    return $preview_url;
}

/**
 * 获取视频海报
 */
function weiruan_video_get_poster($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $poster = get_post_meta($post_id, '_video_poster', true);

    if (empty($poster) && has_post_thumbnail($post_id)) {
        $poster = get_the_post_thumbnail_url($post_id, 'full');
    }

    return $poster;
}

/**
 * 获取最高可用分辨率
 */
function weiruan_video_get_best_quality($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $files = weiruan_video_get_files($post_id);
    $priority = array('4k', '1080p', '720p', '480p', '360p');

    foreach ($priority as $res) {
        if (isset($files[$res]['url']) && !empty($files[$res]['url'])) {
            return $res;
        }
    }

    return null;
}

/**
 * 获取视频质量标签
 */
function weiruan_video_get_quality_label($quality) {
    $labels = array(
        '4k'    => '4K',
        '1080p' => 'FHD',
        '720p'  => 'HD',
        '480p'  => 'SD',
        '360p'  => 'LD',
    );

    return isset($labels[$quality]) ? $labels[$quality] : $quality;
}

/**
 * 生成下载链接
 */
function weiruan_video_get_download_url($post_id, $resolution) {
    $nonce = wp_create_nonce('video_download_' . $post_id);

    return add_query_arg(array(
        'download_video' => '1',
        'video_id'       => $post_id,
        'resolution'     => $resolution,
        '_wpnonce'       => $nonce,
    ), home_url());
}

/**
 * 检查用户是否可以下载
 */
function weiruan_video_can_download() {
    $require_login = weiruan_video_get_download_option('require_login', false);

    if ($require_login && !is_user_logged_in()) {
        return false;
    }

    return true;
}

/**
 * 获取相关视频
 */
function weiruan_video_get_related_videos($post_id = null, $count = 4) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // 获取当前视频的分类
    $categories = wp_get_post_terms($post_id, 'video_category', array('fields' => 'ids'));
    $tags = wp_get_post_terms($post_id, 'video_tag', array('fields' => 'ids'));

    $args = array(
        'post_type'      => 'video',
        'posts_per_page' => $count,
        'post__not_in'   => array($post_id),
        'orderby'        => 'rand',
    );

    if (!empty($categories) || !empty($tags)) {
        $args['tax_query'] = array('relation' => 'OR');

        if (!empty($categories)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'video_category',
                'field'    => 'term_id',
                'terms'    => $categories,
            );
        }

        if (!empty($tags)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'video_tag',
                'field'    => 'term_id',
                'terms'    => $tags,
            );
        }
    }

    return new WP_Query($args);
}

/**
 * 获取热门视频
 */
function weiruan_video_get_popular_videos($count = 5, $days = 30) {
    $args = array(
        'post_type'      => 'video',
        'posts_per_page' => $count,
        'meta_key'       => '_video_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );

    if ($days > 0) {
        $args['date_query'] = array(
            array(
                'after' => $days . ' days ago',
            ),
        );
    }

    return new WP_Query($args);
}

/**
 * 获取最近视频
 */
function weiruan_video_get_recent_videos($count = 8) {
    return new WP_Query(array(
        'post_type'      => 'video',
        'posts_per_page' => $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
}

/**
 * 获取视频分类列表
 */
function weiruan_video_get_categories($args = array()) {
    $defaults = array(
        'taxonomy'   => 'video_category',
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
    );

    $args = wp_parse_args($args, $defaults);

    return get_terms($args);
}

/**
 * 面包屑导航
 */
function weiruan_video_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    $separator = '<span class="breadcrumb-separator">/</span>';
    $home = '<a href="' . home_url() . '">' . __('首页', 'weiruan-video') . '</a>';

    echo '<nav class="breadcrumbs">';
    echo $home;

    if (is_post_type_archive('video')) {
        echo $separator . __('视频库', 'weiruan-video');
    } elseif (is_tax('video_category')) {
        echo $separator . '<a href="' . get_post_type_archive_link('video') . '">' . __('视频库', 'weiruan-video') . '</a>';
        echo $separator . single_term_title('', false);
    } elseif (is_tax('video_tag')) {
        echo $separator . '<a href="' . get_post_type_archive_link('video') . '">' . __('视频库', 'weiruan-video') . '</a>';
        echo $separator . __('标签: ', 'weiruan-video') . single_term_title('', false);
    } elseif (is_singular('video')) {
        echo $separator . '<a href="' . get_post_type_archive_link('video') . '">' . __('视频库', 'weiruan-video') . '</a>';
        $categories = get_the_terms(get_the_ID(), 'video_category');
        if ($categories && !is_wp_error($categories)) {
            $category = $categories[0];
            echo $separator . '<a href="' . get_term_link($category) . '">' . $category->name . '</a>';
        }
        echo $separator . '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_single()) {
        $categories = get_the_category();
        if ($categories) {
            echo $separator . '<a href="' . get_category_link($categories[0]->term_id) . '">' . $categories[0]->name . '</a>';
        }
        echo $separator . '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_page()) {
        echo $separator . '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_search()) {
        echo $separator . sprintf(__('搜索结果: %s', 'weiruan-video'), get_search_query());
    } elseif (is_404()) {
        echo $separator . __('页面未找到', 'weiruan-video');
    } elseif (is_category()) {
        echo $separator . single_cat_title('', false);
    } elseif (is_tag()) {
        echo $separator . __('标签: ', 'weiruan-video') . single_tag_title('', false);
    } elseif (is_archive()) {
        echo $separator . get_the_archive_title();
    }

    echo '</nav>';
}

/**
 * 自定义分页
 */
function weiruan_video_pagination($query = null) {
    global $wp_query;

    if (!$query) {
        $query = $wp_query;
    }

    $total_pages = $query->max_num_pages;

    if ($total_pages <= 1) {
        return;
    }

    $current_page = max(1, get_query_var('paged'));

    echo '<nav class="pagination">';

    // 上一页
    if ($current_page > 1) {
        echo '<a href="' . get_pagenum_link($current_page - 1) . '" class="prev">&laquo;</a>';
    }

    // 页码
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    if ($start > 1) {
        echo '<a href="' . get_pagenum_link(1) . '">1</a>';
        if ($start > 2) {
            echo '<span class="dots">...</span>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i === $current_page) {
            echo '<span class="current">' . $i . '</span>';
        } else {
            echo '<a href="' . get_pagenum_link($i) . '">' . $i . '</a>';
        }
    }

    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            echo '<span class="dots">...</span>';
        }
        echo '<a href="' . get_pagenum_link($total_pages) . '">' . $total_pages . '</a>';
    }

    // 下一页
    if ($current_page < $total_pages) {
        echo '<a href="' . get_pagenum_link($current_page + 1) . '" class="next">&raquo;</a>';
    }

    echo '</nav>';
}

/**
 * 社交分享按钮
 */
function weiruan_video_social_share($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $url = urlencode(get_permalink($post_id));
    $title = urlencode(get_the_title($post_id));
    $thumbnail = urlencode(get_the_post_thumbnail_url($post_id, 'medium'));

    $share_links = array(
        'weibo'    => 'https://service.weibo.com/share/share.php?url=' . $url . '&title=' . $title . '&pic=' . $thumbnail,
        'qzone'    => 'https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' . $url . '&title=' . $title . '&pics=' . $thumbnail,
        'wechat'   => '#',
        'twitter'  => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
        'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
    );

    echo '<div class="social-share">';
    echo '<span class="share-label">' . __('分享:', 'weiruan-video') . '</span>';

    foreach ($share_links as $platform => $link) {
        $class = 'share-' . $platform;
        if ($platform === 'wechat') {
            echo '<button type="button" class="share-btn ' . $class . '" data-url="' . esc_url(get_permalink($post_id)) . '" title="' . __('分享到微信', 'weiruan-video') . '">';
        } else {
            echo '<a href="' . esc_url($link) . '" class="share-btn ' . $class . '" target="_blank" rel="noopener noreferrer">';
        }
        echo '<span class="share-icon icon-' . $platform . '"></span>';
        if ($platform === 'wechat') {
            echo '</button>';
        } else {
            echo '</a>';
        }
    }

    echo '</div>';
}

/**
 * 视频评分显示
 */
function weiruan_video_rating_display($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $rating = get_post_meta($post_id, '_video_rating', true);

    if (!$rating) {
        return;
    }

    $rating = floatval($rating);
    $full_stars = floor($rating / 2);
    $half_star = ($rating / 2) - $full_stars >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

    echo '<div class="video-rating">';
    echo '<span class="rating-stars">';

    for ($i = 0; $i < $full_stars; $i++) {
        echo '<span class="star full"></span>';
    }
    if ($half_star) {
        echo '<span class="star half"></span>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        echo '<span class="star empty"></span>';
    }

    echo '</span>';
    echo '<span class="rating-number">' . number_format($rating, 1) . '</span>';
    echo '</div>';
}

/**
 * 视频信息显示
 */
function weiruan_video_info_display($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $info = array(
        'duration'     => weiruan_video_get_duration($post_id),
        'language'     => get_post_meta($post_id, '_video_language', true),
        'subtitles'    => get_post_meta($post_id, '_video_subtitles', true),
        'director'     => get_post_meta($post_id, '_video_director', true),
        'actors'       => get_post_meta($post_id, '_video_actors', true),
        'release_date' => get_post_meta($post_id, '_video_release_date', true),
    );

    $language_labels = array(
        'chinese'  => __('中文', 'weiruan-video'),
        'english'  => __('英语', 'weiruan-video'),
        'japanese' => __('日语', 'weiruan-video'),
        'korean'   => __('韩语', 'weiruan-video'),
        'french'   => __('法语', 'weiruan-video'),
        'german'   => __('德语', 'weiruan-video'),
        'spanish'  => __('西班牙语', 'weiruan-video'),
        'other'    => __('其他', 'weiruan-video'),
    );

    echo '<div class="video-info-list">';

    if (!empty($info['duration'])) {
        echo '<div class="info-item"><span class="label">' . __('时长:', 'weiruan-video') . '</span><span class="value">' . esc_html($info['duration']) . '</span></div>';
    }

    if (!empty($info['language']) && isset($language_labels[$info['language']])) {
        echo '<div class="info-item"><span class="label">' . __('语言:', 'weiruan-video') . '</span><span class="value">' . esc_html($language_labels[$info['language']]) . '</span></div>';
    }

    if (!empty($info['subtitles'])) {
        echo '<div class="info-item"><span class="label">' . __('字幕:', 'weiruan-video') . '</span><span class="value">' . esc_html($info['subtitles']) . '</span></div>';
    }

    if (!empty($info['director'])) {
        echo '<div class="info-item"><span class="label">' . __('导演:', 'weiruan-video') . '</span><span class="value">' . esc_html($info['director']) . '</span></div>';
    }

    if (!empty($info['actors'])) {
        echo '<div class="info-item"><span class="label">' . __('演员:', 'weiruan-video') . '</span><span class="value">' . esc_html($info['actors']) . '</span></div>';
    }

    if (!empty($info['release_date'])) {
        echo '<div class="info-item"><span class="label">' . __('发布日期:', 'weiruan-video') . '</span><span class="value">' . esc_html($info['release_date']) . '</span></div>';
    }

    echo '</div>';
}
