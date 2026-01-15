<?php
/**
 * 模板标签
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 输出视频卡片
 */
function weiruan_video_card($post_id = null, $args = array()) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $defaults = array(
        'show_category' => true,
        'show_duration' => true,
        'show_quality'  => true,
        'show_views'    => true,
        'thumb_size'    => 'video-thumb-large',
    );

    $args = wp_parse_args($args, $defaults);

    $thumbnail = get_the_post_thumbnail_url($post_id, $args['thumb_size']);
    $duration = weiruan_video_get_duration($post_id);
    $views = weiruan_video_get_views($post_id);
    $best_quality = weiruan_video_get_best_quality($post_id);
    $categories = get_the_terms($post_id, 'video_category');
    ?>
    <article class="video-card" data-video-id="<?php echo esc_attr($post_id); ?>">
        <div class="video-thumbnail">
            <?php if ($thumbnail) : ?>
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" loading="lazy">
            <?php else : ?>
                <div class="no-thumbnail">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/>
                    </svg>
                </div>
            <?php endif; ?>

            <?php if ($args['show_quality'] && $best_quality) : ?>
                <span class="video-quality-badge <?php echo esc_attr($best_quality); ?>">
                    <?php echo esc_html(weiruan_video_get_quality_label($best_quality)); ?>
                </span>
            <?php endif; ?>

            <?php if ($args['show_duration'] && $duration) : ?>
                <span class="video-duration"><?php echo esc_html($duration); ?></span>
            <?php endif; ?>

            <a href="<?php echo get_permalink($post_id); ?>" class="video-play-overlay" aria-label="<?php _e('播放视频', 'weiruan-video'); ?>"></a>
        </div>

        <div class="video-info">
            <h3 class="video-title">
                <a href="<?php echo get_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a>
            </h3>

            <div class="video-meta">
                <?php if ($args['show_views']) : ?>
                    <span class="views">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                        </svg>
                        <?php echo weiruan_video_format_number($views); ?>
                    </span>
                <?php endif; ?>

                <span class="date">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                    </svg>
                    <?php echo get_the_date('Y-m-d', $post_id); ?>
                </span>
            </div>

            <?php if ($args['show_category'] && $categories && !is_wp_error($categories)) : ?>
                <a href="<?php echo get_term_link($categories[0]); ?>" class="video-category">
                    <?php echo esc_html($categories[0]->name); ?>
                </a>
            <?php endif; ?>
        </div>
    </article>
    <?php
}

/**
 * 输出视频播放器
 */
function weiruan_video_player($post_id = null, $args = array()) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $defaults = array(
        'autoplay' => get_post_meta($post_id, '_video_autoplay', true) === '1',
        'loop'     => get_post_meta($post_id, '_video_loop', true) === '1',
        'muted'    => get_post_meta($post_id, '_video_muted', true) === '1',
        'controls' => get_post_meta($post_id, '_video_controls', true) !== '0',
        'poster'   => weiruan_video_get_poster($post_id),
    );

    $args = wp_parse_args($args, $defaults);
    $video_url = weiruan_video_get_preview_url($post_id);
    $video_files = weiruan_video_get_files($post_id);

    if (empty($video_url)) {
        echo '<div class="video-player-error">' . __('视频文件不可用', 'weiruan-video') . '</div>';
        return;
    }
    ?>
    <div class="video-player-container">
        <div class="video-player" id="video-player-<?php echo esc_attr($post_id); ?>" data-video-id="<?php echo esc_attr($post_id); ?>">
            <video
                id="video-<?php echo esc_attr($post_id); ?>"
                class="video-element"
                <?php echo $args['autoplay'] ? 'autoplay' : ''; ?>
                <?php echo $args['loop'] ? 'loop' : ''; ?>
                <?php echo $args['muted'] ? 'muted' : ''; ?>
                <?php if ($args['poster']) : ?>poster="<?php echo esc_url($args['poster']); ?>"<?php endif; ?>
                playsinline
                preload="metadata"
            >
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                <?php _e('您的浏览器不支持HTML5视频播放', 'weiruan-video'); ?>
            </video>

            <!-- 自定义控制条 -->
            <div class="player-controls">
                <div class="progress-bar" id="progress-bar-<?php echo esc_attr($post_id); ?>">
                    <div class="progress" style="width: 0%;">
                        <div class="progress-buffered"></div>
                    </div>
                </div>

                <div class="controls-row">
                    <div class="controls-left">
                        <button type="button" class="control-btn play-btn" title="<?php _e('播放/暂停', 'weiruan-video'); ?>">
                            <svg class="icon-play" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <svg class="icon-pause" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                        </button>

                        <button type="button" class="control-btn volume-btn" title="<?php _e('静音/取消静音', 'weiruan-video'); ?>">
                            <svg class="icon-volume" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                            </svg>
                            <svg class="icon-mute" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                                <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                            </svg>
                        </button>

                        <input type="range" class="volume-slider" min="0" max="100" value="100" title="<?php _e('音量', 'weiruan-video'); ?>">

                        <span class="time-display">
                            <span class="current-time">0:00</span>
                            <span>/</span>
                            <span class="total-time">0:00</span>
                        </span>
                    </div>

                    <div class="controls-right">
                        <?php if (count($video_files) > 1) : ?>
                        <div class="quality-selector">
                            <button type="button" class="quality-btn">
                                <?php echo esc_html(weiruan_video_get_quality_label(weiruan_video_get_best_quality($post_id))); ?>
                            </button>
                            <div class="quality-menu">
                                <?php foreach ($video_files as $res => $file) : if (!empty($file['url'])) : ?>
                                    <button type="button" data-quality="<?php echo esc_attr($res); ?>" data-src="<?php echo esc_url($file['url']); ?>">
                                        <?php echo esc_html(weiruan_video_get_quality_label($res)); ?>
                                    </button>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <button type="button" class="control-btn pip-btn" title="<?php _e('画中画', 'weiruan-video'); ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 7h-8v6h8V7zm2-4H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/>
                            </svg>
                        </button>

                        <button type="button" class="control-btn fullscreen-btn" title="<?php _e('全屏', 'weiruan-video'); ?>">
                            <svg class="icon-fullscreen" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                            </svg>
                            <svg class="icon-fullscreen-exit" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                                <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 大播放按钮 -->
            <div class="big-play-button">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>

            <!-- 加载指示器 -->
            <div class="video-loading">
                <div class="loading-spinner"></div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 输出下载面板
 */
function weiruan_video_download_panel($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $video_files = weiruan_video_get_files($post_id);
    $can_download = weiruan_video_can_download();
    $download_enabled = weiruan_video_get_download_option('enable_download', true);
    $show_file_size = weiruan_video_get_download_option('show_file_size', true);

    if (!$download_enabled || empty($video_files)) {
        return;
    }

    $resolution_order = array('4k', '1080p', '720p', '480p', '360p');
    $resolution_labels = array(
        '4k'    => array('label' => '4K Ultra HD', 'badge' => 'uhd'),
        '1080p' => array('label' => '1080p Full HD', 'badge' => 'fhd'),
        '720p'  => array('label' => '720p HD', 'badge' => 'hd'),
        '480p'  => array('label' => '480p SD', 'badge' => ''),
        '360p'  => array('label' => '360p', 'badge' => ''),
    );
    ?>
    <div class="download-panel">
        <div class="download-panel-header">
            <div class="download-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                </svg>
            </div>
            <div>
                <h3 class="download-panel-title"><?php _e('下载视频', 'weiruan-video'); ?></h3>
                <p class="download-panel-subtitle"><?php _e('选择合适的分辨率下载', 'weiruan-video'); ?></p>
            </div>
        </div>

        <?php if (!$can_download) : ?>
            <div class="download-login-required">
                <p><?php _e('请登录后下载视频', 'weiruan-video'); ?></p>
                <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn"><?php _e('立即登录', 'weiruan-video'); ?></a>
            </div>
        <?php else : ?>
            <div class="download-options">
                <?php foreach ($resolution_order as $res) :
                    if (!isset($video_files[$res]) || empty($video_files[$res]['url'])) {
                        continue;
                    }
                    $file = $video_files[$res];
                    $label = isset($resolution_labels[$res]) ? $resolution_labels[$res] : array('label' => $res, 'badge' => '');
                    $download_url = weiruan_video_get_download_url($post_id, $res);
                ?>
                <div class="download-option">
                    <div class="download-info">
                        <span class="resolution-badge <?php echo esc_attr($label['badge']); ?>">
                            <?php echo esc_html(strtoupper($res)); ?>
                        </span>
                        <div class="download-details">
                            <span class="download-quality"><?php echo esc_html($label['label']); ?></span>
                            <span class="download-specs">
                                <?php echo esc_html(strtoupper($file['format'])); ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($show_file_size && !empty($file['size'])) : ?>
                        <span class="download-size"><?php echo esc_html($file['size']); ?></span>
                    <?php endif; ?>

                    <a href="<?php echo esc_url($download_url); ?>" class="download-btn" data-resolution="<?php echo esc_attr($res); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                        </svg>
                        <?php _e('下载', 'weiruan-video'); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($video_files) > 1) : ?>
            <div class="download-all">
                <button type="button" class="download-all-btn" data-video-id="<?php echo esc_attr($post_id); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                    <?php _e('下载全部分辨率', 'weiruan-video'); ?>
                </button>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 输出视频统计信息
 */
function weiruan_video_stats_display($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $views = weiruan_video_get_views($post_id);
    $downloads = weiruan_video_get_downloads($post_id);
    $likes = weiruan_video_get_likes($post_id);
    ?>
    <div class="video-stats">
        <div class="stat-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
            </svg>
            <span><?php printf(__('%s 次观看', 'weiruan-video'), number_format_i18n($views)); ?></span>
        </div>

        <div class="stat-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
            </svg>
            <span><?php printf(__('%s 次下载', 'weiruan-video'), number_format_i18n($downloads)); ?></span>
        </div>

        <div class="stat-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <span><?php printf(__('%s 次点赞', 'weiruan-video'), number_format_i18n($likes)); ?></span>
        </div>

        <div class="stat-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
            </svg>
            <span><?php echo get_the_date('Y-m-d', $post_id); ?></span>
        </div>
    </div>
    <?php
}

/**
 * 输出视频操作按钮
 */
function weiruan_video_action_buttons($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $likes = weiruan_video_get_likes($post_id);
    $is_liked = isset($_COOKIE['video_liked_' . $post_id]);
    ?>
    <div class="video-actions">
        <button type="button" class="action-btn like-btn <?php echo $is_liked ? 'liked' : ''; ?>" data-video-id="<?php echo esc_attr($post_id); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <span class="like-count"><?php echo weiruan_video_format_number($likes); ?></span>
        </button>

        <button type="button" class="action-btn share-btn" data-video-id="<?php echo esc_attr($post_id); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92z"/>
            </svg>
            <?php _e('分享', 'weiruan-video'); ?>
        </button>

        <button type="button" class="action-btn collect-btn" data-video-id="<?php echo esc_attr($post_id); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
            </svg>
            <?php _e('收藏', 'weiruan-video'); ?>
        </button>
    </div>
    <?php
}

/**
 * 输出相关视频
 */
function weiruan_video_related_display($post_id = null, $count = 4) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $related = weiruan_video_get_related_videos($post_id, $count);

    if (!$related->have_posts()) {
        return;
    }
    ?>
    <div class="related-videos">
        <div class="section-header">
            <h3 class="section-title"><?php _e('相关视频', 'weiruan-video'); ?></h3>
        </div>

        <div class="video-grid">
            <?php while ($related->have_posts()) : $related->the_post(); ?>
                <?php weiruan_video_card(); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php
}
