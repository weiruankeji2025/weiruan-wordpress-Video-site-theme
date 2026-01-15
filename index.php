<?php
/**
 * 主模板文件
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container">
    <?php if (is_front_page() && get_theme_mod('weiruan_show_hero', true)) : ?>
    <!-- 首页横幅 -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                <?php echo esc_html(get_theme_mod('weiruan_hero_title', __('高清视频下载站', 'weiruan-video'))); ?>
            </h1>
            <p class="hero-subtitle">
                <?php echo esc_html(get_theme_mod('weiruan_hero_subtitle', __('海量高清视频资源，支持多种分辨率下载', 'weiruan-video'))); ?>
            </p>

            <div class="hero-search">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="hidden" name="post_type" value="video">
                    <input type="search"
                           class="search-field"
                           placeholder="<?php esc_attr_e('搜索您想要的视频...', 'weiruan-video'); ?>"
                           value="<?php echo get_search_query(); ?>"
                           name="s">
                    <button type="submit" class="search-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <?php _e('搜索', 'weiruan-video'); ?>
                    </button>
                </form>
            </div>

            <!-- 分类标签 -->
            <?php
            $categories = weiruan_video_get_categories(array('number' => 8));
            if (!empty($categories) && !is_wp_error($categories)) :
            ?>
            <div class="category-tabs">
                <a href="<?php echo get_post_type_archive_link('video'); ?>" class="category-tab active">
                    <?php _e('全部', 'weiruan-video'); ?>
                </a>
                <?php foreach ($categories as $cat) : ?>
                    <a href="<?php echo get_term_link($cat); ?>" class="category-tab">
                        <?php echo esc_html($cat->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <div class="content-area <?php echo !is_active_sidebar('sidebar-main') ? 'full-width' : ''; ?>">
        <div class="main-content">
            <?php if (!is_front_page()) : ?>
                <?php weiruan_video_breadcrumbs(); ?>
            <?php endif; ?>

            <?php if (is_front_page()) : ?>
                <!-- 热门视频区块 -->
                <?php
                $popular_videos = weiruan_video_get_popular_videos(8);
                if ($popular_videos->have_posts()) :
                ?>
                <section class="video-section">
                    <div class="section-header">
                        <h2 class="section-title"><?php _e('热门视频', 'weiruan-video'); ?></h2>
                        <a href="<?php echo add_query_arg('orderby', 'views', get_post_type_archive_link('video')); ?>" class="view-all-link">
                            <?php _e('查看更多', 'weiruan-video'); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="video-grid">
                        <?php while ($popular_videos->have_posts()) : $popular_videos->the_post(); ?>
                            <?php weiruan_video_card(); ?>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 最新视频区块 -->
                <?php
                $recent_videos = weiruan_video_get_recent_videos(12);
                if ($recent_videos->have_posts()) :
                ?>
                <section class="video-section">
                    <div class="section-header">
                        <h2 class="section-title"><?php _e('最新视频', 'weiruan-video'); ?></h2>
                        <a href="<?php echo get_post_type_archive_link('video'); ?>" class="view-all-link">
                            <?php _e('查看更多', 'weiruan-video'); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="video-grid">
                        <?php while ($recent_videos->have_posts()) : $recent_videos->the_post(); ?>
                            <?php weiruan_video_card(); ?>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
                <?php endif; ?>

            <?php else : ?>
                <!-- 非首页显示文章循环 -->
                <?php if (have_posts()) : ?>
                    <div class="video-grid">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php
                            if (get_post_type() === 'video') {
                                weiruan_video_card();
                            } else {
                                get_template_part('template-parts/content', get_post_type());
                            }
                            ?>
                        <?php endwhile; ?>
                    </div>

                    <?php weiruan_video_pagination(); ?>

                <?php else : ?>
                    <div class="no-content">
                        <h2><?php _e('暂无内容', 'weiruan-video'); ?></h2>
                        <p><?php _e('抱歉，没有找到相关内容。', 'weiruan-video'); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if (is_active_sidebar('sidebar-main')) : ?>
        <aside class="sidebar">
            <?php dynamic_sidebar('sidebar-main'); ?>
        </aside>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
