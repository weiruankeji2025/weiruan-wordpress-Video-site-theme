<?php
/**
 * 短代码
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 视频网格短代码
 * 使用: [video_grid count="8" category="" columns="4"]
 */
function weiruan_video_grid_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count'    => 8,
        'category' => '',
        'tag'      => '',
        'columns'  => 4,
        'orderby'  => 'date',
        'order'    => 'DESC',
    ), $atts, 'video_grid');

    $args = array(
        'post_type'      => 'video',
        'posts_per_page' => absint($atts['count']),
        'orderby'        => sanitize_key($atts['orderby']),
        'order'          => sanitize_key($atts['order']),
    );

    if (!empty($atts['category'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'video_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($atts['category']),
        );
    }

    if (!empty($atts['tag'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'video_tag',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($atts['tag']),
        );
    }

    $videos = new WP_Query($args);

    if (!$videos->have_posts()) {
        return '<p>' . __('暂无视频', 'weiruan-video') . '</p>';
    }

    $columns = absint($atts['columns']);
    $column_width = floor(100 / $columns) . '%';

    ob_start();
    ?>
    <div class="video-grid shortcode-grid" style="--grid-columns: <?php echo esc_attr($columns); ?>;">
        <?php while ($videos->have_posts()) : $videos->the_post(); ?>
            <?php weiruan_video_card(); ?>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <style>
        .shortcode-grid {
            display: grid;
            grid-template-columns: repeat(var(--grid-columns), 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 992px) {
            .shortcode-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .shortcode-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .shortcode-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('video_grid', 'weiruan_video_grid_shortcode');

/**
 * 单个视频播放器短代码
 * 使用: [video_player id="123"]
 */
function weiruan_video_player_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'       => 0,
        'autoplay' => 'false',
        'loop'     => 'false',
        'muted'    => 'false',
        'controls' => 'true',
    ), $atts, 'video_player');

    $video_id = absint($atts['id']);

    if (!$video_id) {
        return '<p>' . __('请指定视频ID', 'weiruan-video') . '</p>';
    }

    $video = get_post($video_id);

    if (!$video || $video->post_type !== 'video') {
        return '<p>' . __('视频不存在', 'weiruan-video') . '</p>';
    }

    ob_start();

    weiruan_video_player($video_id, array(
        'autoplay' => $atts['autoplay'] === 'true',
        'loop'     => $atts['loop'] === 'true',
        'muted'    => $atts['muted'] === 'true',
        'controls' => $atts['controls'] !== 'false',
    ));

    return ob_get_clean();
}
add_shortcode('video_player', 'weiruan_video_player_shortcode');

/**
 * 视频下载按钮短代码
 * 使用: [video_download id="123" resolution="1080p"]
 */
function weiruan_video_download_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'         => 0,
        'resolution' => '',
        'text'       => __('下载视频', 'weiruan-video'),
    ), $atts, 'video_download');

    $video_id = absint($atts['id']);

    if (!$video_id) {
        return '';
    }

    $files = weiruan_video_get_files($video_id);

    if (empty($files)) {
        return '<p>' . __('暂无下载', 'weiruan-video') . '</p>';
    }

    ob_start();

    if (!empty($atts['resolution']) && isset($files[$atts['resolution']])) {
        // 单个分辨率下载按钮
        $download_url = weiruan_video_get_download_url($video_id, $atts['resolution']);
        ?>
        <a href="<?php echo esc_url($download_url); ?>" class="download-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
            </svg>
            <?php echo esc_html($atts['text']); ?> (<?php echo esc_html(strtoupper($atts['resolution'])); ?>)
        </a>
        <?php
    } else {
        // 所有分辨率下载面板
        weiruan_video_download_panel($video_id);
    }

    return ob_get_clean();
}
add_shortcode('video_download', 'weiruan_video_download_shortcode');

/**
 * 热门视频短代码
 * 使用: [popular_videos count="6" days="30"]
 */
function weiruan_video_popular_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 6,
        'days'  => 30,
        'title' => __('热门视频', 'weiruan-video'),
    ), $atts, 'popular_videos');

    $videos = weiruan_video_get_popular_videos(absint($atts['count']), absint($atts['days']));

    if (!$videos->have_posts()) {
        return '<p>' . __('暂无热门视频', 'weiruan-video') . '</p>';
    }

    ob_start();
    ?>
    <div class="popular-videos-section">
        <?php if (!empty($atts['title'])) : ?>
            <div class="section-header">
                <h3 class="section-title"><?php echo esc_html($atts['title']); ?></h3>
            </div>
        <?php endif; ?>

        <div class="video-grid">
            <?php while ($videos->have_posts()) : $videos->the_post(); ?>
                <?php weiruan_video_card(); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('popular_videos', 'weiruan_video_popular_shortcode');

/**
 * 视频分类列表短代码
 * 使用: [video_categories show_count="true"]
 */
function weiruan_video_categories_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_count' => 'true',
        'columns'    => 4,
    ), $atts, 'video_categories');

    $categories = weiruan_video_get_categories();

    if (empty($categories) || is_wp_error($categories)) {
        return '<p>' . __('暂无分类', 'weiruan-video') . '</p>';
    }

    ob_start();
    ?>
    <div class="category-tabs" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
        <?php foreach ($categories as $category) : ?>
            <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-tab">
                <?php echo esc_html($category->name); ?>
                <?php if ($atts['show_count'] === 'true') : ?>
                    <span class="count">(<?php echo absint($category->count); ?>)</span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('video_categories', 'weiruan_video_categories_shortcode');

/**
 * 视频搜索框短代码
 * 使用: [video_search placeholder="搜索视频..."]
 */
function weiruan_video_search_shortcode($atts) {
    $atts = shortcode_atts(array(
        'placeholder' => __('搜索视频...', 'weiruan-video'),
    ), $atts, 'video_search');

    ob_start();
    ?>
    <form role="search" method="get" class="search-form video-search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="hidden" name="post_type" value="video">
        <input type="search"
               class="search-field"
               placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
               value="<?php echo get_search_query(); ?>"
               name="s">
        <button type="submit" class="search-submit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
        </button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('video_search', 'weiruan_video_search_shortcode');

/**
 * 视频信息短代码
 * 使用: [video_info id="123" field="duration"]
 */
function weiruan_video_info_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'    => 0,
        'field' => 'title',
    ), $atts, 'video_info');

    $video_id = absint($atts['id']);

    if (!$video_id) {
        $video_id = get_the_ID();
    }

    $video = get_post($video_id);

    if (!$video || $video->post_type !== 'video') {
        return '';
    }

    switch ($atts['field']) {
        case 'title':
            return esc_html($video->post_title);
        case 'duration':
            return esc_html(weiruan_video_get_duration($video_id));
        case 'views':
            return weiruan_video_format_number(weiruan_video_get_views($video_id));
        case 'downloads':
            return weiruan_video_format_number(weiruan_video_get_downloads($video_id));
        case 'likes':
            return weiruan_video_format_number(weiruan_video_get_likes($video_id));
        case 'date':
            return get_the_date('', $video_id);
        case 'quality':
            return esc_html(weiruan_video_get_quality_label(weiruan_video_get_best_quality($video_id)));
        default:
            return '';
    }
}
add_shortcode('video_info', 'weiruan_video_info_shortcode');

/**
 * 视频统计短代码
 * 使用: [video_stats]
 */
function weiruan_video_stats_shortcode($atts) {
    $atts = shortcode_atts(array(), $atts, 'video_stats');

    // 计算总统计
    global $wpdb;

    $total_videos = wp_count_posts('video')->publish;
    $total_views = $wpdb->get_var("SELECT SUM(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = '_video_views'");
    $total_downloads = $wpdb->get_var("SELECT SUM(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = '_video_downloads'");
    $total_categories = wp_count_terms('video_category');

    ob_start();
    ?>
    <div class="video-total-stats">
        <div class="stat-box">
            <span class="stat-number"><?php echo weiruan_video_format_number($total_videos); ?></span>
            <span class="stat-label"><?php _e('视频总数', 'weiruan-video'); ?></span>
        </div>
        <div class="stat-box">
            <span class="stat-number"><?php echo weiruan_video_format_number($total_views); ?></span>
            <span class="stat-label"><?php _e('总观看', 'weiruan-video'); ?></span>
        </div>
        <div class="stat-box">
            <span class="stat-number"><?php echo weiruan_video_format_number($total_downloads); ?></span>
            <span class="stat-label"><?php _e('总下载', 'weiruan-video'); ?></span>
        </div>
        <div class="stat-box">
            <span class="stat-number"><?php echo weiruan_video_format_number($total_categories); ?></span>
            <span class="stat-label"><?php _e('分类数', 'weiruan-video'); ?></span>
        </div>
    </div>
    <style>
        .video-total-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        .stat-box {
            background: var(--bg-card, #1a1a2e);
            border: 1px solid var(--border-color, #27273a);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color, #6366f1);
        }
        .stat-label {
            display: block;
            margin-top: 0.5rem;
            color: var(--text-muted, #71717a);
        }
        @media (max-width: 768px) {
            .video-total-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('video_stats', 'weiruan_video_stats_shortcode');
