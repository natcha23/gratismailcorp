<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>
    @section('title')
      @if(isset($title))
        {{ $title }}
      @else
        {{ "Little World" }}
      @endif
       :: GratisMail
    @show
  </title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- CSS files -->
  {{ HTML::style(Theme::localAsset('css/bootstrap.min.css')) }}
  {{ HTML::style(Theme::localAsset('css/font-awesome.min.css')) }}
  {{ HTML::style(Theme::localAsset('css/tagging.css')) }}
  {{ HTML::style(Theme::localAsset('css/custom.css')) }}

  <!-- JS files -->
  {{ HTML::script(Theme::localAsset('js/jquery.min.js')) }}
  {{ HTML::script(Theme::localAsset('js/bootstrap.min.js')) }}
  {{ HTML::script(Theme::localAsset('js/tagging.min.js')) }}
  {{ HTML::script(Theme::localAsset('libs/historyjs/scripts/bundled/html4+html5/jquery.history.js')) }}
  @if(isset($libs) AND in_array('ckeditor', $libs))
    {{ HTML::script(Theme::localAsset('libs/ckeditor/ckeditor.js')) }}
    {{ HTML::script(Theme::localAsset('js/get_ckeditor.js')) }}
  @endif
  @if(isset($libs) AND in_array('dropzone', $libs))
    {{ HTML::script(Theme::localAsset('js/dropzone.js')) }}
    {{ HTML::script(Theme::localAsset('js/dropzone_upload.js')) }}
  @endif
  {{ HTML::script(Theme::localAsset('js/html5.js')) }}
  {{ HTML::script(Theme::localAsset('js/custom.js')) }}
</head>
<body class="page-<?php echo Request::segment(1) ?>">
  
  @if(Session::get('logged_in'))
  <nav class="navbar navbar-default navbar-ais navbar-fixed-top hidden-print" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-member">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        {{ link_to('/', 'GratisMail', array("class" => "navbar-brand")) }}
      </div>
      <div class="collapse navbar-collapse" id="navbar-collapse-member">
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
              <span class="glyphicon glyphicon-user"></span> {{ Session::get('logged_in')->username }} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li>{{ html_entity_decode(link_to('/profile', '<span class="glyphicon glyphicon-user"></span> Profile')) }}</li>
              <li>{{ html_entity_decode(link_to('/how-to-use', '<i class="fa fa-question-circle"></i> How to use')) }}</li>
              <li>{{ html_entity_decode(link_to('/settings', '<span class="glyphicon glyphicon-cog"></span> Settings')) }}</li>
              <li class="divider"></li>
              <li>{{ html_entity_decode(link_to('/logout', '<i class="fa fa-sign-out"></i> Log out')) }}</li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  @endif

  <div class="container-fluid">
    <div class="row">
      @if(Session::get('logged_in'))
      <div id="sidebar" class="hidden-print">
        <div class="compose-email text-center">
          <?php // html_entity_decode(link_to('compose/new', '<i class="fa fa-edit"></i> compose', ['class' => 'btn btn-danger btn-sm'])) ?>
          {{ html_entity_decode(link_to('compose/new', '<i class="fa fa-edit"></i>', ['class' => 'btn btn-danger btn-sm', 'data-toggle' => 'tooltip', 'data-placement' => 'right', 'data-original-title' => 'Compose'])) }}
        </div>
        <ul id="folders">
          <?php
            $folders = GMFolder::get();
            foreach($folders as $folder):
              if(Request::segment(2) == $folder->name OR $folder->folder_id == Session::get('folder_id')):
                $classActive = 'active';
              else:
                $classActive = '';
              endif;
              // echo "<li class=\"folder-{$folder->name} {$classActive}\">".html_entity_decode(link_to("folder/{$folder->name}", "<i class=\"fa fa-{$folder->icon}\"></i> <span class=\"label-text\">{$folder->name}</span>"))."</li>";
              echo "<li class=\"folder-{$folder->name} {$classActive}\">".html_entity_decode(link_to("folder/{$folder->name}", "<i class=\"fa fa-{$folder->icon}\"></i>", ['data-toggle' => 'tooltip', 'data-placement' => 'right', 'data-original-title' => ucfirst($folder->name)]))."</li>";
            endforeach;
          ?>
        </ul>
        @if(trim($__env->yieldContent('sidebar')))
          <hr>
          <!-- Sidebar -->
          @yield('sidebar')
        @endif
      </div>
      @endif

      <div id="content">

        <!-- Alert -->
        @if(isset($error_message) OR Session::get('error_message'))
          <div class="alert alert-danger alert-dismissible no-border-radius" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">ปิด</span></button>
            {{ Session::get('error_message') ? Session::get('error_message') : $error_message }}
          </div>
        @endif
        @if(isset($success_message) OR Session::get('success_message'))
          <div class="alert alert-success alert-dismissible no-border-radius" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">ปิด</span></button>
            {{ Session::get('success_message') ? Session::get('success_message') : $success_message }}
          </div>
        @endif

        <!-- Content -->
        @yield('content')
      </div>
    </div>

  </div>

</body>
</html>