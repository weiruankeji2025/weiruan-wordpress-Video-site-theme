/**
 * 威软视频下载站主题 - 下载管理器
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const DownloadManager = {
        /**
         * 初始化
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * 绑定事件
         */
        bindEvents: function() {
            const self = this;

            // 单个下载按钮
            $(document).on('click', '.download-btn', function(e) {
                const $btn = $(this);
                const resolution = $btn.data('resolution');
                const videoId = $btn.closest('.download-panel').find('[data-video-id]').data('video-id') ||
                               $btn.closest('[data-video-id]').data('video-id');

                if (videoId && resolution) {
                    self.recordDownload(videoId, resolution);
                }

                // 显示下载中状态
                self.showDownloadStatus($btn);
            });

            // 下载全部按钮
            $(document).on('click', '.download-all-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const videoId = $btn.data('video-id');
                const $panel = $btn.closest('.download-panel');
                const $downloadBtns = $panel.find('.download-btn');

                self.downloadAll($downloadBtns, videoId);
            });

            // 下载延迟倒计时（如果启用）
            $(document).on('click', '.download-btn-delayed', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const delay = parseInt($btn.data('delay')) || 5;
                const href = $btn.attr('href');

                self.countdownDownload($btn, delay, href);
            });
        },

        /**
         * 记录下载
         */
        recordDownload: function(videoId, resolution) {
            $.ajax({
                url: weiruanVideo.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'weiruan_video_record_download',
                    nonce: weiruanVideo.nonce,
                    video_id: videoId,
                    resolution: resolution
                },
                success: function(response) {
                    if (response.success) {
                        // 更新下载计数显示
                        const $statsDownloads = $('.stat-item .downloads, .video-stats .downloads');
                        if ($statsDownloads.length) {
                            $statsDownloads.each(function() {
                                $(this).text(response.data.downloads);
                            });
                        }
                    }
                }
            });
        },

        /**
         * 显示下载状态
         */
        showDownloadStatus: function($btn) {
            const originalText = $btn.html();
            const originalWidth = $btn.width();

            $btn.css('min-width', originalWidth + 'px');
            $btn.html('<span class="loading-spinner small"></span> ' + weiruanVideo.strings.downloading);
            $btn.addClass('downloading');

            setTimeout(function() {
                $btn.html(originalText);
                $btn.removeClass('downloading');

                // 显示下载成功提示
                if (window.WeiruanVideo && typeof window.WeiruanVideo.showToast === 'function') {
                    window.WeiruanVideo.showToast(weiruanVideo.strings.downloaded);
                }
            }, 2000);
        },

        /**
         * 下载全部
         */
        downloadAll: function($downloadBtns, videoId) {
            const self = this;
            const urls = [];

            $downloadBtns.each(function() {
                const href = $(this).attr('href');
                const resolution = $(this).data('resolution');
                if (href && href !== '#') {
                    urls.push({ url: href, resolution: resolution });
                }
            });

            if (urls.length === 0) {
                if (window.WeiruanVideo && typeof window.WeiruanVideo.showToast === 'function') {
                    window.WeiruanVideo.showToast('暂无可下载的文件', 'warning');
                }
                return;
            }

            // 弹出确认
            if (!confirm('即将下载 ' + urls.length + ' 个文件，确定要继续吗？')) {
                return;
            }

            // 依次触发下载
            let index = 0;
            const downloadNext = function() {
                if (index >= urls.length) {
                    if (window.WeiruanVideo && typeof window.WeiruanVideo.showToast === 'function') {
                        window.WeiruanVideo.showToast('所有文件已开始下载');
                    }
                    return;
                }

                const item = urls[index];
                self.triggerDownload(item.url);
                self.recordDownload(videoId, item.resolution);

                index++;
                setTimeout(downloadNext, 1000); // 每秒下载一个，避免浏览器阻止
            };

            downloadNext();
        },

        /**
         * 触发下载
         */
        triggerDownload: function(url) {
            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        /**
         * 倒计时下载
         */
        countdownDownload: function($btn, delay, href) {
            const originalText = $btn.html();
            let countdown = delay;

            $btn.addClass('counting');
            $btn.prop('disabled', true);

            const timer = setInterval(function() {
                $btn.html(countdown + ' 秒后开始下载...');
                countdown--;

                if (countdown < 0) {
                    clearInterval(timer);
                    $btn.html(originalText);
                    $btn.removeClass('counting');
                    $btn.prop('disabled', false);

                    // 触发下载
                    window.location.href = href;
                }
            }, 1000);
        },

        /**
         * 检查文件有效性
         */
        checkFileValidity: function(url, callback) {
            $.ajax({
                url: url,
                type: 'HEAD',
                success: function() {
                    callback(true);
                },
                error: function() {
                    callback(false);
                }
            });
        },

        /**
         * 格式化文件大小
         */
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    };

    // DOM Ready
    $(document).ready(function() {
        DownloadManager.init();
    });

    // 暴露到全局
    window.DownloadManager = DownloadManager;

})(jQuery);

// 添加下载相关样式
const downloadStyles = `
    .download-btn.downloading {
        pointer-events: none;
        opacity: 0.7;
    }

    .download-btn.counting {
        pointer-events: none;
        background: var(--bg-card-hover);
    }

    .loading-spinner.small {
        width: 14px;
        height: 14px;
        border-width: 2px;
        display: inline-block;
        vertical-align: middle;
        margin-right: 4px;
    }

    .download-progress {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        min-width: 280px;
    }

    .download-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .download-progress-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .download-progress-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 18px;
    }

    .download-progress-bar {
        height: 6px;
        background: var(--bg-card-hover);
        border-radius: 3px;
        overflow: hidden;
    }

    .download-progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .download-progress-info {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
        font-size: 0.85rem;
        color: var(--text-muted);
    }
`;

const downloadStyleSheet = document.createElement('style');
downloadStyleSheet.textContent = downloadStyles;
document.head.appendChild(downloadStyleSheet);
