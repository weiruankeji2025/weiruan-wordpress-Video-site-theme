/**
 * 威软视频下载站主题 - 主脚本文件
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // 主题命名空间
    const WeiruanVideo = {
        /**
         * 初始化
         */
        init: function() {
            this.mobileMenu();
            this.smoothScroll();
            this.lazyLoad();
            this.searchToggle();
            this.videoActions();
            this.loadMore();
            this.filterVideos();
            this.shareButtons();
            this.backToTop();
            this.tooltips();
            this.visitorStats();
        },

        /**
         * 移动端菜单
         */
        mobileMenu: function() {
            const menuToggle = $('#menu-toggle');
            const navigation = $('#site-navigation');

            menuToggle.on('click', function() {
                navigation.toggleClass('active');
                $(this).attr('aria-expanded', navigation.hasClass('active'));
            });

            // 点击外部关闭菜单
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.site-header').length) {
                    navigation.removeClass('active');
                    menuToggle.attr('aria-expanded', 'false');
                }
            });

            // 子菜单展开
            $('.nav-menu .menu-item-has-children > a').on('click', function(e) {
                if ($(window).width() <= 768) {
                    e.preventDefault();
                    $(this).parent().toggleClass('submenu-open');
                }
            });
        },

        /**
         * 平滑滚动
         */
        smoothScroll: function() {
            $('a[href*="#"]:not([href="#"])').on('click', function() {
                if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') &&
                    location.hostname === this.hostname) {
                    let target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        $('html, body').animate({
                            scrollTop: target.offset().top - 80
                        }, 500);
                        return false;
                    }
                }
            });
        },

        /**
         * 懒加载图片
         */
        lazyLoad: function() {
            if ('IntersectionObserver' in window) {
                const lazyImages = document.querySelectorAll('img[loading="lazy"]');
                const imageObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src || img.src;
                            img.classList.add('loaded');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(function(img) {
                    imageObserver.observe(img);
                });
            }
        },

        /**
         * 搜索切换
         */
        searchToggle: function() {
            const searchField = $('.header-search .search-field');

            searchField.on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });
        },

        /**
         * 视频操作（点赞、收藏等）
         */
        videoActions: function() {
            const self = this;

            // 点赞
            $(document).on('click', '.like-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const videoId = $btn.data('video-id');

                if ($btn.hasClass('loading')) return;
                $btn.addClass('loading');

                $.ajax({
                    url: weiruanVideo.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'weiruan_video_like',
                        nonce: weiruanVideo.nonce,
                        video_id: videoId
                    },
                    success: function(response) {
                        if (response.success) {
                            $btn.toggleClass('liked', response.data.liked);
                            $btn.find('.like-count').text(response.data.likes);
                            self.showToast(response.data.liked ? '已点赞' : '已取消点赞');
                        }
                    },
                    error: function() {
                        self.showToast(weiruanVideo.strings.error, 'error');
                    },
                    complete: function() {
                        $btn.removeClass('loading');
                    }
                });
            });

            // 收藏
            $(document).on('click', '.collect-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const videoId = $btn.data('video-id');

                if ($btn.hasClass('loading')) return;
                $btn.addClass('loading');

                $.ajax({
                    url: weiruanVideo.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'weiruan_video_collect',
                        nonce: weiruanVideo.nonce,
                        video_id: videoId
                    },
                    success: function(response) {
                        if (response.success) {
                            $btn.toggleClass('collected', response.data.collected);
                            self.showToast(response.data.message);
                        } else if (response.data.require_login) {
                            self.showToast('请先登录', 'warning');
                            window.location.href = weiruanVideo.loginUrl;
                        }
                    },
                    error: function() {
                        self.showToast(weiruanVideo.strings.error, 'error');
                    },
                    complete: function() {
                        $btn.removeClass('loading');
                    }
                });
            });

            // 分享按钮
            $(document).on('click', '.share-btn', function(e) {
                e.preventDefault();
                const $modal = self.createShareModal($(this).data('video-id'));
                $('body').append($modal);
                $modal.fadeIn(200);
            });
        },

        /**
         * 创建分享弹窗
         */
        createShareModal: function(videoId) {
            const url = window.location.href;
            const title = document.title;

            return $(`
                <div class="share-modal-overlay">
                    <div class="share-modal">
                        <div class="share-modal-header">
                            <h3>分享视频</h3>
                            <button type="button" class="close-modal">&times;</button>
                        </div>
                        <div class="share-modal-body">
                            <div class="share-buttons">
                                <a href="https://service.weibo.com/share/share.php?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}" target="_blank" class="share-weibo">
                                    微博
                                </a>
                                <a href="https://connect.qq.com/widget/shareqq/index.html?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}" target="_blank" class="share-qq">
                                    QQ
                                </a>
                                <button type="button" class="share-copy" data-url="${url}">
                                    复制链接
                                </button>
                            </div>
                            <div class="share-url">
                                <input type="text" readonly value="${url}">
                            </div>
                        </div>
                    </div>
                </div>
            `).on('click', '.close-modal, .share-modal-overlay', function(e) {
                if (e.target === this) {
                    $(this).closest('.share-modal-overlay').fadeOut(200, function() {
                        $(this).remove();
                    });
                }
            }).on('click', '.share-copy', function() {
                const url = $(this).data('url');
                navigator.clipboard.writeText(url).then(() => {
                    WeiruanVideo.showToast('链接已复制到剪贴板');
                });
            });
        },

        /**
         * 加载更多
         */
        loadMore: function() {
            const self = this;
            let loading = false;
            let page = 1;

            $(document).on('click', '.load-more-btn', function(e) {
                e.preventDefault();

                if (loading) return;

                const $btn = $(this);
                const $grid = $('#video-grid');
                const category = $btn.data('category') || '';
                const tag = $btn.data('tag') || '';

                loading = true;
                $btn.addClass('loading').text(weiruanVideo.strings.loading);
                page++;

                $.ajax({
                    url: weiruanVideo.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'weiruan_video_load_more',
                        nonce: weiruanVideo.nonce,
                        page: page,
                        category: category,
                        tag: tag
                    },
                    success: function(response) {
                        if (response.success) {
                            $grid.append(response.data.html);

                            if (!response.data.has_more) {
                                $btn.hide();
                            }
                        } else {
                            self.showToast(response.data.message, 'warning');
                            $btn.hide();
                        }
                    },
                    error: function() {
                        self.showToast(weiruanVideo.strings.error, 'error');
                    },
                    complete: function() {
                        loading = false;
                        $btn.removeClass('loading').text('加载更多');
                    }
                });
            });
        },

        /**
         * 视频筛选
         */
        filterVideos: function() {
            const self = this;

            $(document).on('change', '#video-sort', function() {
                const sort = $(this).val();
                const url = new URL(window.location.href);
                url.searchParams.set('orderby', sort);
                window.location.href = url.toString();
            });

            // AJAX筛选
            $(document).on('click', '.filter-item[data-ajax="true"]', function(e) {
                e.preventDefault();
                const $item = $(this);
                const $grid = $('#video-grid');
                const category = $item.data('category') || '';
                const quality = $item.data('quality') || '';
                const sort = $('#video-sort').val() || 'date';

                $('.filter-item').removeClass('active');
                $item.addClass('active');

                $grid.addClass('loading');

                $.ajax({
                    url: weiruanVideo.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'weiruan_video_filter',
                        nonce: weiruanVideo.nonce,
                        category: category,
                        quality: quality,
                        sort: sort,
                        page: 1
                    },
                    success: function(response) {
                        if (response.success) {
                            $grid.html(response.data.html);
                        } else {
                            $grid.html('<p class="no-results">' + response.data.message + '</p>');
                        }
                    },
                    error: function() {
                        self.showToast(weiruanVideo.strings.error, 'error');
                    },
                    complete: function() {
                        $grid.removeClass('loading');
                    }
                });
            });
        },

        /**
         * 分享按钮
         */
        shareButtons: function() {
            $(document).on('click', '.social-share a', function(e) {
                if ($(this).attr('href') !== '#') {
                    e.preventDefault();
                    window.open($(this).attr('href'), 'share', 'width=600,height=400');
                }
            });
        },

        /**
         * 返回顶部
         */
        backToTop: function() {
            const $btn = $('<button type="button" class="back-to-top" aria-label="返回顶部"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg></button>');
            $('body').append($btn);

            $(window).on('scroll', function() {
                if ($(this).scrollTop() > 300) {
                    $btn.addClass('visible');
                } else {
                    $btn.removeClass('visible');
                }
            });

            $btn.on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 500);
            });
        },

        /**
         * 工具提示
         */
        tooltips: function() {
            $('[title]').each(function() {
                const $el = $(this);
                const title = $el.attr('title');
                $el.removeAttr('title').attr('data-tooltip', title);
            });
        },

        /**
         * 显示提示信息
         */
        showToast: function(message, type = 'success') {
            const $toast = $(`<div class="toast toast-${type}">${message}</div>`);
            $('body').append($toast);

            setTimeout(function() {
                $toast.addClass('show');
            }, 10);

            setTimeout(function() {
                $toast.removeClass('show');
                setTimeout(function() {
                    $toast.remove();
                }, 300);
            }, 3000);
        },

        /**
         * 访客统计实时更新
         */
        visitorStats: function() {
            const $onlineCount = $('.visitor-stats-bar .online-count');
            const $totalCount = $('.visitor-stats-bar .total-count');

            // 如果页面上没有统计栏，则不执行
            if ($onlineCount.length === 0) {
                return;
            }

            // 定时更新在线人数（每30秒更新一次）
            const updateOnlineCount = function() {
                $.ajax({
                    url: weiruanVideo.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'weiruan_video_get_online_count',
                        nonce: weiruanVideo.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            // 更新在线人数
                            if (response.data.online !== undefined) {
                                const currentOnline = parseInt($onlineCount.text());
                                const newOnline = response.data.online;

                                // 带动画效果更新数字
                                if (currentOnline !== newOnline) {
                                    $onlineCount.addClass('updating');
                                    setTimeout(function() {
                                        $onlineCount.text(newOnline);
                                        $onlineCount.removeClass('updating');
                                    }, 150);
                                }
                            }

                            // 更新总访问量（如果返回了）
                            if (response.data.total !== undefined && $totalCount.length) {
                                const currentTotal = parseInt($totalCount.text().replace(/,/g, ''));
                                const newTotal = response.data.total;

                                if (currentTotal !== newTotal) {
                                    $totalCount.addClass('updating');
                                    setTimeout(function() {
                                        $totalCount.text(newTotal.toLocaleString());
                                        $totalCount.removeClass('updating');
                                    }, 150);
                                }
                            }
                        }
                    }
                });
            };

            // 每30秒更新一次
            setInterval(updateOnlineCount, 30000);

            // 页面可见性API - 当用户切换回页面时立即更新
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    updateOnlineCount();
                }
            });
        }
    };

    // DOM Ready
    $(document).ready(function() {
        WeiruanVideo.init();
    });

    // 暴露到全局
    window.WeiruanVideo = WeiruanVideo;

})(jQuery);

// 返回顶部按钮和提示样式
const toastStyles = `
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
    }

    .toast {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        padding: 12px 24px;
        background: var(--bg-card);
        color: var(--text-primary);
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .toast-success {
        border-left: 4px solid var(--success-color);
    }

    .toast-error {
        border-left: 4px solid var(--accent-color);
    }

    .toast-warning {
        border-left: 4px solid var(--warning-color);
    }

    .share-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10001;
    }

    .share-modal {
        background: var(--bg-card);
        border-radius: 16px;
        padding: 24px;
        width: 90%;
        max-width: 400px;
        border: 1px solid var(--border-color);
    }

    .share-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .share-modal-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .close-modal {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
    }

    .share-buttons {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }

    .share-buttons a,
    .share-buttons button {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .share-weibo {
        background: #e6162d;
        color: white;
    }

    .share-qq {
        background: #12b7f5;
        color: white;
    }

    .share-copy {
        background: var(--bg-card-hover);
        color: var(--text-primary);
    }

    .share-url input {
        width: 100%;
        padding: 12px;
        background: var(--bg-card-hover);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    [data-tooltip] {
        position: relative;
    }

    [data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 6px 12px;
        background: var(--bg-card);
        color: var(--text-primary);
        border-radius: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        z-index: 100;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    /* 访客统计数字更新动画 */
    .visitor-stats-bar .online-count,
    .visitor-stats-bar .total-count {
        transition: transform 0.15s ease, opacity 0.15s ease;
        display: inline-block;
    }

    .visitor-stats-bar .online-count.updating,
    .visitor-stats-bar .total-count.updating {
        transform: scale(1.2);
        opacity: 0.7;
    }
`;

// 注入样式
const styleSheet = document.createElement('style');
styleSheet.textContent = toastStyles;
document.head.appendChild(styleSheet);
