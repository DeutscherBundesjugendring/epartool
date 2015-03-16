CKEDITOR.editorConfig = function( config ) {
  config.extraPlugins = 'colorbutton,colordialog,div,find,flash,font,iframe,image2,selectall,smiley';

  config.toolbar = [
    {name: 'document', items: ['Source']},
    {name: 'clipboard', items: ['PasteText', 'PasteFromWord']},
    {name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll']},
    {
      name: 'basicstyles',
      items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
    },
    {
      name: 'paragraph',
      items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv']
    },
    {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
    {name: 'insert', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'Iframe']},
    {name: 'styles', items: ['Format', 'Font', 'FontSize']},
    {name: 'colors', items: ['TextColor', 'BGColor']},
    {name: 'tools', items: ['Maximize', 'ShowBlocks']},
    {name: 'about', items: ['About']}
  ];

  config.removeDialogTabs = 'link:advanced';
};
