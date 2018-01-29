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
                    }
                }
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
            $oldContent = @file_get_contents($file);
            $newContent = $oldContent . $content;
            file_put_contents($file, $newContent);
        }
    
    }
    function progress_google($url, $file) {
        
        $html = loadURL($url, COOKIE_GOOGLE, USER_AGENT_WINDOWS);
        libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
        $dom = new DOMDocument('1.0', 'UTF-8');
        // @$dom->loadHTML($html);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = true;
        save_data_key($dom, $file); // save data local
        foreach ($dom->getElementsByTagName('div') as $node) {
            if ($node->hasAttribute( 'data-cid' )) {
                $data_cid = $node->getAttribute( 'data-cid' );
                $url_action = "https://scholar.google.com.br/citations?hl=pt-BR&xsrf=" . XSRF_GOOGLE . "&continue=/scholar?q=" . QUERY . "&hl=pt-BR&as_sdt=0,5&citilm=1&json=&update_op=library_add&info=" . $data_cid;
                $returno = loadURL($url_action, COOKIE_GOOGLE, USER_AGENT_WINDOWS);
                echo "<pre>"; var_dump($returno);
                sleep(rand(3,5));
            }
        }       
    }

    // libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
    // $dom = new DOMDocument('1.0', 'UTF-8');
    // $html = file_get_contents("google5.html");
    // $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    // save_data_key($dom, $file);
    
    // exit;

    $page = 640; // 650
    $file = "google_scholar_Internet_of_Things_Healthcare.csv";
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=" . QUERY . "&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file);
    sleep(10);
    $page = 650; // 650
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=" . QUERY . "&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file);
?>