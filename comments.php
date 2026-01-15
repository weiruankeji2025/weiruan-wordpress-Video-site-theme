<?php
/**
 * 评论模板
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// 如果当前帖子受密码保护且访客尚未输入密码
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-section">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comments_count = get_comments_number();
            printf(
                _n('%s 条评论', '%s 条评论', $comments_count, 'weiruan-video'),
                number_format_i18n($comments_count)
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'weiruan_video_comment_callback',
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation(array(
            'prev_text' => __('较早的评论', 'weiruan-video'),
            'next_text' => __('较新的评论', 'weiruan-video'),
        ));
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php _e('评论已关闭。', 'weiruan-video'); ?></p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply'          => __('发表评论', 'weiruan-video'),
        'title_reply_to'       => __('回复 %s', 'weiruan-video'),
        'cancel_reply_link'    => __('取消回复', 'weiruan-video'),
        'label_submit'         => __('提交评论', 'weiruan-video'),
        'comment_field'        => '<div class="form-group comment-form-comment"><label for="comment" class="form-label">' . __('评论内容', 'weiruan-video') . '</label><textarea id="comment" name="comment" class="form-textarea" rows="5" required></textarea></div>',
        'class_form'           => 'comment-form',
        'class_submit'         => 'btn submit-btn',
        'comment_notes_before' => '<p class="comment-notes">' . __('您的邮箱地址不会被公开。', 'weiruan-video') . '</p>',
    ));
    ?>

</div>

<?php
/**
 * 自定义评论回调函数
 */
function weiruan_video_comment_callback($comment, $args, $depth) {
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('comment'); ?>>
        <article class="comment-body">
            <div class="comment-author">
                <div class="comment-avatar">
                    <?php echo get_avatar($comment, 48); ?>
                </div>
                <div class="comment-meta">
                    <span class="comment-author-name"><?php echo get_comment_author_link(); ?></span>
                    <span class="comment-date">
                        <time datetime="<?php echo get_comment_date('c'); ?>">
                            <?php echo get_comment_date(); ?> <?php echo get_comment_time(); ?>
                        </time>
                    </span>
                </div>
            </div>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <?php if ('0' == $comment->comment_approved) : ?>
                <p class="comment-awaiting-moderation"><?php _e('您的评论正在等待审核。', 'weiruan-video'); ?></p>
            <?php endif; ?>

            <div class="comment-actions">
                <?php
                comment_reply_link(array_merge($args, array(
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'class'     => 'comment-reply-link',
                )));
                ?>
                <?php edit_comment_link(__('编辑', 'weiruan-video'), '<span class="edit-link">', '</span>'); ?>
            </div>
        </article>
    <?php
}
