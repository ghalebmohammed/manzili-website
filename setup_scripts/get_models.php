<?php
$apiKey = "AIzaSyA3vJ8hSZ3YbHDIQBRQ8ZO_PmjXXKdnjaE";
$json = file_get_contents("https://generativelanguage.googleapis.com/v1beta/models?key=$apiKey");
$data = json_decode($json, true);
foreach ($data['models'] as $m) {
    if (in_array("generateContent", $m['supportedGenerationMethods'] ?? [])) {
        echo $m['name'] . "\n";
    }
}
