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

    function save_data_bibtex($url, $file) {
        $parameters = array();
        $content    = "";
        $parameters["host"] = "scholar.google.com.br";        
        $html = loadURL($url, COOKIE_GOOGLE, USER_AGENT_WINDOWS, array(), $parameters);
        libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = true;

        $parameters["host"] = "scholar.googleusercontent.com";
        $parameters["referer"] = $url;
        
        foreach ($dom->getElementsByTagName('div') as $node) {
            if ($node->hasAttribute( 'id' )) {
                if ($node->getAttribute( 'id' ) == "gs_citi") {
                    $child = $node->firstChild;
                    $urlBibtex = trim($child->getAttribute( 'href' ));
                    $content .=  loadURL($urlBibtex, COOKIE_GOOGLE, USER_AGENT_WINDOWS, array(), $parameters);
                    break;
                }
            }
        }
        var_dump($content);
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
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = true;
        save_data_key($dom, $file . ".csv"); // save data local 
        foreach ($dom->getElementsByTagName('div') as $node) {
            if ($node->hasAttribute( 'data-cid' )) {
                $data_cid = $node->getAttribute( 'data-cid' );
                //$url_action = "https://scholar.google.com.br/citations?hl=pt-BR&xsrf=" . XSRF_GOOGLE . "&continue=/scholar?q=" . QUERY . "&hl=pt-BR&as_sdt=0,5&citilm=1&json=&update_op=library_add&info=" . $data_cid;
                //$url_action = "https://scholar.googleusercontent.com/scholar.bib?q=info:" . $data_cid . ":scholar.google.com/&output=citation&scisig=" . XSRF_GOOGLE . "&scisf=4&ct=citation&cd=-1&hl=pt-BR";
                $url_action = "https://scholar.google.com.br/scholar?q=info:" . $data_cid . ":scholar.google.com/&output=cite&scirp=0&hl=pt-BR";
                save_data_bibtex($url_action, $file . ".bib");
                //echo "<pre>"; var_dump($retorno); exit;
                exit;
                sleep(rand(5,8));
            }
        }       
    }

    // libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
    // $dom = new DOMDocument('1.0', 'UTF-8');
    // $html = file_get_contents("google5.html");
    // $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    // save_data_key($dom, $file);

    // $html = file_get_contents("bibtex_google.html");
    // $file = "google_scholar_health_IoT.bib";
    // save_data_bibtex($html, $file);

    $page = 0; 
    $file = "google_scholar_health_IoT2";
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=" . QUERY . "&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file);
    exit;
    sleep(rand(6, 8));
    $page = 420; // 
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=" . QUERY . "&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file);
    sleep(rand(8, 12));
    $page = 430; // 
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=" . QUERY . "&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file);
    sleep(rand(9, 11));
    $page = 440; // 
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=" . QUERY . "&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file);
    sleep(rand(10, 12));
    $page = 450; // 
    $url = "https://scholar.google.com.br/scholar?start=" . $page . "&q=" . QUERY . "&hl=pt-BR&as_sdt=0,5";
    progress_google($url, $file); 
?>