@extends('layout/master')

@section('content')
  <ul id="control" class="clearfix hidden-print">
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-inbox"></i> <span class="labe-textl">inbox</span>', ['class' => 'btn-return', 'data-message' => 'Do you want to discard?'])) }}</li>
    <li class="break"></li>
    <li>{{ html_entity_decode(link_to('send', '<i class="fa fa fa-send"></i> <span class="labe-textl">send</span>', ['class' => 'disable-link submit', 'data-form' => '#send-compose', 'data-this-value' => 'send'])) }}</li>
    <li>{{ html_entity_decode(link_to('draft', '<i class="fa fa fa-pencil"></i> <span class="labe-textl">save draft</span>', ['class' => 'disable-link submit', 'data-form' => '#send-compose', 'data-message' => 'Do you want to save draft?', 'data-this-value' => 'savedraft'])) }}</li>
  </ul>
  <article id="mail">
    <h2 class="header"><i class="fa fa-edit" style="color: #AAA"></i> New compose</h2>
    {{ Form::open(['url' => 'compose/new', 'class' => 'form-inline disable-enter', 'id' => 'send-compose']) }}
      <input type="hidden" name="form-mode" class="form-mode" class="form-mode" value="savedraft">
      <div class="send-to" style="margin-bottom: 10px">
        <div class="to">
          <span class="text-label"><strong>To</strong></span>
          <span class="text-field">
            <div data-tags-input-name="to" class="tagging email"></div>
          </span>
        </div>
        <span class="cc">
          <span class="text-label">{{ link_to('', 'Cc', ['class' => 'disable-link']) }}</span>
          <span class="text-field hidden">
            <div data-tags-input-name="cc" class="tagging email"></div>
          </span>
        </span>
        <span class="bcc">
          <span class="text-label">{{ link_to('', 'Bcc', ['class' => 'disable-link']) }}</span>
          <span class="text-field hidden">
            <div data-tags-input-name="bcc" class="tagging email"></div>
          </span>
        </span>
      </div>
      <div class="form-group" style="width:100%; margin-bottom: 5px;">
        {{ Form::label('txtSubject', 'Subject') }}
        {{ Form::text('txtSubject', null, ['class' => 'form-control input-sm', 'style' => 'display:block; width:100%; border-color: #DDD']) }}
      </div>
      <div class="form-group" style="width:100%; margin-bottom: 5px;">
        {{ html_entity_decode(Form::label('lstTemplate', 'Email templates <span class="caret"></span>', ['data-toggle' => 'collapse', 'href' => '#collapseTemplates', 'aria-expanded' => 'false', 'aria-controls' => 'collapseTemplates'])) }}
        <div class="collapse" id="collapseTemplates">
          <div id="template-result" class="hidden"></div>
          <ul class="templates">
            <?php
              $templates = scandir(public_path('themes/default/views/emails'));
              foreach($templates as $template):
                if($template != '.' AND $template != '..'):
                  echo '<li>'.link_to("#{$template}", $template, ['data-file-name' => $template, 'class' => 'change-template disable-link']).'</li>';
                endif;
              endforeach;
            ?>
          </ul>
        </div>
      </div>
      <div class="form-group" style="width:100%; margin-bottom: 5px;">
        <textarea id="txtDetail" name="txtDetail"></textarea>
      </div>
      <div class="form-group" style="width:100%; margin-bottom: 5px;">
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
      <div class="form-group" style="width:100%; margin-bottom: 5px;">
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
                      echo html_entity_decode(link_to("public/attachments/{$attachment->file_name}", "<i class=\"{$icon}\"></i> ".$attachment->file_name_original, ['target' => '_blank']));
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
    {{ Form::close() }}
  </article>
@stop