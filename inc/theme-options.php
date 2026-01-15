<?php
/**
 * 主题设置页面
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 添加主题设置菜单
 */
function weiruan_video_add_admin_menu() {
    add_menu_page(
        __('威软视频主题设置', 'weiruan-video'),
        __('视频主题设置', 'weiruan-video'),
        'manage_options',
        'weiruan-video-settings',
        'weiruan_video_settings_page',
        'dashicons-video-alt3',
        60
    );

    add_submenu_page(
        'weiruan-video-settings',
        __('常规设置', 'weiruan-video'),
        __('常规设置', 'weiruan-video'),
        'manage_options',
        'weiruan-video-settings',
        'weiruan_video_settings_page'
    );

    add_submenu_page(
        'weiruan-video-settings',
        __('播放器设置', 'weiruan-video'),
        __('播放器设置', 'weiruan-video'),
        'manage_options',
        'weiruan-video-player-settings',
        'weiruan_video_player_settings_page'
    );

    add_submenu_page(
        'weiruan-video-settings',
        __('下载设置', 'weiruan-video'),
        __('下载设置', 'weiruan-video'),
        'manage_options',
        'weiruan-video-download-settings',
        'weiruan_video_download_settings_page'
    );

    add_submenu_page(
        'weiruan-video-settings',
        __('广告设置', 'weiruan-video'),
        __('广告设置', 'weiruan-video'),
        'manage_options',
        'weiruan-video-ads-settings',
        'weiruan_video_ads_settings_page'
    );
}
add_action('admin_menu', 'weiruan_video_add_admin_menu');

/**
 * 注册设置
 */
function weiruan_video_register_settings() {
    // 常规设置
    register_setting('weiruan_video_general', 'weiruan_video_options');

    add_settings_section(
        'weiruan_video_general_section',
        __('常规设置', 'weiruan-video'),
        '__return_false',
        'weiruan-video-settings'
    );

    add_settings_field(
        'videos_per_page',
        __('每页显示视频数', 'weiruan-video'),
        'weiruan_video_number_field',
        'weiruan-video-settings',
        'weiruan_video_general_section',
        array('id' => 'videos_per_page', 'default' => 12)
    );

    add_settings_field(
        'enable_views_count',
        __('启用观看统计', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-settings',
        'weiruan_video_general_section',
        array('id' => 'enable_views_count', 'default' => true)
    );

    add_settings_field(
        'enable_download_count',
        __('启用下载统计', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-settings',
        'weiruan_video_general_section',
        array('id' => 'enable_download_count', 'default' => true)
    );

    add_settings_field(
        'enable_likes',
        __('启用点赞功能', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-settings',
        'weiruan_video_general_section',
        array('id' => 'enable_likes', 'default' => true)
    );

    add_settings_field(
        'footer_text',
        __('底部版权信息', 'weiruan-video'),
        'weiruan_video_textarea_field',
        'weiruan-video-settings',
        'weiruan_video_general_section',
        array('id' => 'footer_text', 'default' => '')
    );

    // 播放器设置
    register_setting('weiruan_video_player', 'weiruan_video_player_options');

    add_settings_section(
        'weiruan_video_player_section',
        __('播放器设置', 'weiruan-video'),
        '__return_false',
        'weiruan-video-player-settings'
    );

    add_settings_field(
        'default_quality',
        __('默认播放质量', 'weiruan-video'),
        'weiruan_video_select_field',
        'weiruan-video-player-settings',
        'weiruan_video_player_section',
        array(
            'id' => 'default_quality',
            'options' => array(
                'auto'  => __('自动', 'weiruan-video'),
                '1080p' => '1080p',
                '720p'  => '720p',
                '480p'  => '480p',
                '360p'  => '360p',
            ),
            'default' => 'auto'
        )
    );

    add_settings_field(
        'autoplay',
        __('自动播放', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-player-settings',
        'weiruan_video_player_section',
        array('id' => 'autoplay', 'default' => false)
    );

    add_settings_field(
        'player_color',
        __('播放器主题色', 'weiruan-video'),
        'weiruan_video_color_field',
        'weiruan-video-player-settings',
        'weiruan_video_player_section',
        array('id' => 'player_color', 'default' => '#6366f1')
    );

    // 下载设置
    register_setting('weiruan_video_download', 'weiruan_video_download_options');

    add_settings_section(
        'weiruan_video_download_section',
        __('下载设置', 'weiruan-video'),
        '__return_false',
        'weiruan-video-download-settings'
    );

    add_settings_field(
        'enable_download',
        __('启用下载功能', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-download-settings',
        'weiruan_video_download_section',
        array('id' => 'enable_download', 'default' => true)
    );

    add_settings_field(
        'require_login',
        __('需要登录才能下载', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-download-settings',
        'weiruan_video_download_section',
        array('id' => 'require_login', 'default' => false)
    );

    add_settings_field(
        'download_delay',
        __('下载延迟（秒）', 'weiruan-video'),
        'weiruan_video_number_field',
        'weiruan-video-download-settings',
        'weiruan_video_download_section',
        array('id' => 'download_delay', 'default' => 0)
    );

    add_settings_field(
        'show_file_size',
        __('显示文件大小', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-download-settings',
        'weiruan_video_download_section',
        array('id' => 'show_file_size', 'default' => true)
    );

    // 广告设置
    register_setting('weiruan_video_ads', 'weiruan_video_ads_options');

    add_settings_section(
        'weiruan_video_ads_section',
        __('广告设置', 'weiruan-video'),
        '__return_false',
        'weiruan-video-ads-settings'
    );

    add_settings_field(
        'header_ad',
        __('头部广告代码', 'weiruan-video'),
        'weiruan_video_textarea_field',
        'weiruan-video-ads-settings',
        'weiruan_video_ads_section',
        array('id' => 'header_ad', 'default' => '', 'rows' => 5)
    );

    add_settings_field(
        'sidebar_ad',
        __('侧边栏广告代码', 'weiruan-video'),
        'weiruan_video_textarea_field',
        'weiruan-video-ads-settings',
        'weiruan_video_ads_section',
        array('id' => 'sidebar_ad', 'default' => '', 'rows' => 5)
    );

    add_settings_field(
        'video_before_ad',
        __('视频前广告代码', 'weiruan-video'),
        'weiruan_video_textarea_field',
        'weiruan-video-ads-settings',
        'weiruan_video_ads_section',
        array('id' => 'video_before_ad', 'default' => '', 'rows' => 5)
    );

    add_settings_field(
        'video_after_ad',
        __('视频后广告代码', 'weiruan-video'),
        'weiruan_video_textarea_field',
        'weiruan-video-ads-settings',
        'weiruan_video_ads_section',
        array('id' => 'video_after_ad', 'default' => '', 'rows' => 5)
    );
}
add_action('admin_init', 'weiruan_video_register_settings');

/**
 * 设置字段回调函数
 */
function weiruan_video_number_field($args) {
    $options = get_option('weiruan_video_options');
    $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
    ?>
    <input type="number"
           name="weiruan_video_options[<?php echo esc_attr($args['id']); ?>]"
           value="<?php echo esc_attr($value); ?>"
           class="small-text"
           min="0">
    <?php
}

function weiruan_video_checkbox_field($args) {
    $option_group = 'weiruan_video_options';
    if (strpos($args['id'], 'player_') !== false || in_array($args['id'], array('autoplay', 'default_quality'))) {
        $option_group = 'weiruan_video_player_options';
    } elseif (strpos($args['id'], 'download') !== false || in_array($args['id'], array('enable_download', 'require_login', 'download_delay', 'show_file_size'))) {
        $option_group = 'weiruan_video_download_options';
    }
    $options = get_option($option_group);
    $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
    ?>
    <label>
        <input type="checkbox"
               name="<?php echo esc_attr($option_group); ?>[<?php echo esc_attr($args['id']); ?>]"
               value="1"
               <?php checked($value, true); ?>>
        <?php _e('启用', 'weiruan-video'); ?>
    </label>
    <?php
}

function weiruan_video_textarea_field($args) {
    $option_group = 'weiruan_video_options';
    if (strpos($args['id'], '_ad') !== false) {
        $option_group = 'weiruan_video_ads_options';
    }
    $options = get_option($option_group);
    $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
    $rows = isset($args['rows']) ? $args['rows'] : 3;
    ?>
    <textarea name="<?php echo esc_attr($option_group); ?>[<?php echo esc_attr($args['id']); ?>]"
              class="large-text"
              rows="<?php echo esc_attr($rows); ?>"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function weiruan_video_select_field($args) {
    $option_group = 'weiruan_video_player_options';
    $options = get_option($option_group);
    $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
    ?>
    <select name="<?php echo esc_attr($option_group); ?>[<?php echo esc_attr($args['id']); ?>]">
        <?php foreach ($args['options'] as $key => $label) : ?>
            <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function weiruan_video_color_field($args) {
    $option_group = 'weiruan_video_player_options';
    $options = get_option($option_group);
    $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
    ?>
    <input type="color"
           name="<?php echo esc_attr($option_group); ?>[<?php echo esc_attr($args['id']); ?>]"
           value="<?php echo esc_attr($value); ?>">
    <?php
}

/**
 * 常规设置页面
 */
function weiruan_video_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <div class="weiruan-admin-header" style="background: linear-gradient(135deg, #6366f1, #0ea5e9); color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="margin: 0; color: white;"><?php _e('威软视频下载站主题', 'weiruan-video'); ?></h2>
            <p style="margin: 10px 0 0; opacity: 0.9;"><?php _e('专业的WordPress视频下载站主题，功能强大，自定义能力出众。', 'weiruan-video'); ?></p>
            <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.7;"><?php printf(__('版本 %s | 威软科技出品', 'weiruan-video'), WEIRUAN_VIDEO_VERSION); ?></p>
        </div>

        <form action="options.php" method="post">
            <?php
            settings_fields('weiruan_video_general');
            do_settings_sections('weiruan-video-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * 播放器设置页面
 */
function weiruan_video_player_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('播放器设置', 'weiruan-video'); ?></h1>

        <form action="options.php" method="post">
            <?php
            settings_fields('weiruan_video_player');
            do_settings_sections('weiruan-video-player-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * 下载设置页面
 */
function weiruan_video_download_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('下载设置', 'weiruan-video'); ?></h1>

        <form action="options.php" method="post">
            <?php
            settings_fields('weiruan_video_download');
            do_settings_sections('weiruan-video-download-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * 广告设置页面
 */
function weiruan_video_ads_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('广告设置', 'weiruan-video'); ?></h1>

        <div class="notice notice-info">
            <p><?php _e('在这里添加广告代码，支持HTML、JavaScript广告代码。', 'weiruan-video'); ?></p>
        </div>

        <form action="options.php" method="post">
            <?php
            settings_fields('weiruan_video_ads');
            do_settings_sections('weiruan-video-ads-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * 获取主题选项
 */
function weiruan_video_get_option($key, $default = '') {
    $options = get_option('weiruan_video_options');
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * 获取播放器选项
 */
function weiruan_video_get_player_option($key, $default = '') {
    $options = get_option('weiruan_video_player_options');
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * 获取下载选项
 */
function weiruan_video_get_download_option($key, $default = '') {
    $options = get_option('weiruan_video_download_options');
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * 获取广告选项
 */
function weiruan_video_get_ads_option($key, $default = '') {
    $options = get_option('weiruan_video_ads_options');
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * 自定义器设置
 */
function weiruan_video_customize_register($wp_customize) {
    // 主题颜色面板
    $wp_customize->add_panel('weiruan_video_colors', array(
        'title'    => __('主题颜色', 'weiruan-video'),
        'priority' => 30,
    ));

    // 主色调
    $wp_customize->add_section('weiruan_video_primary_colors', array(
        'title'    => __('主色调', 'weiruan-video'),
        'panel'    => 'weiruan_video_colors',
        'priority' => 10,
    ));

    $wp_customize->add_setting('weiruan_primary_color', array(
        'default'           => '#6366f1',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'weiruan_primary_color', array(
        'label'    => __('主色调', 'weiruan-video'),
        'section'  => 'weiruan_video_primary_colors',
        'settings' => 'weiruan_primary_color',
    )));

    $wp_customize->add_setting('weiruan_secondary_color', array(
        'default'           => '#0ea5e9',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'weiruan_secondary_color', array(
        'label'    => __('辅助色', 'weiruan-video'),
        'section'  => 'weiruan_video_primary_colors',
        'settings' => 'weiruan_secondary_color',
    )));

    // 首页设置
    $wp_customize->add_section('weiruan_video_homepage', array(
        'title'    => __('首页设置', 'weiruan-video'),
        'priority' => 40,
    ));

    $wp_customize->add_setting('weiruan_hero_title', array(
        'default'           => __('高清视频下载站', 'weiruan-video'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('weiruan_hero_title', array(
        'label'   => __('首页标题', 'weiruan-video'),
        'section' => 'weiruan_video_homepage',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('weiruan_hero_subtitle', array(
        'default'           => __('海量高清视频资源，支持多种分辨率下载', 'weiruan-video'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('weiruan_hero_subtitle', array(
        'label'   => __('首页副标题', 'weiruan-video'),
        'section' => 'weiruan_video_homepage',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('weiruan_show_hero', array(
        'default'           => true,
        'sanitize_callback' => 'weiruan_video_sanitize_checkbox',
    ));

    $wp_customize->add_control('weiruan_show_hero', array(
        'label'   => __('显示首页横幅', 'weiruan-video'),
        'section' => 'weiruan_video_homepage',
        'type'    => 'checkbox',
    ));
}
add_action('customize_register', 'weiruan_video_customize_register');

/**
 * 复选框清理函数
 */
function weiruan_video_sanitize_checkbox($checked) {
    return ((isset($checked) && true === $checked) ? true : false);
}

/**
 * 输出自定义CSS
 */
function weiruan_video_customizer_css() {
    $primary_color = get_theme_mod('weiruan_primary_color', '#6366f1');
    $secondary_color = get_theme_mod('weiruan_secondary_color', '#0ea5e9');

    if ($primary_color !== '#6366f1' || $secondary_color !== '#0ea5e9') {
        ?>
        <style type="text/css">
            :root {
                --primary-color: <?php echo esc_attr($primary_color); ?>;
                --secondary-color: <?php echo esc_attr($secondary_color); ?>;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'weiruan_video_customizer_css');
