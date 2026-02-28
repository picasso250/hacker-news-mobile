import os
import re
from datetime import datetime, timezone
from playwright.sync_api import sync_playwright

def get_text_with_playwright(url):
    try:
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            context = browser.new_context(
                user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36'
            )
            page = context.new_page()
            page.goto(url, wait_until="networkidle", timeout=30000)

            # Remove script and style elements
            page.evaluate('''() => {
                const elements = document.querySelectorAll('script, style, nav, footer, header');
                for (const el of elements) {
                    el.remove();
                }
            }''')

            text = page.inner_text('body')
            browser.close()

            # Basic cleaning
            lines = (line.strip() for line in text.splitlines())
            text = '\n'.join(line for line in lines if line)
            return text
    except Exception as e:
        return f"Error fetching {url} with Playwright: {e}"

def main():
    today = datetime.now(timezone.utc).strftime('%Y-%m-%d')
    md_filepath = f"data/{today}.md"

    if not os.path.exists(md_filepath):
        print(f"File {md_filepath} not found.")
        return

    with open(md_filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find URLs starting with "URL: "
    urls = re.findall(r'URL: (https?://\S+)', content)

    for i, url in enumerate(urls[:3]):
        print(f"Fetching {url}...")
        article_text = get_text_with_playwright(url)

        output_filepath = f"data/{today}_{i+1}.txt"
        with open(output_filepath, 'w', encoding='utf-8') as f:
            f.write(f"Source URL: {url}\n\n")
            f.write(article_text)
        print(f"Saved to {output_filepath}")

if __name__ == "__main__":
    main()
