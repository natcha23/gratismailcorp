
$(function() {
	
	var url = $(location).attr('pathname').split('/');
	var $templateName = url[url.length-1];
	
	tinymce.init({
	  	selector: "#txtDetail",
	  	height: '410px',
		plugins: [
		  "advlist autolink lists link image charmap print preview anchor",
		  "searchreplace visualblocks code fullscreen",
		  "insertdatetime media table contextmenu paste jbimages"
		],
		  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",

		relative_urls:false,
		init_instance_callback : function(editor) {
//			var d = new Date();
//			var t = d.getTime();
//			$("#template-result").load("/SMMGetInfo/gratismailcorp/public/themes/ais/views/emails/" + $templateName + '?t=' + t , function() {
			$("#template-result").load("/SMMGetInfo/gratismailcorp/public/themes/ais/views/emails/" + $templateName , function() {
					$content = $(this).find('#template_content').html();
					editor.setContent($content);
		  	});
		 }
	});

});