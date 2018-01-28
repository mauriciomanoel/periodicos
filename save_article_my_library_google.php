<?php
    set_time_limit(0);
    include('config.php');
    include('functions.php');
    
    function progress_google($url) {
        $dom = new DOMDocument;
        $html = loadURL($url, COOKIE_GOOGLE, USER_AGENT_WINDOWS);
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        foreach ($dom->getElementsByTagName('div') as $node) {
            if ($node->hasAttribute( 'data-cid' )) {
                $data_cid = $node->getAttribute( 'data-cid' );
                $url_action = "https://scholar.google.com.br/citations?hl=pt-BR&xsrf=AMstHGQAAAAAWm5-XM6CMbDt7XWwaB55t0iAJ2HN7TPo&continue=/scholar?q=%22Internet+of+Things%3B+Medical%22&hl=pt-BR&as_sdt=0,5&citilm=1&json=&update_op=library_add&info=" . $data_cid;
                $returno = loadURL($url_action, COOKIE_GOOGLE, USER_AGENT_WINDOWS);
                echo "<pre>"; var_dump($returno);
            }
        }
        sleep(rand(4, 7));
    }

    libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
    $dom = new DOMDocument('1.0', 'UTF-8');
    $html = file_get_contents('google.html');
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    // $dom = new DOMDocument();
    // $html = file_get_contents('google.html');
    // $html = str_replace('<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">', '', $html);
    // @$dom->loadHTML($html);
    // var_dump($dom->saveHTML()); exit;
    foreach ($dom->getElementsByTagName('div') as $node) {
        // if ($node->hasAttribute( 'data-cid' )) {
        //     var_dump($node->attributes); exit;
        //     // var_dump($node); exit;
        //     $data_cid = $node->getAttribute( 'data-cid' );            
        //     echo "<pre>"; var_dump($data_cid);
        //     $novo = $node->getElementsByTagName('a');
        // }

        if ($node->hasAttribute( 'class' )) {
            if ($node->getAttribute( 'class' ) == "gs_or_ggsm") {
                $child = $node->firstChild;
                echo "<pre>"; var_dump($child->getAttribute( 'href' ));
            }
            if ($node->getAttribute( 'class' ) == "gs_ri") {
                $childs = $node->childNodes;
                $nodeTitle = $childs->item(1);
                
                var_dump(@$nodeTitle->getElementsByTagName('a')->item(0)->getAttribute( 'href' ), $nodeTitle->textContent); 
                //  var_dump($childs->item(2)); exit;
                // $nodeTitle = $childs[0]->getElementsByTagName('a')->item(0);
                // var_dump($dom->saveHTML($nodeTitle)); exit;
                // echo "<pre>"; var_dump($nodeTitle->getAttribute( 'href' ), $nodeTitle->textContent); exit;
                
            }
        }
    }
exit;

    $page = 50;
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=%22Internet+of+Things%3B+Medical%22&hl=pt-BR&as_sdt=0,5";
    progress_google($url);
?>