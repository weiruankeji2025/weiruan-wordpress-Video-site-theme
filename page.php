<?php
/**
 * 页面模板
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

    <div class="content-area full-width">
        <div class="main-content">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                    <header class="page-header">
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="page-body">
                        <?php the_content(); ?>

                        <?php
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . __('页面:', 'weiruan-video'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <?php if (comments_open() || get_comments_number()) : ?>
                        <div class="comments-area">
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
    .page-content {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: var(--spacing-2xl);
        border: 1px solid var(--border-color);
    }

    .page-header {
        margin-bottom: var(--spacing-xl);
        padding-bottom: var(--spacing-lg);
        border-bottom: 1px solid var(--border-color);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
    }

    .page-body {
        line-height: 1.8;
        color: var(--text-secondary);
    }

    .page-body h2,
    .page-body h3,
    .page-body h4 {
        color: var(--text-primary);
        margin-top: var(--spacing-xl);
        margin-bottom: var(--spacing-md);
    }

    .page-body p {
        margin-bottom: var(--spacing-md);
    }

    .page-body ul,
    .page-body ol {
        margin-bottom: var(--spacing-md);
        padding-left: var(--spacing-xl);
    }

    .page-body li {
        margin-bottom: var(--spacing-sm);
    }

    .page-body a {
        color: var(--primary-light);
    }

    .page-body img {
        border-radius: var(--radius-md);
        margin: var(--spacing-lg) 0;
    }

    .page-body blockquote {
        border-left: 4px solid var(--primary-color);
        padding-left: var(--spacing-lg);
        margin: var(--spacing-lg) 0;
        color: var(--text-muted);
        font-style: italic;
    }

    .page-body code {
        background: var(--bg-card-hover);
        padding: 2px 6px;
        border-radius: var(--radius-sm);
        font-family: monospace;
    }

    .page-body pre {
        background: var(--bg-card-hover);
        padding: var(--spacing-lg);
        border-radius: var(--radius-md);
        overflow-x: auto;
    }

    .page-body pre code {
        background: transparent;
        padding: 0;
    }

    .page-links {
        margin-top: var(--spacing-xl);
        padding-top: var(--spacing-lg);
        border-top: 1px solid var(--border-color);
    }
</style>

<?php
get_footer();
