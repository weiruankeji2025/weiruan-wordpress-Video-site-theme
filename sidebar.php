<?php
/**
 * 侧边栏模板
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_active_sidebar('sidebar-main')) {
    return;
}
?>

<aside class="sidebar" role="complementary">
    <?php dynamic_sidebar('sidebar-main'); ?>
</aside>
