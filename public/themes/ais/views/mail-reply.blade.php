@extends('layout/master')

@section('content')
  <ul id="control" class="clearfix hidden-print">
    <li>{{ html_entity_decode(link_to("mail/detail/{$mail->mail_id}".Func::requestLogin(), '<i class="fa fa fa-level-up"></i> <span class="labe-textl">return</span>', ['class' => 'btn-return', 'data-message' => 'Do you want to discard?'])) }}</li>
    <li class="break"></li>
    <!-- <li>{{ html_entity_decode(link_to('send', '<i class="fa fa fa-send"></i> <span class="labe-textl">send</span>', ['class' => 'disable-link submit', 'data-form' => '#send-reply', 'onclick' => 'parent.postMessage(\'emailsent:1\',\'*\');'])) }}</li> -->
    <li>{{ html_entity_decode(link_to('send', '<i class="fa fa fa-send"></i> <span class="labe-textl">send</span>', ['class' => 'disable-link submit', 'data-form' => '#send-reply'])) }}</li>
  </ul>
  <article id="mail">
    <h2 class="header"><i class="fa fa-<?php echo $icon ?>" style="color: #AAA"></i> {{ $title }}</h2>
    {{ Form::open(['url' => 'mail/reply/'.$mail->mail_id.Func::requestLogin(), 'class' => 'form-inline disable-enter', 'id' => 'send-reply']) }}

      <!-- Send Section
      ===================================================== --> 
      <input type="hidden" id="mailID" value="<?php echo $mail->mail_id ?>">
      <div class="send-to" style="margin-bottom: 10px">
        <div class="to">
          <span class="text-label">To</span>
          <span class="text-field">
            <div data-tags-input-name="to" class="tagging email">{{ $mail->sent_from }}</div>
          </span>
        </div>
        <span class="cc">
          <span class="text-label">{{ link_to('', 'Cc', ['class' => 'disable-link']) }}</span>
          <span class="text-field <?php echo $mail_cc_class ?>">
            <div data-tags-input-name="cc" class="tagging email"><?php echo $mail_cc ?></div>
          </span>
        </span>
        <span class="bcc">
          <span class="text-label">{{ link_to('', 'Bcc', ['class' => 'disable-link']) }}</span>
          <span class="text-field hidden">
            <div data-tags-input-name="bcc" class="tagging email"></div>
          </span>
        </span>
      </div>

      

      <!-- Subject
      ===================================================== --> 
      <div class="form-group" style="width:100%; margin-bottom: 5px;">
        <label for="txtSubject">Subject</label>
        <input type="text" class="form-control" id="txtSubject" name="txtSubject" value="<?php echo $title ?>" style="width: 100%; display:block">
      </div>

      <!-- Uploadt Attachment file
      ===================================================== --> 
      <div class="form-group hidden-print" style="width:100%; margin-bottom: 5px;">
        {{ Form::label('file', 'File attachment', array()) }}
        <div class="dropzone">
          <div class="progress hidden">
            <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            </div>
          </div>
          <div class="dz-status"></div>
          <div class="dz-uploader" data-dz-message>Drag and drop or click to choose your files on here!</div>
          <div class="dz-uploaded"></div>
        </div>
      </div>
      <div class="form-group hidden-print" style="width:100%; margin-bottom: 5px;">
        <div class="attachment">
          <ul>
          @if($attachments)
            @foreach($attachments as $attachment)
              <li>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="files[]" value="<?php echo $attachment->fid ?>" checked="checked">
                    <?php
                      $icon = Func::getIcon($attachment->file_name_original);
                      $link = html_entity_decode(link_to("public/attachments/{$attachment->file_name}", "<i class=\"{$icon}\"></i> ".$attachment->file_name_original, ['target' => '_blank']));
                      echo str_replace('/index.php/', '/', $link);
                    ?>
                    <a href="/" class="remove" data-file-id="<?php echo $attachment->fid ?>"><span class="fa fa-remove"></span></a>
                  </label>
                </div>
              </li>
            @endforeach
          @endif
          </ul>
        </div>
      </div>

      <!-- Email Template
      ===================================================== --> 
      <div class="form-group" style="width:100%; margin-bottom: 5px;">
        <div id="template-result" class="hidden"></div>
        <?php
          $templates = scandir(public_path('themes/ais/views/emails'));
          $template_dropdown['default'] = '-- No template or remove --';
          foreach($templates as $template):
            if($template != '.' AND $template != '..'):
              $template_dropdown[$template] = $template;
            endif;
          endforeach;
        ?>
        {{ Form::label('lstTemplate', 'Email templates') }}
        {{ Form::select('lstTemplate', $template_dropdown, NULL, ['class' => 'form-control lstTemplate', 'style' => 'width: 100%; display:block']) }}
      </div>

      <!-- Editor Tinymce
      ===================================================== --> 
      <textarea id="txtDetail" name="txtDetail">
        &nbsp
        <blockquote class="gmail_quote" style="margin:0 0 0 .8ex;border-left:1px #ccc solid;padding-left:1ex">
          From : {{ $mail->sent_from_blockquote }} <br>
          Date: {{ date('D, M d, Y \a\t H:i A', strtotime($mail->created_at)) }} <br>
          Subject : {{ $mail->subject }} <br>
          To: {{ $mail->sent_to_blockquote }} <br>
          @if ( !empty($mail->mail_cc_blockquote))
            Cc: {{ str_replace('1149test1@ais.co.th', $mail->sending_addr, $mail->mail_cc_blockquote) }}  
          @endif
          <br><br>

          {{ $mail->text }}
        </blockquote>
      </textarea>
    {{ Form::close() }}
  </article>
@stop