<?php
/**
 * 访客统计功能
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 初始化访客统计
 */
function weiruan_video_init_visitor_stats() {
    // 创建数据库表（如果不存在）
    global $wpdb;
    $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        visitor_ip varchar(45) NOT NULL,
        visit_time datetime DEFAULT CURRENT_TIMESTAMP,
        page_url text,
        user_agent text,
        session_id varchar(64),
        PRIMARY KEY (id),
        KEY visitor_ip (visitor_ip),
        KEY visit_time (visit_time),
        KEY session_id (session_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'weiruan_video_init_visitor_stats');

/**
 * 确保表在插件激活时创建
 */
function weiruan_video_check_stats_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        weiruan_video_init_visitor_stats();
    }
}
add_action('init', 'weiruan_video_check_stats_table');

/**
 * 记录访客
 */
function weiruan_video_record_visitor() {
    // 不记录管理员和机器人
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }

    // 检测机器人
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (weiruan_video_is_bot($user_agent)) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

    // 获取访客信息
    $visitor_ip = weiruan_video_get_visitor_ip();
    $page_url = isset($_SERVER['REQUEST_URI']) ? esc_url_raw($_SERVER['REQUEST_URI']) : '';

    // 生成或获取会话ID
    if (!isset($_COOKIE['weiruan_session_id'])) {
        $session_id = md5($visitor_ip . time() . wp_rand());
        setcookie('weiruan_session_id', $session_id, time() + 1800, COOKIEPATH, COOKIE_DOMAIN); // 30分钟
    } else {
        $session_id = sanitize_text_field($_COOKIE['weiruan_session_id']);
        // 刷新cookie过期时间
        setcookie('weiruan_session_id', $session_id, time() + 1800, COOKIEPATH, COOKIE_DOMAIN);
    }

    // 检查是否在短时间内已记录（防止重复记录）
    $recent_visit = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name
        WHERE visitor_ip = %s AND visit_time > DATE_SUB(NOW(), INTERVAL 1 MINUTE)",
        $visitor_ip
    ));

    if ($recent_visit == 0) {
        // 插入访问记录
        $wpdb->insert(
            $table_name,
            array(
                'visitor_ip' => $visitor_ip,
                'visit_time' => current_time('mysql'),
                'page_url'   => $page_url,
                'user_agent' => $user_agent,
                'session_id' => $session_id,
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );

        // 更新总访问量
        $total_visits = get_option('weiruan_total_visits', 0);
        update_option('weiruan_total_visits', $total_visits + 1);
    } else {
        // 更新会话时间
        $wpdb->update(
            $table_name,
            array('visit_time' => current_time('mysql')),
            array('session_id' => $session_id),
            array('%s'),
            array('%s')
        );
    }
}
add_action('wp', 'weiruan_video_record_visitor');

/**
 * 获取访客IP
 */
function weiruan_video_get_visitor_ip() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return sanitize_text_field(trim($ip));
}

/**
 * 检测是否为机器人
 */
function weiruan_video_is_bot($user_agent) {
    $bots = array(
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
        'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
        'bot', 'spider', 'crawl', 'curl', 'wget', 'python', 'java'
    );

    $user_agent = strtolower($user_agent);

    foreach ($bots as $bot) {
        if (strpos($user_agent, $bot) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * 获取总访问量
 */
function weiruan_video_get_total_visits() {
    $total = get_option('weiruan_total_visits', 0);

    // 如果option不存在，从数据库统计
    if ($total == 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            update_option('weiruan_total_visits', $total);
        }
    }

    return intval($total);
}

/**
 * 获取今日访问量
 */
function weiruan_video_get_today_visits() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return 0;
    }

    $today = current_time('Y-m-d');
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT visitor_ip) FROM $table_name WHERE DATE(visit_time) = %s",
        $today
    ));

    return intval($count);
}

/**
 * 获取当前在线人数（最近15分钟内有活动的独立会话）
 */
function weiruan_video_get_online_count() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return 1; // 至少有当前访客
    }

    $count = $wpdb->get_var(
        "SELECT COUNT(DISTINCT session_id) FROM $table_name
        WHERE visit_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        AND session_id IS NOT NULL AND session_id != ''"
    );

    return max(1, intval($count)); // 至少返回1
}

/**
 * 获取昨日访问量
 */
function weiruan_video_get_yesterday_visits() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return 0;
    }

    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT visitor_ip) FROM $table_name WHERE DATE(visit_time) = %s",
        $yesterday
    ));

    return intval($count);
}

/**
 * 清理旧的访问记录（保留最近30天）
 */
function weiruan_video_cleanup_old_stats() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weiruan_visitor_stats';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $wpdb->query(
            "DELETE FROM $table_name WHERE visit_time < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
    }
}
add_action('wp_scheduled_delete', 'weiruan_video_cleanup_old_stats');

/**
 * 输出访客统计栏
 */
function weiruan_video_visitor_stats_bar() {
    $total_visits = weiruan_video_get_total_visits();
    $today_visits = weiruan_video_get_today_visits();
    $online_count = weiruan_video_get_online_count();
    $yesterday_visits = weiruan_video_get_yesterday_visits();
    ?>
    <div class="visitor-stats-bar">
        <div class="container">
            <div class="visitor-stats-inner">
                <div class="stat-item">
                    <span class="stat-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </span>
                    <span class="stat-label"><?php _e('当前在线', 'weiruan-video'); ?></span>
                    <span class="stat-value online-count" data-count="<?php echo esc_attr($online_count); ?>">
                        <span class="online-dot"></span>
                        <?php echo number_format_i18n($online_count); ?>
                    </span>
                </div>

                <div class="stat-divider"></div>

                <div class="stat-item">
                    <span class="stat-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                        </svg>
                    </span>
                    <span class="stat-label"><?php _e('今日访问', 'weiruan-video'); ?></span>
                    <span class="stat-value"><?php echo number_format_i18n($today_visits); ?></span>
                </div>

                <div class="stat-divider"></div>

                <div class="stat-item">
                    <span class="stat-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </span>
                    <span class="stat-label"><?php _e('昨日访问', 'weiruan-video'); ?></span>
                    <span class="stat-value"><?php echo number_format_i18n($yesterday_visits); ?></span>
                </div>

                <div class="stat-divider"></div>

                <div class="stat-item">
                    <span class="stat-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                        </svg>
                    </span>
                    <span class="stat-label"><?php _e('总访问量', 'weiruan-video'); ?></span>
                    <span class="stat-value total-visits"><?php echo number_format_i18n($total_visits); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * AJAX获取实时在线人数
 */
function weiruan_video_ajax_get_online_count() {
    wp_send_json_success(array(
        'online' => weiruan_video_get_online_count(),
        'today'  => weiruan_video_get_today_visits(),
        'total'  => weiruan_video_get_total_visits(),
    ));
}
add_action('wp_ajax_weiruan_get_online_count', 'weiruan_video_ajax_get_online_count');
add_action('wp_ajax_nopriv_weiruan_get_online_count', 'weiruan_video_ajax_get_online_count');

/**
 * 在主题设置中添加访客统计选项
 */
function weiruan_video_add_stats_settings() {
    add_settings_field(
        'enable_visitor_stats',
        __('启用访客统计', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-settings',
        'weiruan_video_general_section',
        array('id' => 'enable_visitor_stats', 'default' => true)
    );

    add_settings_field(
        'show_visitor_bar',
        __('显示访客统计栏', 'weiruan-video'),
        'weiruan_video_checkbox_field',
        'weiruan-video-settings',
        'weiruan_video_general_section',
        array('id' => 'show_visitor_bar', 'default' => true)
    );
}
add_action('admin_init', 'weiruan_video_add_stats_settings', 20);
