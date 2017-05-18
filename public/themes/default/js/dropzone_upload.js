$(function(){
  $dz_status = $(".dz-status");
  $dz_uploaded = $(".dz-uploaded");
  $progress = $(".progress");
  $progress_bar = $(".progress-bar");
  Dropzone.autoDiscover = false;
  $dropzone = new Dropzone(".dz-uploader", {
    url: '/gratismailcorp/file/upload',
    maxFilesize: 1,
    parallelUploads: 1,
    previewsContainer: false,
    uploadMultiple: true,
    init: function() {
      this.on("addedfile", function(file) {
        // $dz_status.empty();
        // if(file.size > 1024000){
        //   $dz_status.addClass('alert alert-danger').removeClass('hidden');
        //   $dz_status.append('<strong>' + file.name + '</strong> มีขนาดเกินมากกว่าที่ระบบกำหนด (ขนาดไม่เกิน 1Mb)<br/>');
        // }else{
        //   $progress.removeClass('hidden');
        //   $progress_bar.attr('aria-valuenow', 0).css('width', '0%');
        // }
        $progress.removeClass('hidden');
        $progress_bar.attr('aria-valuenow', 0).css('width', '0%');
      });
      this.on('uploadprogress', function (file, progress) {
        $dz_status.addClass('hidden');
        $progress_bar.attr('aria-valuenow', progress).css('width', progress + '%');
      });
      this.on("success", function(file, responseText) {
        if(responseText.result == 'uploaded'){
          $(".attachment ul").append(responseText.html);
          setTimeout(function(){
            $progress.addClass('hidden');
          }, 1500);
        }
      });
    }
  });
});

var getFileExtension = function(filename){
  var ext = /^.+\.([^.]+)$/.exec(filename);
  return ext == null ? "" : ext[1];
}

var removePicture = function(picture_id, trip_code){
  $dz_picture = $(".dz-uploaded .picture.picture-" + picture_id);
  $dz_picture.remove();
  $.post('/traova/file/remove', { picture_id: picture_id, trip_code: trip_code })
    .done(function(res) {
      console.log(res)
  });
}
