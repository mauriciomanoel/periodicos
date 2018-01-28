<?php
    set_time_limit(0);
    include('config.php');
    include('functions.php');

    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/basket.do?fn=display&vid=CAPES_V1&folderId=1159158862';
    $html = loadURL($url, COOKIE, USER_AGENT);

    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $dom->preserveWhiteSpace = true;
    $docs = array();
    foreach ($dom->getElementsByTagName('a') as $node) {
        if ($node->hasAttribute( 'href' )) {
            if (strpos($node->getAttribute( 'href' ), 'email.do?vid=CAPES_V1&docs=') !== false) {
                $docs[] = trim(str_replace("email.do?vid=CAPES_V1&docs=", "", $node->getAttribute( 'href' )));
            }                
        }
    }
    if (count($docs) > 0) {
        $fields = array(
            'encode' => 'UTF-8',
            'Button' => 'OK'
        );

        $fields_string = "";
        $bibtex        = "";
        foreach($docs as $key => $doc) {

            $fields_string .= 'docs='.$doc.'&'; 
            if ($key > 0 && ($key+1)%30 == 0) {
                $fields_string = rtrim($fields_string, '&');
                $url = "http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/PushToAction.do?pushToType=BibTeXPushTo&fromBasket=true&" . $fields_string;
                $bibtex .= loadURL($url, COOKIE, USER_AGENT, $fields);
                $fields_string = "";
            }
        }

        if (!empty($fields_string)) {
            $url = "http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/PushToAction.do?pushToType=BibTeXPushTo&fromBasket=true&" . $fields_string;
            $bibtex .= loadURL($url, COOKIE, USER_AGENT, $fields);
        }

        $name = "periodicos_capes_Internet_of_Things_and_Health1.bib";
        file_put_contents($name,$bibtex);
        echo "<pre>"; var_dump($bibtex);        
    }
?>