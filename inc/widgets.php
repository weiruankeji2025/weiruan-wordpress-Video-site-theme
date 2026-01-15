<?php
/**
 * 自定义小工具
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 热门视频小工具
 */
class Weiruan_Video_Popular_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'weiruan_video_popular',
            __('热门视频', 'weiruan-video'),
            array('description' => __('显示热门视频列表', 'weiruan-video'))
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('热门视频', 'weiruan-video');
        $count = !empty($instance['count']) ? absint($instance['count']) : 5;
        $days = !empty($instance['days']) ? absint($instance['days']) : 30;

        $videos = weiruan_video_get_popular_videos($count, $days);

        if (!$videos->have_posts()) {
            return;
        }

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        ?>
        <div class="popular-videos-list">
            <?php while ($videos->have_posts()) : $videos->the_post(); ?>
                <div class="popular-video-item">
                    <div class="popular-video-thumb">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('video-thumb-small'); ?>
                            </a>
                        <?php endif; ?>
                        <?php $duration = weiruan_video_get_duration(); ?>
                        <?php if ($duration) : ?>
                            <span class="video-duration"><?php echo esc_html($duration); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="popular-video-info">
                        <h4 class="popular-video-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="popular-video-meta">
                            <?php echo weiruan_video_format_number(weiruan_video_get_views()); ?> <?php _e('次观看', 'weiruan-video'); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('热门视频', 'weiruan-video');
        $count = !empty($instance['count']) ? absint($instance['count']) : 5;
        $days = !empty($instance['days']) ? absint($instance['days']) : 30;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('标题:', 'weiruan-video'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e('显示数量:', 'weiruan-video'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="number" value="<?php echo esc_attr($count); ?>" min="1" max="20">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('days')); ?>"><?php _e('统计天数 (0为全部):', 'weiruan-video'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('days')); ?>" name="<?php echo esc_attr($this->get_field_name('days')); ?>" type="number" value="<?php echo esc_attr($days); ?>" min="0">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? absint($new_instance['count']) : 5;
        $instance['days'] = isset($new_instance['days']) ? absint($new_instance['days']) : 30;
        return $instance;
    }
}

/**
 * 视频分类小工具
 */
class Weiruan_Video_Categories_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'weiruan_video_categories',
            __('视频分类', 'weiruan-video'),
            array('description' => __('显示视频分类列表', 'weiruan-video'))
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('视频分类', 'weiruan-video');
        $show_count = !empty($instance['show_count']);

        $categories = weiruan_video_get_categories(array('hide_empty' => true));

        if (empty($categories) || is_wp_error($categories)) {
            return;
        }

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        ?>
        <ul class="category-list">
            <?php foreach ($categories as $category) : ?>
                <li class="category-item">
                    <a href="<?php echo esc_url(get_term_link($category)); ?>">
                        <?php echo esc_html($category->name); ?>
                    </a>
                    <?php if ($show_count) : ?>
                        <span class="category-count"><?php echo absint($category->count); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('视频分类', 'weiruan-video');
        $show_count = !empty($instance['show_count']);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('标题:', 'weiruan-video'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" <?php checked($show_count); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>"><?php _e('显示视频数量', 'weiruan-video'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_count'] = !empty($new_instance['show_count']);
        return $instance;
    }
}

/**
 * 视频标签云小工具
 */
class Weiruan_Video_Tags_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'weiruan_video_tags',
            __('视频标签云', 'weiruan-video'),
            array('description' => __('显示视频标签云', 'weiruan-video'))
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('热门标签', 'weiruan-video');
        $count = !empty($instance['count']) ? absint($instance['count']) : 20;

        $tags = get_terms(array(
            'taxonomy'   => 'video_tag',
            'hide_empty' => true,
            'number'     => $count,
            'orderby'    => 'count',
            'order'      => 'DESC',
        ));

        if (empty($tags) || is_wp_error($tags)) {
            return;
        }

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        ?>
        <div class="tag-cloud">
            <?php foreach ($tags as $tag) : ?>
                <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="tag-link">
                    <?php echo esc_html($tag->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('热门标签', 'weiruan-video');
        $count = !empty($instance['count']) ? absint($instance['count']) : 20;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('标题:', 'weiruan-video'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e('显示数量:', 'weiruan-video'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="number" value="<?php echo esc_attr($count); ?>" min="1" max="50">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? absint($new_instance['count']) : 20;
        return $instance;
    }
}

/**
 * 最新视频小工具
 */
class Weiruan_Video_Recent_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'weiruan_video_recent',
            __('最新视频', 'weiruan-video'),
            array('description' => __('显示最新发布的视频', 'weiruan-video'))
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('最新视频', 'weiruan-video');
        $count = !empty($instance['count']) ? absint($instance['count']) : 5;

        $videos = weiruan_video_get_recent_videos($count);

        if (!$videos->have_posts()) {
            return;
        }

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        ?>
        <div class="popular-videos-list">
            <?php while ($videos->have_posts()) : $videos->the_post(); ?>
                <div class="popular-video-item">
                    <div class="popular-video-thumb">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('video-thumb-small'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="popular-video-info">
                        <h4 class="popular-video-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="popular-video-meta">
                            <?php echo get_the_date(); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('最新视频', 'weiruan-video');
        $count = !empty($instance['count']) ? absint($instance['count']) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('标题:', 'weiruan-video'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e('显示数量:', 'weiruan-video'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="number" value="<?php echo esc_attr($count); ?>" min="1" max="20">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? absint($new_instance['count']) : 5;
        return $instance;
    }
}

/**
 * 注册小工具
 */
function weiruan_video_register_widgets() {
    register_widget('Weiruan_Video_Popular_Widget');
    register_widget('Weiruan_Video_Categories_Widget');
    register_widget('Weiruan_Video_Tags_Widget');
    register_widget('Weiruan_Video_Recent_Widget');
}
add_action('widgets_init', 'weiruan_video_register_widgets');
