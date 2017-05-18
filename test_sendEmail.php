<?php

//require("../../PHPMailer/class.phpmailer.php");
//require("../../PHPMailer/class.pop3.php");
require("/var/www/html/SMMGetInfo/gratismailcorp/public/libraries/PHPMailer/class.phpmailer.php");
require("/var/www/html/SMMGetInfo/gratismailcorp/public/libraries/PHPMailer/class.pop3.php");

echo "<pre>Start test Send Mail [".date('Y-m-d H:i:s')."].</pre>";

//$EMAILSENDER = "1149test1@ais.co.th";
//$SENTPWD = "3UPN4r@5Yb#e";

$EMAILSENDER = "1149test3@ais.co.th";
$SENTPWD = "D!XyV#2g3$$bD";

$EMAIL_ADDR = "1149test1@ais.co.th";
$EMAIL_SUBJECT = "[SMM] Test Email for corporate";
$EMAIL_MSG = "test msg - ข้อความทดสอบ [".date('Y-m-d H:i:s')."]. ";

/*
$mail = new PHPMailer();
$mail->SMTPDebug = 3; 
//$mail->SMTPAuth = false; 
$mail->IsSMTP(); // send via SMTP

$mail->Host     = "mailgw.channel.ais.co.th"; // SMTP servers
$mail->SMTPAuth = false;     // turn on SMTP authentication
$mail->Username = "";  // SMTP username
$mail->Password = ""; // SMTP password
//$mail->Username = $EMAILSENDER;  // SMTP username
//$mail->Password = $SENTPWD; // SMTP password

//$mail->Host     = "extsmtp.corp.ais900.org"; // SMTP servers
//$mail->CharSet = "Shift_JIS"; // ADD BY SUWICH //
$mail->Encoding = "base64"; // ADD BY SUWICH //
//$mail->SMTPAuth = true;     // turn on SMTP authentication
//$mail->Username = $EMAILSENDER;  // SMTP username
//$mail->Password = $SENTPWD; // SMTP password


$mail->From     = $EMAILSENDER;
$mail->FromName = "1149 TEST 3";
  
$mail->AddAddress($EMAIL_ADDR);         
//$mail->AddAddress("suwich@suwich.com");               // optional name

$mail->WordWrap = 50;                              // set word wrap
//$mail->AddAttachment("/var/tmp/file.tar.gz");      // attachment
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); 
$mail->IsHTML(true);                               // send as HTML

$mail->Subject  =  $EMAIL_SUBJECT;
$mail->Body     =  $EMAIL_MSG;
$mail->AltBody  =  $EMAIL_MSG;

//send the message, check for errors
if (!$mail->send()) {
    print "Mailer Error: " . $mail->ErrorInfo;
	exit;return;
} else {
    print "Message sent!";
}*/

print "<pre>Preparing Email ... </pre>"; 
						$mail = new PHPMailer();
						$mail->SMTPDebug = 1;
						$mail->IsSMTP();
						// $mail->SMTPDebug = 3;
						$mail->Host     = "mailgw.channel.ais.co.th";
						$mail->SMTPAuth = false;
						$mail->Username = $EMAILSENDER;
						$mail->Password = $SENTPWD;
						$mail->Encoding = "base64";
						$mail->CharSet  = "UTF-8";
						$mail->From     = $EMAILSENDER;
						$mail->FromName = "";
						$mail->WordWrap = 50;
						$mail->AddAddress('1149test1@ais.co.th');
						//$mail->AddAddress($EMAIL_ADDR);
						
						$mail->IsHTML(true);
						$mail->Subject  = $EMAIL_SUBJECT;
						$mail->Body     =  $EMAIL_MSG;
						
						print "<pre>"; print_r($mail); print "</pre>";
						if(!$mail->send())
						{
						  print "Mailer Error: " . $mail->ErrorInfo;
						}	
						print "Message sent!";

?>
