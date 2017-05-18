$(function(){
  CKEDITOR.replace('txtDetail', {
    height: '410px',
    extraPlugins: 'base64image',
    skin: 'bootstrapck',
    extraPlugins: 'templates',
    extraPlugins: 'youtube',
    templates: 'default',
    tabSpaces  : 4,
    pasteFromWordRemoveFontStyles : false,
    pasteFromWordRemoveStylesfal : false,
    toolbar: [
      ['Maximize', 'TextColor', 'BGColor', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat', 'Font', 'FontSize', 'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'Link', 'Unlink', '-', 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'],
    ],
    allowedContent: true,
    filebrowserBrowseUrl: '/SMMGetInfo/gratismailcorp/public/libraries/ckfinder/ckfinder.html',
    filebrowserImageBrowseUrl: '/SMMGetInfo/gratismailcorp/public/libraries/ckfinder/ckfinder.html?Type=Images',
    filebrowserFlashBrowseUrl: '/SMMGetInfo/gratismailcorp/public/libraries/ckfinder/ckfinder.html?Type=Flash',
    filebrowserUploadUrl: '/SMMGetInfo/gratismailcorp/public/libraries/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
    filebrowserImageUploadUrl: '/SMMGetInfo/gratismailcorp/public/libraries/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
    filebrowserFlashUploadUrl: '/SMMGetInfo/gratismailcorp/public/libraries/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
  });

  CKEDITOR.on( 'dialogDefinition', function( ev ) {

    /* Disable link menu */
    // var dialogName = ev.data.name;
    // var dialogDefinition = ev.data.definition;
    // if ( dialogName == 'image' || dialogName == 'link' ) {
    //   dialogDefinition.removeContents('Link');
    //   dialogDefinition.removeContents('link');
    // }

    /* Upload is default tab */
    var dialogName = ev.data.name;
    var dialog = ev.data.definition.dialog;
    if (dialogName == 'image') {
      dialog.on('show', function () {
        this.selectPage('Upload');
      });
    }
  });

});