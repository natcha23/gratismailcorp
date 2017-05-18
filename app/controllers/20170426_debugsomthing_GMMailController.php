<?php

class GMMailController extends BaseController {

	/**
	 * Account test
	 * - 1149test1 (MTE0OXRlc3QxQGFpcy5jby50aA==), 3UPN4r@5Yb#e
	 * - 1149test2 (MTE0OXRlc3QyQGFpcy5jby50aA==), Q66w@8#NmE!p
	 */

	private static $imap_url = '{mailaccess.ais.co.th:110/pop3}INBOX';

	/**
	 * Get imap opening to mail server
	 * @return ImapMailbox alias of imap_open
	 */
	private static function getMailbox($folder = ''){
		// if(Request::get('u') AND Request::get('p')):
		// $username = base64_decode(Request::get('u'));
		// $password = base64_decode(Request::get('p'));
		// else:
		// $username = Session::get('logged_in')->username;
		// $password = Session::get('logged_in')->password;
		// endif;
		// $mailbox = new ImapMailbox(self::$imap_url.$folder, $username, $password, public_path('attachments'), 'utf-8');
		// return $mailbox;

		//$mailbox = new ImapMailbox(self::$imap_url.$folder, Session::get('logged_in')->username, Session::get('logged_in')->password, public_path('attachments'), 'utf-8');
		$mailbox = new ImapMailbox(self::$imap_url.$folder, Session::get('logged_in')->username, Session::get('logged_in')->password, public_path('/attachments'), 'utf-8');
		return $mailbox;
	}

	/**
	 * Checking login
	 * @paramstring $username, $password
	 * @return status on true or false.
	 */
	public static function checkLogin($username, $password){
		//$mailbox = new ImapMailbox(self::$imap_url, $username, $password, public_path('attachments'), 'utf-8');
		$mailbox = new ImapMailbox(self::$imap_url, $username, $password, public_path('/attachments'), 'utf-8');
		return $mailbox->getImapStream();
	}

	public static function emailList($folder)
	{
		// Forget session about email.
		Session::forget('url_previous');
		Session::forget('folder_id');
		Session::forget('mail_draft');
		
		// Fetch folder_id and emails from database.
		$folder = GMFolder::where('name', '=', $folder)->first();
		$mails = GMMail::where('user', '=', Session::get('logged_in')->username)->where('folder_id', '=', $folder->folder_id)->where('deleted', '=', 0)->orderBy('udate', 'desc')->paginate(5);

		// Fetch folder_id and emails from database.
		Session::put('folder_id', $folder->folder_id);
		Session::put('folder_name', ($folder->name == 'inbox' ? '' : '.'.ucfirst($folder->name)));

		return View::make('inbox')->with(['mails' => $mails]);
	}

	/**
	 * Get email detail
	 * @paramint $mail_id
	 * @return view email detail and show attachments.
	 */
	public static function getEmailDetail($mail_id)
	{
		// Get mail from database.
		if(!Request::get('CASEID')):
			GMMail::where('mail_id', '=', $mail_id)->where('user', '=', Session::get('logged_in')->username)->update(['seen' => 1]);
			$mail = GMMail::where('mail_id', '=', $mail_id)->first();
		else:
			GMMail::where('mail_id', '=', $mail_id)->update(['seen' => 1]);
			$mail = GMMail::where('mail_id', '=', $mail_id)->first();
		endif;
		$mail_attachments = [];

		// Create url previous
		if(!Session::get('url_previous')):
			Session::put('url_previous', URL::previous());
		endif;

		// Check mail master and folder_id
		if(!$mail AND !Session::get('folder_id')):
			return Response::view('404', array(), 404);
		endif;


		// Check detail email if has no it will going to fetch all detail from mail server.
		if(empty($mail->text) AND $mail->folder_id != 4):
		
			try {
				$mailbox = GMMailController::getMailbox(Session::get('folder_name'));
				$mail_from_server = $mailbox->getMail($mail->suid);
			
			} catch ( Exception $e ) {
				//echo "Caught exception : <b>".$e->getMessage()."</b><br/>";
				return View::make('mail-detail')->with(['title' => $mail->subject, 'mail' => $mail, 'attachments' => $mail_attachments]);die();
			}
			
			GMMail::where('mail_id', '=', $mail_id)->update([
				'text' => ($mail_from_server->textHtml ? $mail_from_server->textHtml : $mail_from_server->textPlain),
				'sent_from' => $mail_from_server->fromAddress,
				'sent_to' => serialize($mail_from_server->toString),
				'sent_cc' => ($mail_from_server->cc ? serialize($mail_from_server->cc) : ''),
				'reply_to' => ($mail_from_server->replyTo ? serialize($mail_from_server->replyTo) : ''),
			]);

			$mail = GMMail::where('mail_id', '=', $mail_id)->first();

			$attachments = $mail_from_server->attachments;
			if(!empty($attachments)):
				foreach($attachments as $fid => $attachment):
					GMAttachment::insert([
						'mail_id' => $mail_id,
						'fid' => $fid,
						//'file_name' => $attachment->fullName,
						//'file_name_original' => $attachment->name,
						'file_name' => date('Ymd') . '/' . $attachment->fullName,
						'file_name_original' => date('Ymd') . '/' . $attachment->name,
						'created_at' => date('Y-M-d H:i:s'),
						'updated_at' => date('Y-M-d H:i:s'),
					]);
				endforeach;
			endif;
		endif;

		$mail_attachments = removeImageInlineFromAttachmentLists(GMAttachment::where('mail_id', '=', $mail_id)->get(), $mail->text);
		$count_meta = substr_count($mail->text, "<meta");
		if ($count_meta == 0) {
			$mail->text = nl2br($mail->text);
		}
		if($mail->folder_id == 4):
			$libs = ['ckeditor', 'dropzone'];
			Session::put('mail_draft', $mail);
			Session::put('mail_edit', 1);
			return View::make('mail-edit')->with(['title' => $mail->subject, 'mail' => $mail, 'attachments' => $mail_attachments, 'libs' => $libs]);
		else:
			return View::make('mail-detail')->with(['title' => $mail->subject, 'mail' => $mail, 'attachments' => $mail_attachments]);
		endif;
	}

	public static function getEmailForward($mail_id)
	{
// 		$environment = App::environment();
		$mail = GMMail::where('mail_id', '=', $mail_id)->first();
		$mail->text = $mail->text;
		$mail_attachments = removeImageInlineFromAttachmentLists(GMAttachment::where('mail_id', '=', $mail_id)->get(), $mail->text);

		$sent_to = $mail->sent_to;

		//$sent_to_arr = unserialize($sent_to);
		$sent_to_arr = @unserialize($sent_to);

		if($sent_to_arr !== false || $sent_to === 'b:0;')
			$sent_to_arr = unserialize($sent_to);
		else
			$sent_to_arr = $sent_to;


		$sent_to_temp = [];
		$sent_to_blockquote_temp = [];

		$isSentToArray = is_array($sent_to_arr);
		if( $isSentToArray )
		{
			foreach($sent_to_arr as $mail_account => $name)
			{
				$mailAddress = '1149test1@ais.co.th';	// Dev Env
//				$mailAddress = 'corporatecallcenter@ais.co.th';	// Prod Env
				if( $mail_account != $mailAddress)
				{
					$sent_to_temp[] = $mail_account;
				}
				$sent_to_blockquote_temp[] = $mail_account;
			}

			$mail->sent_to = implode(', ', $sent_to_temp);
			$mail->sent_to_blockquote = implode(', ', $sent_to_blockquote_temp);

		} else {
			$mail->sent_to = $sent_to_arr;
			$mail->sent_to_blockquote =$sent_to_arr;
		}

		$sender = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username)->first();

		$ccs = unserialize($mail->sent_cc);
		$mail_cc = '';
		$mail_cc_class = 'hidden';
		$mail_cc = [];
		if(!empty($ccs)):

			foreach($ccs as $mail_account => $mail_name):
				if( $mail_account != $mail->sent_to )
				$mail_cc[] = $mail_account;
			endforeach;

			$mail_cc = implode(', ', $mail_cc);
			$mail_cc_class = '';
		endif;

		$mail->sending_addr = $sender->sentaddr;
		$mail->sending_name = $sender->acctname;

		$mail->sent_from_blockquote = $mail->sent_from;
		//$mail->sent_to_blockquote = implode(', ', $sent_to_blockquote_temp);
		
		// 2017-03-29
		$count_meta = substr_count($mail->text, "<meta");
		if ($count_meta == 0) {
			$mail->text = nl2br($mail->text);
		}

		Session::forget('mail_draft');
		Session::forget('mail_edit');
		return View::make('mail-forward')->with(['title' => 'FW: '.$mail->subject,'mail_cc' => $mail_cc,'mail' => $mail, 'attachments' => $mail_attachments, 'libs' => ['ckeditor', 'dropzone']]);
	}

	public static function getEmailReply($mail_id){
		$mail = GMMail::where('mail_id', '=', $mail_id)->first();
		if(!$mail):
			return Response::view('404', array(), 404);
		endif;
		
		$mail_attachments = removeImageInlineFromAttachmentLists(GMAttachment::where('mail_id', '=', $mail_id)->get(), $mail->text);

		$reply = (Input::get('reply') ? Input::get('reply') : '');
		// echo '<pre>';print_r($mail);die();

		// remove email: corporatecallcenter@ais.co.th from Send to , because we don't need to send itself
		$sent_to = $mail->sent_to;
		//$sent_to_arr = unserialize($sent_to);
		$sent_to_arr = @unserialize($sent_to);

		if($sent_to_arr !== false || $sent_to === 'b:0;')
			$sent_to_arr = unserialize($sent_to);
		else
			$sent_to_arr = $sent_to;

		$sent_to_temp = [];
		$sent_to_blockquote_temp = [];
		$mail->sent_from_blockquote = $mail->sent_from;


		$isSentToArray = is_array($sent_to_arr);
		if( $isSentToArray )
		{
			foreach($sent_to_arr as $mail_account => $name)
			{
				$mailAddress = '1149test1@ais.co.th';	// Dev Env
//				$mailAddress = 'corporatecallcenter@ais.co.th';	// Prod Env
				if( $mail_account != $mailAddress AND $mail_account != $mail->sent_from )
				{
					$sent_to_temp[] = $mail_account;
				}
				$sent_to_blockquote_temp[] = $mail_account;
			}

			$mail->sent_to =implode(', ', $sent_to_temp);
			$mail->sent_to_blockquote = implode(', ', $sent_to_blockquote_temp);
			
		} else {
			$mail->sent_to = $sent_to_arr;
			$mail->sent_to_blockquote = $sent_to_arr;
		}


		// check has email: corporatecallcenter@ais.co.th or not, then remove it from cc list.
		$mail_cc_class = '';
		$icon = 'reply-all';
		$ccs = unserialize($mail->sent_cc);
		$mail_cc = '';
		$mail_cc_class = 'hidden';

		$isSentToEmailNotEmpty = !empty($mail->sent_to);
		if(!empty($ccs)):
			$mail_cc = [];
			$mail_cc_blockquote = [];
			foreach($ccs as $mail_account => $mail_name):
				$mail_cc_blockquote[] = $mail_account;
				if($mail_account ==$mail->email)
				{
					continue;
				}
				if( $isSentToEmailNotEmpty )
				{
					if( strstr($mail_account, $mail->sent_to) )
					{
						continue;
					}
				}
				$mail_cc[] = $mail_account;
			endforeach;

			$mail_cc = implode(', ', $mail_cc);
			$mail->mail_cc_blockquote = implode(', ', $mail_cc_blockquote);
			$mail_cc_class = '';
		endif;


		$isReplyAllPage = Input::get('reply') == 'all';
		if( $isReplyAllPage )
		{ 
			if( $mail->sent_to != $mail->sent_from)
			{
				$mail->sent_to = $mail->sent_from . ', ' . $mail->sent_to;
				$mail->sent_from = $mail->sent_to;
			}
		} else {
			$mail_cc = '';
		}

		$sender = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username)->first();

		$mail->sending_addr = $sender->sentaddr;
		$mail->sending_name = $sender->acctname;
		
		// 2017-03-29
		$count_meta = substr_count($mail->text, "<meta");
		if ($count_meta == 0) {
			$mail->text = nl2br($mail->text);
		}
		
		Session::forget('mail_draft');
		Session::forget('mail_edit');
		Session::forget('folder_id');
		return View::make('mail-reply')->with(['title' => 'RE: '.$mail->subject, 'mail' => $mail, 'attachments' => $mail_attachments, 'libs' => ['ckeditor', 'dropzone'], 'mail_cc' => $mail_cc, 'mail_cc_class' => $mail_cc_class, 'icon' => $icon]);
	}

	public static function postEmailReply($mail_id) {
		$data = Input::all();
		$files = [];
		$mail_id_current = $mail_id;

		if(Session::get('mail_draft')):
			$mail = Session::get('mail_draft');
			$mail_id = $mail->mail_id;
			GMMail::where('mail_id', '=', $mail_id)->update([
				'mail_id' => $mail_id,
				'folder_id' => 2,
				'subject' => $data['txtSubject'],
				'text' => $data['txtDetail'],
				'email' => Session::get('logged_in')->username,
				'sent_from' => Session::get('logged_in')->username,
				'sent_to' => serialize($data['to']),
				'sent_cc' => (isset($data['cc']) ? serialize($data['cc']) : ''),
				'sent_bcc' => (isset($data['bcc']) ? serialize($data['bcc']) : ''),
				'udate' => time(),
				'seen' => 1,
				'created_at' => date('Y-M-d H:i:s'),
				'updated_at' => date('Y-M-d H:i:s'),
			]);
		else:
			// Update Insert SQL string due to single quote in subject problem
			// Using array of value instead of normal string

/*			$insertGMMAIL = "INSERT INTO GM_MAILS ( 
				MAIL_ID,
				FOLDER_ID,
				SUBJECT,
				EMAIL,
				SENT_FROM,
				SENT_TO,
				SENT_CC,
				SENT_BCC,
				UDATE,
				SEEN,
				CREATED_AT,
				UPDATED_AT) VALUES ( 
				 SEQ_MAIL_ID.nextval,
				 2,
				 '" . $data['txtSubject'] . "',
				 '" . Session::get('logged_in')->username . "',
				 '" . Session::get('logged_in')->username . "',
				 '" . serialize($data['to']) . "',
				 '" . (isset($data['cc']) ? serialize($data['cc']) : '') . "',
				 '" . (isset($data['bcc']) ? serialize($data['bcc']) : '') . "',
				 '" . time() . "',
				 1,
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS'),
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS')
				) returning MAIL_ID into :id";*/

			$columnName = implode(',', [
				'MAIL_ID',
				'FOLDER_ID',
				'SUBJECT',
				'EMAIL',
				'SENT_FROM',
				'SENT_TO',
				'SENT_CC',
				'SENT_BCC',
				'UDATE',
				'SEEN',
				'CREATED_AT',
				'UPDATED_AT'
			]);

			$bindColumn = implode(',', [
				':folderid',
				':subject',
				':email',
				':sent_from',
				':sent_to',
				':sent_cc',
				':sent_bcc',
				':udate',
				':seen'
			]);

			$columnVal = [
				':folderid' => 2,
				':subject' => $data['txtSubject'],
				':email' => Session::get('logged_in')->username,
				':sent_from' => Session::get('logged_in')->username,
				':sent_to' => serialize($data['to']),
				':sent_cc' => (isset($data['cc']) ? serialize($data['cc']) : ''),
				':sent_bcc' => (isset($data['bcc']) ? serialize($data['bcc']) : ''),
				':udate' => time(),
				':seen' => 1
			];

			$insertGMMAIL = "INSERT INTO GM_MAILS (" .  $columnName . ") VALUES ( 
				 SEQ_MAIL_ID.nextval,
				" . $bindColumn . ",
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS'),
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS')
				) returning MAIL_ID into :id";
			
			$connection = new CDatabase();
			$connection->Connect();
//			$mail_id = $connection->InsertThenReturnLastId($insertGMMAIL, array());
			$mail_id = $connection->InsertThenReturnLastId($insertGMMAIL, $columnVal);

			GMMail::where('mail_id', '=', $mail_id)->update([ 'text' => $data['txtDetail'] ]);

		endif;

		if(!empty($data['files'])):
			$attachments = $data['files'];
			foreach($attachments as $fid):
				$file = GMAttachment::find($fid);
				$fid = $mail_id.'_'.time().'_'.Func::charRandom(10);
				$ext = pathinfo($file->file_name, PATHINFO_EXTENSION);
				$file_name = $fid.'.'.$ext;
				if($mail_id_current == $file->mail_id):
				//$old = public_path('attachments/'.$file->file_name);
				//$new = public_path('attachments/'.$file_name);
				//$old = public_path('attachments/'. date('Ymd') . '/' . urldecode($file->file_name) );
				//$new = public_path('attachments/'. date('Ymd') . '/' . urldecode($file_name) );
				$old = public_path('attachments/'. urldecode($file->file_name) );
				$new = public_path('attachments/'. urldecode($file_name) );
				copy($old, $new);
				GMAttachment::insert([
					'mail_id' => $mail_id,
					'fid' => $fid,
					'file_name' => $file_name,
					'file_name_original' => $file->file_name_original,
					'created_at' => date('Y-M-d H:i:s'),
					'updated_at' => date('Y-M-d H:i:s'),
				]);
				endif;
			endforeach;
		endif;

		$sender = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username)->first();
		// BCCADDR
		File::requireOnce('public/libraries/PHPMailer/PHPMailerAutoload.php');
		$mail = new PHPMailer();
		$mail->SMTPDebug = false;
		$mail->IsSMTP();
		$mail->Host = "mailgw.channel.ais.co.th";
		$mail->SMTPAuth = false;
		$mail->Username = $sender->sentaddr;
		$mail->Password = $sender->sentpwd;
		$mail->Encoding = "base64";
		$mail->CharSet= "UTF-8";
		$mail->From = $sender->sentaddr;
		$mail->FromName = $sender->acctname;
		$mail->WordWrap = 50;

		foreach($data['to'] as $sent_to):
			$mail->AddAddress($sent_to);
		endforeach;

		if(isset($data['cc'])):
			foreach($data['cc'] as $sent_cc):
				$mail->addCC($sent_cc);
			endforeach;
		endif;

		/* Add */
		// $mail->addBCC('fc-web_sub@ais.co.th');
		if(isset($data['bcc'])):
			foreach($data['bcc'] as $sent_bcc):
				$mail->addBCC($sent_bcc);
			endforeach;
		endif;
		$bcc_list = explode(',', $sender->bccaddr);
		foreach($bcc_list as $bcc):
			$mail->addBCC($bcc);
		endforeach;

		if(isset($data['files'])):
			foreach($data['files'] as $file):
				$file = GMAttachment::find($file);
				$mail->AddAttachment("public/attachments/{$file->file_name}", $file->file_name_original);
			endforeach;
		endif;

		$mail->IsHTML(true);
		$mail->Subject=$data['txtSubject'];
		$data['txtDetail'] = self::fixContentFontFamilyTahoma($data['txtDetail']);
//		$mail->Body = self::convertHostImageFileToPublicHost( $data['txtDetail']); // Change mail template to be picture [20161102 remove feature] 
		$mail->Body = $data['txtDetail'];
		$mail->AltBody = $data['txtDetail'];

		GMMail::where('mail_id', '=', $mail_id)->update([ 'text' => $mail->Body ]);

		//$mail->send();
		if(!$mail->send())
		{
			write_sendmail_log('Send Fail: ' . $mail->ErrorInfo);
		}
		write_sendmail_log('======================================================================================');

		return Redirect::to("mail/detail/{$mail_id}?u=".Request::get('u')."&sending=sent")->with(['success_message' => 'Your message has been sent.']);
	}

	public static function getEmailDelete($mail_id){
		$mail = GMMail::where('mail_id', '=', $mail_id)->first();
		$mail->deleted = 1;
		$mail->save();
		$folder = GMFolder::find(Session::get('folder_id'));
		return Redirect::to("folder/{$folder->name}")->with(['success_message' => 'Your message has been deleted.']);
	}

	private static function ifNotFoundSessionLogged_inThenGetItFromDatabaseByGetParameter()
	{
		$isNotFoundSessionLogged_id = !isset(Session::get('logged_in')->username);
		if( $isNotFoundSessionLogged_id )
		{
			$account = EMConfig::where('emailaddr', '=', base64_decode(Request::get('u')))->first();
			$username = base64_decode(Request::get('u'));
			$password = $account->acctpwd;
			Session::put('logged_in', (object)['username' => $username, 
				'password' => $password, 
				'u' => base64_encode($username), 
				'p' => base64_encode($password), 
				'name' => $account->acctname, 
				'sent_email' => $account->sentaddr]
			);
		}
	}

	public static function getEmailCompose()
	{
		$sent_to = '';
		$case_detail = '';

		//self::ifNotFoundSessionLogged_inThenGetItFromDatabaseByGetParameter();
		//$emconfig = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username)->first();

		if(Request::get('CASEID')):
			$caseinfo = CASEINFO::where('caseid', '=', Request::get('CASEID'))->where('FEEDTYPE', '=', 'WA')->first();
			$caseinfo = $caseinfo->toArray();
			$sent_to = $caseinfo['feeduserid'];
			$caseinfodetail = DB::table('fb_app_fast_complaint')->where('CASE_UNIQUE_ID', '=', $caseinfo['feedid'])->first();
			$caseinfodetail = (array)$caseinfodetail;
			$case_detail = array_merge($caseinfo, $caseinfodetail);
		endif;

		Session::forget('folder_id');
		$libs = ['ckeditor', 'dropzone'];
		if(Session::get('mail_draft')):
			Session::put('mail_edit', 0);
			$mail = Session::get('mail_draft');
			$mail_attachments = GMAttachment::where('mail_id', '=', $mail->uid)->get();
		else:
			$mail_attachments = '';
		endif;

		$mail_bcc_class = '';
/*		if($emconfig->BCCSTATUS > 0):
			$bccs = explode(',', $emconfig->BCCADDR);
			foreach($bccs as $bcc):
				$mail_bcc[] = $bcc;
			endforeach;
		endif;
		$mail_bcc[] = '';
		$mail_bcc = implode(', ', $mail_bcc);*/
		$mail_bcc = '';
		return View::make('compose-new')->with(['libs' => $libs, 'attachments' => $mail_attachments, 'mail_bcc' => $mail_bcc, 'mail_bcc_class' => $mail_bcc_class, 'sent_to' => $sent_to, 'case_detail' => $case_detail]);
	}

	public static function postEmailCompose()
	{
		$data = Input::all();
		if($data['form-mode'] == 'send'):
			if (isset(Session::get('logged_in')->username)) {
				$sender = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username)->first();
			} else {
				$sender = EMConfig::where('status', '=', 1)->first();
			}

			File::requireOnce('public/libraries/PHPMailer/PHPMailerAutoload.php');
			$mail = new PHPMailer();
			$mail->SMTPDebug = false;
			$mail->IsSMTP();
			$mail->SMTPDebug = 3;
			$mail->Debugoutput = function($str, $level) {
				write_sendmail_log($str);
			};
			$mail->Host = "mailgw.channel.ais.co.th";
			$mail->SMTPAuth = false;
			$mail->Username = $sender->sentaddr;
			$mail->Password = $sender->sentpwd;
			$mail->Encoding = "base64";
			$mail->CharSet= "UTF-8";
			$mail->From = $sender->sentaddr;
			$mail->FromName = $sender->acctname;
			$mail->WordWrap = 50;

			foreach($data['to'] as $sent_to):
				$mail->AddAddress($sent_to);
			endforeach;

			if(isset($data['cc'])):
				foreach($data['cc'] as $sent_cc):
				$mail->addCC($sent_cc);
				endforeach;
			endif;

			if(isset($data['bcc'])):
				foreach($data['bcc'] as $sent_bcc):
				$mail->addBCC($sent_bcc);
				endforeach;
			endif;

			$isBccStatusEnableThenForceAddBccEmail = $sender->bccstatus != 0;
			if( $isBccStatusEnableThenForceAddBccEmail )
			{
				$bcc_list = explode(',', $sender->bccaddr);
				foreach($bcc_list as $bcc):
					$mail->addBCC($bcc);
				endforeach;
			}

			if(isset($data['files'])):
				foreach($data['files'] as $file):
					$file = GMAttachment::find($file);
					$mail->AddAttachment("public/attachments/{$file->file_name}", $file->file_name_original);
				endforeach;
			endif;

			$mail->IsHTML(true);
			$mail->Subject = $data['txtSubject'];
			$data['txtDetail'] = self::fixContentFontFamilyTahoma($data['txtDetail']);
//			$mail->Body = self::convertHostImageFileToPublicHost( $data['txtDetail']); // Remove convert template to pic feature 20161102
			$mail->Body = $data['txtDetail'];
			$mail->AltBody = $data['txtDetail'];

			//$mail->send();
			if(!$mail->send())
			{
				write_sendmail_log('Send Fail: ' . $mail->ErrorInfo);
			}
			write_sendmail_log('======================================================================================');
		endif;

		if(Session::get('mail_draft')):
			$mail_id = Session::get('mail_draft')->mail_id;
			GMMail::where('mail_id', '=', $mail_id)->update([
				'mail_id' => $mail_id,
				'folder_id' => 2,
				'subject' => $data['txtSubject'],
				'text' => $data['txtDetail'],
				'email' => ( isset(Session::get('logged_in')->username) ? Session::get('logged_in')->username : $sender->sentaddr),
				'sent_from' => ( isset(Session::get('logged_in')->username) ? Session::get('logged_in')->username : $sender->sentaddr),
				'sent_to' => serialize($data['to']),
				'sent_cc' => (isset($data['cc']) ? serialize($data['cc']) : ''),
				'sent_bcc' => (isset($data['bcc']) ? serialize($data['bcc']) : ''),
				'udate' => time(),
				'seen' => 1,
				'created_at' => date('Y-M-d H:i:s'),
				'updated_at' => date('Y-M-d H:i:s'),
			]);
		else:
/*			$insertGMMAIL = "INSERT INTO GM_MAILS ( 
				MAIL_ID,
				FOLDER_ID,
				SUBJECT,
				EMAIL,
				SENT_FROM,
				SENT_TO,
				SENT_CC,
				SENT_BCC,
				UDATE,
				SEEN,
				CREATED_AT,
				UPDATED_AT) VALUES ( 
				 SEQ_MAIL_ID.nextval,
				 2,
				 '" . $data['txtSubject'] . "',
				 '" . Session::get('logged_in')->username . "',
				 '" . Session::get('logged_in')->username . "',
				 '" . serialize($data['to']) . "',
				 '" . (isset($data['cc']) ? serialize($data['cc']) : '') . "',
				 '" . (isset($data['cc']) ? serialize($data['bcc']) : '') . "',
				 '" . time() . "',
				 1,
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS'),
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS')
				) returning MAIL_ID into :id";*/

			$columnName = implode(',', [
				'MAIL_ID',
				'FOLDER_ID',
				'SUBJECT',
				'EMAIL',
				'SENT_FROM',
				'SENT_TO',
				'SENT_CC',
				'SENT_BCC',
				'UDATE',
				'SEEN',
				'CREATED_AT',
				'UPDATED_AT'
			]);

			$bindColumn = implode(',', [
				':folderid',
				':subject',
				':email',
				':sent_from',
				':sent_to',
				':sent_cc',
				':sent_bcc',
				':udate',
				':seen'
			]);

			$columnVal = [
				':folderid' => 2,
				':subject' => $data['txtSubject'],
				':email' => Session::get('logged_in')->username,
				':sent_from' => Session::get('logged_in')->username,
				':sent_to' => serialize($data['to']),
				':sent_cc' => (isset($data['cc']) ? serialize($data['cc']) : ''),
				':sent_bcc' => (isset($data['bcc']) ? serialize($data['bcc']) : ''),
				':udate' => time(),
				':seen' => 1
			];

			$insertGMMAIL = "INSERT INTO GM_MAILS (" .$columnName . ") VALUES ( 
				 SEQ_MAIL_ID.nextval,
				" . $bindColumn . ",
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS'),
				 TO_Date( '" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS')
				) returning MAIL_ID into :id";

			$connection = new CDatabase();
			$connection->Connect();
			//$mail_id = $connection->InsertThenReturnLastId($insertGMMAIL, array());
			$mail_id = $connection->InsertThenReturnLastId($insertGMMAIL, $columnVal);
			//	echo "mail_id = ".$mail_id."<br/>";die;

//			GMMail::where('mail_id', '=', $mail_id)->update([ 'text' => $data['txtDetail'] ]);
			GMMail::where('mail_id', '=', $mail_id)->update([ 'text' => $mail->Body ]);
		endif;
		Session::forget('mail_draft');
		/* This section P'Suwich can do. */
		// DB::table('newpostinfo')->insert([
		// 'feedtype' => 'em',
		// 'feedsubtype' => 'em',
		// 'agentid' => $data['USERID'],
		// 'topictxt' => $data['txtSubject'],
		// 'msgtxt' => $data['txtDetail'],
		// 'createddate' => date('Y-m-d H:i:s'),
		// ]);
		// $return_id = GMMail::orderBy('mail_id', 'desc')->first();
		return Redirect::to("mail/detail/{$mail_id}?u=".Request::get('u')."&sending=sent".(Request::get('CASEID') ? '&CASEID='.Request::get('CASEID') : ''))->with(['success_message' => 'Your message has been sent.']);
	}

	public static function convertHostImageFileToPublicHost($html)
	{
		/* 
			Gmail can't get image if it on http://activities.ais.co.th
			that why we need to change path 
		*/

		$aisHost = 'http://activities.ais.co.th';
		$newfile = 'public/files/images/';
		
		$publicHost = _PUBLIC_HOST_;
// 		$publicHost = 'http://dev.smm.ais.co.th/SMMGetInfo/gratismailcorp/';	// dev
//		$publicHost = 'http://202.149.30.144/SMMGetInfo/gratismailcorp/';		// prod
//		$publicHost = 'http://crawl3.smm.ais.co.th/SMMGetInfo/gratismailcorp/';	// prod

		$pattern = '%' . $aisHost . '(.*?)"%isu';
		preg_match_all($pattern, $html, $matches);

		$isNoMatchThenReturnOriginalHtml = empty($matches);
		if( $isNoMatchThenReturnOriginalHtml )
		{
			return $html;
		}
//		echo '<pre>'; print_r($matches); echo '</pre>';

		foreach ($matches[1] as $url) 
		{
//			$aisImageFile = file_get_contents("'" .$aisHost . $url . "'");
			$aisImageFile = file_get_contents($aisHost.$url);
			preg_match('%id=(.*)%isu', $url, $id);
			$pathImage = $newfile . $id[1] . '.jpg';

			$noImageFileThenUploadToPublicHost = !file_exists($pathImage);
			if( $noImageFileThenUploadToPublicHost )
			{
				self::_uploadFile($pathImage,$aisImageFile); 
			}
			else
			{
				$tellvoiceImageFile = file_get_contents($publicHost . $newfile . $id[1] . '.jpg');
				$aisImageFileMD5 = md5($aisImageFile);
				$tellvoiceImageFileMD5 = md5($tellvoiceImageFile); 

				$isNewImageThenUploadFile = $aisImageFileMD5 != $tellvoiceImageFileMD5;
				if( $isNewImageThenUploadFile )
				{
				self::_uploadFile($pathImage,$aisImageFile);
				}
			}

			$search =$aisHost . '/app/email_filter/imgpage.aspx?id=' . $id[1];
			$html = str_replace(
				$search, 
				$publicHost . $newfile . $id[1] . '.jpg', 
				$html
			);
		}
		return $html;
	}

	public static function _uploadFile($pathImage, $imageFile)
	{
		$fp = fopen($pathImage, 'w');
		fwrite($fp, $imageFile);
		fclose($fp);
	}

	public static function getEditEmailDraft($mail_id){
		return $mail_id;
	}

	public static function saveDraft(){
//		$last_mail = GMMail::orderBy('mail_id', 'DESC')->first();
//		$mail_id = ($last_mail->mail_id + 1);
//		$mail = GMMail::insert([
//		'mail_id' => $mail_id,
//		'folder_id' => 3,
//		'subject' => 'No subject',
//		'text' => 'No text',
//		'email' => Session::get('logged_in')->username,
//		'udate' => time(),
//		'draft' => 1,
//		'created_at' => date('Y-M-d H:i:s'),
//		'updated_at' => date('Y-M-d H:i:s')
//		]);

		$currTime = time();

		$columnName = implode(',', [
			'MAIL_ID',
			'FOLDER_ID',
			'EMAIL',
			'SUBJECT',
			'TEXT',
			'UDATE',
			'DRAFT',
			'CREATED_AT',
			'UPDATED_AT'
		]);

		$bindColumn = implode(',', [
			':folder_id',
			':email',
			':subject',
			':text',
			':udate',
			':draft'
		]);

		$columnVal = [
			':folder_id' => 3,
			':email' => Session::get('logged_in')->username,
			':subject' => 'No subject',
			':text' => 'No text',
			':udate' => $currTime,
			':draft' => 1
		];

		$insertGMMAIL = "INSERT INTO GM_MAILS (" .  $columnName . ") VALUES ( 
							 SEQ_MAIL_ID.nextval,
							" . $bindColumn . ",
							 TO_Date( '" . date('Y-m-d H:i:s', $currTime) . "', 'YYYY-MM-DD HH24:MI:SS'),
							 TO_Date( '" . date('Y-m-d H:i:s', $currTime) . "', 'YYYY-MM-DD HH24:MI:SS')
							) returning MAIL_ID into :id";

		$connection = new CDatabase();
		$connection->Connect();
		$mail_id = $connection->InsertThenReturnLastId($insertGMMAIL, $columnVal);
						$hasMailIdReturnThenCreateCaseInfo = $mail_id != null;
						if( $hasMailIdReturnThenCreateCaseInfo )
						{
							write_log('hasMailIdReturnThenCreateCaseInfo\n', 'error_GMCallerController');
						} else {
							write_log('NOT hasMailIdReturnThenCreateCaseInfo\n', 'error_GMCallerController');
						}
//		$connection->Connect();

		$mail = GMMail::where('mail_id', '=', $mail_id)->first();
		Session::put('mail_draft', $mail);
		return $mail;
	}

	// public static function replacePicture($text){
		// write_log('text = ' . $text);
		// $originalText = $text;
		// preg_match_all('/<img\s+src="([^"]+)"[^>]+>/siu', $text, $sources, PREG_PATTERN_ORDER);
		// if(!empty($sources)):
			// foreach($sources[1] as $source):
				// if(strstr($source, 'cid')):
				// $source = substr($source, 4);
				// $replace = GMAttachment::find($source);
				// if($replace):
					// $replace = 'http://dev.smm.ais.co.th/SMMGetInfo/gratismailcorp/public/attachments/'.$replace->file_name;
					// $text = str_replace('cid:'.$source, $replace, $originalText);
					// $text = str_replace('/index.php/', '/', $text);
				// endif;
				// endif;
			// endforeach;
		// endif;
		// return $text;
	// }

	public static function replaceImageSourceToTellvoicePath($text)
	{
		$patternDetectAllImageInlineInEmailMessage = '%<img\s[^>]*?src\s*=\s*[\'\"]([^\'\"]*?)[\'\"][^>]*?>%siu';
		
		$publicFilePath = _PUBLIC_ATTACHMENTS_PATH_;
// 		$publicFilePath = 'http://dev.smm.ais.co.th/SMMGetInfo/gratismailcorp/public/attachments/'; // dev
		//$publicFilePath = 'http://202.149.30.144/SMMGetInfo/gratismailcorp/public/attachments/'; // production
		//$publicFilePath = 'http://crawl3.smm.ais.co.th/SMMGetInfo/gratismailcorp/public/attachments/'; // production
		preg_match_all($patternDetectAllImageInlineInEmailMessage, $text, $imgTagInMessage, PREG_PATTERN_ORDER);

		self::ifNoFolderDateThenCreateIt();

		$hasImageInMessage = !empty($imgTagInMessage);
		if( $hasImageInMessage )
		{
			foreach($imgTagInMessage[1] as $imgSRC)
			{
				$originalText = $text;
				$hasCidInSource = strstr($imgSRC, 'cid');
				if( $hasCidInSource )
				{
					$fidValue = substr($imgSRC, 4);
					$fileInEmail = GMAttachment::find($fidValue);

					$hasFileInDatabase = !empty($fileInEmail);
					if( $hasFileInDatabase )
					{
						$publicFileDomain = $publicFilePath . $fileInEmail->file_name;
						$text = str_replace('cid:' . $fidValue, $publicFileDomain, $originalText);
					}
				}
			}
		}
		return $text;
	}

	public static function ifNoFolderDateThenCreateIt()
	{
		$pathForCheck = '/var/www/html/SMMGetInfo/gratismailcorp/public/attachments/' . date('Ymd') . '/';

		$isNotFoundFolderThenCreateFolder = !file_exists($pathForCheck);
		if( $isNotFoundFolderThenCreateFolder )
		{
			if (!mkdir($pathForCheck, 0755, true)) {
				write_log('Fail to create folder date time','error_create_folder');
			}
		}
	}

	public static function fixContentFontFamilyTahoma($content)
	{
		return '<div style="font-family:\'tahoma\';font-size:10pt;">' . $content . '</div>';
	}

	public static function test(){
		$file = public_path('attachments/2_41834580302678050_1379976_10152242740783332_728061104_n.jpg');
		echo date("d/m/Y H:i:s", fileatime($file));
		echo '<br>';
		echo date("d/m/Y H:i:s", filectime($file));
		// $mailbox = GMMailController::getMailbox('.Sent');
		// $mailbox = GMMailController::getMailbox();
		// $checking = imap_check($mailbox->getImapStream());
		// Func::pr($checking);
		// return View::make('test_test');
	}
}
?>
