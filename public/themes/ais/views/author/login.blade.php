@extends('../layout/master')

@section('content')
  {{ Form::open(array('url' => 'login', 'method' => 'post', 'class' => 'form-horizontal login-panel')) }}
    <div class="form-group">
      <label for="txtEmail" class="col-sm-2 control-label">Email</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="txtEmail" name="txtEmail" placeholder="Email">
      </div>
    </div>
    <div class="form-group">
      <label for="txtPassword" class="col-sm-2 control-label">Password</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="Password">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Sign in</button>
      </div>
    </div>
  {{ Form::close() }}
  <?php
    // $url = ['u'=>base64_encode('admin@chockpermpoon.com'), 'p'=>base64_encode('cpru7KUN')];
    // $url = http_build_query($url);
    // echo $url;
    // $account = ['u'=>Input::get('u'), 'p'=>Input::get('p')];
    // Func::pr(base64_decode($account['u']));
    // Func::pr(base64_decode($account['p']));
  ?>
@stop