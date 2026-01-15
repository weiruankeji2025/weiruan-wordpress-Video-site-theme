# 威软视频下载站主题

一款专业的WordPress视频下载站主题，支持内嵌视频播放器、多分辨率视频下载、视频分类管理等强大功能。

## 主题特色

### 视频管理
- 自定义视频文章类型
- 视频分类和标签系统
- 支持多种视频分辨率（4K/1080p/720p/480p/360p）
- 视频时长、来源、导演等详细信息管理
- 观看次数、下载次数、点赞数统计

### 视频播放器
- 内置HTML5视频播放器
- 支持播放/暂停、音量控制、全屏
- 支持画中画模式
- 多分辨率切换
- 进度条拖动
- 键盘快捷键支持

### 下载功能
- 多分辨率下载支持
- 下载次数统计
- 可配置登录下载限制
- 下载延迟倒计时（可选）
- 批量下载全部分辨率

### 主题定制
- 深色视频站点主题设计
- 自定义主色调
- 首页横幅可配置
- 广告位管理
- 多个小工具区域

### 其他功能
- 响应式设计，移动端适配
- SEO友好
- 点赞/收藏功能
- 社交分享
- 面包屑导航
- 相关视频推荐
- AJAX无刷新加载

## 文件结构

```
weiruan-video-theme/
├── assets/
│   ├── css/
│   │   ├── admin.css          # 后台样式
│   │   ├── icons.css          # 图标样式
│   │   └── video-player.css   # 播放器样式
│   ├── js/
│   │   ├── admin.js           # 后台脚本
│   │   ├── download-manager.js# 下载管理
│   │   ├── main.js            # 主脚本
│   │   └── video-player.js    # 播放器脚本
│   └── images/
├── inc/
│   ├── ajax-handlers.php      # AJAX处理
│   ├── custom-post-types.php  # 自定义文章类型
│   ├── meta-boxes.php         # 元数据框
│   ├── shortcodes.php         # 短代码
│   ├── template-functions.php # 模板函数
│   ├── template-tags.php      # 模板标签
│   ├── theme-options.php      # 主题设置
│   └── widgets.php            # 小工具
├── template-parts/
│   └── content.php            # 内容模板
├── 404.php                    # 404页面
├── archive-video.php          # 视频归档页
├── comments.php               # 评论模板
├── footer.php                 # 底部模板
├── functions.php              # 核心功能
├── header.php                 # 头部模板
├── index.php                  # 主模板
├── page.php                   # 页面模板
├── search.php                 # 搜索页面
├── sidebar.php                # 侧边栏
├── single-video.php           # 单视频页面
└── style.css                  # 主样式文件
```

## 短代码

### 视频网格
```
[video_grid count="8" category="" columns="4"]
```

### 视频播放器
```
[video_player id="123" autoplay="false"]
```

### 视频下载
```
[video_download id="123" resolution="1080p"]
```

### 热门视频
```
[popular_videos count="6" days="30" title="热门视频"]
```

### 视频分类
```
[video_categories show_count="true"]
```

### 视频搜索框
```
[video_search placeholder="搜索视频..."]
```

### 网站统计
```
[video_stats]
```

## 小工具

- 热门视频小工具
- 最新视频小工具
- 视频分类小工具
- 视频标签云小工具

## 主题设置

在WordPress后台 -> 视频主题设置 中可以配置：

- **常规设置**：每页视频数、启用统计功能等
- **播放器设置**：默认播放质量、自动播放、主题色等
- **下载设置**：启用下载、登录限制、下载延迟等
- **广告设置**：头部、侧边栏、视频前后广告位

## 安装说明

1. 下载主题压缩包
2. 在WordPress后台 -> 外观 -> 主题 -> 添加新主题
3. 上传主题压缩包并安装
4. 激活主题
5. 在 外观 -> 自定义 中配置主题选项
6. 在 视频主题设置 中进行详细配置

## 系统要求

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+ / MariaDB 10.0+

## 版本信息

- 版本：1.0.0
- 作者：威软科技
- 许可：GPL v2 or later

## 署名

**威软视频下载站主题** - 由威软科技开发

---

Copyright (C) 2024 威软科技
