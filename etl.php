<?php
include "libcurl.php";
include "rules.php";

$rule_hits=array();
$sentences=array();

define("MAX_RULES",4);
$query="flipkart";


$page="http://en.wikipedia.org/wiki/Flipkart";
//$page="http://en.wikipedia.org/wiki/Mahatma_Gandhi";

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
//Now process the rule_hits array

foreach($rule_hits as $sentence_hits)
{
  $event=array();
  foreach($sentence_hits as $hit)
  {
    print_r($hit);
    $event['sid']=$hit['sid'];
    if(isset($hit['day'])) $event['day']=$hit['day'];
    if(isset($hit['month'])) $event['month']=$hit['month'];
    if(isset($hit['year'])) $event['year']=$hit['year'];
    $eid=sprintf("%4d%02d%02d",$event['year'],$event['month'],$event['day']);
    $eid=ceil($eid);

    echo "End of hit with index $eid\n";
  }
  $events[$eid]=$event;
}
ksort($events,SORT_NUMERIC);
print_r($events);
//printing in formatted way
foreach($events as $event)
{
print_event($event);
}
$cx=count($events);
echo "Count=$cx\n";
function print_event($event)
{
global $sentences;
global $months;
$year=$event['year'];
$month=$months[$event['month']];
$day=$event['day'];
$sentence=$sentences[$event['sid']];
echo "$year-$month-$day $sentence\n";
}


function process_page($content)
{
  global $rule_hits;
  global $sentences;
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
  $content = preg_replace('/[\x00-\x1F\x80-\x9F]/u', ' . ', $content);
  $end_pos= strrpos ( $content , 'References' );
  $start_pos=strpos($content,'From Wikipedia');
  if($start_pos === false)
    $start_pos = 0;
  if($end_pos === false)
    $end_pos = strlen($content);

  $content = substr($content,$start_pos, $end_pos - $start_pos);

  //extract content between 'From Wikipedia, the free encyclopedia' till references section

  //$content=get_content_between($content, 'From Wikipedia','References');
  $content = strip_square_brackets($content);
  $content = replace_square_brackets($content);
  $content = replace_brackets($content);
  $content = strip_duplicate_spaces($content);
  $sentences = explode('. ',$content);
  print_r($sentences);
  $count = count($sentences);
  echo "There are $count sentences\n";
  //ignore the last reference section
  for($i=0;$i<$count-1;$i++)
  {
    $sentence=$sentences[$i];
    process_sentence($sentence,$i);
  }
  print_r($rule_hits);
  printf("Number of hits=%d\n",count($rule_hits));
}

function process_sentence($sentence,$sentence_id)
{
  //$sentence = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $sentence);
  $sentence = str_replace('\r', '', $sentence);
  $sentence = str_replace('\n', '', $sentence);
  $sentence = str_replace('\t', '', $sentence);
  $sentence = trim($sentence);
  $sentence = strtolower($sentence);
  if(strlen($sentence) < 20) return;
  if(strlen($sentence) > 150) return;
  //display_bytes($sentence);
  //return;
  for($i=0;$i<MAX_RULES;$i++)
  {
    $fun="rule_$i";
    $ret = $fun($sentence,$sentence_id);
    if($ret)
    {
      print_r($ret);
      //var_dump($ret);
    }
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

function strip_square_brackets($string)
{
  $string=preg_replace('/\[\d*\]/',"",$string);
  return $string;
}

function replace_square_brackets($string)
{
  $string=str_replace("[","",$string);
  $string=str_replace("]","",$string);
  return $string;
}

function replace_brackets($string)
{
  $string=str_replace("(","",$string);
  $string=str_replace(")","",$string);
  return $string;
}
function display_bytes($var)
{
  echo "Var=$var\n\n\n\n\n";
  for($i = 0; $i < strlen($var); $i++)
  {
    echo "c=".$var[$i]."code=".ord($var[$i])."\n";
  }
}


function get_content_between($content, $start, $end)
{
  $r = explode($start, $content);
  if (isset($r[count($r)-1])){
    $r = explode($end, $r[count($r)-1]);
    return $r[0];
  }
  return '';
}
