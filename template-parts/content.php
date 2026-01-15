<?php
/**
 * 文章内容模板部件
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('video-thumb-large'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="post-content">
        <header class="entry-header">
            <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>'); ?>

            <div class="entry-meta">
                <span class="posted-on">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/>
                    </svg>
                    <?php echo get_the_date(); ?>
                </span>
                <span class="byline">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <?php the_author(); ?>
                </span>
            </div>
        </header>

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div>

        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php _e('阅读更多', 'weiruan-video'); ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                </svg>
            </a>
        </footer>
    </div>
</article>

<style>
    .post-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: all var(--transition-normal);
    }

    .post-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary-color);
        box-shadow: var(--shadow-glow);
    }

    .post-thumbnail {
        aspect-ratio: 16/9;
        overflow: hidden;
    }

    .post-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform var(--transition-normal);
    }

    .post-card:hover .post-thumbnail img {
        transform: scale(1.05);
    }

    .post-content {
        padding: var(--spacing-lg);
    }

    .entry-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: var(--spacing-sm);
        line-height: 1.4;
    }

    .entry-title a {
        color: var(--text-primary);
    }

    .entry-title a:hover {
        color: var(--primary-light);
    }

    .entry-meta {
        display: flex;
        gap: var(--spacing-md);
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: var(--spacing-md);
    }

    .entry-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .entry-summary {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: var(--spacing-md);
    }

    .read-more {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--primary-light);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .read-more:hover {
        color: var(--primary-color);
    }
</style>
