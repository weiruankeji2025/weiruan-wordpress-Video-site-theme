<?php
/**
 * 单个视频模板
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

    <div class="single-video-wrapper">
        <?php while (have_posts()) : the_post(); ?>

            <?php
            // 视频前广告
            $video_before_ad = weiruan_video_get_ads_option('video_before_ad', '');
            if (!empty($video_before_ad)) :
            ?>
            <div class="video-ad video-before-ad">
                <?php echo $video_before_ad; ?>
            </div>
            <?php endif; ?>

            <!-- 视频播放器 -->
            <?php weiruan_video_player(); ?>

            <!-- 视频详情 -->
            <div class="video-details">
                <div class="video-header">
                    <h1 class="video-main-title"><?php the_title(); ?></h1>
                    <?php weiruan_video_action_buttons(); ?>
                </div>

                <!-- 视频统计 -->
                <?php weiruan_video_stats_display(); ?>

                <!-- 视频分类和标签 -->
                <div class="video-taxonomies">
                    <?php
                    $categories = get_the_terms(get_the_ID(), 'video_category');
                    if ($categories && !is_wp_error($categories)) :
                    ?>
                    <div class="video-categories">
                        <span class="taxonomy-label"><?php _e('分类:', 'weiruan-video'); ?></span>
                        <?php foreach ($categories as $cat) : ?>
                            <a href="<?php echo get_term_link($cat); ?>" class="video-category">
                                <?php echo esc_html($cat->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php
                    $tags = get_the_terms(get_the_ID(), 'video_tag');
                    if ($tags && !is_wp_error($tags)) :
                    ?>
                    <div class="video-tags">
                        <span class="taxonomy-label"><?php _e('标签:', 'weiruan-video'); ?></span>
                        <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo get_term_link($tag); ?>" class="tag-link">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 视频信息 -->
                <div class="video-description">
                    <h3><?php _e('视频简介', 'weiruan-video'); ?></h3>
                    <?php the_content(); ?>

                    <?php weiruan_video_info_display(); ?>

                    <?php weiruan_video_rating_display(); ?>
                </div>
            </div>

            <!-- 下载面板 -->
            <?php weiruan_video_download_panel(); ?>

            <?php
            // 视频后广告
            $video_after_ad = weiruan_video_get_ads_option('video_after_ad', '');
            if (!empty($video_after_ad)) :
            ?>
            <div class="video-ad video-after-ad">
                <?php echo $video_after_ad; ?>
            </div>
            <?php endif; ?>

            <!-- 相关视频 -->
            <?php weiruan_video_related_display(get_the_ID(), 4); ?>

            <!-- 评论区 -->
            <?php if (comments_open() || get_comments_number()) : ?>
            <div class="comments-area">
                <?php comments_template(); ?>
            </div>
            <?php endif; ?>

        <?php endwhile; ?>
    </div>
</div>

<?php
get_footer();
