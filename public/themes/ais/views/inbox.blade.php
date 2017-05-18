@extends('layout/master')

@section('content')
  @if($mails->isEmpty())
    <article>
      <div class="alert alert-warning no-border-radius" role="alert">
        <i class="fa fa-warning"></i> No email in this folder.
      </div>
    </article>
  @else
    <div class="mail control hidden">
      {{ Form::checkbox('all', 1, false, ['style' => 'margin: 0']) }}
      <span class="dropdown">
        <button class="btn btn-default dropdown-toggle no-border-radius no-border" type="button" id="dropdownAction" data-toggle="dropdown" aria-expanded="true">
          Actions
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownAction">
          <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-trash"></i> Delete</a></li>
          <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-folder-open-o"></i> Mark as read</a></li>
          <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-folder-o"></i> Mark as unread</a></li>
        </ul>
      </span>
      <!-- <div class="form-inline">
        <div class="form-group">
          <div>
            <div class="checkbox">
              {{ Form::checkbox('all', 1, false, ['style' => 'margin: 0']) }}
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="delete">
            {{ html_entity_decode(link_to('', '<i class="fa fa-trash"></i> delete', ['class' => 'btn'])) }}
          </div>
        </div>
      </div> -->
    </div>
    <table class="table inbox">
      <tbody>
        @foreach($mails as $mail)
          <?php
            /* Count an attactment in email */
            $attachment = GMAttachment::where('mail_id', '=', $mail->mail_id)->count();
            $hasAttachment = '';
            if($attachment > 0):
              $hasAttachment = '<i class="fa fa-paperclip"></i>';
            endif;

            /* Get text in email */
            if($mail->text):
              $text = strip_tags($mail->text);
              $text_substr = iconv_substr(strip_tags($mail->text), 0, 100, 'UTF-8');
              $text = (strlen($text) > 100 ? $text_substr.' ...' : $text_substr);
            else:
              $text = 'No email detail. Please checking this email to retrieve the detail.';
            endif;
          ?>
          <tr class="<?php echo ($mail->seen == 0 ? 'unseen' : '') ?>">
            <td width="55"><div class="text-center">{{ Form::checkbox('lst', true) }}</div></td>
            <td>
              <div class="subject">{{ link_to("mail/detail/{$mail->mail_id}", $mail->subject) }}</div>
              <div class="reviews">{{ $hasAttachment }} {{ link_to("mail/detail/{$mail->mail_id}", $text) }}</div>
            </td>
            <td width="100">
              <div class="text-left">{{ date('d/m/y', $mail->udate) }}</div>
              <div class="text-left sub-description">{{ Carbon::createFromTimeStamp($mail->udate)->diffForHumans() }}</div>
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3">
            {{ $mails->links() }}
          </td>
        </tr>
      </tfoot>
    </table>
  @endif
@stop