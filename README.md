```text
╔══════════════════════════════════════════════════════════════════╗
║                                                                  ║
║      🐟   摸 鱼 博 客   ·   M o y u   B l o g s                ║
║                                                                  ║
║      Lightweight PHP Blog System                                 ║
║                                                                  ║
║      认真工作是为了更好地摸鱼                                      ║
║                                                                  ║
║      ────────────────────────────────────────                    ║
║                                                                  ║
║      📦  开源仓库  https://github.com/moyu678/moyu-blogs         ║
║      💰  打赏赞助  http://ifdian.net/a/jiadian                   ║
║                                                                  ║
╚══════════════════════════════════════════════════════════════════╝


[PHP 7.4+] [MySQL 5.7+] [MIT License] [Version 1.0]

纯 PHP + MySQL · 零框架依赖 · 开箱即用
整个系统仅 6 个文件，上传即用

GitHub 仓库 (https://github.com/moyu678/moyu-blogs) · 赞助支持 (http://ifdian.net/a/jiadian) · 快速开始 (见下文)


简介

摸鱼博客是一个轻量级个人博客系统，仅 6 个 PHP 文件，无需 Composer，无需 Node.js，上传到 Web 目录即可运行。


功能一览

┌──────────────────────────────────────────────────────────┐
│                                                          │
│  📝  博客文章 + 个人分享 双模式发布                      │
│  ⭐  留言许愿墙（留言 / 许愿 双模式）                     │
│  🔍  文章分类筛选 + 关键词搜索 + 分页                     │
│  📱  完整响应式布局（手机 / 平板 / 电脑）                 │
│  ✏️  Quill.js 富文本编辑器（支持图片/代码/引用）          │
│  🖼️  文章封面图片上传                                    │
│  📊  后台仪表盘（发布数/草稿/阅读量/留言统计）            │
│  ⭐  文章精选标记，用于引导页突出展示                      │
│  🔗  伪静态 URL 支持（Apache + Nginx）                   │
│  📂  分类管理（自定义颜色 / 排序）                        │
│  ⚙️  系统设置（博客信息 / 社交链接 / 留言开关 / 改密）    │
│  ⚡  纯原生 PHP，零依赖，一个文件一个功能                  │
│  🐟  认真工作是为了更好地摸鱼                              │
│                                                          │
└──────────────────────────────────────────────────────────┘


项目结构

moyu-blogs/
│
├── .htaccess           Apache 伪静态规则
├── config.php          配置 + 数据库连接 + 工具函数
├── install.php         一键安装向导（自动建库建表）
├── api.php             留言许愿墙 API 接口
├── index.php           前台全部页面（路由 + 渲染）
├── admin.php           后台管理面板（全部 CRUD）
│
├── uploads/            图片上传目录（安装时自动创建）
└── README.md

总计 6 个 PHP 文件 + 1 个 .htaccess + 1 个 README。


快速开始

环境要求

┌─────────────────────────┐
│                         │
│   PHP    >= 7.4         │
│   MySQL  >= 5.7         │
│   Apache 或 Nginx       │
│   mod_rewrite（Apache） │
│                         │
└─────────────────────────┘

安装步骤

第一步：下载代码

git clone https://github.com/moyu678/moyu-blogs.git
cd moyu-blogs

第二步：放入 Web 目录

# Apache 默认目录
cp -r * /var/www/html/blog/

# 或 Nginx 默认目录
cp -r * /usr/share/nginx/html/blog/

第三步：设置权限

chmod 755 uploads/
chown -R www-data:www-data /var/www/html/blog/

第四步：配置数据库

打开 config.php，修改以下四行：

define('DB_HOST', 'localhost');    // 数据库地址
define('DB_NAME', 'moyu_blog');   // 数据库名（会自动创建）
define('DB_USER', 'root');        // 数据库用户名
define('DB_PASS', '');            // 数据库密码

第五步：运行安装

浏览器访问：
http://你的域名/install.php

填写管理员账号，点击「开始安装」，系统会自动：
- 创建数据库 moyu_blog
- 创建全部数据表
- 初始化默认分类（技术折腾 / 摸鱼日记 / 项目作品 / 读书笔记 / 生活杂谈）
- 写入默认设置
- 创建一篇欢迎文章

第六步：开始使用

┌─────────────────────────────────────────────────┐
│                                                 │
│   前台首页    http://你的域名/                    │
│   后台管理    http://你的域名/admin.php           │
│                                                 │
│   默认账号    admin                              │
│   默认密码    moyu123                            │
│                                                 │
│   ⚠️  请登录后立即修改默认密码                     │
│                                                 │
└─────────────────────────────────────────────────┘


伪静态配置

Apache

项目自带 .htaccess，确保 mod_rewrite 已启用：

sudo a2enmod rewrite
sudo systemctl restart apache2

同时确保 Apache 配置中 AllowOverride All：

<Directory /var/www/html>
    AllowOverride All
</Directory>

Nginx

在 server 块中添加：

server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/blog;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location /uploads/ {
        expires 30d;
        access_log off;
    }

    location ~ /\.(ht|git) {
        deny all;
    }
}

伪静态 URL 对照

┌─────────────────────────────────────────────────┐
│                                                 │
│   /                 →  引导页（Landing）         │
│   /blog             →  博客列表                 │
│   /blog/page/3      →  博客第 3 页              │
│   /blog/tech        →  分类筛选                 │
│   /blog/tech/page/2 →  分类 + 分页              │
│   /blog?q=关键词     →  搜索                    │
│   /post/welcome     →  文章详情                 │
│   /sharing          →  个人分享                 │
│   /wish             →  留言许愿墙               │
│   /about            →  关于页面                 │
│   /admin            →  管理后台                 │
│                                                 │
└─────────────────────────────────────────────────┘

旧版 query string 链接（如 index.php?page=blog）仍然可用，完全向后兼容。


后台功能

┌──────────────────────────────────────────────────────────┐
│                                                          │
│  ◆ 仪表盘                                                │
│    ├── 已发布 / 草稿 / 总阅读 / 留言 四项统计卡片        │
│    └── 最近更新文章列表（快速编辑入口）                    │
│                                                          │
│  📝 文章管理                                              │
│    ├── 新建 / 编辑 / 删除文章                             │
│    ├── Quill.js 富文本编辑器                              │
│    ├── 封面图片上传                                       │
│    ├── 分类选择 + 精选标记                                │
│    ├── 文章 / 分享 类型切换                               │
│    └── 一键发布 / 下架                                    │
│                                                          │
│  📂 分类管理                                              │
│    ├── 新建 / 编辑 / 删除分类                             │
│    ├── 自定义颜色 + 排序                                  │
│    └── 文章数量统计                                       │
│                                                          │
│  ⭐ 许愿墙管理                                            │
│    ├── 查看全部留言 / 许愿                                │
│    ├── 隐藏 / 恢复 / 删除                                 │
│    └── 显示访客 IP                                       │
│                                                          │
│  ⚙ 系统设置                                              │
│    ├── 博客名称 / 标语 / 描述                             │
│    ├── 作者名称 / 头衔 / 简介                             │
│    ├── 社交链接（GitHub / Twitter / Email）               │
│    ├── ICP 备案号                                         │
│    ├── 留言许愿开关                                       │
│    └── 账户安全（修改密码）                                │
│                                                          │
└──────────────────────────────────────────────────────────┘


留言许愿墙

访客无需登录即可发表留言或许愿：

┌──────────────────────────────────────────────────────────┐
│                                                          │
│  💬 留言模式                                              │
│     输入昵称 + 内容，发表普通留言                          │
│     蓝色标签显示                                          │
│                                                          │
│  ⭐ 许愿模式                                              │
│     输入昵称 + 内容，发表许愿                              │
│     金色标签显示                                          │
│                                                          │
│  特性：                                                   │
│    • 昵称自动生成彩色头像（基于名字哈希）                  │
│    • 支持分页浏览                                         │
│    • 后台可隐藏 / 恢复 / 删除任意留言                     │
│    • XSS 防护（所有输出经过 htmlspecialchars）             │
│    • 内容限制 1-500 字                                    │
│                                                          │
└──────────────────────────────────────────────────────────┘


数据库说明

安装时自动创建以下表：

表名         说明
users        管理员用户表
categories   文章分类表
posts        文章 / 分享表
settings     系统设置表（KV 存储）
wishes       留言许愿表

如需手动建表，完整 SQL 在 install.php 中。


常见问题

Q: 安装后页面空白？

检查 PHP 错误日志：
tail -f /var/log/php_errors.log

常见原因：
1. 数据库连接信息错误 → 修改 config.php
2. PHP 版本低于 7.4  → 升级 PHP
3. 缺少 PDO 扩展     → apt install php-mysql

Q: 伪静态不生效（返回 404）？

Apache：
  ✅ mod_rewrite 是否启用 → a2enmod rewrite
  ✅ AllowOverride 是否为 All
  ✅ .htaccess 文件是否存在

Nginx：
  ✅ try_files 配置是否正确
  ✅ nginx.conf 是否 reload → nginx -s reload

Q: 图片上传失败？

检查 uploads/ 目录权限：
  chmod 755 uploads/
  chown -R www-data:www-data uploads/

检查 PHP 上传限制：
  upload_max_filesize = 10M
  post_max_size = 10M

Q: 如何修改默认管理员密码？

登录后台 → 设置 → 账户安全 → 修改密码

Q: 如何关闭留言功能？

登录后台 → 设置 → 取消勾选「开启留言许愿」→ 保存设置


更新日志

v1.0.0 (2025-01-01)

┌──────────────────────────────────────────────────────────┐
│                                                          │
│  🎉  首次发布                                             │
│                                                          │
│  ✅  引导页（Hero + 精选 + 分类 + 分享）                  │
│  ✅  博客列表（分类筛选 / 搜索 / 分页）                   │
│  ✅  文章详情（阅读时间 / 推荐阅读）                      │
│  ✅  个人分享展示                                         │
│  ✅  留言许愿墙（留言 / 许愿双模式）                      │
│  ✅  关于页面                                             │
│  ✅  后台管理面板（仪表盘/文章/分类/留言/设置）           │
│  ✅  Quill.js 富文本编辑器                                │
│  ✅  伪静态 URL 支持（Apache + Nginx）                   │
│  ✅  一键安装向导                                         │
│  ✅  响应式布局                                           │
│                                                          │
└──────────────────────────────────────────────────────────┘


参与贡献

欢迎提交 Issue 和 Pull Request！

┌──────────────────────────────────────────────────────────┐
│                                                          │
│  1. Fork 本仓库                                          │
│  2. 创建特性分支  git checkout -b feature/amazing         │
│  3. 提交更改      git commit -m '添加某个特性'            │
│  4. 推送分支      git push origin feature/amazing         │
│  5. 提交 Pull Request                                    │
│                                                          │
└──────────────────────────────────────────────────────────┘


开源协议

本项目基于 MIT License 开源。

MIT License

Copyright (c) 2025 摸鱼博客

你可以自由地使用、修改和分发本项目，
但请保留原始版权声明。


支持作者

如果摸鱼博客对你有帮助，欢迎请作者喝杯咖啡 ☕

┌──────────────────────────────────────────────────────────┐
│                                                          │
│   🐟  摸鱼博客 · 支持作者                                │
│                                                          │
│   📦  GitHub 仓库                                        │
│      https://github.com/moyu678/moyu-blogs               │
│                                                          │
│   💰  爱发电赞助                                         │
│      http://ifdian.net/a/jiadian                         │
│                                                          │
│   你的每一份支持都是我继续摸鱼的动力 🎣                    │
│                                                          │
└──────────────────────────────────────────────────────────┘


相关链接

GitHub 仓库    https://github.com/moyu678/moyu-blogs
赞助支持       http://ifdian.net/a/jiadian
提交 Bug       https://github.com/moyu678/moyu-blogs/issues
功能建议       https://github.com/moyu678/moyu-blogs/issues


╔══════════════════════════════════════════════════════════════════╗
║                                                                  ║
║   🐟  摸 鱼 博 客  ·  M o y u   B l o g s                      ║
║                                                                  ║
║   认真工作是为了更好地摸鱼                                        ║
║                                                                  ║
║   📦  https://github.com/moyu678/moyu-blogs                      ║
║   💰  http://ifdian.net/a/jiadian                                ║
║                                                                  ║
╚══════════════════════════════════════════════════════════════════╝
