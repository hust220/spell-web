<?php

function chiron_mail($email_address, $subject, $body){ # eris email sender
  
  //translate plain text to html text
  // match protocol://address/path/
  $body_html = ereg_replace("[a-zA-Z]+://([.]?[a-zA-Z0-9_/-])*", "<a href=\"\\0\">\\0</a>", $body);
  // match www.something
  $body_html = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a href=\"http://\\2\">\\2</a>", $body_html);
  // \n => <br>
  $body_html = str_replace("\n","<br>\n",$body_html);
  
  $eol = "\n"; # for unix
  # Boundry for marking the split & Multitype Headers
  $mime_boundary=md5(time());

  $headers .= 'From: chiron: Rapid protein energy minimization server '.$eol;
  $headers .= 'Reply-To: no-reply@email.unc.edu'.$eol;
  $headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
  $headers .= 'MIME-Version: 1.0'.$eol;
  $headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

  $mail_subject = $subject;

  $msg = "";
  $msg .= "--".$mime_boundary.$eol;
  #text version
  $msg .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
  $msg .="\n";
  $msg .= $body;
  $msg .= $eol.$eol;
  #html version
  $msg .= "--".$mime_boundary.$eol;
  $msg .= "Content-Type: text/html; charset=iso-8859-1".$eol;
  $msg .="\n";
  $msg .= $body_html;
  $msg .= $eol.$eol;

  mail($email_address, $mail_subject, $msg, $headers);
  return(0);
}
?>
