# Hacker News Mobile 精选

一个自动获取 Hacker News 热门内容并辅助翻译、总结的项目，专为移动端阅读优化。

## 功能特点

- **自动获取**: 自动抓取 Hacker News Top 3 热门文章及其前几条热门评论。
- **全文提取**: 使用 Playwright 渲染网页并提取核心正文内容，自动剔除脚本、样式和导航等干扰。
- **多阶段处理**: 从元数据抓取到全文提取，为翻译和总结提供原始素材。
- **移动端优化**: 生成适合移动端阅读的 HTML 页面（存放在 `public/` 目录）。

## 工作流程

1. **抓取元数据**: 运行 `fetch_hn.py`，从 Hacker News Firebase API 获取 Top 3 故事及评论，保存至 `data/YYYY-MM-DD.md`。
2. **提取全文**: 运行 `fetch_content.py`，读取上一步的 Markdown 文件，使用 Playwright 访问文章链接并提取正文，保存为 `data/YYYY-MM-DD_N.txt`。
3. **翻译与总结**: （目前为人工/AI 协作步骤）基于提取的内容生成中文总结和评论翻译，输出为 `public/YYYY-MM-DD.html`。
4. **部署**: 项目配置了 Netlify 自动部署，发布目录设置为 `public/`。

## 目录结构

- `data/`: 存放每日抓取的 Markdown 元数据和提取的文章全文。
- `public/`: 存放最终生成的 HTML 页面，也是 Web 服务的发布目录。
- `fetch_hn.py`: 负责调用 HN API。
- `fetch_content.py`: 负责使用 Playwright 提取网页正文。
- `netlify.toml`: Netlify 部署配置。

## 开发与使用

1. **安装依赖**:
   ```bash
   pip install -r requirements.txt
   playwright install chromium
   ```

2. **执行任务**:
   ```bash
   python fetch_hn.py
   python fetch_content.py
   ```

## 部署

只需将代码推送到 GitHub 并连接到 Netlify，它会自动识别 `netlify.toml` 并部署 `public/` 目录下的内容。
