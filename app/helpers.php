<?php

function pr($object){
  echo '<pre>';
  print_r($object);
  echo '</pre>';
}

/*
 *  2017 March, 28
 *  natcha@tellvoice.com
 *  Display debug.
 */
function _print($data=null) {
	echo 'Developer is debugging.'.'</br></br>';
	if(!empty($data)) {
		echo '<pre>'.print_r($data, 1).'</pre>';
	} else {
		echo 'empty data!';
	}
}

/*
 * 2017 Apr, 05
 * natcha@tellvoice.com
 * ===========
 * Fill - <td> tag for display on tinymce.
 * ===========
 */
function substituteHtmlSpecialContent($content=NULL) {
	if (empty($content)) {	return null;	}
	$newstr = $content;
	$str_to_insert = "</td>";
	$count_str = strlen($str_to_insert);

	$body_pattern = "/<tr(.*?)>(.*?)[^<\/td><\/tr><tr]/s";
	preg_match_all($body_pattern, $content, $body_matches, PREG_OFFSET_CAPTURE);

	$num = count($body_matches[0]);
	$increase = $separate = 0;
	foreach($body_matches[0] as $index => $element) {
		$increase+=1;

		if ($increase < $num) {
			$position = $body_matches[0][$increase][1];
				
			if($index > 0) {
				$separate+=$count_str; // </td> is 5 characters.
				$position = $position+$separate;
			}
			$newstr = substr_replace($newstr, $str_to_insert, $position, 0); //insert string at position define.
		}

	}
	return $newstr;
}

/*
 * 2017 May, 17
 * natcha@tellvoice.com
 * Find - Is first character html tag. 
 */
function firstCharacterIsHTMLTag($content=null) {

	if (empty($content)) { return false; }

	$body_pattern = "/^\s*<[html|meta](.*?)>/s"; // Finded <tr..>...<tr..> excepted <tr..>...</tr><tr..>
	preg_match_all($body_pattern, $content, $body_matches, PREG_OFFSET_CAPTURE);

	if (!empty($body_matches[0])) {
		return true;

	} else {
		return false;
	}

}


function get_priority($type, $subtype, $language, $feature01 = '', $feature02 = '', $feature03 = '', $feature04 = ''){
  // return [$type, $subtype, $language];
  $score = DB::table('CASEPRIORITY')->where('feedtype', '=', $type)->where('language', '=', $language);
  if($feature01)
    $score = $score->where('feature01', '=', $feature01);
  if($feature02)
    $score = $score->where('feature02', '=', $feature03);
  if($feature03)
    $score = $score->where('feature03', '=', $feature03);
  if($feature04)
    $score = $score->where('feature04', '=', $feature04);
  $score = $score->first();
  if(!$score):
    $score = DB::table('CASEPRIORITY')->where('feedtype', '=', $type)->first();
    return $score->priorityscore;
  else:
    return $score->priorityscore;
  endif;
}

function removeImageInlineFromAttachmentLists($mail_attachments, $mailMessage)
{
  $attachments = array();
  // echo count($mail_attachments);die();
  // echo '<pre>'; print_r($mail_attachments);die();
  foreach ($mail_attachments as $key => $attachmentObject) {

//    preg_match_all('%' . $attachmentObject->file_name . '%siu', $mailMessage, $matches);
    preg_match_all('%' . str_replace("%","\%", $attachmentObject->file_name) . '%siu', $mailMessage, $matches);
    
    $isFoundInMessageItNotAttachmentThenRemoveItOutOfList =  empty($matches[0][0]);

    if( $isFoundInMessageItNotAttachmentThenRemoveItOutOfList )
    {
      $attachments[] = $attachmentObject;
    }
  }
  return $attachments;
}

function is_thai($text){
  $preg_text = preg_replace('/[ก-๙]/u', '', $text);
  if(strlen($preg_text) != strlen($text))
    return true;
  return false;
}

function write_autoreply_log($message, $file_name = 'gmemail_autoreply') {
  $datetime = date('Ymd');
  _write_log('logs/autoreply/'.($file_name ? $file_name.'_' : '').$datetime.'.log', $message);
}

function write_log($message, $file_name = 'gmmail_caller'){
  $datetime = date('Ymd');
  _write_log('logs/'.($file_name ? $file_name.'_' : '').$datetime.'.log', $message);
}


function write_sendmail_log($message, $file_name = 'gmmail_sendmail'){
  $datetime = date('Ymd');
  _write_log('logs/sendmail/'.($file_name ? $file_name.'_' : '').$datetime.'.log', $message);
}

function write_caller_log($message, $file_name = 'gmmail_caller')
{
  $datetime = date('Ymd');
  _write_log('logs/caller/'.($file_name ? $file_name.'_' : '').$datetime.'.log', $message); 
}

function _write_log($path, $message) {
  
  $log_path = public_path($path);
  $fp = fopen($log_path, 'a');
  if($fp == null){
    return;
  }
  $time = date('[Y-M-d H:i:s]');
  fwrite($fp, "$time $message" . PHP_EOL);
  fclose($fp);  
}

/*function write_log($message, $file_name = 'gmmail_caller'){
  $datetime = date('Ymd');
  $log_path = public_path('logs/'.($file_name ? $file_name.'_' : '').$datetime.'.log');
  $fp = fopen($log_path, 'a');
  if($fp == null){
    return;
  }
  $time = date('[Y-M-d H:i:s]');
  fwrite($fp, "$time $message" . PHP_EOL);
  fclose($fp);
}*/

?>
