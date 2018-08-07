<?php

function useradmintablesort($query,$page,$nlimit,$url,$options){
# the total number of results
  $result = mysql_query($query);
  $nrow = mysql_num_rows($result);
# only the page
  $offset = $page*$nlimit;
  $query.="LIMIT $offset,$nlimit";
  $result = mysql_query($query);

  $sortswitch_id=$options['id'];
  $sortswitch_tlogin=$options['tlogin'];
  $sortswitch_level=$options['level'];

  $tableheader = "<table class='queryTable' width=100%>
    <tbody id='tbodytag'>
    <tr class='queryTitle' align=center>
    <td><a href='useradmin.php?sortcol=id&sortorder=$sortswitch_id'>UserID</a></td>
    <td>UserName</td>
    <td>Full Name</td>
    <td>Affiliation</td>
    <td>Email</td>
    <td>Status </td>
    <td><a href='useradmin.php?sortcol=level&sortorder=$sortswitch_level'>Level</a></td>
    <td>Action</td>
    </tr>";
  echo "$tableheader";
  $flag = -1;
  while ( $row = mysql_fetch_array($result)){
    $userid = $row['id'];
    $username = $row['username'];
    $namefirst = $row['firstname'];
    $namelast = $row['lastname'];
    $organization = $row['organization'];
    $email = $row['email'];
    $emailConfirmed = $row['emailConfirmed'];
    $approved = $row['emailApproved'];
    $level = $row['userlevel'];

    $emailstr="";
    if ($emailConfirmed == 1){
      $emailstr="<img src='style/img/verified.png' title='email verified' height='14px'>";
    }
    $statusstr ="";
    switch ($approved) {
      case 0:
	$statusstr="Pending";break;
      case 1:
	$statusstr="Approved";break;
      case 2:
	$statusstr="Suspended";break;
    }
    $levelstr="";
    if ($level==1) {$levelstr="User";}
    elseif ($level > 1) {$levelstr="<font color='red'> Admin</font>";}
    
    $actionstr=gen_action_str($row); #generate action forms for this user
    $actionstr.="<img src='style/img/info.png' title='details' onclick='user_details(\"$userid\")' >";
    $bgcolorstr= "";
    if($flag == 1) {$bgcolorstr="bgcolor='#ECE9DA'";}
    echo "<tr id='entrytag$userid' $bgcolorstr>";
    echo "<td align=left valign=top>$userid</td>";
    echo "<td align=left valign=top>$username</td>";
    echo "<td align=left valign=top>$namefirst $namelast </td>";
    echo "<td align=left valign=top>$organization</td>";
    echo "<td align=center valign=top>$email $emailstr</td>";
    echo "<td align=left valign=top> $statusstr </td>";
    echo "<td align=left valign=top> $levelstr </td>";
    echo "<td align=center valign=top> $actionstr </td>";
    echo "</tr>";
    $flag*=-1;
  }

  echo "</tbody></table>";

  ## navigation through pages
  $pagelast = $page - 1;
  $pagenext = $page + 1;
  $ntotalpage = ceil($nrow/$nlimit);
  if ($ntotalpage == 0) { # for display
    $pageindex = 0;
  }
  else { 
    $pageindex = $page +1;
  }

  if ($offset > 0) {
    echo " <a href=\"$url&page=$pagelast\"> &lt;prev </a>"; 
  }
  echo "( page $pageindex/$ntotalpage )";
  if ($offset+$nlimit < $nrow) {
    echo " <a href=\"$url&page=$pagenext\"> next &gt;</a>";
  }
}


function gen_action_str($row){ #generate action forms for this user
  $userid = $row['id'];
  $username = $row['username'];
  $namefirst = $row['firstname'];
  $namelast = $row['lastname'];
  $organization = $row['organization'];
  $email = $row['email'];
  $emailConfirmed = $row['emailConfirmed'];
  $approved = $row['emailApproved'];
  $level = $row['userlevel'];
  
  $useradmin_action_url="config/admin/useradmin_action.php";
  $str="";
  $str.="<select id='sel_$userid' with=70 style='width: 70px' onchange=\"action('$useradmin_action_url','$userid','$username') \">";
  $str.=" <option value=''> Action </option>";
  if($approved == 0){
    $str.="<option value='Disapprove'> Disapprove </option>";
    $str.="<option value='Approve'> Approve </option>";
  }
  if($approved == 1){
    $str.="<option value='Suspend'> Suspend </option>";
  }
  if($approved == 2){
    $str.="<option value='Resume'> Resume </option>";
  }
  $str.="</select>";
  return($str); 
}
?>
