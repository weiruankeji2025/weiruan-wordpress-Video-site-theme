<?php
/**
 * 搜索结果模板
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

    <div class="search-header">
        <h1 class="search-title">
            <?php printf(__('搜索结果: "%s"', 'weiruan-video'), get_search_query()); ?>
        </h1>
        <p class="search-count">
            <?php
            global $wp_query;
            printf(
                _n('找到 %d 个结果', '找到 %d 个结果', $wp_query->found_posts, 'weiruan-video'),
                $wp_query->found_posts
            );
            ?>
        </p>
    </div>

    <!-- 搜索表单 -->
    <div class="search-form-wrapper">
        <form role="search" method="get" class="search-form large-search" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="hidden" name="post_type" value="video">
            <input type="search"
                   class="search-field"
                   placeholder="<?php esc_attr_e('输入关键词搜索...', 'weiruan-video'); ?>"
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

    <div class="content-area <?php echo !is_active_sidebar('sidebar-main') ? 'full-width' : ''; ?>">
        <div class="main-content">
            <?php if (have_posts()) : ?>
                <div class="video-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        if (get_post_type() === 'video') {
                            weiruan_video_card();
                        } else {
                            get_template_part('template-parts/content', 'search');
                        }
                        ?>
                    <?php endwhile; ?>
                </div>

                <?php weiruan_video_pagination(); ?>

            <?php else : ?>
                <div class="no-content">
                    <div class="no-content-icon">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor" style="opacity: 0.3;">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </div>
                    <h2><?php _e('未找到结果', 'weiruan-video'); ?></h2>
                    <p><?php _e('抱歉，没有找到与您搜索词匹配的内容。请尝试其他关键词。', 'weiruan-video'); ?></p>

                    <div class="search-suggestions">
                        <h3><?php _e('热门分类', 'weiruan-video'); ?></h3>
                        <div class="category-tabs">
                            <?php
                            $categories = weiruan_video_get_categories(array('number' => 8));
                            if (!empty($categories) && !is_wp_error($categories)) :
                                foreach ($categories as $cat) :
                            ?>
                                <a href="<?php echo get_term_link($cat); ?>" class="category-tab">
                                    <?php echo esc_html($cat->name); ?>
                                </a>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (is_active_sidebar('sidebar-main')) : ?>
        <aside class="sidebar">
            <?php dynamic_sidebar('sidebar-main'); ?>
        </aside>
        <?php endif; ?>
    </div>
</div>

<style>
    .search-header {
        text-align: center;
        padding: var(--spacing-xl) 0;
    }

    .search-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: var(--spacing-sm);
    }

    .search-count {
        color: var(--text-muted);
    }

    .search-form-wrapper {
        max-width: 600px;
        margin: 0 auto var(--spacing-xl);
    }

    .large-search {
        background: var(--bg-card);
        border-radius: var(--radius-xl);
        padding: var(--spacing-sm);
        display: flex;
        gap: var(--spacing-sm);
    }

    .large-search .search-field {
        flex: 1;
        padding: var(--spacing-md) var(--spacing-lg);
        font-size: 1rem;
    }

    .large-search .search-submit {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-md) var(--spacing-xl);
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        border-radius: var(--radius-lg);
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .large-search .search-submit:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-glow);
    }

    .search-suggestions {
        margin-top: var(--spacing-xl);
    }

    .search-suggestions h3 {
        margin-bottom: var(--spacing-md);
        color: var(--text-secondary);
    }
</style>

<?php
get_footer();
