<?php
$months=array("January","February","March","April","May","June","July","August",
              "September","October","November","December");
function rule_0($sentence,$sentence_id)
{
  global $rule_hits;

  //check for presence of structure like
  //in 2007

  //we will split the sentence on in, and then for each element from pos 1, check
  //if there is a 4 digits and a space.
  $arr = explode(" in ",$sentence);
  $count = count($arr);
  for($i=1;$i<$count;$i++)
  {
    if($year=is_year($arr[$i]))
    {
      //printf("rule_0::match found for %s in sentence %s on fragment=%d\n",
        //     $arr[$i],$sentence,$i);
      $hit=array();
      $hit["sid"]=$sentence_id;
      $hit["rule"]=0;
      $hit["year"]=$year;
      $hit["sentence"]=$arr[$i-1]." in ".$arr[$i];
      $rule_hits[$sentence_id][]=$hit;
    }
  }
}

function rule_1($sentence,$sentence_id)
{
  global $rule_hits;
  if($year=has_year($sentence))
  {
    $hit=array();
    $hit["sid"]=$sentence_id;
    $hit["rule"]=1;
    $hit["year"]=$year;
    $hit["sentence"]=$sentence;
    $rule_hits[$sentence_id][]=$hit;
    return;
  }
}
function rule_2($sentence,$sentence_id)
{
  global $rule_hits;
  global $months;
  $max_months=count($months);
  $present_months=array();

  //search if there are any months and if yes, note them aside

  for($i=0;$i<$max_months;$i++)
  {
    //printf("ISMONTH::%s\n",$months[$i]);
    if(strstr($sentence,strtolower($months[$i]))||strstr($sentence,
    substr(strtolower($months[$i]),0,3)))
    {
      $present_months[]=$i;
    }
  }
  if(!$present_months) return;
  for($year=2014;$year>1000;$year--)
  {
    for($i=0;$i<count($present_months);$i++)
    {
      $str="$year ".strtolower($months[$present_months[$i]]);
      if(strstr($sentence,$str))
      {
        $hit=array();
        $hit["sid"]=$sentence_id;
        $hit["rule"]=2;
        $hit["year"]=$year;
        $hit["month"]=$month;
        $hit["sentence"]=$sentence;
        $rule_hits[$sentence_id][]=$hit;
      }
      $str=strtolower($months[$present_months[$i]])." $year";
      if(strstr($sentence,$str))
      {
        $hit=array();
        $hit["sid"]=$sentence_id;
        $hit["rule"]=2.1;
        $hit["year"]=$year;
        $hit["month"]=$present_months[$i];
        $hit["sentence"]=$sentence;
        $rule_hits[$sentence_id][]=$hit;
      }
    }
  }
}

function rule_3($sentence, $sentence_id)
{
  global $rule_hits;
  global $months;
  $present_months=array();
  $max_months=count($months);
  $sentence=str_replace(",","",$sentence);
  for($i=0;$i<$max_months;$i++)
  {
    for($day=31;$day>0;$day--)
    {
      $str=substr(strtolower($months[$i]),0,3)." ".$day." ";
      //echo "rule3 checking $str\n";
      if(strstr($sentence,$str))
      {
        $hit=array();
        $hit["sid"]=$sentence_id;
        $hit["rule"]=3;
        $hit["month"]=$i;
        $hit["day"]=$day;
        $hit["sentence"]=$sentence;
        $hit["search"]=$str;
        $rule_hits[$sentence_id][]=$hit;
      }
      $str=$day." ".substr(strtolower($months[$i]),0,3);
      //echo "rule3 checking $str\n";
      if(strstr($sentence,$str))
      {
        $hit=array();
        $hit["sid"]=$sentence_id;
        $hit["rule"]=3;
        $hit["month"]=$i;
        $hit["day"]=$day;
        $hit["sentence"]=$sentence;
        $hit["search"]=$str;
        $rule_hits[$sentence_id][]=$hit;
      }
    }
  }
}

function is_year($part)
{
  $year=substr($part,0,4);
  for($i=2014;$i>1000;$i--)
  {
    if($i==$year)
      return $year;
  }
  return 0;
}
function has_year($part)
{
  //echo "entered has_year with $part\n";
  for($i=2014;$i>1000;$i--)
  {
    if(strstr($part,"$i"))
    {
      //echo "Match found for $i\n";
      return $i;
    }
  }
  return 0;
}
function last_few_tokens($string,$howmany)
{
  $ret='';
  $arr=explode(" ",$string);
  $count=count($arr);
  $count-=$howmany;
  for($i=0;$i<=$howmany;$i++)
  {
    $ret.= $arr[$count+$i]." ";
  }
  return trim($ret);
}
