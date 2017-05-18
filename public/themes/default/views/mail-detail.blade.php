@extends('layout/master')

@section('content')
  <ul id="control" class="clearfix hidden-print">
    <li>{{ html_entity_decode(link_to(Session::get('url_previous'), '<i class="fa fa-level-up"></i> <span class="labe-textl">return</span>')) }}</li>
    <li class="break"></li>
    <li>{{ html_entity_decode(link_to("mail/reply/{$mail->mail_id}", '<i class="fa fa-mail-reply"></i> <span class="labe-textl">reply</span>')) }}</li>
    <li>{{ html_entity_decode(link_to("mail/reply/{$mail->mail_id}?reply=all", '<i class="fa fa-mail-reply-all"></i> <span class="labe-textl">reply all</span>')) }}</li>
    <li>{{ html_entity_decode(link_to("mail/forward/{$mail->mail_id}", '<i class="fa fa-mail-forward"></i> <span class="labe-textl">forward</span>')) }}</li>
    <li class="break"></li>
    <li>{{ html_entity_decode(link_to("mail/print/{$mail->mail_id}", '<i class="fa fa-print"></i> <span class="labe-textl">print</span>')) }}</li>
    <li class="break"></li>
    <li>{{ html_entity_decode(link_to("mail/delete/{$mail->mail_id}", '<i class="fa fa-trash"></i> <span class="labe-textl">delete</span>', ['onclick' => 'return confirm(\'Do you want to delete this email?\')'])) }}</li>
  </ul>
  <article id="mail">
    <h2 class="header">
      <i class="fa fa-quote-right" style="color: #AAA"></i> {{ $title }}
      <a href="#" class="disable-link" data-div=".sent-to" title="Information"><i class="sent-info fa fa-info-circle"></i></a>
      <?php
        $ccs = [];
        $mail->cc = unserialize($mail->cc);
        if($mail->cc):
          foreach($mail->cc as $key => $value):
            $ccs[] = link_to("compose/new?to={$key}", $key);
          endforeach;
        endif;
        $sent_to = ['me']; // me is $mail->to.
        $sent_to = array_merge($ccs, $sent_to);
      ?>
      <div class="small sent-to information hidden">From: {{ link_to("compose/new?to={$mail->from}", $mail->from) }}</div>
      <div class="small sent-to information hidden">Sent to: {{ implode(', ', $sent_to) }}</div>
    </h2>
    <div class="text">
      {{ $mail->text }}
    </div>
    @if(count($attachments) > 0)
      <div class="attachment">
        <div style="margin-bottom: 5px">
          <strong><i class="fa fa-paperclip"></i> Attachment</strong>
        </div>
        <ul>
        @foreach($attachments as $attachment)
          <li>
            <?php
              $icon = Func::getIcon($attachment->file_name_original);
              echo html_entity_decode(link_to("public/attachments/{$attachment->file_name}", "<i class=\"{$icon}\"></i> ".$attachment->file_name_original, ['target' => '_blank']));
            ?>
          </li>
        @endforeach
        </ul>
      </div>
    @endif
  </article>
@stop