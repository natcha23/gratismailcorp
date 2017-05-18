tinymce.init({
  	selector: "#txtDetail",
  	height: '410px',
  	
//  	cleanup_on_startup : false,
//  	verify_html : false,
//  	extended_valid_elements : 'table[*]*',
//  	element_format : 'html',
  	valid_elements : '*[*]',
//  	encoding: 'xml',
//  	verify_html : false,
//  	extended_valid_elements : 'tr[*],td[*]',
//  	valid_children : '+body[style],-body[div],p[strong|a|#text]'
//  	schema: 'html5',
//  	valid_children : '+table[tr]',
//  	entity_encoding : "raw",
  	protect: [
//  	         /\<\/?(if|endif)\>/g,  // Protect <if> & </endif>
//  	         /\<xsl\:[^>]+\>/g,  // Protect <xsl:...>
//  	         /<\?php.*?\?>/g  // Protect php code
//				/\<\/?(if|endif)\>/g,  // Protect <if> & </endif>
//  	        /<\?php.*?\?>/g  // Protect php code
  	],
  	
	plugins: [
	  "advlist autolink lists link image charmap print preview anchor",
	  "searchreplace visualblocks code fullscreen",
	  "insertdatetime media table textcolor colorpicker contextmenu paste jbimages"
	],
	fontsize_formats: "10pt 12pt 14pt 18pt 20pt 24pt 36pt",

	toolbar: "paste insertfile undo redo | styleselect | fontsizeselect bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor link image jbimages",
//	font_formats: 'Tahoma,Arial=arial,helvetica,sans-serif;Courier New=courier new,Times New Roman=times new roman',
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