/**
 * 威软视频下载站主题 - 视频播放器
 *
 * @package Weiruan_Video
 * @since 1.0.0
 */

(function($) {
    'use strict';

    class VideoPlayer {
        constructor(container) {
            this.$container = $(container);
            this.$video = this.$container.find('video');
            this.video = this.$video[0];

            if (!this.video) return;

            this.isPlaying = false;
            this.isMuted = false;
            this.isFullscreen = false;
            this.currentQuality = 'auto';

            this.init();
        }

        init() {
            this.cacheElements();
            this.bindEvents();
            this.initializeVideo();
        }

        cacheElements() {
            this.$playBtn = this.$container.find('.play-btn');
            this.$bigPlayBtn = this.$container.find('.big-play-button');
            this.$volumeBtn = this.$container.find('.volume-btn');
            this.$volumeSlider = this.$container.find('.volume-slider');
            this.$fullscreenBtn = this.$container.find('.fullscreen-btn');
            this.$pipBtn = this.$container.find('.pip-btn');
            this.$progressBar = this.$container.find('.progress-bar');
            this.$progress = this.$progressBar.find('.progress');
            this.$currentTime = this.$container.find('.current-time');
            this.$totalTime = this.$container.find('.total-time');
            this.$qualityBtn = this.$container.find('.quality-btn');
            this.$qualityMenu = this.$container.find('.quality-menu');
            this.$controls = this.$container.find('.player-controls');
            this.$loading = this.$container.find('.video-loading');
        }

        bindEvents() {
            const self = this;

            // 播放/暂停
            this.$playBtn.on('click', () => this.togglePlay());
            this.$bigPlayBtn.on('click', () => this.togglePlay());
            this.$video.on('click', () => this.togglePlay());

            // 视频事件
            this.video.addEventListener('play', () => this.onPlay());
            this.video.addEventListener('pause', () => this.onPause());
            this.video.addEventListener('ended', () => this.onEnded());
            this.video.addEventListener('timeupdate', () => this.onTimeUpdate());
            this.video.addEventListener('loadedmetadata', () => this.onLoadedMetadata());
            this.video.addEventListener('waiting', () => this.showLoading());
            this.video.addEventListener('canplay', () => this.hideLoading());
            this.video.addEventListener('progress', () => this.updateBuffered());

            // 音量
            this.$volumeBtn.on('click', () => this.toggleMute());
            this.$volumeSlider.on('input', (e) => this.setVolume(e.target.value));

            // 进度条
            this.$progressBar.on('click', (e) => this.seekTo(e));
            this.$progressBar.on('mousemove', (e) => this.showSeekPreview(e));
            this.$progressBar.on('mouseleave', () => this.hideSeekPreview());

            // 全屏
            this.$fullscreenBtn.on('click', () => this.toggleFullscreen());
            document.addEventListener('fullscreenchange', () => this.onFullscreenChange());
            document.addEventListener('webkitfullscreenchange', () => this.onFullscreenChange());

            // 画中画
            this.$pipBtn.on('click', () => this.togglePiP());

            // 质量选择
            this.$qualityBtn.on('click', (e) => {
                e.stopPropagation();
                this.$qualityMenu.toggleClass('show');
            });

            this.$qualityMenu.find('button').on('click', function() {
                self.changeQuality($(this).data('quality'), $(this).data('src'));
            });

            // 点击其他地方关闭质量菜单
            $(document).on('click', () => this.$qualityMenu.removeClass('show'));

            // 键盘快捷键
            $(document).on('keydown', (e) => this.handleKeyboard(e));

            // 显示/隐藏控制条
            let hideControlsTimer;
            this.$container.on('mousemove', () => {
                this.$controls.css('opacity', '1');
                clearTimeout(hideControlsTimer);
                if (this.isPlaying) {
                    hideControlsTimer = setTimeout(() => {
                        this.$controls.css('opacity', '0');
                    }, 3000);
                }
            });
        }

        initializeVideo() {
            // 设置初始音量
            this.video.volume = 1;
            this.$volumeSlider.val(100);
        }

        togglePlay() {
            if (this.video.paused) {
                this.video.play();
            } else {
                this.video.pause();
            }
        }

        onPlay() {
            this.isPlaying = true;
            this.$container.addClass('playing');
            this.$playBtn.find('.icon-play').hide();
            this.$playBtn.find('.icon-pause').show();
            this.$bigPlayBtn.hide();
        }

        onPause() {
            this.isPlaying = false;
            this.$container.removeClass('playing');
            this.$playBtn.find('.icon-play').show();
            this.$playBtn.find('.icon-pause').hide();
            this.$bigPlayBtn.show();
        }

        onEnded() {
            this.isPlaying = false;
            this.$container.removeClass('playing');
            this.$playBtn.find('.icon-play').show();
            this.$playBtn.find('.icon-pause').hide();
            this.$bigPlayBtn.show();
        }

        onTimeUpdate() {
            const current = this.video.currentTime;
            const duration = this.video.duration;
            const percent = (current / duration) * 100;

            this.$progress.css('width', percent + '%');
            this.$currentTime.text(this.formatTime(current));
        }

        onLoadedMetadata() {
            this.$totalTime.text(this.formatTime(this.video.duration));
        }

        showLoading() {
            this.$loading.show();
        }

        hideLoading() {
            this.$loading.hide();
        }

        updateBuffered() {
            if (this.video.buffered.length > 0) {
                const bufferedEnd = this.video.buffered.end(this.video.buffered.length - 1);
                const duration = this.video.duration;
                const percent = (bufferedEnd / duration) * 100;
                this.$progress.find('.progress-buffered').css('width', percent + '%');
            }
        }

        toggleMute() {
            this.isMuted = !this.isMuted;
            this.video.muted = this.isMuted;

            if (this.isMuted) {
                this.$volumeBtn.find('.icon-volume').hide();
                this.$volumeBtn.find('.icon-mute').show();
                this.$volumeSlider.val(0);
            } else {
                this.$volumeBtn.find('.icon-volume').show();
                this.$volumeBtn.find('.icon-mute').hide();
                this.$volumeSlider.val(this.video.volume * 100);
            }
        }

        setVolume(value) {
            const volume = value / 100;
            this.video.volume = volume;
            this.video.muted = volume === 0;
            this.isMuted = volume === 0;

            if (volume === 0) {
                this.$volumeBtn.find('.icon-volume').hide();
                this.$volumeBtn.find('.icon-mute').show();
            } else {
                this.$volumeBtn.find('.icon-volume').show();
                this.$volumeBtn.find('.icon-mute').hide();
            }
        }

        seekTo(e) {
            const rect = this.$progressBar[0].getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.video.currentTime = percent * this.video.duration;
        }

        showSeekPreview(e) {
            // 可以添加预览缩略图功能
        }

        hideSeekPreview() {
            // 隐藏预览
        }

        toggleFullscreen() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                if (this.$container[0].requestFullscreen) {
                    this.$container[0].requestFullscreen();
                } else if (this.$container[0].webkitRequestFullscreen) {
                    this.$container[0].webkitRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }
        }

        onFullscreenChange() {
            this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);

            if (this.isFullscreen) {
                this.$fullscreenBtn.find('.icon-fullscreen').hide();
                this.$fullscreenBtn.find('.icon-fullscreen-exit').show();
                this.$container.addClass('fullscreen');
            } else {
                this.$fullscreenBtn.find('.icon-fullscreen').show();
                this.$fullscreenBtn.find('.icon-fullscreen-exit').hide();
                this.$container.removeClass('fullscreen');
            }
        }

        async togglePiP() {
            try {
                if (document.pictureInPictureElement) {
                    await document.exitPictureInPicture();
                } else if (document.pictureInPictureEnabled) {
                    await this.video.requestPictureInPicture();
                }
            } catch (error) {
                console.error('PiP error:', error);
            }
        }

        changeQuality(quality, src) {
            if (!src) return;

            const currentTime = this.video.currentTime;
            const wasPlaying = !this.video.paused;

            this.currentQuality = quality;
            this.video.src = src;
            this.video.load();

            this.video.addEventListener('loadedmetadata', () => {
                this.video.currentTime = currentTime;
                if (wasPlaying) {
                    this.video.play();
                }
            }, { once: true });

            this.$qualityBtn.text(quality.toUpperCase());
            this.$qualityMenu.find('button').removeClass('active');
            this.$qualityMenu.find(`button[data-quality="${quality}"]`).addClass('active');
            this.$qualityMenu.removeClass('show');
        }

        handleKeyboard(e) {
            // 只有当播放器获得焦点时才响应
            if (!this.$container.is(':hover') && !this.isFullscreen) return;

            switch (e.key) {
                case ' ':
                case 'k':
                    e.preventDefault();
                    this.togglePlay();
                    break;
                case 'f':
                    e.preventDefault();
                    this.toggleFullscreen();
                    break;
                case 'm':
                    e.preventDefault();
                    this.toggleMute();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.video.currentTime -= 5;
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.video.currentTime += 5;
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.setVolume(Math.min(100, this.video.volume * 100 + 10));
                    this.$volumeSlider.val(this.video.volume * 100);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.setVolume(Math.max(0, this.video.volume * 100 - 10));
                    this.$volumeSlider.val(this.video.volume * 100);
                    break;
                case '0':
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                    e.preventDefault();
                    this.video.currentTime = (parseInt(e.key) / 10) * this.video.duration;
                    break;
            }
        }

        formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';

            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);

            if (hrs > 0) {
                return `${hrs}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
    }

    // 初始化所有播放器
    $(document).ready(function() {
        $('.video-player').each(function() {
            new VideoPlayer(this);
        });
    });

    // 暴露到全局
    window.VideoPlayer = VideoPlayer;

})(jQuery);
