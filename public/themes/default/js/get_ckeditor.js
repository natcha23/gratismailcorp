$(function(){
  CKEDITOR.replace('txtDetail', {
    skin: 'bootstrapck',
    extraPlugins: 'templates',
    extraPlugins: 'youtube',
    templates: 'default',
    toolbar: [
      ['TextColor', 'BGColor', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat', 'Font', 'FontSize', 'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'Link', 'Unlink', '-', 'Image', 'Flash', 'Youtube', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'],
    ],
    allowedContent: true,
  });
});