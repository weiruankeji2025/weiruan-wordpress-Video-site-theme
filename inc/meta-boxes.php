<?php
/**
 * 视频自定义元数据框
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 注册元数据框
 */
function weiruan_video_add_meta_boxes() {
    // 视频文件元数据框
    add_meta_box(
        'weiruan_video_files',
        __('视频文件管理', 'weiruan-video'),
        'weiruan_video_files_callback',
        'video',
        'normal',
        'high'
    );

    // 视频信息元数据框
    add_meta_box(
        'weiruan_video_info',
        __('视频信息', 'weiruan-video'),
        'weiruan_video_info_callback',
        'video',
        'normal',
        'high'
    );

    // 视频播放设置
    add_meta_box(
        'weiruan_video_player_settings',
        __('播放器设置', 'weiruan-video'),
        'weiruan_video_player_settings_callback',
        'video',
        'side',
        'default'
    );

    // 视频统计
    add_meta_box(
        'weiruan_video_stats',
        __('视频统计', 'weiruan-video'),
        'weiruan_video_stats_callback',
        'video',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'weiruan_video_add_meta_boxes');

/**
 * 视频文件元数据框回调
 */
function weiruan_video_files_callback($post) {
    wp_nonce_field('weiruan_video_files_nonce', 'weiruan_video_files_nonce');

    $video_files = get_post_meta($post->ID, '_video_files', true);
    if (!is_array($video_files)) {
        $video_files = array();
    }

    // 预定义分辨率
    $resolutions = array(
        '4k'    => array('label' => '4K Ultra HD (2160p)', 'width' => 3840, 'height' => 2160),
        '1080p' => array('label' => '1080p Full HD', 'width' => 1920, 'height' => 1080),
        '720p'  => array('label' => '720p HD', 'width' => 1280, 'height' => 720),
        '480p'  => array('label' => '480p SD', 'width' => 854, 'height' => 480),
        '360p'  => array('label' => '360p', 'width' => 640, 'height' => 360),
    );
    ?>
    <div class="weiruan-video-files-wrapper">
        <p class="description"><?php _e('为不同分辨率添加视频文件，用户可以选择下载不同质量的视频。', 'weiruan-video'); ?></p>

        <table class="widefat video-files-table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th style="width: 150px;"><?php _e('分辨率', 'weiruan-video'); ?></th>
                    <th><?php _e('视频文件 URL', 'weiruan-video'); ?></th>
                    <th style="width: 100px;"><?php _e('文件大小', 'weiruan-video'); ?></th>
                    <th style="width: 100px;"><?php _e('格式', 'weiruan-video'); ?></th>
                    <th style="width: 80px;"><?php _e('操作', 'weiruan-video'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resolutions as $key => $res) :
                    $file_data = isset($video_files[$key]) ? $video_files[$key] : array();
                    $url = isset($file_data['url']) ? $file_data['url'] : '';
                    $size = isset($file_data['size']) ? $file_data['size'] : '';
                    $format = isset($file_data['format']) ? $file_data['format'] : 'mp4';
                ?>
                <tr>
                    <td>
                        <strong><?php echo esc_html($res['label']); ?></strong>
                        <br>
                        <small style="color: #666;"><?php echo $res['width'] . 'x' . $res['height']; ?></small>
                    </td>
                    <td>
                        <input type="text"
                               name="video_files[<?php echo $key; ?>][url]"
                               value="<?php echo esc_url($url); ?>"
                               class="widefat video-file-url"
                               placeholder="<?php _e('输入视频URL或点击选择文件', 'weiruan-video'); ?>">
                    </td>
                    <td>
                        <input type="text"
                               name="video_files[<?php echo $key; ?>][size]"
                               value="<?php echo esc_attr($size); ?>"
                               class="widefat"
                               placeholder="<?php _e('如: 1.5GB', 'weiruan-video'); ?>">
                    </td>
                    <td>
                        <select name="video_files[<?php echo $key; ?>][format]" class="widefat">
                            <option value="mp4" <?php selected($format, 'mp4'); ?>>MP4</option>
                            <option value="webm" <?php selected($format, 'webm'); ?>>WebM</option>
                            <option value="mkv" <?php selected($format, 'mkv'); ?>>MKV</option>
                            <option value="avi" <?php selected($format, 'avi'); ?>>AVI</option>
                            <option value="mov" <?php selected($format, 'mov'); ?>>MOV</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="button select-video-file" data-resolution="<?php echo $key; ?>">
                            <?php _e('选择', 'weiruan-video'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 15px; padding: 15px; background: #f0f0f1; border-radius: 4px;">
            <h4 style="margin: 0 0 10px;"><?php _e('预览视频（主播放器）', 'weiruan-video'); ?></h4>
            <p class="description"><?php _e('选择在页面上播放的默认视频文件URL', 'weiruan-video'); ?></p>
            <?php
            $preview_url = get_post_meta($post->ID, '_video_preview_url', true);
            ?>
            <input type="text"
                   name="video_preview_url"
                   id="video_preview_url"
                   value="<?php echo esc_url($preview_url); ?>"
                   class="widefat"
                   style="margin-top: 10px;"
                   placeholder="<?php _e('输入预览视频URL或从上方分辨率中选择', 'weiruan-video'); ?>">
            <button type="button" class="button" id="select-preview-video" style="margin-top: 10px;">
                <?php _e('从媒体库选择', 'weiruan-video'); ?>
            </button>
        </div>
    </div>

    <style>
        .video-files-table th,
        .video-files-table td {
            vertical-align: middle;
            padding: 12px 8px;
        }
        .video-files-table tbody tr:nth-child(odd) {
            background: #f9f9f9;
        }
    </style>
    <?php
}

/**
 * 视频信息元数据框回调
 */
function weiruan_video_info_callback($post) {
    wp_nonce_field('weiruan_video_info_nonce', 'weiruan_video_info_nonce');

    // 获取已保存的值
    $duration = get_post_meta($post->ID, '_video_duration', true);
    $source = get_post_meta($post->ID, '_video_source', true);
    $source_url = get_post_meta($post->ID, '_video_source_url', true);
    $release_date = get_post_meta($post->ID, '_video_release_date', true);
    $actors = get_post_meta($post->ID, '_video_actors', true);
    $director = get_post_meta($post->ID, '_video_director', true);
    $language = get_post_meta($post->ID, '_video_language', true);
    $subtitles = get_post_meta($post->ID, '_video_subtitles', true);
    $rating = get_post_meta($post->ID, '_video_rating', true);
    ?>
    <div class="weiruan-video-info-wrapper">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="video_duration"><?php _e('视频时长', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <input type="text"
                           name="video_duration"
                           id="video_duration"
                           value="<?php echo esc_attr($duration); ?>"
                           class="regular-text"
                           placeholder="<?php _e('如: 01:30:45 或 90分钟', 'weiruan-video'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_source"><?php _e('视频来源', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <input type="text"
                           name="video_source"
                           id="video_source"
                           value="<?php echo esc_attr($source); ?>"
                           class="regular-text"
                           placeholder="<?php _e('如: YouTube, Vimeo, 原创等', 'weiruan-video'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_source_url"><?php _e('原始链接', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <input type="url"
                           name="video_source_url"
                           id="video_source_url"
                           value="<?php echo esc_url($source_url); ?>"
                           class="large-text"
                           placeholder="<?php _e('视频原始来源链接', 'weiruan-video'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_release_date"><?php _e('发布日期', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <input type="date"
                           name="video_release_date"
                           id="video_release_date"
                           value="<?php echo esc_attr($release_date); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_director"><?php _e('导演/作者', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <input type="text"
                           name="video_director"
                           id="video_director"
                           value="<?php echo esc_attr($director); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_actors"><?php _e('演员/参与者', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <textarea name="video_actors"
                              id="video_actors"
                              class="large-text"
                              rows="2"
                              placeholder="<?php _e('多个演员用逗号分隔', 'weiruan-video'); ?>"><?php echo esc_textarea($actors); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_language"><?php _e('语言', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <select name="video_language" id="video_language" class="regular-text">
                        <option value=""><?php _e('-- 选择语言 --', 'weiruan-video'); ?></option>
                        <option value="chinese" <?php selected($language, 'chinese'); ?>><?php _e('中文', 'weiruan-video'); ?></option>
                        <option value="english" <?php selected($language, 'english'); ?>><?php _e('英语', 'weiruan-video'); ?></option>
                        <option value="japanese" <?php selected($language, 'japanese'); ?>><?php _e('日语', 'weiruan-video'); ?></option>
                        <option value="korean" <?php selected($language, 'korean'); ?>><?php _e('韩语', 'weiruan-video'); ?></option>
                        <option value="french" <?php selected($language, 'french'); ?>><?php _e('法语', 'weiruan-video'); ?></option>
                        <option value="german" <?php selected($language, 'german'); ?>><?php _e('德语', 'weiruan-video'); ?></option>
                        <option value="spanish" <?php selected($language, 'spanish'); ?>><?php _e('西班牙语', 'weiruan-video'); ?></option>
                        <option value="other" <?php selected($language, 'other'); ?>><?php _e('其他', 'weiruan-video'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_subtitles"><?php _e('字幕', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <input type="text"
                           name="video_subtitles"
                           id="video_subtitles"
                           value="<?php echo esc_attr($subtitles); ?>"
                           class="regular-text"
                           placeholder="<?php _e('如: 中文字幕, 英文字幕, 无字幕', 'weiruan-video'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="video_rating"><?php _e('评分', 'weiruan-video'); ?></label>
                </th>
                <td>
                    <input type="number"
                           name="video_rating"
                           id="video_rating"
                           value="<?php echo esc_attr($rating); ?>"
                           class="small-text"
                           min="0"
                           max="10"
                           step="0.1"
                           placeholder="0-10">
                    <span class="description"><?php _e('0-10分', 'weiruan-video'); ?></span>
                </td>
            </tr>
        </table>
    </div>
    <?php
}

/**
 * 播放器设置元数据框回调
 */
function weiruan_video_player_settings_callback($post) {
    wp_nonce_field('weiruan_video_player_nonce', 'weiruan_video_player_nonce');

    $autoplay = get_post_meta($post->ID, '_video_autoplay', true);
    $loop = get_post_meta($post->ID, '_video_loop', true);
    $muted = get_post_meta($post->ID, '_video_muted', true);
    $controls = get_post_meta($post->ID, '_video_controls', true);
    $poster = get_post_meta($post->ID, '_video_poster', true);

    // 默认显示控件
    if ($controls === '') {
        $controls = '1';
    }
    ?>
    <div class="weiruan-player-settings">
        <p>
            <label>
                <input type="checkbox" name="video_autoplay" value="1" <?php checked($autoplay, '1'); ?>>
                <?php _e('自动播放', 'weiruan-video'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="video_loop" value="1" <?php checked($loop, '1'); ?>>
                <?php _e('循环播放', 'weiruan-video'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="video_muted" value="1" <?php checked($muted, '1'); ?>>
                <?php _e('默认静音', 'weiruan-video'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="video_controls" value="1" <?php checked($controls, '1'); ?>>
                <?php _e('显示控制条', 'weiruan-video'); ?>
            </label>
        </p>

        <hr style="margin: 15px 0;">

        <p>
            <label for="video_poster"><strong><?php _e('视频海报', 'weiruan-video'); ?></strong></label>
        </p>
        <p>
            <input type="text"
                   name="video_poster"
                   id="video_poster"
                   value="<?php echo esc_url($poster); ?>"
                   class="widefat"
                   placeholder="<?php _e('海报图片URL', 'weiruan-video'); ?>">
        </p>
        <p>
            <button type="button" class="button" id="select-poster-image">
                <?php _e('选择图片', 'weiruan-video'); ?>
            </button>
        </p>
        <?php if ($poster) : ?>
        <p>
            <img src="<?php echo esc_url($poster); ?>" style="max-width: 100%; height: auto; border-radius: 4px;">
        </p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 视频统计元数据框回调
 */
function weiruan_video_stats_callback($post) {
    $views = get_post_meta($post->ID, '_video_views', true);
    $downloads = get_post_meta($post->ID, '_video_downloads', true);
    $likes = get_post_meta($post->ID, '_video_likes', true);

    $views = $views ? intval($views) : 0;
    $downloads = $downloads ? intval($downloads) : 0;
    $likes = $likes ? intval($likes) : 0;
    ?>
    <div class="weiruan-video-stats">
        <table style="width: 100%;">
            <tr>
                <td><strong><?php _e('观看次数', 'weiruan-video'); ?></strong></td>
                <td style="text-align: right;"><?php echo number_format_i18n($views); ?></td>
            </tr>
            <tr>
                <td><strong><?php _e('下载次数', 'weiruan-video'); ?></strong></td>
                <td style="text-align: right;"><?php echo number_format_i18n($downloads); ?></td>
            </tr>
            <tr>
                <td><strong><?php _e('点赞数', 'weiruan-video'); ?></strong></td>
                <td style="text-align: right;"><?php echo number_format_i18n($likes); ?></td>
            </tr>
        </table>

        <hr style="margin: 15px 0;">

        <p>
            <label>
                <input type="checkbox" name="reset_video_stats" value="1">
                <?php _e('重置统计数据', 'weiruan-video'); ?>
            </label>
        </p>
        <p class="description" style="color: #d63638;">
            <?php _e('勾选后保存将清零所有统计数据', 'weiruan-video'); ?>
        </p>
    </div>
    <?php
}

/**
 * 保存视频元数据
 */
function weiruan_video_save_meta($post_id) {
    // 检查自动保存
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 检查权限
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 验证nonce并保存视频文件
    if (isset($_POST['weiruan_video_files_nonce']) && wp_verify_nonce($_POST['weiruan_video_files_nonce'], 'weiruan_video_files_nonce')) {
        // 保存视频文件
        if (isset($_POST['video_files'])) {
            $video_files = array();
            foreach ($_POST['video_files'] as $resolution => $file_data) {
                if (!empty($file_data['url'])) {
                    $video_files[sanitize_key($resolution)] = array(
                        'url'    => esc_url_raw($file_data['url']),
                        'size'   => sanitize_text_field($file_data['size']),
                        'format' => sanitize_text_field($file_data['format']),
                    );
                }
            }
            update_post_meta($post_id, '_video_files', $video_files);
        }

        // 保存预览视频URL
        if (isset($_POST['video_preview_url'])) {
            update_post_meta($post_id, '_video_preview_url', esc_url_raw($_POST['video_preview_url']));
        }
    }

    // 验证nonce并保存视频信息
    if (isset($_POST['weiruan_video_info_nonce']) && wp_verify_nonce($_POST['weiruan_video_info_nonce'], 'weiruan_video_info_nonce')) {
        $info_fields = array(
            'video_duration'     => '_video_duration',
            'video_source'       => '_video_source',
            'video_source_url'   => '_video_source_url',
            'video_release_date' => '_video_release_date',
            'video_director'     => '_video_director',
            'video_actors'       => '_video_actors',
            'video_language'     => '_video_language',
            'video_subtitles'    => '_video_subtitles',
            'video_rating'       => '_video_rating',
        );

        foreach ($info_fields as $post_key => $meta_key) {
            if (isset($_POST[$post_key])) {
                if ($post_key === 'video_source_url') {
                    update_post_meta($post_id, $meta_key, esc_url_raw($_POST[$post_key]));
                } elseif ($post_key === 'video_rating') {
                    $rating = floatval($_POST[$post_key]);
                    $rating = max(0, min(10, $rating));
                    update_post_meta($post_id, $meta_key, $rating);
                } else {
                    update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
                }
            }
        }
    }

    // 验证nonce并保存播放器设置
    if (isset($_POST['weiruan_video_player_nonce']) && wp_verify_nonce($_POST['weiruan_video_player_nonce'], 'weiruan_video_player_nonce')) {
        $player_fields = array(
            'video_autoplay' => '_video_autoplay',
            'video_loop'     => '_video_loop',
            'video_muted'    => '_video_muted',
            'video_controls' => '_video_controls',
        );

        foreach ($player_fields as $post_key => $meta_key) {
            $value = isset($_POST[$post_key]) ? '1' : '0';
            update_post_meta($post_id, $meta_key, $value);
        }

        if (isset($_POST['video_poster'])) {
            update_post_meta($post_id, '_video_poster', esc_url_raw($_POST['video_poster']));
        }

        // 重置统计数据
        if (isset($_POST['reset_video_stats']) && $_POST['reset_video_stats'] === '1') {
            update_post_meta($post_id, '_video_views', 0);
            update_post_meta($post_id, '_video_downloads', 0);
            update_post_meta($post_id, '_video_likes', 0);
        }
    }
}
add_action('save_post_video', 'weiruan_video_save_meta');
