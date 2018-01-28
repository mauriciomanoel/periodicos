<?php
    set_time_limit(0);
    include('config.php');
    include('functions.php');
    
    function save_data_key($dom, $file) {
        $content        = "";
        $data_cid       = "";
        $link_pdf       = "";
        $link_article   = "";
        $title_article  = "";
        $cited_by       = "";
        foreach ($dom->getElementsByTagName('div') as $node) {

            if ($node->hasAttribute( 'data-cid' )) {
                $data_cid = trim($node->getAttribute( 'data-cid' ));
            }
            if ($node->hasAttribute( 'class' )) {
                if ($node->getAttribute( 'class' ) == "gs_or_ggsm") {
                    $child = $node->firstChild;
                    $link_pdf = trim($child->getAttribute( 'href' ));
                    // echo "<pre>"; var_dump($child->getAttribute( 'href' ));
                }
                if ($node->getAttribute( 'class' ) == "gs_ri") {
                    $childsDiv = $node->childNodes;
                    $nodeTitle = $childsDiv->item(0);
                    if ($nodeTitle->getElementsByTagName('a')->length > 0) {
                        $link_article = trim($nodeTitle->getElementsByTagName('a')->item(0)->getAttribute( 'href' ));
                    }                    
                    $title_article = trim($nodeTitle->textContent);
                    
                    foreach($childsDiv as $child) {
                        //  var_dump($child);
                        $textContent = trim($child->textContent);
                        if (!empty($textContent) && strpos($textContent, "Citado por") !== false) {
                            preg_match('/\d+/', $textContent, $matches);
                            $cited_by = $matches[0];
                            break;
                        }
                        // if ($child->getAttribute( 'class' ) == "gs_fl") {
                        //     $childsA = $node->childNodes;
                        //     $nodeTitle = $childsA->item(5);
                        //     if (strpos(trim($nodeTitle->textContent), "Citado por") !== false) {
                        //         $cited_by = trim(str_replace("Citado por","",trim($nodeTitle->textContent)));
                        //         var_dump( $cited_by); exit;
                        //     }

                        // }
                    }
                }

                // if ($node->getAttribute( 'class' ) == "gs_fl") {
                //     $childs = $node->childNodes;
                //     $nodeTitle = $childs->item(5);
                //     if (strpos(trim($nodeTitle->textContent), "Citado por") !== false) {
                //         $cited_by = trim(str_replace("Citado por","",trim($nodeTitle->textContent)));
                //         var_dump( $cited_by);
                //     } 
                // }                
            }
            if (!empty($data_cid) && !empty($title_article) && !empty($link_article)) {
                // var_dump($cited_by); exit;
                $content .= $data_cid . "|" . slug($title_article) . "|" . $title_article . "|" . $link_article . "|". $link_pdf . "|". $cited_by . "\r\n";
                $data_cid = "";
                $link_pdf = "";
                $link_article = "";
                $title_article = "";
                $cited_by = "";
            }
        }
        
        if (!empty($content)) {
            var_dump( $content);
            file_put_contents($file, $content);
        }
    
    }
    function progress_google($url, $file) {
        
        $html = loadURL($url, COOKIE_GOOGLE, USER_AGENT_WINDOWS);
        libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
        $dom = new DOMDocument('1.0', 'UTF-8');
        // @$dom->loadHTML($html);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = true;
        save_data_key($dom, $file);
        foreach ($dom->getElementsByTagName('div') as $node) {
            if ($node->hasAttribute( 'data-cid' )) {
                $data_cid = $node->getAttribute( 'data-cid' );
                $url_action = "https://scholar.google.com.br/citations?hl=pt-BR&xsrf=AMstHGQAAAAAWm7Akp7CS8tPX2LZeIp3OCG_YbL6uQqI&continue=/scholar?q=%22Internet+of+Things%3B+Medical%22&hl=pt-BR&as_sdt=0,5&citilm=1&json=&update_op=library_add&info=" . $data_cid;
                $returno = loadURL($url_action, COOKIE_GOOGLE, USER_AGENT_WINDOWS);
                echo "<pre>"; var_dump($returno);
                sleep(0.5);
            }
        }       
    }

    $page = 270;
    $file = $page . "_Internet of Things_Medical.csv";

    libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
        $dom = new DOMDocument('1.0', 'UTF-8');
        $html = file_get_contents("google5.html");
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        save_data_key($dom, $file);
    
        exit;
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=%22Internet+of+Things%3B+Medical%22&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file);
?>