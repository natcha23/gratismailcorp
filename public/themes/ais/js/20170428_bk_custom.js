$(function(){

  $("[data-toggle='tooltip']").tooltip();

  if($("a.btn-return")){
    $("a.btn-return").on('click', function(){
    	
      $message = $(this).data('message');
     
      if($message){
    	  $confirm = confirm($message);
      }else{
    	  $confirm = confirm("Do you want to cancel on your doing?");
      }
     
      if($confirm == true){
        return true;
      }else{
        return false;
      }
    	
    });
  }

  if($("a.disable-link")){
    $("a.disable-link").on('click', function(event){
      event.preventDefault();
      $parant = $(this).parent().parent();
      if($('.text-field', $parant).hasClass('hidden')){
        $('.text-field', $parant).removeClass('hidden');
        $parant.addClass('clearfix');
        $(this).addClass('active');
      }else{
        $('.text-field', $parant).addClass('hidden');
        $('.text-field .tagging.email', $parant).tagging("reset");
        $parant.removeClass('clearfix');
        $(this).removeClass('active');
      }
    });
  }

  if($(".attachment ul li a.remove")){
    $(document).on("click", ".attachment ul li a.remove", function(event){
      event.preventDefault();
      $fid = $(this).data('file-id');
      $parant = $(this).parent().parent().parent();
      $confirm = confirm("Do you want to remove this file?");
      if($confirm == true){
        $parant.remove();
        $.post('/SMMGetInfo/gratismailcorp/index.php/file/remove', { fid: $fid }).done(function( data ) {
          console.log(data);
        });
      }
    });
  }

  if($(".tagging")){
    var tagging_options_email = {
      "edit-on-delete": false,
      "no-duplicate": true,
      "no-duplicate-callback": window.alert,
      "no-duplicate-text": "Duplicate email",
      "forbidden-chars": [",", "#", "!", "$", "%", "^", "&", "*", "(", ")", "="]
    };
    $(".tagging.email").tagging(tagging_options_email);
  }

  $("form.disable-enter").keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $("body #content h2.header a.disable-link").on('click', function(){
    $div_info = $($(this).data('div'));
    if($div_info.hasClass('hidden')){
      $(this).addClass('active');
      $div_info.removeClass('hidden');
    }else{
      $(this).removeClass('active');
      $div_info.addClass('hidden');
    }
  });

  $("a.submit").on('click', function(){
    $form = $($(this).data('form'));
    $message = $(this).data('message');
    if($message == undefined || $message == "") {
    	$message = "Do you want to send this email?";
    }
    $value = $(this).data('this-value');
    if($value == 'send'){
      $('.form-mode').val('send');
    }
    
 // ================
    $("#confirmModal").modal("toggle").on("shown.bs.modal", function (e) {
      //$message = $(e.relatedTarget).attr('data-message');
      $(this).find('.modal-body p').text($message);
      $title = $(e.relatedTarget).attr('data-title');
      $(this).find('.modal-title').text($title);

      /* Pass form reference to modal for submission on yes/ok */
      var form = $(e.relatedTarget).closest('form');
      $(this).find('.modal-footer #btnSubmit').data('form', form);
    });

	    /* Form confirm (yes/ok) handler, submits form */
	    $('#confirmModal').find('.modal-footer #btnSubmit').on('click', function(){
	    	$form.submit(); //        $(this).data('form').submit();
	        
	    });
	    
  // ================
    
//    if($confirm == true){
//    	$form.submit();
//    }
    
  });

  if($(".lstTemplate")){
    // localStorage.setItem("mail_reply", 0)
    // localStorage.setItem("mail_id", 0)
    $(".lstTemplate").on('change', function(){
      if(localStorage.getItem("mail_reply")){
        if($("#mailID").val() == localStorage.getItem("mail_id")){
          $mail_value = localStorage.getItem("mail_reply");
        }else{
          $mail_value = tinyMCE.get('txtDetail').getContent();
          localStorage.setItem("mail_reply", tinyMCE.get('txtDetail').getContent());
          localStorage.setItem("mail_id", $("#mailID").val());
        }
      }else{
        localStorage.setItem("mail_reply", tinyMCE.get('txtDetail').getContent());
        localStorage.setItem("mail_id", $("#mailID").val());
        $mail_value = localStorage.getItem("mail_reply");
      }
      $fileName = $(this).val();
      if($fileName != 'default'){
        $("#template-result").load("/SMMGetInfo/gratismailcorp/public/themes/ais/views/emails/" + $fileName + " #template_content", function() {

          $content = $(this).html();
          console.log($content);
          $content = $content.replace("{{ content }}", $mail_value);
          // Sets the content of a specific editor (my_editor in this example)
          tinyMCE.get('txtDetail').setContent($content);
        });
      }else{
        tinyMCE.get('txtDetail').setContent('');
      }
    });

    //  BACKUP
    //  if($(".lstTemplate")){
    // // localStorage.setItem("mail_reply", 0)
    // // localStorage.setItem("mail_id", 0)
    // $(".lstTemplate").on('change', function(){
    //   if(localStorage.getItem("mail_reply")){
    //     if($("#mailID").val() == localStorage.getItem("mail_id")){
    //       $mail_value = localStorage.getItem("mail_reply");
    //     }else{
    //       $mail_value = CKEDITOR.instances.txtDetail.getData();
    //       localStorage.setItem("mail_reply", CKEDITOR.instances.txtDetail.getData());
    //       localStorage.setItem("mail_id", $("#mailID").val());
    //     }
    //   }else{
    //     localStorage.setItem("mail_reply", CKEDITOR.instances.txtDetail.getData());
    //     localStorage.setItem("mail_id", $("#mailID").val());
    //     $mail_value = localStorage.getItem("mail_reply");
    //   }
    //   $fileName = $(this).val();
    //   if($fileName != 'default'){
    //     $("#template-result").load("/SMMGetInfo/gratismailcorp/public/themes/ais/views/emails/" + $fileName, function() {
 
    //       $content = $(this).html();
    //       $content = $content.replace("{{ content }}", $mail_value);
    //       CKEDITOR.instances.txtDetail.setData($content);
    //     });
    //   }else{
    //     CKEDITOR.instances.txtDetail.setData('');
    //   }
    // });

  }
});

