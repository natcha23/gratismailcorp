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
  {{ HTML::script(Theme::localAsset('js/typeahead.js')) }}
  {{ HTML::script(Theme::localAsset('libs/historyjs/scripts/bundled/html4+html5/jquery.history.js')) }}
  @if(isset($libs) AND in_array('ckeditor', $libs))
    {{-- Don't use this because can not paste table from MS excel
    {{ HTML::script(Theme::localAsset('libs/ckeditor/ckeditor.js')) }}
    {{ HTML::script(Theme::localAsset('js/get_ckeditor.js')) }}
    --}}
    {{ HTML::script(Theme::localAsset('libs/tinymce/tinymce.min.js')) }}
    {{ HTML::script(Theme::localAsset('js/get_tinymce.js?t=' . time())) }}
  @endif
  @if(isset($libs) AND in_array('dropzone', $libs))
    {{ HTML::script(Theme::localAsset('js/dropzone.js')) }}
    {{ HTML::script(Theme::localAsset('js/dropzone_upload.js?t=' . time())) }}
  @endif
  {{ HTML::script(Theme::localAsset('js/html5.js')) }}
  {{ HTML::script(Theme::localAsset('js/custom.js')) }}
</head>
<body class="page-<?php echo Request::segment(1) ?>">
  <div id="content">
  
<div id="discardModal" class="modal fade">
  <div class="modal-dialog" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Discard</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnDiscard" class="btn btn-primary">OK</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
  
<div id="confirmModal" class="modal fade">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSubmit" class="btn btn-primary">OK</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


    <!-- Alert -->
    @if(isset($error_message) OR Session::get('error_message'))
      <div class="alert alert-danger alert-dismissible no-border-radius" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">เธ�เธดเธ”</span></button>
        {{ Session::get('error_message') ? Session::get('error_message') : $error_message }}
      </div>
    @endif
    @if(isset($success_message) OR Session::get('success_message'))
      <div class="alert alert-success alert-dismissible no-border-radius" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">เธ�เธดเธ”</span></button>
        {{ Session::get('success_message') ? Session::get('success_message') : $success_message }}
      </div>
    @endif

    <!-- Content -->
    @yield('content')
  </div>

</body>
</html>