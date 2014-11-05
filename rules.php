<?php
function rule_0($sentence)
{
//check for presence of structure like
//in 2007

//we will split the sentence on in, and then for each element from pos 1, check
//if there is a 4 digits and a space.
$arr = explode("in ",$sentence);
$count = count($arr);
for($i=1;$i<$count;$i++)
{
  if(is_year($arr[$i]))
  {
    printf("rule_0::match found for %s in sentence $sentence\n",$arr[$i]);
  }
}
}

function rule_1($sentence)
{
}

function is_year($part)
{
  $year=substr($part,0,4);
  for($i=2014;$i>1000;$i--)
  {
    if($i==$year)
      return 1;
  }
  return 0;
}

