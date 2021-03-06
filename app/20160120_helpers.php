<?php

function pr($object){
  echo '<pre>';
  print_r($object);
  echo '</pre>';
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
    foreach ($mail_attachments as $key => $attachmentObject) 
    { 
        preg_match_all('%' . $attachmentObject->file_name . '%siu', $mailMessage, $matches);

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



function write_log($message, $file_name = 'gmmail_caller'){
  $datetime = date('Ymd');
  $log_path = public_path('logs/'.($file_name ? $file_name.'_' : '').$datetime.'.log');
  $fp = fopen($log_path, 'a');
  if($fp == null){
    return;
  }
  $time = date('[Y-M-d H:i:s]');
  fwrite($fp, "$time $message" . PHP_EOL);
  fclose($fp);
}
?>