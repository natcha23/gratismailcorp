@extends('layout/master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form action="" method="post">
				<h2>จัดวางส่วนเพิ่มเติมของ Layout : {{$filename}}</h2>
				<input type="hidden" id="template-result" value="">
				<p>
					หากต้องการใส่เนื้อหาหลังจาก เรียก template ใส่คำว่า &#123;&#123; content &#125;&#125; ระบบจะเอาส่วนเนื้อหาเดิมใส่แทนตรง 
					&#123;&#123; content &#125;&#125; ให้
				</p>
				<textarea name="template-html" id="txtDetail" cols="30" rows="10"></textarea>
				<div class="handle-button-group">
					<button class="btn btn-primary" name="edit" value="true">
						Update template
					</button>
					<button class="btn btn-primary">
						Leave without modify
					</button>
					<a href="{{ action('GMTemplateController@index') }}" class="btn btn-default">
						Back
					</a>
				</div><!-- handle-button-group -->
			</form><!-- form -->
		</div><!-- col-md-12 -->
	</div><!-- row -->
</div><!-- container -->

{{ HTML::script(Theme::localAsset('libs/tinymce/tinymce.min.js')) }}
{{ HTML::script(Theme::localAsset('js/handle-template.js')) }}

@stop