tinymce.init({
  	selector: "#txtDetail",
  	height: '410px',
	plugins: [
	  "advlist autolink lists link image charmap print preview anchor",
	  "searchreplace visualblocks code fullscreen",
	  "insertdatetime media table textcolor colorpicker contextmenu paste jbimages"
	],
	fontsize_formats: "10pt 12pt 14pt 18pt 20pt 24pt 36pt",

	toolbar: "paste insertfile undo redo | styleselect | fontsizeselect bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor link image jbimages",
	font_formats: 'Tahoma=tahoma',
	relative_urls:false,
	paste_retain_style_properties : "all",
    paste_strip_class_attributes : "none",
    theme_advanced_fonts : 'Tahoma=tahoma',
   //paste_remove_spans : true,  
	setup: function(ed) {
		ed.on('init', function() {
	        this.getDoc().body.style.fontFamily = 'tahoma';
	        this.getDoc().body.style.fontSize = '10pt';
	    });

		ed.on('keydown', function(event) 
		{
		  	if (event.keyCode == 9) { // tab pressed
			  	if (event.shiftKey) {
			  		ed.execCommand('Outdent');
			  	}
			  	else {
			  		ed.insertContent('&nbsp;&nbsp;&nbsp;&nbsp;');
			  	}

			  	event.preventDefault();
			  	return false;
			}
		});
	}
});