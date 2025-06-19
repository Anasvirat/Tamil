<?php
set_time_limit(0); // Keep running
ob_implicit_flush(); // Output logs immediately

$channels = [
    'starsports1tamil' => 'https://TS-j8bh.onrender.com/Box.ts?id=4',
    'sonyyay' => 'https://TS-j8bh.onrender.com/Box.ts?id=3',
];

$segmentDuration = 10; // seconds
$maxSegments = 5;
$baseDir = __DIR__ . '/output';

if (!file_exists($baseDir)) mkdir($baseDir);

foreach ($channels as $name => $url) {
    echo "▶ Starting $name<br>";

    $outputDir = "$baseDir/$name";
    if (!file_exists($outputDir)) mkdir($outputDir, 0777, true);

    $segments = [];
    $segmentIndex = 0;

    // Loop until manually stopped (or browser closed)
    while (true) {
        $segmentFile = "$outputDir/index$segmentIndex.ts";

        // Add cache buster to avoid caching
        $liveUrl = $url . '&cache=' . time();

        // Real-time FFmpeg command
        $cmd = "ffmpeg -y -fflags +discardcorrupt -re -rw_timeout 5000000 -i \"$liveUrl\" -t $segmentDuration -c copy \"$segmentFile\" 2>&1";
        echo "<pre>$cmd</pre>";
        $output = shell_exec($cmd);
        echo "<pre>$output</pre>";

        // Check if segment created
        if (file_exists($segmentFile)) {
            $segments[] = "index$segmentIndex.ts";

            // Remove old segments
            if (count($segments) > $maxSegments) {
                $old = array_shift($segments);
                if (file_exists("$outputDir/$old")) {
                    unlink("$outputDir/$old");
                    echo "🗑 Deleted $old<br>";
                }
            }

            // Write playlist
            $m3u8 = "#EXTM3U\n#EXT-X-VERSION:3\n#EXT-X-TARGETDURATION:$segmentDuration\n";
            $m3u8 .= "#EXT-X-MEDIA-SEQUENCE:" . ($segmentIndex - count($segments) + 1) . "\n";

            foreach ($segments as $seg) {
                $m3u8 .= "#EXTINF:$segmentDuration,\n$seg\n";
            }

            file_put_contents("$outputDir/index.m3u8", $m3u8);
            echo "✅ Segment $segmentIndex saved<br>";
        } else {
            echo "❌ Failed to save segment $segmentIndex<br>";
        }

        $segmentIndex++;
        sleep($segmentDuration);
    }
}
?>
