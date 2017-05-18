@extends('layout/master')

@section('content')

  <div id="myModal" class="modal fade">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>Proceed...</p>
      </div>
    </div>
  </div>
  </div>

  <input type="hidden" name="mail_id" id="mail_id" value="<?php echo $mail->mail_id ?>">
  <input type="hidden" name="mail_address" id="mail_address" value="<?php echo base64_decode(Request::get('u')) ?>">
  @if(Request::get('sending') == 'sent')
    <script>
	  	$('#myModal').modal('show');
	    window.setTimeout(function () {
	        $("#myModal").modal("hide");
	        parent.postMessage('emailsent:' + $('#mail_id').val() + ':' + $('#mail_address').val(), '*');
	    }, 2000);
//   	alert('Your message has been sent');
//      parent.postMessage('emailsent:' + $('#mail_id').val() + ':' + $('#mail_address').val(), '*');  
    </script>
  @endif


  <ul id="control" class="clearfix hidden-print">
    <li>{{ html_entity_decode(link_to("mail/reply/{$mail->mail_id}".Func::requestLogin(), '<i class="fa fa-mail-reply"></i> <span class="labe-textl">reply</span>')) }}</li>
    <li>{{ html_entity_decode(link_to("mail/reply/{$mail->mail_id}?reply=all".Func::requestLogin('&'), '<i class="fa fa-mail-reply-all"></i> <span class="labe-textl">reply all</span>')) }}</li>
    <li>{{ html_entity_decode(link_to("mail/forward/{$mail->mail_id}".Func::requestLogin(), '<i class="fa fa-mail-forward"></i> <span class="labe-textl">Forward</span>')) }}</li>
    <!-- <li>{{ html_entity_decode(link_to("mail/forward/{$mail->mail_id}".Func::requestLogin(), '<i class="fa fa-mail-forward"></i> <span class="labe-textl">forward</span>')) }}</li> -->
    <!-- <li class="break"></li>
    <li>{{ html_entity_decode(link_to("mail/print/{$mail->mail_id}", '<i class="fa fa-print"></i> <span class="labe-textl">print</span>')) }}</li> -->
    <!-- <li class="break"></li> -->
    <!-- <li>{{ html_entity_decode(link_to("mail/delete/{$mail->mail_id}".Func::requestLogin(), '<i class="fa fa-trash"></i> <span class="labe-textl">delete</span>', ['onclick' => 'return confirm(\'Do you want to delete this email?\')'])) }}</li> -->
  </ul>
  <article id="mail">
    <h2 class="header">
      <i class="fa fa-quote-right" style="color: #AAA"></i> {{ $title }}
      <a href="#" class="disable-link" data-div=".sent-to" title="Information"><i class="sent-info fa fa-info-circle"></i></a>
      <?php
        $ccs = [];
        $mail->cc = unserialize($mail->cc);
        if($mail->cc):
          if($mail->cc):
            foreach($mail->cc as $key => $value):
              $ccs[] = link_to("compose/new?to={$key}".Func::requestLogin('&'), $key);
            endforeach;
          endif;
        endif;
        $sent_to = ['me']; // me is $mail->to.
        $sent_to = array_merge($ccs, $sent_to);
        if(!$mail->from_name):
          $mail->from_name = $mail->email;
        endif;
      ?>
      <div class="small sent-to information hidden">From: {{ link_to("compose/new?to={$mail->sent_from}".Func::requestLogin('&'), $mail->from_name, ['style' => 'color: #418906; text-decoration: none']) }}</div>
      <div class="small sent-to information hidden">Sent to: {{ implode(', ', $sent_to) }}</div>
    </h2>
    @if(count($attachments) > 0)
      <div class="attachment top hidden-print">
        <div style="margin-bottom: 5px">
          <strong><i class="fa fa-paperclip"></i> Attachment</strong>
        </div>
        <ul>
        @foreach($attachments as $attachment)
          <li>
            <?php
              $icon = Func::getIcon($attachment->file_name_original);
              $link = html_entity_decode(link_to("public/attachments/{$attachment->file_name}", "<i class=\"{$icon}\"></i> ".$attachment->file_name_original, ['target' => '_blank']));
              echo str_replace('/index.php/', '/', $link);
            ?>
          </li>
        @endforeach
        </ul>
      </div>
    @endif
    <div class="text">
      {{ $mail->text }}
    </div>
  </article>
@stop