$(function(){
  $dz_status = $(".dz-status");
  $dz_uploaded = $(".dz-uploaded");
  $progress = $(".progress");
  $progress_bar = $(".progress-bar");
  Dropzone.autoDiscover = false;
  $dropzone = new Dropzone(".dz-uploader", {
    url: '/SMMGetInfo/gratismailcorp/index.php/file/upload',
    maxFilesize: 10,
    parallelUploads: 1,
    previewsContainer: false,
    uploadMultiple: true,
    init: function() {
      this.on("addedfile", function(file) {
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
  $.post('/SMMGetInfo/gratismailcorp/index.php/file/remove', { picture_id: picture_id, trip_code: trip_code })
    .done(function(res) {
      console.log(res)
  });
}