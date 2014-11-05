<?php
include "libcurl.php";
include "rules.php";

define("MAX_RULES",2);
$query="flipkart";


$page="http://en.wikipedia.org/wiki/Flipkart";

$header = get_web_page($page);

$http_code = $header['http_code'];

if($http_code ==0)
{
  echo "Could not connect to server\n";
  exit(1);
}
$content = $header['content'];
process_page($content);
printf("max_rules=%d\n",MAX_RULES);



function process_page($content)
{
  $len=strlen($content);
  echo "Content size=$len\n";
  $docObj = new DOMDocument();
  $docObj->loadHTML( $content );
  $xpath = new DOMXPath( $docObj );
  $nodes=$xpath->query('//div[@id="bodyContent"]');
  $count=0;
  $content ='';
  foreach($nodes as $node)
  {
    $content .= $node->nodeValue;
    $count++;
  }
  echo "\n\n\nCount=$count\n";
  //do some preprocessing to  support .coms
  $content = str_replace(".[",". [",$content);
  $sentences = explode('. ',$content);
  print_r($sentences);
  $count = count($sentences);
  echo "There are $count sentences\n";
  //ignore the last reference section
  for($i=0;$i<$count-1;$i++)
  {
    $sentence=$sentences[$i];
    process_sentence($sentence);
  }
}

function process_sentence($sentence)
{
  //$sentence = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $sentence);
  $sentence = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $sentence);
  $sentence = strip_duplicate_spaces($sentence);
  $sentence = str_replace('\r', '', $sentence);
  $sentence = str_replace('\n', '', $sentence);
  $sentence = trim($sentence);
  $sentence = strtolower($sentence);
  for($i=0;$i<MAX_RULES;$i++)
  {
    $fun="rule_$i";
    $fun($sentence);
  }

}

function strip_duplicate_spaces($content)
{
  $new=$content;
  do
  {
    $content = $new;
    $new=str_replace("  "," ",$content);
  }
  while($content !=$new);
  return $content;
}
