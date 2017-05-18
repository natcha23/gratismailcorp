@extends('layout/master')

@section('content')

  <nav class="navbar navbar-inverse" style="border-radius: 0">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <li><a class="navbar-brand" href="#">{{ $filename }}</a></li>
        <li>{{ html_entity_decode(link_to('templates', 'Back')) }}</li>
        <li>{{ link_to('template/download/'.$filename, 'Download') }}</li>
      </ul>
    </div>
  </nav>

  <style>
    iframe { width: 100% }
  </style>
  <script>
    $(function(){
      $body = $("body").height();
      $fix_iframe = $body - 80;
      $("iframe").height($fix_iframe);
    });
  </script>
  <iframe src="<?php echo $dir ?>" frameborder="0"></iframe>

@stop