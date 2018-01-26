<?php
set_time_limit(0);

function loadURL($url) {
    $encoded = "";
    $ch 		= curl_init($url);
    curl_setopt( $ch, CURLOPT_POSTFIELDS,  $encoded );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Cookie: JSESSIONID=2E550C27EE4B0B6AFAE06243254DCEA7; sto-id-%3FSaaS-A_prod%3FPMTNA03.prod.primo.1701=HNHIBMAK; PRIMO_RT="));
    curl_setopt( $ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0');
    $output 	= curl_exec($ch);
    curl_close( $ch );
    return $output;
}

function progress_capes($url) {
    $dom = new DOMDocument;
    $html = loadURL($url);
    @$dom->loadHTML($html);
    $dom->preserveWhiteSpace = true;
    foreach ($dom->getElementsByTagName('a') as $node) {
        if ($node->hasAttribute( 'href' )) {

            if (strpos($node->getAttribute( 'href' ), 'basket.do') !== false) {
                $urls = explode("?fn=", $node->getAttribute( 'href' ));
                $url_action = "http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/basket.do?fn=" . $urls[1];
                loadURL($url_action);
                echo ' <a href="' . $url . '">' . $url . '</a><br>'; 
            }                
        }
    }
    sleep(3);
}

$time = time() . '000';
$url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?fn=search&ct=search&initialSearch=true&mode=Basic&tab=default_tab&indx=1&dum=true&srt=rank&vid=CAPES_V1&frbg=&vl%28freeText0%29=%22Internet+of+Things%22+and+Medical&scp.scps=scope%3A%28%22CAPES%22%29%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe';
progress_capes($url);
$url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?ct=Next+Page&pag=nxt&indx=1&pageNumberComingFrom=1&frbg=&&indx=1&fn=search&dscnt=0&scp.scps=scope%3A(%22CAPES%22)%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe&mode=Basic&vid=CAPES_V1&ct=search&srt=rank&tab=default_tab&vl(freeText0)=%22Internet%20of%20Things%22%20and%20Medical&dum=true&dstmp=' . time() . '000';
progress_capes($url);

$page = 3;
for($page=3;$page<=62;$page++) {
    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?ct=Next+Page&pag=nxt&indx=' . ($page-2) . '1&pageNumberComingFrom=' . ($page-1) . '&frbg=&indx=' . ($page-2) . '1&fn=search&dscnt=0&scp.scps=scope%3A(%22CAPES%22)%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe&vid=CAPES_V1&mode=Basic&ct=Next%20Page&srt=rank&tab=default_tab&dum=true&vl(freeText0)=%22Internet%20of%20Things%22%20and%20Medical&dstmp=' . time() . '000';
    var_dump($page);
    progress_capes($url);
}

?>