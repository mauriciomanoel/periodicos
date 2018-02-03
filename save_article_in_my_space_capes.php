<?php
    set_time_limit(0);
    include('config.php');
    include('functions.php');

    function progress_capes($url) {
        $dom = new DOMDocument;
        $html = loadURL($url, COOKIE_CAPES, USER_AGENT_WINDOWS);
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        foreach ($dom->getElementsByTagName('a') as $node) {
            if ($node->hasAttribute( 'href' )) {
                while (@ ob_end_flush()); // end all output buffers if any
                if (strpos($node->getAttribute( 'href' ), 'basket.do') !== false) {
                    $urls = explode("?fn=", $node->getAttribute( 'href' ));
                    $url_action = "http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/basket.do?fn=" . $urls[1];
                    loadURL($url_action, COOKIE_CAPES, USER_AGENT_WINDOWS);
                    echo ' <a href="' . $url . '">' . $url . '</a><br>';
                    @ flush();
                    sleep(2);
                }
            }
        }
        sleep(rand(3,5));
    }

    echo "Page: 1 <br>";
    $time = time() . '000';
    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?ct=facet&fctN=facet_lang&fctV=eng&rfnGrp=2&rfnGrpCounter=2&frbg=&rfnGrpCounter=1&indx=1&fn=search&mulIncFctN=facet_rtype&mulIncFctN=facet_rtype&dscnt=0&rfnIncGrp=1&rfnIncGrp=1&scp.scps=scope%3A(%22CAPES%22)%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe&mode=Basic&vid=CAPES_V1&ct=facet&srt=rank&tab=default_tab&dum=true&fctIncV=newspaper_articles&fctIncV=articles&dstmp=' . $time . '&vl(freeText0)=' . QUERY;
    progress_capes($url);
    echo "Page: 2 <br>";
    $time = time() . '000';
    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?ct=Next+Page&pag=nxt&indx=1&pageNumberComingFrom=1&frbg=&rfnGrpCounter=2&fn=search&indx=1&mulIncFctN=facet_rtype&mulIncFctN=facet_rtype&dscnt=0&scp.scps=scope%3A(%22CAPES%22)%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe&rfnIncGrp=1&rfnIncGrp=1&vid=CAPES_V1&fctV=eng&mode=Basic&ct=facet&rfnGrp=2&tab=default_tab&srt=rank&fctN=facet_lang&dum=true&fctIncV=newspaper_articles&fctIncV=articles&dstmp=' . $time . '&vl(freeText0)=' . QUERY;
    progress_capes($url);
    for($page=3;$page<=52;$page++) {
        echo "Page: " . $page . "<br>";
        $time = time() . '000';        
        $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?ct=Next+Page&pag=nxt&indx=' . ($page-2) . '1&pageNumberComingFrom=' . ($page-1) . '&frbg=&rfnGrpCounter=2&indx=' . ($page-2) . '1&fn=search&mulIncFctN=facet_rtype&mulIncFctN=facet_rtype&dscnt=0&scp.scps=scope%3A(%22CAPES%22)%2CEbscoLocalCAPES%2Cprimo_central_multiple_fe&rfnIncGrp=1&rfnIncGrp=1&fctV=eng&mode=Basic&vid=CAPES_V1&ct=Next%20Page&rfnGrp=2&srt=rank&tab=default_tab&fctN=facet_lang&dum=true&fctIncV=newspaper_articles&fctIncV=articles&dstmp=' . $time . '&vl(freeText0)=' . QUERY;
        progress_capes($url);
    }

?>