<?php

set_time_limit(120);

class GMCallerController extends BaseController {
	/**
	* Get all email with specifily account
	* - URL: SMMGetInfo/gratismailcorp/index.php/caller/get/emails/inbox?u=MTE0OXRlc3Qx&p=M1VQTjRyQDVZYiNl&reset=users
	*/

//	private static $imap_url = '{mailaccess.ais.co.th:110/pop3}';
	private static $imap_url = '{mailaccess.ais.co.th:143/imap}';

	/**
	* Private: Get imap opening to mail server
	* @return ImapMailbox alias of imap_open
	*/
	private static function getMailbox($folder = ''){
		if(Request::get('u') AND Request::get('p')):
			$username = base64_decode(Request::get('u'));
			$password = base64_decode(Request::get('p'));
		else:
			$username = Session::get('logged_in')->username;
			$username = explode('@', $username)[0];
			$password = Session::get('logged_in')->password;
		endif;
		// $username = 'api@chockpermpoon.com';
		// $username = explode('@', $username)[0];
		// $password = 'UJbnNV2S';
		// pr([$username, $password]); exit;
		GMMailController::ifNoFolderDateThenCreateIt();

		$mailbox = new ImapMailbox(self::$imap_url.$folder, $username, $password, public_path('/attachments/' . date('Ymd') ), 'utf-8');
//		$mailbox = new ImapMailbox(self::$imap_url.$folder, $username, $password, public_path('attachments'), 'utf-8');
		return $mailbox;
	}

	/**
	* Caller: get all folders from server.
	* @return All folders and save it to database.
	*/
	public static function getFolders(){
		$mailbox = GMCallerController::getMailbox();
		$folders = $mailbox->getListingFolders();

		foreach($folders as $folder):
			if(empty($folder) OR strtolower($folder) == 'inbox'):
				$folder = 'inbox';
			else:
				$folder = strtolower(substr($folder, 1));
			endif;

			$folder_in_db = GMFolder::where('name', '=', $folder)->first();
			if(!$folder_in_db):
				$folder_id = GMFolder::orderBy('folder_id', 'DESC')->first();
					if(!$folder_id)
						$folder_id = 0;
					else
						$folder_id = $folder_id->folder_id;
				$folder_id = ($folder_id + 1);
				GMFolder::insert([
					'folder_id' => $folder_id,
					'icon' => 'fa fa-inbox',
					'name' => $folder,
					'created_at' => date('Y-M-d H:i:s'),
					'updated_at' => date('Y-M-d H:i:s'),
				]);
				echo "$folder is added.<br>";
			else:
				echo "$folder is exist in database.<br>";
			endif;
		endforeach;
	}

	public static function getAllEmails(){
		$emails = EMConfig::where('status', '=', 1)->get();
		foreach($emails as $email):
			Session::put('logged_in', (object)['username' => $email->emailaddr, 'password' => $email->acctpwd, 'u' => base64_encode($email->emailaddr), 'p' => base64_encode($email->acctpwd), 'name' => $email->acctname, 'sent_email' => $email->sentaddr]);
			echo $email->emailaddr.': '.GMCallerController::getEmails().'<br>';
		endforeach;
	}

	public static function generateRandomString($length = 10) 
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	/**
	 * Caller: get all emails from server.
	 * @return All email from server, save all emails to database and then return string: added, uptodate or false.
	 */
	public static function getEmails($folder = 'inbox', $limit = 100){
		// File::requireOnce('../../prioritylib/includes/priorityfunc.php'); // This for new Priority asked P' Poom when need to use this one.

		if($folder == 'inbox'):
			$mailbox = 'inbox';
		else:
			$mailbox = '.'.ucfirst($folder);
		endif;

		$current_email = Session::get('logged_in')->username;
		$current_date = date('Y-m-d H:i:s');
//		$pri_score = get_priority('EM', '', 'T', $current_email);
		$folder = GMFolder::select('folder_id')->where('name', '=', $folder)->first();
		$mailbox = GMCallerController::getMailbox($mailbox);
//		$mailsIds = $mailbox->searchMailBoxNoCharset('ON "'. date("d M Y H:i").'"'); // AIS mailbox is not UTF-8. If we set UTF-8 then can't get email
		$mailsIds = $mailbox->searchMailBoxNoCharset('UNSEEN'); // AIS mailbox is not UTF-8. If we set UTF-8 then can't get email
		// getPriorityInfo($priorityInfo); // This for new Priority asked P' Poom when need to use this one.

		if(!$mailsIds):
			die('Mailbox is empty');
		else:
			$added = 0;
			$mails = $mailbox->getMailsInfo($mailsIds);
			$tokenLoop = self::generateRandomString();
			write_caller_log('==================================================');
			write_caller_log('Token Loop : ' . $tokenLoop);
			write_caller_log('==================================================');

			foreach($mails as $mail):
				$uid = $mail->uid;
				if($mail->udate > time()):
					$date = new DateTime($mail->date);
					$date->setTimezone(new DateTimeZone('Asia/Bangkok'));
					$mail->date = $date->format('D, d M Y H:i:s O');
					$mail->udate = strtotime($date->format('D, d M Y H:i:s O'));
				endif;

				$mail_in_db = GMMail::where('email', '=', $current_email)->where('folder_id', '=', 1)->where('udate', '=', $mail->udate)->first();
				if(!$mail_in_db):
					$send_email = 1;
					$mail_from_server = $mailbox->getMail($mail->uid);
					$mailbox->markMailAsRead($mail->uid);
					$mail_in_case = CASEINFO::where('CASECREATEDDT', '=', date('Y-m-d H:i:s', $mail->udate))->where('FEEDUSERID', '=', $mail_from_server->fromAddress)->where('feature01', '=', Session::get('logged_in')->username)->first();

					/* ignore email lists */
//					$ignore_lists = array('1149test1@ais.co.th');	// Dev Env
//					$ignore_lists = array('corporatecallcenter@ais.co.th');	//Prod Env
					$ignore_lists = array('corporatecallcenter@ais.co.th','1149test1@ais.co.th');
					if(!$mail_in_case AND !in_array($mail_from_server->fromAddress, $ignore_lists)):

						write_caller_log('subject : ' . $mail_from_server->subject);
						write_caller_log('from_name : ' . $mail_from_server->fromAddress);
						write_caller_log('mail_to_server_date : ' . $mail_from_server->date);
						write_caller_log('token loop : ' . $tokenLoop);
						write_caller_log('==================================================');

/*						$insertGMMAIL = "INSERT INTO GM_MAILS ( 
							MAIL_ID,
							SUID,
							FOLDER_ID,
							EMAIL,
							SUBJECT,
							FROM_NAME,
							SENT_FROM,
							SENT_TO,
							SENT_CC,
							SENT_BCC,
							REPLY_TO,
							UDATE,
							MASSAGE_NO,
							RECENT,
							FLAGGED,
							ANSWERED,
							DELETED,
							SEEN,
							DRAFT,
							CREATED_AT,
							UPDATED_AT,
							IS_FROM_CHAT) VALUES ( 
							 SEQ_MAIL_ID.nextval,
							 " . $uid . ",
							 " . $folder->folder_id . ",
							 '" . Session::get('logged_in')->username . "',
							 '" . mb_substr(strip_tags($mail_from_server->subject), 0, 100, 'UTF-8') . "',
							 '" . ($mail_from_server->fromName ? htmlspecialchars($mail_from_server->fromName) .' ('.$mail_from_server->fromAddress.')' : $mail_from_server->fromAddress) . "',
							 '" . $mail_from_server->fromAddress . "',
							 '" . serialize(array_map('GMCallerController::removeSingleQuote', $mail_from_server->to)) . "',
							 '" . ($mail_from_server->cc ? serialize(array_map('GMCallerController::removeSingleQuote', $mail_from_server->cc)) : '') . "',
							 '',
							 '" . ($mail_from_server->replyTo ? serialize(array_map('GMCallerController::removeSingleQuote', $mail_from_server->replyTo)) : '') . "',
							 " . $mail->udate . ",
							 " . $mail->msgno . ",
							 " . $mail->recent . ",
							 " . $mail->flagged . ",
							 " . $mail->answered . ",
							 " . $mail->deleted . ",
							 " . $mail->seen . ",
							 " . $mail->draft . ",
							 TO_Date( '" . date('Y-m-d H:i:s', $mail->udate) . "', 'YYYY-MM-DD HH24:MI:SS'),
							 TO_Date( '" . $current_date . "', 'YYYY-MM-DD HH24:MI:SS'),
							0 
							) returning MAIL_ID into :id";*/

						$columnName = implode(',', [
							'MAIL_ID',
							'SUID',
							'FOLDER_ID',
							'EMAIL',
							'SUBJECT',
							'FROM_NAME',
							'SENT_FROM',
							'SENT_TO',
							'SENT_CC',
							'SENT_BCC',
							'REPLY_TO',
							'UDATE',
							'MASSAGE_NO',
							'RECENT',
							'FLAGGED',
							'ANSWERED',
							'DELETED',
							'SEEN',
							'DRAFT',
							'IS_FROM_CHAT',
							'CREATED_AT',
							'UPDATED_AT'
						]);

						$bindColumn = implode(',', [
							':suid',
							':folder_id',
							':email',
							':subject',
							':from_name',
							':sent_from',
							':sent_to',
							':sent_cc',
							':sent_bcc',
							':reply_to',
							':udate',
							':massage_no',
							':recent',
							':flagged',
							':answered',
							':deleted',
							':seen',
							':draft',
							':is_from_chat'
						]);

						$columnVal = [
							':suid' => $uid,
							':folder_id' => $folder->folder_id,
							':email' => Session::get('logged_in')->username,
							':subject' => mb_substr(strip_tags($mail_from_server->subject), 0, 100, 'UTF-8'),
							':from_name' => ($mail_from_server->fromName ? htmlspecialchars($mail_from_server->fromName) .' ('.$mail_from_server->fromAddress.')' : $mail_from_server->fromAddress),
							':sent_from' => $mail_from_server->fromAddress,
							':sent_to' => serialize(array_map('GMCallerController::removeSingleQuote', $mail_from_server->to)),
							':sent_cc' => ($mail_from_server->cc ? serialize(array_map('GMCallerController::removeSingleQuote', $mail_from_server->cc)) : ''),
							':sent_bcc' => '',
							':reply_to' => ($mail_from_server->replyTo ? serialize(array_map('GMCallerController::removeSingleQuote', $mail_from_server->replyTo)) : ''),
							':udate' => $mail->udate,
							':massage_no' => $mail->msgno,
							':recent' => $mail->recent,
							':flagged' => $mail->flagged,
							':answered' => $mail->answered,
							':deleted' => $mail->deleted,
							':seen' => $mail->seen,
							':draft' => $mail->draft,
							':is_from_chat' => 0
						];

						$insertGMMAIL = "INSERT INTO GM_MAILS (" .  $columnName . ") VALUES ( 
							 SEQ_MAIL_ID.nextval,
							" . $bindColumn . ",
							 TO_Date( '" . date('Y-m-d H:i:s', $mail->udate) . "', 'YYYY-MM-DD HH24:MI:SS'),
							 TO_Date( '" . $current_date . "', 'YYYY-MM-DD HH24:MI:SS')
							) returning MAIL_ID into :id";

						$connection = new CDatabase();
						$connection->Connect();
//						$mail_id = $connection->InsertThenReturnLastId($insertGMMAIL, array());
						$mail_id = $connection->InsertThenReturnLastId($insertGMMAIL, $columnVal);

						$hasMailIdReturnThenCreateCaseInfo = $mail_id != null;
						if( $hasMailIdReturnThenCreateCaseInfo )
						{
							$attachments = $mail_from_server->attachments;
							if(!empty($attachments)):
								foreach($attachments as $fid => $attachment):
									try{
										GMAttachment::insert([
											'mail_id' => $mail_id,
											'fid' => $fid,
											'file_name' => date('Ymd') . '/' . $attachment->fullName,
											'file_name_original' => date('Ymd') . '/' . $attachment->name,
											'created_at' => date('Y-M-d H:i:s'),
											'updated_at' => date('Y-M-d H:i:s'),
										]);
									} catch (Exception $e) {
										write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
									}
								endforeach;
							endif;

							if($mail_from_server->textHtml):
								$message_text = GMMailController::replaceImageSourceToTellvoicePath($mail_from_server->textHtml);
							else:
								$message_text = $mail_from_server->textPlain;
							endif;

							/* Update message_text when replace picture into textHTML */
							try{
								GMMail::where('mail_id', '=', $mail_id)->update([ 'text' => $message_text ]);
							} catch (Exception $e) {
								write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
							}

							$countCase = CASEINFO::where('feedid', '=', $mail_id)->where('feedtype', '=', 'em')->where('feedsubtype', '=', 'em')->count();
							if($countCase == 0):
								$pri_score = 70;
								$message_text_check_lang = strip_tags($mail_from_server->subject).' '.$message_text;
								if(is_thai($message_text_check_lang) == 'T'):
									$chklang = 'T';
									$templateLang = 'th';
								else:
									$chklang = 'E';
									$templateLang = 'en';
								endif;

								// $pri_score = getPriorityScore_New($priorityInfo, $message_text, explode('@', $mail_from_server->fromAddress)[1], 'EM', 'EM', $chklang, Session::get('logged_in')->username, '', '', '');
								$pri_score = get_priority('EM', '', $chklang, $current_email);
								$options = DB::table('suw_options')->first();
								$emconfig = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username)->first();
								$caseinfo = CASEINFO::orderBy('caseid', 'DESC')->first();
								$sequence = DB::getSequence();
								$case_id = $sequence->nextValue('SEQ_CASEID');

								// Check SUW_EM_CONFIG.SLASEC if value is '-1' use SUW_OPTIONS.SLASEC_EM instead [fixed on 20161027 pour]
								if ($emconfig->slasec == '-1') {
									$slasec = $options->slasec_em;
								} else {
									$slasec = $emconfig->slasec;
								}

								try {
									CASEINFO::insert([
										'caseid' => $case_id,
										'feedid' => $mail_id,
										'feedtype' => 'EM',
										'feedsubtype' => 'EM',
										'casestatus' => 'N',
										'casecreationdt' => $current_date,
										'casecreateddt' => date('Y-m-d H:i:s', $mail->udate),
//										'caseduedt' => date('Y-m-d H:i:s', ($mail->udate + $options->slasec_em)),
										'caseduedt' => date('Y-m-d H:i:s', ($mail->udate + $slasec)),
										'sentiment_polarity' => 'N',
										'priorityscore' => $pri_score,
										'feeduserid' => mb_substr(strip_tags($mail_from_server->fromAddress), 0, 40, 'UTF-8'),
										'feedusername' => ($mail_from_server->fromName ? $mail_from_server->fromName.' ('.$mail_from_server->fromAddress.')' : $mail_from_server->fromAddress),
										'feedtitle' => mb_substr(strip_tags($mail_from_server->subject), 0, 100, 'UTF-8'),
										'slasec' => $options->slasec_em,
										'feature01' => Session::get('logged_in')->username,
										'language' => $chklang,
										'agentid' => -1,
										'isparentresp' => 1,
										'isais' => 0
									]);
								} catch (Exception $e) {
									write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
								}

								CASEINFO::where('caseid', '=', $case_id)->update([
									'caselongmsg' => $message_text
								]);
								$added++;

								if($send_email == 1):
									$send_email = 0;
									unset($mail);
									$mail = '';
								endif;
							endif;
						} // end hasMailIdReturnThenCreateCaseInfo
					endif;
/*
					try {
						$mail_from_server = $mailbox->getMail($mail->uid);
					} catch (Exception $e) {
						write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
						continue;
					}

					//$mailbox->markMailAsRead($mail->uid);
					$mail_in_case = CASEINFO::where('CASECREATEDDT', '=', date('Y-m-d H:i:s', $mail->udate))->where('FEEDUSERID', '=', $mail_from_server->fromAddress)->where('feature01', '=', Session::get('logged_in')->username)->first();
					/* ignore email lists * /
//					$ignore_lists = array('1149test1@ais.co.th');	// Dev Env
					$ignore_lists = array('corporatecallcenter@ais.co.th');	//Prod Env

					if(!$mail_in_case AND !in_array($mail_from_server->fromAddress, $ignore_lists)):
						$mailRelease = GMMail::orderBy('mail_id', 'desc')->first();
						if(isset($mailRelease)):
							//$mail_id = $mailRelease->mail_id;
							$sequence = DB::getSequence();
							$mail_id = $sequence->nextValue('SEQ_MAIL_ID');
						else:
							$mail_id = 800000;
						endif;
						//$mail_id = ($mail_id + 1);

						try{
							$insertGMMAIL = GMMail::insert([
								'mail_id' => $mail_id,
								'suid' => $uid,
								'folder_id' => $folder->folder_id,
								'email' => Session::get('logged_in')->username,
								'massage_no' => $mail->msgno,
								'subject' => mb_substr(strip_tags($mail_from_server->subject), 0, 100, 'UTF-8'),
								'from_name' => ($mail_from_server->fromName ? $mail_from_server->fromName.' ('.$mail_from_server->fromAddress.')' : $mail_from_server->fromAddress),
								'udate' => $mail->udate,
								'recent' => $mail->recent,
								'flagged' => $mail->flagged,
								'answered' => $mail->answered,
								'deleted' => $mail->deleted,
								'seen' => $mail->seen,
								'draft' => $mail->draft,
								'created_at' => date('Y-m-d H:i:s', $mail->udate),
								'updated_at' => $current_date,
								'sent_from' => $mail_from_server->fromAddress,
								'sent_to' => serialize($mail_from_server->to),
								'sent_cc' => ($mail_from_server->cc ? serialize($mail_from_server->cc) : ''),
								'reply_to' => ($mail_from_server->replyTo ? serialize($mail_from_server->replyTo) : '')
							]);
						} catch (Exception $e) {
							write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
						}

						$attachments = $mail_from_server->attachments;
						if(!empty($attachments)):
							foreach($attachments as $fid => $attachment):
								try{
									write_log('$fid = ' . $fid);
									GMAttachment::insert([
										'mail_id' => $mail_id,
										'fid' => $fid,
										//'file_name' => $attachment->fullName,
										//'file_name_original' => $attachment->name,
										'file_name' =>date('Ymd') . '/' . $attachment->fullName,
										'file_name_original' => date('Ymd') . '/' . $attachment->name,
										'created_at' => date('Y-M-d H:i:s'),
										'updated_at' => date('Y-M-d H:i:s'),
									]);
								} catch (Exception $e) {
									write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
								}
							endforeach;
						endif;

						if($mail_from_server->textHtml):
							$message_text = GMMailController::replaceImageSourceToTellvoicePath($mail_from_server->textHtml);
						else:
							$message_text = $mail_from_server->textPlain;
						endif;

						/* Update message_text when replace picture into textHTML * /
						try{
							GMMail::where('mail_id', '=', $mail_id)->update([ 'text' => $message_text ]);
						} catch (Exception $e) {
							write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
						}

						$countCase = CASEINFO::where('feedid', '=', $mail_id)->where('feedtype', '=', 'em')->where('feedsubtype', '=', 'em')->count();
						if($countCase == 0):
							$pri_score = 70;
							$message_text_check_lang = strip_tags($mail_from_server->subject).' '.$message_text;
							if(is_thai($message_text_check_lang) == 'T'):
								$chklang = 'T';
								$templateLang = 'th';
							else:
								$chklang = 'E';
								$templateLang = 'en';
							endif;

							// $pri_score = getPriorityScore_New($priorityInfo, $message_text, explode('@', $mail_from_server->fromAddress)[1], 'EM', 'EM', $chklang, Session::get('logged_in')->username, '', '', '');
							$pri_score = get_priority('EM', '', $chklang, $current_email);
							$options = DB::table('suw_options')->first();
							$emconfig = EMConfig::where('emailaddr', '=', Session::get('logged_in')->username)->first();
							$caseinfo = CASEINFO::orderBy('caseid', 'DESC')->first();
							$sequence = DB::getSequence();
							$case_id = $sequence->nextValue('SEQ_CASEID');

							try {
								CASEINFO::insert([
									'caseid' => $case_id,
									'feedid' => $mail_id,
									'feedtype' => 'EM',
									'feedsubtype' => 'EM',
									'casestatus' => 'N',
									'casecreationdt' => $current_date,
									'casecreateddt' => date('Y-m-d H:i:s', $mail->udate),
									'caseduedt' => date('Y-m-d H:i:s', ($mail->udate + $options->slasec_em)),
									'sentiment_polarity' => 'N',
									'priorityscore' => $pri_score,
									'feeduserid' => mb_substr(strip_tags($mail_from_server->fromAddress), 0, 40, 'UTF-8'),
									'feedusername' => ($mail_from_server->fromName ? $mail_from_server->fromName.' ('.$mail_from_server->fromAddress.')' : $mail_from_server->fromAddress),
									'feedtitle' => mb_substr(strip_tags($mail_from_server->subject), 0, 100, 'UTF-8'),
									'slasec' => $options->slasec_em,
									'feature01' => Session::get('logged_in')->username,
									'language' => $chklang,
									'agentid' => -1,
									'isparentresp' => 1,
									'isais' => 0
								]);
							} catch (Exception $e) {
								write_log('Exception error near line '.(__LINE__).': "'.$e->getMessage().'"', 'error_GMCallerController');
							}
							CASEINFO::where('caseid', '=', $case_id)->update([
								'caselongmsg' => $message_text
							]);
							$added++;

							if($send_email == 1):
								$send_email = 0;
								unset($mail);
								$mail = '';
							endif;
						endif;
					endif;*/
				endif;
			endforeach;

			if($added > 0):
				return 'total added: '.$added.' email'.($added > 1 ? 's' : '').'.';
			elseif($added == 0):
				return 'uptodate';
			else:
				return 'false';
			endif;
		endif;
	}

  public static function removeSingleQuote($array)
  {
    return str_replace("'", '', $array);
  }


  /**
   * Caller: remove all email is has age more than 2 days.
   * @return All email from server and our database.
   */
  public function removeEmails(){
    $emails = EMConfig::where('status', '=', 1)->get();
    $return_results = array();
    foreach($emails as $email):
      Session::put('logged_in', (object)['username' => $email->emailaddr, 'password' => $email->acctpwd, 'u' => base64_encode($email->emailaddr), 'p' => base64_encode($email->acctpwd), 'name' => $email->acctname, 'sent_email' => $email->sentaddr]);
      $mailbox = GMCallerController::getMailbox('inbox');
      $mailsIds = $mailbox->searchMailBoxNoCharset('BEFORE "'.date("d M Y").'"');
      $deleted = 0;
      foreach($mailsIds as $mail_id){
        if($mailbox->deleteMail($mail_id) == 1) $deleted++;
      }
    endforeach;
    return 'Deleted total: '.$deleted.' email'.($deleted > 1 ? 's' : '').'.';
  }

/*      public static function getReplyEmails($mail_from_server)
      {    
        $result = array_diff_key($mail_from_server->to, array(Session::get('logged_in')->username => Session::get('logged_in')->username));
        $hasOneReplyThenReturn = empty($result);
        if( $hasOneReplyThenReturn )
        {
            $result = $mail_from_server->replyTo;
        }
        return implode(', ' , array_keys($result));
      }*/
}
?>
