<?php
    set_time_limit(0);
    include('functions.php');

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
        sleep(2);
    }

    $time = time() . '000';
    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?fn=search&ct=search&initialSearch=true&mode=Basic&tab=default_tab&indx=1&dum=true&srt=rank&vid=CAPES_V1&frbg=&vl%28freeText0%29=%22Internet+of+Things%22+and+%22Healthcare%22&scp.scps=scope%3A%28%22CAPES%22%29%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe';
    progress_capes($url);
    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?ct=Next+Page&pag=nxt&indx=1&pageNumberComingFrom=1&frbg=&&indx=1&fn=search&dscnt=0&scp.scps=scope%3A(%22CAPES%22)%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe&mode=Basic&vid=CAPES_V1&ct=search&srt=rank&tab=default_tab&vl(freeText0)=%22Internet%20of%20Things%22%20and%20%22Healthcare%22&dum=true&dstmp=' . time() . '000';
    progress_capes($url);

    for($page=3;$page<=55;$page++) {
        $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?ct=Next+Page&pag=nxt&indx=' . ($page-2) . '1&pageNumberComingFrom=' . ($page-1) . '&frbg=&indx=' . ($page-2) . '1&fn=search&dscnt=0&scp.scps=scope%3A(%22CAPES%22)%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe&vid=CAPES_V1&mode=Basic&ct=Next%20Page&srt=rank&tab=default_tab&dum=true&vl(freeText0)=%22Internet%20of%20Things%22%20and%20%22Healthcare%22&dstmp=' . time() . '000';
        var_dump($page);
        progress_capes($url);
    }

?>