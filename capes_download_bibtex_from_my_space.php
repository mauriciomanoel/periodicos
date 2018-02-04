<?php
    set_time_limit(0);
    include('config.php');
    include('functions.php');
    $total = 0;
    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/basket.do?fn=display&vid=CAPES&folderId=1176590190';
    //$html = loadURL($url, COOKIE_CAPES, USER_AGENT);
    $html = file_get_contents('capes.html');
    libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $dom->preserveWhiteSpace = true;
    $docs = array();
    foreach ($dom->getElementsByTagName('input') as $node) {        
         if ($node->getAttribute('name') == "docs") {
             $docs[] = $node->getAttribute('value');
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
            if (($key+1)%30 == 0) {
                $fields_string = rtrim($fields_string, '&');
                $url = "http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/PushToAction.do?pushToType=BibTeXPushTo&fromBasket=true&" . $fields_string;
                $bibtex .= loadURL($url, COOKIE_CAPES, USER_AGENT, $fields);
                $fields_string = "";
                $total += 30;
            }
        }

        if (!empty($fields_string)) {
            $url = "http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/PushToAction.do?pushToType=BibTeXPushTo&fromBasket=true&" . $fields_string;
            $bibtex .= loadURL($url, COOKIE_CAPES, USER_AGENT, $fields);
            $total++;
        }

        $name = "periodicos_capes_Internet_of_Things_AND_Medical.bib";
        file_put_contents($name, $bibtex);
        echo "Total de registros: " . $total;
    }
?>