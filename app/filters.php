<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request){
	//
});


App::after(function($request, $response){
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('ais_auth', function(){
	$caseid = Request::get('CASEID');
	if(!$caseid):
		$username = base64_decode(Request::get('u'));
		if(!$username):
			return 'Please enter email';
		else:
			$account = EMConfig::where('emailaddr', '=', $username)->first();
		  if(!$account):
		    return 'Please re-check your email';
		  else:
		    $username = base64_decode(Request::get('u'));
		    $password = $account->acctpwd;
		    Session::put('logged_in', (object)['username' => $username, 'password' => $password, 'u' => base64_encode($username), 'p' => base64_encode($password), 'name' => $account->acctname, 'sent_email' => $account->sentaddr]);
		  endif;
		endif;
	endif;
	// if(!Session::get('logged_in')):
	// 	if(!$username OR !$password):
	// 		return 'Please enter email and password.';
	// 	endif;
	// 	$login = GMMailController::checkLogin($username, $password);
	// 	if(isset($login['error'])):
	// 		return $login['error']['message'];
	// 	else:
	// 		$account = GMAccount::find($username);
	// 		Session::put('logged_in', (object)['username' => $username, 'password' => $password, 'u' => base64_encode($username), 'p' => base64_encode($password), 'name' => $account->name, 'sent_email' => $account->sent_email]);
	// 	endif;
	// elseif(Request::get('reset') == 'users'):
	// 	$account = GMAccount::find($username);
	// 	if(!$account):
	// 		return Response::view('404', array(), 404);
	// 	endif;
	// 	Session::put('logged_in', (object)['username' => $username, 'password' => $password, 'u' => base64_encode($username), 'p' => base64_encode($password), 'name' => $account->name, 'sent_email' => $account->sent_email]);
	// endif;
});

Route::filter('auth', function(){
	if(!Session::get('logged_in')):
		return Redirect::guest('login');
	endif;
});


Route::filter('auth.basic', function(){
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function(){
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function(){
	if (Session::token() !== Input::get('_token')){
		throw new Illuminate\Session\TokenMismatchException;
	}
});
