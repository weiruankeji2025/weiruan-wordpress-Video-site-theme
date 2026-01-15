<?php
/**
 * 视频归档页模板
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
    <?php weiruan_video_breadcrumbs(); ?>

    <div class="archive-header">
        <h1 class="archive-title">
            <?php
            if (is_tax('video_category')) {
                single_term_title();
            } elseif (is_tax('video_tag')) {
                printf(__('标签: %s', 'weiruan-video'), single_term_title('', false));
            } elseif (is_tax('video_quality')) {
                printf(__('质量: %s', 'weiruan-video'), single_term_title('', false));
            } else {
                _e('视频库', 'weiruan-video');
            }
            ?>
        </h1>

        <?php
        $term_description = term_description();
        if ($term_description) :
        ?>
        <div class="archive-description">
            <?php echo wp_kses_post($term_description); ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- 筛选和排序 -->
    <div class="video-filters">
        <div class="filter-categories">
            <span class="filter-label"><?php _e('分类:', 'weiruan-video'); ?></span>
            <a href="<?php echo get_post_type_archive_link('video'); ?>" class="filter-item <?php echo is_post_type_archive('video') ? 'active' : ''; ?>">
                <?php _e('全部', 'weiruan-video'); ?>
            </a>
            <?php
            $categories = weiruan_video_get_categories(array('number' => 10));
            if (!empty($categories) && !is_wp_error($categories)) :
                foreach ($categories as $cat) :
                    $is_active = is_tax('video_category', $cat->slug);
            ?>
                <a href="<?php echo get_term_link($cat); ?>" class="filter-item <?php echo $is_active ? 'active' : ''; ?>">
                    <?php echo esc_html($cat->name); ?>
                </a>
            <?php
                endforeach;
            endif;
            ?>
        </div>

        <div class="filter-sort">
            <span class="filter-label"><?php _e('排序:', 'weiruan-video'); ?></span>
            <select id="video-sort" class="sort-select">
                <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>><?php _e('最新发布', 'weiruan-video'); ?></option>
                <option value="views" <?php selected(get_query_var('orderby'), 'views'); ?>><?php _e('最多观看', 'weiruan-video'); ?></option>
                <option value="downloads" <?php selected(get_query_var('orderby'), 'downloads'); ?>><?php _e('最多下载', 'weiruan-video'); ?></option>
                <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>><?php _e('标题排序', 'weiruan-video'); ?></option>
            </select>
        </div>
    </div>

    <div class="content-area <?php echo !is_active_sidebar('sidebar-video') ? 'full-width' : ''; ?>">
        <div class="main-content">
            <?php if (have_posts()) : ?>
                <div class="video-grid" id="video-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php weiruan_video_card(); ?>
                    <?php endwhile; ?>
                </div>

                <?php weiruan_video_pagination(); ?>

            <?php else : ?>
                <div class="no-content">
                    <div class="no-content-icon">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor" style="opacity: 0.3;">
                            <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/>
                        </svg>
                    </div>
                    <h2><?php _e('暂无视频', 'weiruan-video'); ?></h2>
                    <p><?php _e('该分类下暂时没有视频内容。', 'weiruan-video'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('video'); ?>" class="btn">
                        <?php _e('浏览全部视频', 'weiruan-video'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (is_active_sidebar('sidebar-video')) : ?>
        <aside class="sidebar">
            <?php dynamic_sidebar('sidebar-video'); ?>
        </aside>
        <?php elseif (is_active_sidebar('sidebar-main')) : ?>
        <aside class="sidebar">
            <?php dynamic_sidebar('sidebar-main'); ?>
        </aside>
        <?php endif; ?>
    </div>
</div>

<style>
    .archive-header {
        text-align: center;
        padding: var(--spacing-xl) 0;
        margin-bottom: var(--spacing-lg);
    }

    .archive-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: var(--spacing-sm);
    }

    .archive-description {
        color: var(--text-muted);
        max-width: 600px;
        margin: 0 auto;
    }

    .video-filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        padding: var(--spacing-lg);
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-xl);
        border: 1px solid var(--border-color);
    }

    .filter-categories {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--spacing-sm);
    }

    .filter-label {
        color: var(--text-muted);
        font-weight: 500;
        margin-right: var(--spacing-sm);
    }

    .filter-item {
        padding: var(--spacing-xs) var(--spacing-md);
        background: var(--bg-card-hover);
        border-radius: var(--radius-lg);
        color: var(--text-secondary);
        font-size: 0.9rem;
        transition: all var(--transition-fast);
    }

    .filter-item:hover,
    .filter-item.active {
        background: var(--primary-color);
        color: white;
    }

    .filter-sort {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .sort-select {
        padding: var(--spacing-sm) var(--spacing-md);
        background: var(--bg-card-hover);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        font-size: 0.9rem;
        cursor: pointer;
    }

    .sort-select:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    .no-content {
        text-align: center;
        padding: var(--spacing-2xl);
    }

    .no-content-icon {
        margin-bottom: var(--spacing-lg);
    }

    .no-content h2 {
        margin-bottom: var(--spacing-sm);
    }

    .no-content p {
        color: var(--text-muted);
        margin-bottom: var(--spacing-lg);
    }

    @media (max-width: 768px) {
        .video-filters {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-categories {
            width: 100%;
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: var(--spacing-sm);
        }

        .filter-sort {
            width: 100%;
        }

        .sort-select {
            flex: 1;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('video-sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('orderby', this.value);
            window.location.href = url.toString();
        });
    }
});
</script>

<?php
get_footer();
