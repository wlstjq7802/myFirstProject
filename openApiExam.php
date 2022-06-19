<?php

$ch = curl_init();
$url = 'http://openapi.molit.go.kr:8081/OpenAPI_ToolInstallPackage/service/rest/RTMSOBJSvc/getRTMSDataSvcAptTrade'; /*URL*/
$queryParams = '?' . urlencode('serviceKey') . '=Wpa7W5yrZ5xtUuiURVrcdvlsjNKZ0uDvzQQL3XClvCSNs/vjlo+1zT1NMp0JT96RwontV99HNoO9GxASbvM4Ow=='; /*Service Key*/
$queryParams .= '&' . urlencode('LAWD_CD') . '=' . urlencode('11110'); /**/
$queryParams .= '&' . urlencode('DEAL_YMD') . '=' . urlencode('201512'); /**/

curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$response = curl_exec($ch);
curl_close($ch);

var_dump($response);

?>