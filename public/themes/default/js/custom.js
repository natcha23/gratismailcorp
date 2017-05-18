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
        $.post('/gratismailcorp/file/remove', { fid: $fid }).done(function( data ) {
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
    if($message){
      $confirm = confirm($message);
    }else{
      $confirm = confirm("Do you want to send this email?");
    }
    $value = $(this).data('this-value');
    if($value == 'send'){
      $('.form-mode').val('send');
    }
    if($confirm == true){
      $form.submit();
    }
  });

  if($("a.change-template")){
    $("a.change-template").on('click', function(){
      $("a.change-template").removeClass('active');
      $(this).addClass('active');
      $fileName = $(this).data('file-name');
      $("#template-result").load("/gratismailcorp/public/themes/default/views/emails/" + $fileName, function() {
        CKEDITOR.instances.txtDetail.setData($(this).html());
      });
    });
  }
});
