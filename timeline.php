<head>
<style>
body
{
background: #ffffff;
color: #000000;
text-align: center;
margin: 0px;
padding: 10px 10px;
}
body, td, th, textarea, input, select, h2, h3, h4, h5, h6 
{
font-weight: normal;
font-family: arial,helvetica,verdana,sans-serif;
}
table
{
border: 1px solid #000000;
border-collapse: collapse;
}
th, td
{
padding: 2px 2px;
border: 1px solid #a0a0a0;
font-size: 10pt;
}
a:visited
{
color: #105CB6;
}
a:hover, a:focus
{
color: #000033;
}
a:active
{
color: #000000;
}
.colname
{
font-weight: bold;
}
.tablename
{
font-weight: bold;
font-size: 12pt;
text-align: center;
}
.none
{
}
.waiting
{
background-color: #cccccc;
}
.running
{
background-color: #99ffff;
}
.retry
{
background-color: #d33682;
}
.preempted
{
background-color: #888888;
}
.completed
{ background-color: #99ff99; }
.running { background-color: #ffaaaa; }
.failed { background-color: #ff7777; }
.timeout { background-color: #ee99ee; }
.infra_error { background-color: #ffaaaa; }
.cancelled { background-color: #dddddd; }
.aborted { background-color: #dddddd; }
.batch_separator { background-color: #eeeeee; }
</style>
</head>
<?php
$db = new SQLite3('../db/stats.db',SQLITE3_OPEN_READWRITE);
$sql="select *, end_time - start_time as total_time from stats order by qid desc";
$ret = $db->query($sql);
?>
<table>
<tr>
<td class='colname' align='center'>Id</td>
<td class='colname' align='center'>State</td>
<td class='colname' align='center'>Start Time</td>
<td class='colname' align='center'>End Time</td>
<td class='colname' align='center'>Total Time</td>
<td class='colname' align='center'>Query</td>
</tr>
<?php
while($row = $ret->fetchArray(SQLITE3_ASSOC))
{
  $qid = $row['qid'];
  $state = $row['state'];
  $start_time = $row['start_time'];
  $start_time = date('Y-m-d H:i:s',floor($start_time));
  $end_time = $row['end_time'];
  $end_time = date('Y-m-d H:i:s',floor($end_time));
//  $end_time=time();
  $total_time = $row['total_time'];
  $query = $row['query'];

  if($state == 1)
  {
    $state = "Executing";
    $span_class="running";
  }
  else if($state ==2)
  {
    $state = "Completed";
    $span_class="completed";
  }
  else if($state == 3)
  {
    $state = "Failed";
    $span_class="failed";
  }
  else
  {
    $state = "Unknown";
  }
  //$start_time = round($start_time,4);
  //$end_time = round($end_time,4);
  $total_time = round($total_time,4);
  if(strlen($query)>128)
  $query=substr($query,0,128)." ...";
  ?>
    <tr>
    <td><?php echo $qid;?></td>
    <td class="<?php echo $span_class?>"><?php echo $state;?></td>
    <td><?php echo $start_time;?></td>
    <td><?php echo $end_time;?></td>
    <td><?php echo $total_time;?></td>
    <td><?php echo $query;?></td>
    </tr>
    <?php

}
?>
</table>
