<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('www', function(){
  return $template = file_get_contents('public/email_templates/autoreply/en.html');
});
Route::get('login', 'AuthorizeController@loginForm');
Route::post('login', 'AuthorizeController@loginPost');
Route::get('logout', 'AuthorizeController@logoutGet');


Route::get('/testfn', function () {


    $a = array(
        '1149test1@ais.co.th' => 'AIS 1149 Test1',
        'karun@icctech.co.th' => 'Karun Jaraslertsuwan',
        'oxygen.uo@gmail.com' => 'Karun Jaraslertsuwan'
    );

    $b = array(
        'karun@icctech.co.th' => 'Karun Jaraslertsuwan'
    );
    $result = array_diff_key($a, $b);
    $hasOneReplyThenReturn = empty($result);
    if( $hasOneReplyThenReturn )
    {
      echo 'has One';
    }
    print_r(serialize($result));


});



Route::get('test/folder', function() {

    $pathForCheck = '/var/www/html/SMMGetInfo/gratismailcorp/public/' . date('Ymd');

    $isNotFoundFolderThenCreateFolder = !file_exists($pathForCheck);
    if( $isNotFoundFolderThenCreateFolder )
    {
        if (!mkdir($pathForCheck, 0755, true)) {
            write_log('Fail to create folder date time','error_create_folder');
        }
        echo 'has no folder';
    } else {
      echo 'has folder';
    }

});
// Test
// Route::get('test', function(){

//   // $priorityList = '';
//   // File::requireOnce('../include/utilityfunc.php');
//   // File::requireOnce('../include/priorityfunc.php');
//   // $chklang = ( is_Thai('Ciao Mondo :)')? "T" : "E" );
//   // getPriorityList($priorityList, $numPriority);
//   // $pri_score = getPriorityScore($priorityList, "EM", "EM", $chklang, 48, '', '', '');
//   // return $pri_score;

//   // return 'GOOGLE';

//   // GMMail::insert([
//   //   'folder_id' => 2,
//   //   'subject' => 'หัวข้อ',
//   //   'text' => 'Text',
//   //   'email' => Session::get('logged_in')->username,
//   //   'sent_from' => Session::get('logged_in')->username,
//   //   'sent_to' => serialize(['im@tzv.me']),
//   //   'sent_cc' => (isset($data['cc']) ? serialize($data['cc']) : ''),
//   //   'sent_bcc' => (isset($data['bcc']) ? serialize($data['bcc']) : ''),
//   //   'udate' => time(),
//   //   'seen' => 1,
//   //   'created_at' => date('Y-M-d H:i:s'),
//   //   'updated_at' => date('Y-M-d H:i:s'),
//   // ]);
//   // return 'inserted';

//   // File::requireOnce('public/libraries/PHPMailer/PHPMailerAutoload.php');

//   // $mail = new PHPMailer();
//   // $mail->SMTPDebug = false;
//   // $mail->IsSMTP();
//   // $mail->Host     = "mailgw.channel.ais.co.th";
//   // $mail->SMTPAuth = false;
//   // $mail->Username = "";
//   // $mail->Password = "";
//   // $mail->Encoding = "base64";
//   // $mail->From     = "1149test1@ais.co.th";
//   // $mail->FromName = "AIS Call Center";
//   // $mail->AddAddress("im@tzv.me");
//   // $mail->WordWrap = 50;
//   // // $mail->AddAttachment("/var/tmp/file.tar.gz");
//   // $mail->IsHTML(true);
//   // $mail->Subject  =  "Subject from AIS CALL CENTER";
//   // $mail->Body     =  "Message! sent by SMTP";
//   // $mail->AltBody  =  "Message! sent by SMTP";

//   // if(!$mail->send()):
//   //   return 'Error: '.$mail->ErrorInfo; exit;
//   // else:
//   //   return 'Email is sent.';
//   // endif;

//   // $account = EMConfig::where('emailaddr', '=', base64_decode(Request::get('u')))->first();
//   // if(!$account):
//   //   return 'Please re-check your email';
//   // else:
//   //   $username = base64_decode(Request::get('u'));
//   //   $password = $account->acctpwd;
//   //   Session::put('logged_in', (object)['username' => $username, 'password' => $password, 'u' => base64_encode($username), 'p' => base64_encode($password), 'name' => $account->acctname, 'sent_email' => $account->sentaddr]);
//   // endif;
//   // $data = [
//   //   'to' => 'im@tzv.me',
//   //   'txtSubject' => 'Email from AIS Call Center',
//   //   'txtDetail' => 'Dear Customer'
//   // ];
//   // Mail::send('layout.email-empty', ['data' => $data], function($message) use ($data){
//   //   $message->to($data['to']);
//   //   if(isset($data['cc'])):
//   //     $message->cc($data['cc']);
//   //   endif;
//   //   if(isset($data['bcc'])):
//   //     $message->bcc($data['bcc']);
//   //   endif;
//   //   $message->subject($data['txtSubject']);
//     // if(isset($data['files'])):
//     //   foreach($data['files'] as $file):
//     //     $file = GMAttachment::find($file);
//     //     $message->attach(URL::to("public/attachments/{$file->file_name}"), array('as' => $file->file_name_original, 'mime' => pathinfo($file->file_name_original, PATHINFO_EXTENSION)));
//     //   endforeach;
//     // endif;
//   // });

//   // return 'Email is sent.';

//   // $options = DB::table('suw_options')->first();
//   // Func::pr($options);
//   // exit;

//   // $text = 'Pathatai Pantaeng <pathatai@icctech.co.th>';
//   // $text = str_replace('>', '', explode(' <', $text)[1]);
//   // Func::pr($text);exit;

//   // $caseinfo = CASEINFO::orderBy('caseid', 'DESC')->where('feedtype', '=', 'EM')->take(10)->get();
//   // // $case_id = $caseinfo->caseid;
//   // return $caseinfo;
//   // $emconfig = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username.'@ais.co.th')->first();
//   // return $emconfig;
//   // phpinfo();
//   // $comments = DB::connection('oracle')->table('WBCOMMENT')->get();
//   // return $comments;

//   // $last_mail = GMMail::orderBy('mail_id', 'DESC')->first();
//   // $mail_id = ($last_mail->mail_id + 1);
//   // $mail = GMMail::insert([
//   //   'mail_id' => $mail_id,
//   //   'folder_id' => 3,
//   //   'subject' => 'No subject',
//   //   'text' => 'No text',
//   //   'email' => Session::get('logged_in')->username,
//   //   'udate' => time(),
//   //   'draft' => 1,
//   //   'created_at' => date('Y-M-d H:i:s'),
//   //   'updated_at' => date('Y-M-d H:i:s')
//   // ]);

//   // $accounts = GMAccount::get();
//   // return $accounts;

//   // $account = GMAccount::insert([
//   //   'email' => 'googlexx',
//   //   'name' => 'AIS Call Center',
//   //   'sent_email' => 'callcenter@ais.co.th',
//   //   'last_login_at' => date('Y-M-d H:i:s'),
//   //   'created_at' => date('Y-M-d H:i:s'),
//   //   'updated_at' => date('Y-M-d H:i:s'),
//   // ]);
//   // echo $account;
// });

// Email: remove all
// Route::get('remove-all', function(){
//   GMMail::truncate();
//   return 'GM_MAILS is truncated.';
// });

// File
Route::post('file/upload', 'GMFileController@attachmentUpload');
Route::post('file/remove', 'GMFileController@attachmentRemove');

// Template
Route::get('templates', 'GMTemplateController@index');
Route::post('template/add', 'GMTemplateController@addTemplate');
Route::get('template/remove/{filename}', 'GMTemplateController@removeTemplate');
Route::get('template/view/{filename}', 'GMTemplateController@viewTemplate');
Route::get('template/download/{filename}', 'GMTemplateController@downloadTemplate');
// Handle Template : OxyGenYoYo
Route::get('template/handle/{filename}', 'GMTemplateController@getHandleTemplate');
Route::post('template/handle/{filename}', 'GMTemplateController@postHandleTemplate');



// Because Wecare
Route::get('email/create', 'GMMailController@emailCreate');

Route::group(array('before' => 'ais_auth'), function(){

  // Caller
  Route::get('caller/get/folders', 'GMCallerController@getFolders');
  Route::get('caller/get/emails/{folder}', 'GMCallerController@getEmails');

  // Redirect index to inbox
  Route::get('/', function(){ return Redirect::to('folder/inbox'); });

  // Pages
  Route::get('folder/{folder}', 'GMMailController@emailList');
  Route::get('debug/test', 'GMMailController@test');
  Route::get('compose/new', 'GMMailController@getEmailCompose');
  Route::post('compose/new', 'GMMailController@postEmailCompose');

  // About mails
  Route::get('mail/detail/{mail_id}', 'GMMailController@getEmailDetail');
  Route::get('mail/reply/{mail_id}', 'GMMailController@getEmailReply');
  Route::post('mail/reply/{mail_id}', 'GMMailController@postEmailReply');
  Route::get('mail/forward/{mail_id}', 'GMMailController@getEmailForward');
  Route::get('mail/delete/{mail_id}', 'GMMailController@getEmailDelete');

  
});

// Debug: Email
// Route::get('debug/email', function(){
  // File::requireOnce('public/libraries/PHPMailer/PHPMailerAutoload.php');
  // $mail = new PHPMailer();
  // $mail->SMTPDebug = false;
  // $mail->IsSMTP();
  // $mail->Host     = "mailgw.channel.ais.co.th";
  // $mail->SMTPAuth = false;
  // $mail->Username = "1149test2";
  // $mail->Password = "Q66w@8#NmE!p";
  // $mail->Encoding = "base64";
  // $mail->CharSet  = "UTF-8";
  // $mail->From     = "1149test2@ais.co.th";
  // $mail->FromName = "AIS Call Center";
  // $mail->WordWrap = 50;
  // $mail->AddAddress('im@tzv.me');
  // $mail->IsHTML(true);
  // $mail->Subject  =  'Subject';
  // $mail->Body     =  'Body Message';
  // $mail->AltBody  =  'Alternative Body';
  // $mail->send();
  // return 'Sent';
// });

Route::get('caller/remove/emails', 'GMCallerController@removeEmails');

Route::get('caller/emails', function(){
  $time_start = microtime(true);
  $server = new \Fetch\Server('mailaccess.ais.co.th', 110, 'pop3');
  $server->setAuthentication('1149test3', 'D!XyV#2g3$$bD');
  $messages = $server->getOrderedMessages(SORTDATE, 1, 200);
  // pr($messages);
  // $messages = $server->getMessages(10);
  foreach ($messages as $message) {
    // pr($message);
    echo "Subject: {$message->getSubject()}<br>Body: {$message->getMessageBody()}<br>Date: ".date("d/m/Y H:i:s", $message->getDate())."<hr>";
  }
  $time_end = microtime(true);
  echo '<b>Total Execution Time:</b> '.(($time_end - $time_start) / 60).' mins.';
  exit;
});

// Caller
Route::get('caller/get/emails', 'GMCallerController@getAllEmails');
Route::get('caller/debug/{suid}', function($suid){
  $mail_in_db = GMMail::where('SUID', '=', $suid)->first();
  if(!$mail_in_db):
    echo 'Empty';
  endif;
});


// Get All Emails in INBOX to verify why not see in UNSEEN flag
Route::get('gets/allmails', 'GMCallerController@test_getAllEmails');
