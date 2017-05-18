<?php

class GMTemplateController extends Controller {

	private static $dir = 'themes/ais/views/emails';

	public static function index(){
		$dir = public_path(self::$dir);
		return View::make('template/index')->with(['dir' => $dir]);
	}

	public static function removeTemplate($filename){
		$dir_file = 'public/'.self::$dir.'/'.$filename;
		unlink($dir_file);
		return Redirect::to('templates')->with(['success_message' => $filename.' has been deleted.']);
	}

	public static function addTemplate(){
		if(Input::hasFile('template-html')):
			if(Input::file('template-html')->getMimeType() == 'text/html'):
				$dir = public_path(self::$dir);
				$originalFileName = Input::file('template-html')->getClientOriginalName();
				if(file_exists($dir.'/'. $originalFileName)):
					$filename = date('d_m_Y_H_i_s').'_'.str_replace(" ", "_", $originalFileName);
				else:
					$filename = str_replace(" ", "_", $originalFileName);
				endif;
				Input::file('template-html')->move($dir, $filename);
				return Redirect::to('template/handle/' . $filename);

			else:
				return Redirect::to('templates')->with(['error_message' => 'Template file is allow only html file. (.html)']);
			endif;
		else:
			return Redirect::to('templates')->with(['error_message' => 'Please make sure you file enter.']);
		endif;
	}
	// public static function addTemplate(){
	// 	if(Input::hasFile('template-html')):
	// 		if(Input::file('template-html')->getMimeType() == 'text/html'):
	// 			$dir = public_path(self::$dir);
	// 			$originalFileName = Input::file('template-html')->getClientOriginalName();
	// 			if(file_exists($dir.'/'. $originalFileName)):
	// 				$filename = date('d_m_Y_H_i_s').'_'.str_replace(" ", "_", $originalFileName);
	// 			else:
	// 				$filename = str_replace(" ", "_", $originalFileName);
	// 			endif;
	// 			Input::file('template-html')->move($dir, $filename);
	// 			return Redirect::to('templates')->with(['success_message' => 'Template has been uploaded.']);
	// 		else:
	// 			return Redirect::to('templates')->with(['error_message' => 'Template file is allow only html file. (.html)']);
	// 		endif;
	// 	else:
	// 		return Redirect::to('templates')->with(['error_message' => 'Please make sure you file enter.']);
	// 	endif;
	// }

	public static function viewTemplate($filename){
		$dir = asset('public/'.self::$dir.'/'.$filename);
		return View::make('template/view')->with(['dir' => $dir, 'filename' => $filename]);
	}

	public static function downloadTemplate($filename){
		$dir = 'public/'.self::$dir.'/'.$filename;
		return Response::download($dir);
	}


	public static function getHandleTemplate($filename) {
		$dir = asset('public/'.self::$dir.'/'.$filename);

		$fullPhysicalPath = base_path() . '/public/themes/ais/views/emails/';
		$tmpFile =  $fullPhysicalPath . 'tmp_' . $filename;
		$file = $fullPhysicalPath . $filename;
		$html = file_get_contents($file);

		$html = str_replace('<body>', '<body><div id="template_content">', $html);
		$html = str_replace('</body>', '</div></body>', $html);

		File::put($file, $html);



		return View::make('template/handle-template')->with(['dir' => $dir, 'filename' => $filename]);
	}

	public static function postHandleTemplate($filename) {
		$dir = 'public/' . self::$dir . '/' . $filename;

		$isNoErrorWritingFile = File::put($dir, '<div id="template_content">' . Input::get('template-html') . '</div>');
		if ($isNoErrorWritingFile === false)
		{
		    write_log("Error writing email template name ='" . $filename . "'", 'edit_email_template');
		}
	
		return Redirect::to('templates')->with(['success_message' => 'Template has been uploaded.']);
	}

}
