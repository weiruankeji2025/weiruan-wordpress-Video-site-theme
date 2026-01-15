<?php
/**
 * 404 错误页面模板
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
    <div class="error-404">
        <div class="error-code">404</div>
        <h1 class="error-title"><?php _e('页面未找到', 'weiruan-video'); ?></h1>
        <p class="error-message"><?php _e('抱歉，您访问的页面不存在或已被删除。', 'weiruan-video'); ?></p>

        <div class="error-search">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="hidden" name="post_type" value="video">
                <input type="search"
                       class="search-field"
                       placeholder="<?php esc_attr_e('搜索视频...', 'weiruan-video'); ?>"
                       name="s">
                <button type="submit" class="search-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </button>
            </form>
        </div>

        <div class="error-actions">
            <a href="<?php echo home_url('/'); ?>" class="btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <?php _e('返回首页', 'weiruan-video'); ?>
            </a>
            <a href="<?php echo get_post_type_archive_link('video'); ?>" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/>
                </svg>
                <?php _e('浏览视频库', 'weiruan-video'); ?>
            </a>
        </div>

        <!-- 推荐热门视频 -->
        <?php
        $popular_videos = weiruan_video_get_popular_videos(4);
        if ($popular_videos->have_posts()) :
        ?>
        <div class="error-recommendations">
            <h3><?php _e('热门视频推荐', 'weiruan-video'); ?></h3>
            <div class="video-grid">
                <?php while ($popular_videos->have_posts()) : $popular_videos->the_post(); ?>
                    <?php weiruan_video_card(); ?>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .error-404 {
        text-align: center;
        padding: var(--spacing-2xl) 0;
    }

    .error-search {
        max-width: 500px;
        margin: var(--spacing-xl) auto;
    }

    .error-search .search-form {
        display: flex;
        background: var(--bg-card);
        border-radius: var(--radius-xl);
        overflow: hidden;
    }

    .error-search .search-field {
        flex: 1;
        padding: var(--spacing-md) var(--spacing-lg);
    }

    .error-search .search-submit {
        padding: var(--spacing-md) var(--spacing-lg);
        background: var(--primary-color);
        color: white;
    }

    .error-actions {
        display: flex;
        justify-content: center;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-2xl);
    }

    .error-recommendations {
        margin-top: var(--spacing-2xl);
        padding-top: var(--spacing-2xl);
        border-top: 1px solid var(--border-color);
    }

    .error-recommendations h3 {
        margin-bottom: var(--spacing-lg);
    }

    .error-recommendations .video-grid {
        max-width: 1000px;
        margin: 0 auto;
    }

    @media (max-width: 480px) {
        .error-actions {
            flex-direction: column;
        }
    }
</style>

<?php
get_footer();
