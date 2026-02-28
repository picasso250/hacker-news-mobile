import requests
import json
from datetime import datetime, timezone
import os

def fetch_hn_top_stories(limit=3):
    top_stories_url = "https://hacker-news.firebaseio.com/v0/topstories.json"
    response = requests.get(top_stories_url)
    story_ids = response.json()[:limit]

    stories = []
    for story_id in story_ids:
        item_url = f"https://hacker-news.firebaseio.com/v0/item/{story_id}.json"
        item_response = requests.get(item_url)
        stories.append(item_response.json())
    return stories

def fetch_comments(comment_ids, limit=3):
    comments = []
    if not comment_ids:
        return comments

    # We'll just take the first few top-level comments
    for comment_id in comment_ids[:limit]:
        item_url = f"https://hacker-news.firebaseio.com/v0/item/{comment_id}.json"
        item_response = requests.get(item_url)
        comment_data = item_response.json()
        if comment_data and not comment_data.get('deleted'):
            comments.append(comment_data)
    return comments

def main():
    stories = fetch_hn_top_stories(3)

    today = datetime.now(timezone.utc).strftime('%Y-%m-%d')
    os.makedirs('data', exist_ok=True)
    filepath = f"data/{today}.md"

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(f"# Hacker News Top 3 - {today}\n\n")

        for i, story in enumerate(stories):
            title = story.get('title', 'No Title')
            url = story.get('url', f"https://news.ycombinator.com/item?id={story.get('id')}")
            f.write(f"## {i+1}. {title}\n")
            f.write(f"URL: {url}\n")
            f.write(f"HN Link: https://news.ycombinator.com/item?id={story.get('id')}\n\n")

            f.write("### Top Comments\n")
            comment_ids = story.get('kids', [])
            comments = fetch_comments(comment_ids, 3)

            if not comments:
                f.write("No comments.\n")
            else:
                for comment in comments:
                    text = comment.get('text', '[No Text]')
                    # Basic cleaning of HTML tags in comments if needed,
                    # but MD supports some HTML. HN uses some tags like <p>.
                    f.write(f"- {text}\n\n")
            f.write("---\n\n")

    print(f"Saved to {filepath}")

if __name__ == "__main__":
    main()
