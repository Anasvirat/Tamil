<?php
$mac = "00:1A:79:25:5B:B4";
$portal = "http://jiotv.be/stalker_portal/server/load.php";

$headers = [
    "User-Agent: Mozilla/5.0",
    "Accept: */*",
    "Referer: http://jiotv.be/stalker_portal/c/",
    "Cookie: mac=$mac; stb_lang=en; timezone=GMT",
];

$init_url = "http://jiotv.be/stalker_portal/server/load.php?type=stb&action=handshake&token=&JsHttpRequest=1-xml";
$ch = curl_init($init_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
