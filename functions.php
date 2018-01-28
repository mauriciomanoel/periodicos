<?php

    function loadURL($url, $cookie, $user_agent, $fields=array()) {
        $ch 		= curl_init($url);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt( $ch, CURLOPT_HEADER, 0 );

        if (empty($fields) && count($fields) ==0) {
            curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
        } else {
            $fields_string = "";
            foreach($fields as $key => $value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        }

        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Cookie: " . $cookie));
        curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent);
        $output 	= curl_exec($ch);
        curl_close( $ch );
        return $output;
    }


?>