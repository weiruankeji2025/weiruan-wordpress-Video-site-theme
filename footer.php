<?php
/**
 * 底部模板
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
    </main><!-- .site-content -->

    <?php
    // 显示访客统计栏
    $show_visitor_bar = weiruan_video_get_option('show_visitor_bar', true);
    if ($show_visitor_bar !== false && $show_visitor_bar !== '0') {
        weiruan_video_visitor_stats_bar();
    }
    ?>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <?php if (has_custom_logo()) : ?>
                        <div class="site-logo">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php else : ?>
                        <div class="site-logo">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <span class="footer-brand-name"><?php bloginfo('name'); ?></span>
                    <p class="footer-description">
                        <?php echo esc_html(get_bloginfo('description')); ?>
                    </p>
                </div>

                <?php if (is_active_sidebar('footer-1')) : ?>
                <div class="footer-section">
                    <?php dynamic_sidebar('footer-1'); ?>
                </div>
                <?php else : ?>
                <div class="footer-section">
                    <h4><?php _e('快速链接', 'weiruan-video'); ?></h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo home_url('/'); ?>"><?php _e('首页', 'weiruan-video'); ?></a></li>
                        <li><a href="<?php echo get_post_type_archive_link('video'); ?>"><?php _e('视频库', 'weiruan-video'); ?></a></li>
                        <?php
                        $categories = weiruan_video_get_categories(array('number' => 5));
                        if (!empty($categories) && !is_wp_error($categories)) :
                            foreach ($categories as $cat) :
                        ?>
                            <li><a href="<?php echo get_term_link($cat); ?>"><?php echo esc_html($cat->name); ?></a></li>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (is_active_sidebar('footer-2')) : ?>
                <div class="footer-section">
                    <?php dynamic_sidebar('footer-2'); ?>
                </div>
                <?php else : ?>
                <div class="footer-section">
                    <h4><?php _e('帮助中心', 'weiruan-video'); ?></h4>
                    <ul class="footer-links">
                        <li><a href="#"><?php _e('使用说明', 'weiruan-video'); ?></a></li>
                        <li><a href="#"><?php _e('常见问题', 'weiruan-video'); ?></a></li>
                        <li><a href="#"><?php _e('联系我们', 'weiruan-video'); ?></a></li>
                        <li><a href="#"><?php _e('隐私政策', 'weiruan-video'); ?></a></li>
                        <li><a href="#"><?php _e('服务条款', 'weiruan-video'); ?></a></li>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (is_active_sidebar('footer-3')) : ?>
                <div class="footer-section">
                    <?php dynamic_sidebar('footer-3'); ?>
                </div>
                <?php else : ?>
                <div class="footer-section">
                    <h4><?php _e('联系方式', 'weiruan-video'); ?></h4>
                    <ul class="footer-links">
                        <li><?php _e('邮箱: support@example.com', 'weiruan-video'); ?></li>
                        <li><?php _e('工作时间: 9:00-18:00', 'weiruan-video'); ?></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="footer-bottom">
                <div class="copyright">
                    <?php
                    $footer_text = weiruan_video_get_option('footer_text', '');
                    if (!empty($footer_text)) {
                        echo wp_kses_post($footer_text);
                    } else {
                        printf(
                            /* translators: %1$s: Year, %2$s: Site name, %3$s: Theme name */
                            __('&copy; %1$s %2$s. 主题由 %3$s 提供支持。', 'weiruan-video'),
                            date('Y'),
                            get_bloginfo('name'),
                            '<a href="https://www.weiruan.com" target="_blank" rel="noopener">威软视频下载站主题</a>'
                        );
                    }
                    ?>
                </div>

                <div class="footer-social">
                    <a href="#" class="social-link" title="微信" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 0 0 .167-.054l1.903-1.114a.864.864 0 0 1 .717-.098 10.16 10.16 0 0 0 2.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178A1.17 1.17 0 0 1 4.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178 1.17 1.17 0 0 1-1.162-1.178c0-.651.52-1.18 1.162-1.18zm5.34 2.867c-1.797-.052-3.746.512-5.28 1.786-1.72 1.428-2.687 3.72-1.78 6.22.942 2.453 3.666 4.229 6.884 4.229.826 0 1.622-.12 2.361-.336a.722.722 0 0 1 .598.082l1.584.926a.272.272 0 0 0 .14.047c.134 0 .24-.111.24-.247 0-.06-.023-.12-.038-.177l-.327-1.233a.582.582 0 0 1-.023-.156.49.49 0 0 1 .201-.398C23.024 18.48 24 16.82 24 14.98c0-3.21-2.931-5.837-6.656-6.088v-.002c-.135-.01-.27-.018-.406-.032zm-2.53 3.274c.535 0 .969.44.969.982a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.982.97-.982zm4.844 0c.535 0 .969.44.969.982a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.982.969-.982z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link" title="微博" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10.098 20.323c-3.977.391-7.414-1.406-7.672-4.02-.259-2.609 2.759-5.047 6.74-5.441 3.979-.394 7.413 1.404 7.671 4.018.259 2.6-2.759 5.049-6.737 5.443h-.002zm-.921-7.381c-2.705.276-4.577 1.963-4.182 3.77.395 1.806 2.725 2.932 5.432 2.656 2.704-.274 4.577-1.964 4.183-3.77-.392-1.806-2.726-2.932-5.433-2.656zM9.874 3.27c-.752-.078-1.478.163-2.047.677-.57.514-.878 1.218-.866 1.982.013.764.339 1.456.919 1.948.579.492 1.332.724 2.084.639.752-.086 1.428-.442 1.906-.999.478-.557.709-1.287.649-2.052-.059-.764-.401-1.472-.96-1.993-.558-.521-1.303-.778-2.059-.851a3.278 3.278 0 0 0-.374.023l.002-.002-.254.028zm1.303 3.247a1.18 1.18 0 0 1-.725.396c-.289.033-.568-.062-.786-.266a1.18 1.18 0 0 1-.352-.774c-.005-.293.115-.574.338-.79.223-.216.534-.334.873-.33.339.003.646.129.866.353.22.225.336.52.329.815-.007.296-.129.577-.35.799l-.193-.203zm10.502 2.163c-.427-.12-.827.088-.893.467-.066.379.216.766.631.864.415.098.8-.136.861-.515.062-.379-.201-.748-.599-.816zm-1.403-.311c-1.165-.263-2.465.17-3.129 1.166-.686 1.026-.434 2.275.605 2.855 1.091.608 2.563.282 3.313-.728.739-.993.541-2.227-.517-2.924a2.074 2.074 0 0 0-.272-.369zm-1.119 2.442c-.34.349-.833.466-1.238.292-.406-.174-.599-.599-.486-.978.113-.379.455-.675.86-.747.405-.072.815.068 1.029.352.215.284.177.694-.165 1.081z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link" title="QQ" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.003 2c-2.265 0-6.29 1.364-6.29 7.325v1.195S3.55 14.96 3.55 17.474c0 .665.17 1.025.281 1.025.114 0 .902-.484 1.748-2.072 0 0-.18 2.197 1.904 3.967 0 0-1.77.495-1.77 1.182 0 .686 4.078.43 6.29.43 2.212 0 6.29.256 6.29-.43 0-.687-1.77-1.182-1.77-1.182 2.085-1.77 1.905-3.967 1.905-3.967.845 1.588 1.634 2.072 1.746 2.072.111 0 .283-.36.283-1.025 0-2.514-2.166-6.954-2.166-6.954V9.325C18.29 3.364 14.268 2 12.003 2z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link" title="GitHub" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0 1 12 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</div><!-- .site-wrapper -->

<?php wp_footer(); ?>

</body>
</html>
