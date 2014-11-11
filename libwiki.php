<?php
//get_wiki_title("mahatma gandhi");
//get_wiki_title("flipkart");
function get_wiki_title($search)
{
    $base="http://en.wikipedia.org/w/api.php?action=opensearch&limit=5&namespace=0&format=json&search=".urlencode($search);
    $content = file_get_contents($base);
    $json = json_decode($content,true);
    $result=$json[1];
    $entry=$result[0];
    return $entry;
}
