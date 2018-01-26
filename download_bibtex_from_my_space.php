<?php
    set_time_limit(0);
    include('functions.php');

    
    $url = 'http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/basket.do?fn=display&fromUserArea=true&vid=CAPES_V1&fromPreferences=false&fromLink=gotoeShelfUI#&unzero=true';
    $html = loadURL($url);

    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $dom->preserveWhiteSpace = true;
    $docs = array();
    foreach ($dom->getElementsByTagName('a') as $node) {
        if ($node->hasAttribute( 'href' )) {
            // echo "<pre>"; var_dump($node->getAttribute( 'href' ));
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
                $bibtex .= loadURL("http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/PushToAction.do?pushToType=BibTeXPushTo&fromBasket=true&" . $fields_string, $fields);
                $fields_string = "";
            }

            
            
            // echo "<pre>"; var_dump($bibtex);
        }

        if (!empty($fields_string)) {
            $bibtex .= loadURL("http://rnp-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/PushToAction.do?pushToType=BibTeXPushTo&fromBasket=true&" . $fields_string, $fields);
        }

        $name = "periodicos_capes_Internet_of_Things_and_Medical.bib";
        file_put_contents($name,$bibtex);
        echo "<pre>"; var_dump($bibtex);
        
    }
    // 



?>