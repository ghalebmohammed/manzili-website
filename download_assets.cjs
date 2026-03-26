const fs = require('fs');
const path = require('path');
const https = require('https');

const baseDir = path.join(__dirname, 'public');
const cssDir = path.join(baseDir, 'css');
const jsDir = path.join(baseDir, 'js');
const fontsDir = path.join(baseDir, 'fonts');
const webfontsDir = path.join(baseDir, 'webfonts');

[cssDir, jsDir, fontsDir, webfontsDir].forEach(d => {
    if (!fs.existsSync(d)) fs.mkdirSync(d, { recursive: true });
});

const headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36'
};

function downloadFile(url, dest) {
    return new Promise((resolve, reject) => {
        console.log(`Downloading ${url} to ${dest}`);
        const file = fs.createWriteStream(dest);
        const request = https.get(url, { headers }, (response) => {
            if (response.statusCode === 200) {
                response.pipe(file);
                file.on('finish', () => {
                    file.close(resolve);
                });
            } else if (response.statusCode >= 300 && response.statusCode < 400 && response.headers.location) {
                // handle redirect
                file.close();
                downloadFile(response.headers.location, dest).then(resolve).catch(reject);
            } else {
                file.close();
                fs.unlink(dest, () => reject(new Error(`Failed to download ${url}: ${response.statusCode}`)));
            }
        }).on('error', (err) => {
            fs.unlink(dest, () => reject(err));
        });
    });
}

function fetchText(url) {
    return new Promise((resolve, reject) => {
        https.get(url, { headers }, (response) => {
            let data = '';
            response.on('data', chunk => data += chunk);
            response.on('end', () => resolve(data));
        }).on('error', reject);
    });
}

async function main() {
    try {
        console.log("Downloading Google Fonts CSS...");
        const fontsUrl = "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap";
        let cssContent = await fetchText(fontsUrl);

        const urlRegex = /url\((https:\/\/[^)]+)\)/g;
        let match;
        const fontUrls = new Set();
        while ((match = urlRegex.exec(cssContent)) !== null) {
            fontUrls.add(match[1]);
        }

        for (const url of fontUrls) {
            const filename = url.split('/').pop();
            const localPath = path.join(fontsDir, filename);
            await downloadFile(url, localPath);
            cssContent = cssContent.split(url).join(`../fonts/${filename}`);
        }

        fs.writeFileSync(path.join(cssDir, 'fonts.css'), cssContent, 'utf-8');

        console.log("Downloading FontAwesome...");
        await downloadFile("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css", path.join(cssDir, "all.min.css"));

        const faBase = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts";
        const faFonts = [
            "fa-brands-400.ttf", "fa-brands-400.woff2",
            "fa-regular-400.ttf", "fa-regular-400.woff2",
            "fa-solid-900.ttf", "fa-solid-900.woff2",
            "fa-v4compatibility.ttf", "fa-v4compatibility.woff2"
        ];

        for (const faFont of faFonts) {
            await downloadFile(`${faBase}/${faFont}`, path.join(webfontsDir, faFont));
        }

        console.log("Downloading AOS...");
        await downloadFile("https://unpkg.com/aos@2.3.1/dist/aos.css", path.join(cssDir, "aos.css"));
        await downloadFile("https://unpkg.com/aos@2.3.1/dist/aos.js", path.join(jsDir, "aos.js"));

        console.log("Done!");
    } catch (err) {
        console.error(err);
    }
}

main();
