<?php
# $link = "http://eris.dokhlab.org/";
  $link = "http://chiron.dokhlab.org/";
  $email_addresses = "Nikolay V. Dokholyan <dokh@med.unc.edu>, Feng Ding <fding@unc.edu>, Srinivas Ramachandran <ramachan@email.unc.edu>, Pradeep Kota <pkota@email.unc.edu>";
  $eol = "\n"; # for unix
# Boundry for marking the split & Multitype Headers
  $mime_boundary=md5(time());
  
  $headers .= 'From: Chiron: Rapid protein energy minimization server '.$eol;
  $headers .= 'Reply-To: no-reply@email-notification'.$eol;
  $headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
  $headers .= 'MIME-Version: 1.0'.$eol;
  $headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

  $mail_subject = "Chiron: Significant Update";
  

  $msg = "";
  $msg .= "--".$mime_boundary.$eol;
  $msg .= "Content-Type: text/html; charset=iso-8859-1".$eol;
  $msg .= "\nDear Administrator,<br/><br/>\n" .
  	"The Chiron server has been revamped. We now use a modified protocol for clash removal with alternate quench steps at low temperature. This protocol has been tested on a set of protein structures from the PDB.<br><br>All jobs pending during down time have been processed. The server is now accepting jobs.<br><br>".
        "This e-mail is just a notification. No action is required at this moment<br/>" .
        "<br/><br/>Automated mail sender\n\n<br/><a href=\"$link\">$link</a>".$eol.$eol;
#Text version
  $msg .= "--".$mime_boundary.$eol;
  $msg .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
  $msg .= "\nDear Administrator,<br/><br/>\n" .
  	"The Chiron server has been revamped. We now use a modified protocol for clash removal with alternate quench steps at low temperature. This protocol has been tested on a set of protein structures from the PDB.<br><br>All jobs pending during down time have been processed. The server is now accepting jobs.<br><br>".
        "This e-mail is just a notification. No action is required at this moment<br/>" .
        "<br/><br/>Automated mail sender\n\n<br/><a href=\"$link\">$link</a>".$eol.$eol;
  mail($email_addresses, $mail_subject, $msg, $headers);
?>
