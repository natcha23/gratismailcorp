<?php

class AuthorizeController extends Controller {

  /**
   * Get login form
   *
   * @return view author.login.
   */
  public function loginForm(){
    $username = Input::get('u');
    $password = Input::get('p');
    if($username AND $password):
      $username = base64_decode($username);
      $password = base64_decode($password);
      $login = GMMailController::checkLogin($username, $password);
      if(isset($login['error'])):
        return View::make('author.login')->with(['error_message' => $login['error']['message']]);
      else:
        $account = GMAccount::find($username);
        if(is_null($account)):
          $account_name = 'AIS Call Center';
          $account_email = 'callcenter@ais.co.th';
          $account = GMAccount::insert([
            'email' => $username,
            'name' => $account_name,
            'sent_email' => $account_email,
            'last_login_at' => date('Y-M-d H:i:s'),
            'created_at' => date('Y-M-d H:i:s'),
            'updated_at' => date('Y-M-d H:i:s'),
          ]);
        else:
          $account = GMAccount::update([
            'last_login_at' => date('Y-M-d H:i:s'),
            'updated_at' => date('Y-M-d H:i:s'),
          ])->where('email', '=', $username);
        endif;
        Session::put('logged_in', (object)['username' => $username, 'password' => $password, 'name' => $account_name, 'sent_email' => $account_email]);
        return Redirect::to('/')->with(['success_message' => $login['success']['message']]);
      endif;
    endif;
    return View::make('author.login');
  }

  /**
   * Post login form
   *
   * @return login processing true or false.
   */
  public function loginPost(){
    if(!Request::get('txtEmail') OR !Request::get('txtPassword')):
      return Redirect::to('login')->with(['error_message' => 'Please enter email and password.']);
    endif;
    $login = GMMailController::checkLogin(Request::get('txtEmail'), Request::get('txtPassword'));
    if(isset($login['error'])):
      return View::make('author.login')->with(['error_message' => $login['error']['message']]);
    else:
      $account = GMAccount::find(Request::get('txtEmail'));
      if(is_null($account)):
        $username = Request::get('txtEmail');
        $account_name = 'AIS Call Center';
        $account_email = 'callcenter@ais.co.th';
        $account = GMAccount::insert([
          'email' => $username,
          'name' => $account_name,
          'sent_email' => $account_email,
          'last_login_at' => date('Y-M-d H:i:s'),
          'created_at' => date('Y-M-d H:i:s'),
          'updated_at' => date('Y-M-d H:i:s'),
        ]);
      else:
        $account = GMAccount::update([
          'last_login_at' => date('Y-M-d H:i:s'),
          'updated_at' => date('Y-M-d H:i:s'),
        ])->where('email', '=', $username);
      endif;
      Session::put('logged_in', (object)['username' => Request::get('txtEmail'), 'password' => Request::get('txtPassword'), 'name' => $account_name, 'sent_email' => $account_email]);
      return Redirect::to('/')->with(['success_message' => $login['success']['message']]);
    endif;
  }

  /**
   * Get logout
   *
   * @return processing logout.
   */
  public function logoutGet(){
    Session::forget('logged_in');
    return Redirect::to('/');
  }
}