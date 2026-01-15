/**
 * 威软视频下载站主题 - 后台管理脚本
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const WeiruanVideoAdmin = {
        /**
         * 初始化
         */
        init: function() {
            this.mediaUploader();
            this.previewVideoSync();
            this.metaBoxTabs();
        },

        /**
         * 媒体上传器
         */
        mediaUploader: function() {
            let mediaFrame;

            // 选择视频文件
            $(document).on('click', '.select-video-file', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const resolution = $btn.data('resolution');
                const $row = $btn.closest('tr');
                const $urlInput = $row.find('.video-file-url');

                if (mediaFrame) {
                    mediaFrame.open();
                    return;
                }

                mediaFrame = wp.media({
                    title: '选择视频文件',
                    button: {
                        text: '使用此文件'
                    },
                    library: {
                        type: 'video'
                    },
                    multiple: false
                });

                mediaFrame.on('select', function() {
                    const attachment = mediaFrame.state().get('selection').first().toJSON();
                    $urlInput.val(attachment.url);

                    // 自动填充文件大小
                    if (attachment.filesizeHumanReadable) {
                        $row.find('input[name*="[size]"]').val(attachment.filesizeHumanReadable);
                    }

                    // 自动检测格式
                    if (attachment.subtype) {
                        $row.find('select[name*="[format]"]').val(attachment.subtype);
                    }
                });

                mediaFrame.open();
            });

            // 选择预览视频
            $(document).on('click', '#select-preview-video', function(e) {
                e.preventDefault();

                let previewFrame = wp.media({
                    title: '选择预览视频',
                    button: {
                        text: '使用此视频'
                    },
                    library: {
                        type: 'video'
                    },
                    multiple: false
                });

                previewFrame.on('select', function() {
                    const attachment = previewFrame.state().get('selection').first().toJSON();
                    $('#video_preview_url').val(attachment.url);
                });

                previewFrame.open();
            });

            // 选择海报图片
            $(document).on('click', '#select-poster-image', function(e) {
                e.preventDefault();

                let posterFrame = wp.media({
                    title: '选择海报图片',
                    button: {
                        text: '使用此图片'
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });

                posterFrame.on('select', function() {
                    const attachment = posterFrame.state().get('selection').first().toJSON();
                    $('#video_poster').val(attachment.url);

                    // 显示预览
                    const $preview = $('#video_poster').siblings('p').find('img');
                    if ($preview.length) {
                        $preview.attr('src', attachment.url);
                    } else {
                        $('#video_poster').after('<p><img src="' + attachment.url + '" style="max-width: 100%; height: auto; border-radius: 4px; margin-top: 10px;"></p>');
                    }
                });

                posterFrame.open();
            });
        },

        /**
         * 预览视频同步
         */
        previewVideoSync: function() {
            // 当视频文件URL输入时，可以快速设为预览
            $(document).on('dblclick', '.video-file-url', function() {
                const url = $(this).val();
                if (url) {
                    $('#video_preview_url').val(url);
                    alert('已设置为预览视频');
                }
            });
        },

        /**
         * 元数据框标签切换
         */
        metaBoxTabs: function() {
            // 如果有标签式界面
            $(document).on('click', '.meta-box-tabs .tab-link', function(e) {
                e.preventDefault();
                const $tab = $(this);
                const target = $tab.data('tab');

                $tab.addClass('active').siblings().removeClass('active');
                $('.tab-content').hide();
                $('#' + target).show();
            });
        }
    };

    // DOM Ready
    $(document).ready(function() {
        WeiruanVideoAdmin.init();
    });

})(jQuery);
