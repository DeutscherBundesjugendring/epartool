CKEDITOR.editorConfig = function( config ) {
  config.extraPlugins = 'colorbutton,colordialog,div,find,font,selectall,smiley';
  config.allowedContent = true,
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
    {name: 'links', items: ['Link', 'Unlink']},
    {name: 'insert', items: ['Table', 'HorizontalRule', 'Smiley', 'SpecialChar']},
    {name: 'styles', items: ['Format', 'Font', 'FontSize']},
    {name: 'colors', items: ['TextColor', 'BGColor']},
    {name: 'tools', items: ['Maximize', 'ShowBlocks']},
    {name: 'about', items: ['About']}
  ];

  config.removeDialogTabs = 'link:advanced';
};
