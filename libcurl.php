<?php
function get_web_page( $url, $timeout=3600 )
{
  $options = array(
    CURLOPT_RETURNTRANSFER => true,     // return web page
    CURLOPT_HEADER         => false,    // don't return headers
    CURLOPT_ENCODING       => "",       // handle all encodings
    CURLOPT_USERAGENT      => "Mozilla", // who am i
    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
    CURLOPT_TIMEOUT        => $timeout,      // timeout on response
    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

  $ch      = curl_init( $url );
  curl_setopt_array( $ch, $options );
  $content = curl_exec( $ch );
  $err     = curl_errno( $ch );
  $errmsg  = curl_error( $ch );
  $header  = curl_getinfo( $ch );
  curl_close( $ch );

  $header['errno']   = $err;
  $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $header;
}
/*
function get_content($url )
{
  $data=get_web_page($url);
  $http_code=$data['http_code'];
  $content=$data['content'];
  echo "Http code=$http_code";
  if($http_code==301 || $http_code==302)
  {

    $url=get_redirected_url($url);
    $data=get_web_page($url);
    $http_code=$data['http_code'];
    $content=$data['content'];
    echo "Http code=$http_code";
  }
  else
  {

    if($http_code != 200) return null;
  }
  return $content;
}*/
?>
