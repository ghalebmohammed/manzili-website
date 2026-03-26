import os
import re
import urllib.request
import urllib.parse
from pathlib import Path

# Paths
base_dir = r"c:\Users\ghale\Desktop\projects WebSites with Antigravity\ManziliWebSite\public"
css_dir = os.path.join(base_dir, "css")
js_dir = os.path.join(base_dir, "js")
fonts_dir = os.path.join(base_dir, "fonts")
webfonts_dir = os.path.join(base_dir, "webfonts")

for d in [css_dir, js_dir, fonts_dir, webfonts_dir]:
    os.makedirs(d, exist_ok=True)

# Fake user agent for google fonts to get woff2
headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36'}

def download_file(url, path):
    print(f"Downloading {url} to {path}")
    req = urllib.request.Request(url, headers=headers)
    try:
        with urllib.request.urlopen(req) as response, open(path, 'wb') as out_file:
            data = response.read()
            out_file.write(data)
    except Exception as e:
        print(f"Error downloading {url}: {e}")

# 1. Google Fonts
print("Downloading Google Fonts CSS...")
fonts_url = "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap"
req = urllib.request.Request(fonts_url, headers=headers)
css_content = urllib.request.urlopen(req).read().decode('utf-8')

# Find all font URLs
urls = set(re.findall(r"url\((https://[^)]+)\)", css_content))
for url in urls:
    filename = url.split('/')[-1]
    local_path = os.path.join(fonts_dir, filename)
    download_file(url, local_path)
    # Replace url in CSS
    css_content = css_content.replace(url, f"../fonts/{filename}")

with open(os.path.join(css_dir, "fonts.css"), "w", encoding="utf-8") as f:
    f.write(css_content)

# 2. FontAwesome
print("Downloading FontAwesome...")
fa_css_url = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
download_file(fa_css_url, os.path.join(css_dir, "all.min.css"))

fa_base = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts"
fa_fonts = [
    "fa-brands-400.ttf", "fa-brands-400.woff2",
    "fa-regular-400.ttf", "fa-regular-400.woff2",
    "fa-solid-900.ttf", "fa-solid-900.woff2",
    "fa-v4compatibility.ttf", "fa-v4compatibility.woff2"
]
for fa_font in fa_fonts:
    download_file(f"{fa_base}/{fa_font}", os.path.join(webfonts_dir, fa_font))

# 3. AOS
print("Downloading AOS...")
aos_css_url = "https://unpkg.com/aos@2.3.1/dist/aos.css"
aos_js_url = "https://unpkg.com/aos@2.3.1/dist/aos.js"
download_file(aos_css_url, os.path.join(css_dir, "aos.css"))
download_file(aos_js_url, os.path.join(js_dir, "aos.js"))

print("Done!")
