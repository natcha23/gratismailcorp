<?php

class GMFileController extends BaseController {

  private static function generateHTML($fid, $fname, $fpath){
    $icon = Func::getIcon($fname);
    return "<li><div class=\"checkbox\">
              <label>
                <input type=\"checkbox\" name=\"files[]\" value=\"{$fid}\" checked=\"checked\">
                <a href=\"{$fpath}\" target=\"_blank\"><i class=\"{$icon}\"></i> {$fname}</a>
                <a href=\"/\" class=\"remove\" data-file-id=\"{$fid}\"><span class=\"fa fa-remove\"></span></a>
              </label>
            </div></li>";
  }

  public static function attachmentUpload(){

    if(is_null(Session::get('mail_draft'))):
      $mail = GMMailController::saveDraft();
    elseif(Session::get('mail_draft') AND Session::get('mail_edit') == 1):
      $mail = Session::get('mail_draft');
    else:
      $mail = Session::get('mail_draft');
    endif;

    if($mail->mail_id):
      $mail_id = $mail->mail_id;
    else:
      $mail_id = $mail->suid;
    endif;

    // return [$mail_id, $mail];

    $fid = "{$mail_id}_".time()."_".Func::charRandom(10);
    $fname = $fid.".".pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
    $fname_original = $_FILES['file']['name'][0];
    $fdestination = "public/attachments/{$fname}";

    move_uploaded_file($_FILES['file']['tmp_name'][0], $fdestination);

    $attachment_insert = GMAttachment::insert([
      'mail_id' => $mail_id,
      'fid' => $fid,
      'file_name' => $fname,
      'file_name_original' => $fname_original,
      'created_at' => date('Y-M-d H:i:s'),
      'updated_at' => date('Y-M-d H:i:s'),
    ]);
    // return array($attachment_insert, $mail_id);

    return ['result' => 'uploaded', 'file_url' => url($fdestination), 'html' => GMFileController::generateHTML($fid, $fname_original, str_replace('/index.php/', '/', url($fdestination)))];

  }

  public static function attachmentRemove(){
    $file = GMAttachment::find($_POST['fid']);
    unlink("public/attachments/{$file->file_name}");
    $file->delete();
    return 'removed';
  }

  public static function getAttacmentFiles($mail_id){
    return $mail_id;
  }
}