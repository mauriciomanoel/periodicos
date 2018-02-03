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
                        if (!empty($textContent) && (strpos($textContent, "Citado por") !== false || strpos($textContent, "Cited by") !== false)) {
                            preg_match('/\d+/', $textContent, $matches);
                            $cited_by = $matches[0];
                            break;
                        }
                    }
                }
            }
            if (!empty($data_cid) && !empty($title_article) && !empty($link_article)) {
                $replaces = array("[BOOK][B]", "[PDF][PDF]", "[LIVRO][B]", "[CITAÇÃO][C]", "[HTML][HTML]", "[CITATION][C]");
                $title_article = trim(str_replace($replaces, "", $title_article));
                // var_dump(mb_detect_encoding($title_article));
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
        echo $content . "<br>";
        if (!empty($content)) {
            $oldContent = @file_get_contents($file);
            $newContent = $oldContent . $content;
            file_put_contents($file, $newContent);
        }    
    }

    function progress_google($url, $file) {
        echo "<br>" . $url . "<br>";
        $parameters["referer"]  = $url;
        $parameters["host"]     = "scholar.google.com.br";
        $html = loadURL($url, COOKIE_GOOGLE, USER_AGENT_WINDOWS, array(), $parameters["referer"]);
        libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = true;
        save_data_key($dom, $file . ".csv"); // save data local 
        foreach ($dom->getElementsByTagName('div') as $node) {            
            if ($node->hasAttribute( 'data-cid' )) {
                while (@ ob_end_flush()); // end all output buffers if any
                    $data_cid = $node->getAttribute( 'data-cid' );
                    $url_action = "https://scholar.google.com.br/scholar?q=info:" . $data_cid . ":scholar.google.com/&output=cite&scirp=0&hl=pt-BR";
                    save_data_bibtex($url_action, $file . ".bib");
                @ flush();
                sleep(rand(5,8));

            }
        }       
    }


    $file = "google_scholar_internet_of_things_health";
        
    // $page = 100;
    // $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    // progress_google($url, $file);
    // // sleep(rand(6, 8));
    // exit($url);
    // $page = 110;
    // $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    // progress_google($url, $file);
    // sleep(rand(6, 8));
    // $page = 120; // 
    // $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    // progress_google($url, $file); 
    // sleep(rand(7, 12));
    // $page = 130; // 
    // $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    // progress_google($url, $file); 
    // sleep(rand(6, 11));
    $page = 140; // 
    $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    progress_google($url, $file); 
    sleep(rand(5, 12));
    $page = 150; 
    $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    progress_google($url, $file);
    
    sleep(15);
    
    $page = 160; 
    $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    progress_google($url, $file);
    sleep(rand(6, 8));
    $page = 170; // 
    $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    progress_google($url, $file); 
    sleep(rand(7, 12));
    $page = 180; // 
    $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    progress_google($url, $file); 
    sleep(rand(6, 11));
    $page = 190; // 
    $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    progress_google($url, $file); 
    sleep(rand(5, 12));
    $page = 200; // 
    $url = "https://scholar.google.com/scholar?&hl=en&as_sdt=1,5&&start=" . $page . "&q=" . QUERY;
    progress_google($url, $file);

    
?>