<?php
set_time_limit(0);

$channels = [
    'starsports1tamil' => 'https://TS-j8bh.onrender.com/Box.ts?id=4',
    'sonyyay' => 'https://TS-j8bh.onrender.com/Box.ts?id=3',
];

$segmentDuration = 10;         // Each segment = 10 seconds
$maxSegments = 30;             // 5 minutes = 300 seconds / 10s = 30 segments
$baseDir = __DIR__;            // Save in current directory

foreach ($channels as $name => $url) {
    echo "▶ Starting $name...\n";

    $outputDir = "$baseDir/$name";
    if (!file_exists($outputDir)) mkdir($outputDir, 0777, true);

    $segments = [];
    $segmentIndex = 0;

    while (true) {
        $segmentFile = "$outputDir/index$segmentIndex.ts";
        $liveUrl = $url . '&cache=' . time();

        // Real-time FFmpeg
        $cmd = "ffmpeg -y -fflags +discardcorrupt -re -rw_timeout 5000000 -i \"$liveUrl\" -t $segmentDuration -c copy \"$segmentFile\" 2>&1";
        echo "Running: $cmd\n";
        $output = shell_exec($cmd);
        echo $output;

        if (file_exists($segmentFile)) {
            $segments[] = "index$segmentIndex.ts";

            // Keep only the last 30 segments (~5 min)
            if (count($segments) > $maxSegments) {
                $old = array_shift($segments);
                $oldPath = "$outputDir/$old";
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                    echo "🗑 Deleted old segment: $old\n";
                }
            }

            // Write M3U8
            $m3u8 = "#EXTM3U\n#EXT-X-VERSION:3\n#EXT-X-TARGETDURATION:$segmentDuration\n";
            $m3u8 .= "#EXT-X-MEDIA-SEQUENCE:" . ($segmentIndex - count($segments) + 1) . "\n";
            foreach ($segments as $seg) {
                $m3u8 .= "#EXTINF:$segmentDuration,\n$seg\n";
            }

            file_put_contents("$outputDir/index.m3u8", $m3u8);
            echo "✅ Segment $segmentIndex created for $name\n";
        } else {
            echo "❌ Failed to save segment $segmentIndex for $name\n";
        }

        $segmentIndex++;
        sleep($segmentDuration);
    }
}
