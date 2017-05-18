<?php

class Func {

  public static function requestLogin($start = '?'){
    if(Request::all()):
      $request = Request::all();
      unset($request['reply']);
      return $start.http_build_query($request);
    else:
      $params = (object)['u' => Session::get('logged_in')->u, 'p' => Session::get('logged_in')->p];
      return $start.http_build_query($params);
    endif;
  }

	public static function pr($object, $title = ''){
    if($title):
      echo "<h3>$title</h3>";
    endif;
		echo '<pre>';
		print_r($object);
		echo '</pre>';
	}

  public static function timeNow($format = ''){
    if($format):
      return date($format);
    else:
      /* Default compatible with MySQL: Datetime */
      // return date("Y-m-d H:i:s");
      /* Default compatible with Oracle: Datetime */
      return date("d-M-y h:i:s A");
    endif;
  }

  public static function timeNowUnix(){
    return time();
  }

  public static function charRandom($length){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++):
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    endfor;
    return $randomString;
  }

  public static function strposa($haystack, $needles=array(), $offset = 0) {
    $chr = array();
    foreach($needles as $needle) {
      $res = strpos($haystack, $needle, $offset);
      if ($res !== false) $chr[$needle] = $res;
    }
    if(empty($chr)) return false;
    return min($chr);
  }

  public static function getIcon($filename){
    $images = ['.jpg', '.jpeg', '.gif', '.png'];
    $audios = ['.mp3', '.ogg', '.wma'];
    $codes = ['.php', '.html', '.htm', '.jsp', '.asp', '.aspx', '.css', '.js', '.coffee'];
    $excels = ['.xls', '.xlsx'];
    $powerpoints = ['.ptt', '.pttx'];
    $words = ['.doc', '.docx'];
    $videos = ['.mp4', '.wmv', '.3gp', '.mov', '.flv'];
    $zips = ['.7zip', '.rar', '.zip'];
    $texts = ['.txt'];
    $pdfs = ['.pdf'];
    if(Func::strposa($filename, $images, 1)):
      $icon = 'fa fa-file-image-o';
    elseif(Func::strposa($filename, $audios, 1)):
      $icon = 'fa fa-file-audio-o';
    elseif(Func::strposa($filename, $codes, 1)):
      $icon = 'fa fa-file-code-o';
    elseif(Func::strposa($filename, $excels, 1)):
      $icon = 'fa fa-file-excel-o';
    elseif(Func::strposa($filename, $words, 1)):
      $icon = 'fa fa-file-word-o';
    elseif(Func::strposa($filename, $powerpoints, 1)):
      $icon = 'fa fa-file-powerpoint-o';
    elseif(Func::strposa($filename, $images, 1)):
      $icon = 'fa fa-file-image-o';
    elseif(Func::strposa($filename, $pdfs, 1)):
      $icon = 'fa fa-file-pdf-o';
    elseif(Func::strposa($filename, $texts, 1)):
      $icon = 'fa fa-file-text-o';
    elseif(Func::strposa($filename, $videos, 1)):
      $icon = 'fa fa-file-video-o';
    elseif(Func::strposa($filename, $zips, 1)):
      $icon = 'fa fa-file-zip-o';
    else:
      $icon = 'fa fa-file-o';
    endif;

    return $icon;
  }

}