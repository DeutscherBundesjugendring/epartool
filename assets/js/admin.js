(function() {
  var bindConsultationCustomPhaseNames, bindEmailAddAttachment, bindEmailConsultationSelect, bindEmailTemplateSelect, bindTableRowsToggle, bindToggleAll, i18n, initCKEditor, initDatepicker, initI18n, initSelect2, initSortableFollowupSnippets, initSortableVotingDirs, mediaSelectPopup;

  $(document).ready(function() {
    bindEmailTemplateSelect();
    bindEmailConsultationSelect();
    bindEmailAddAttachment();
    bindTableRowsToggle();
    bindToggleAll();
    bindConsultationCustomPhaseNames();
    initCKEditor();
    initI18n();
    initDatepicker();
    initSortableFollowupSnippets();
    initSortableVotingDirs();
    initSelect2();
    $('[data-toggle="tooltip"]').tooltip();
  });

  i18n = {
    translate: function(key) {
      if (exports.I18N['en'][key]) {
        return exports.I18N['en'][key];
      } else {
        return key;
      }
    }
  };

  bindEmailTemplateSelect = function() {
    return $('.js-template-selector').change((function(eventObj) {
      var bodyHtmlEditor, bodyTextField, subjectField, templateId;
      templateId = $(eventObj.target).val();
      subjectField = $('form.js-send-mail .js-subject');
      bodyTextField = $('form.js-send-mail .js-body-text');
      bodyHtmlEditor = CKEDITOR.instances['body_html'];
      if (templateId * 1 === 0) {
        subjectField.val('');
        bodyTextField.val('');
        return bodyHtmlEditor.setData('');
      } else {
        return $.get(baseUrl + 'mail-send/template-json', {
          templateId: templateId
        }, function(data) {
          subjectField.val(data.subject);
          bodyTextField.val(data.body_text);
          return bodyHtmlEditor.setData(data.body_html);
        }, 'json');
      }
    }));
  };

  bindEmailConsultationSelect = function() {
    var consulRecipients;
    consulRecipients = $('#mail_consultation').data('consultations');
    return $('.js-consultation-selector').change((function(eventObj) {
      var consulId, isFollowupDisabled, isNewsletterDisabled, isParticipantDisabled, isVoterDisabled;
      $('.js-consultation-participant').prop('checked', false);
      $('.js-consultation-voter').prop('checked', false);
      $('.js-consultation-newsletter').prop('checked', false);
      $('.js-consultation-followup').prop('checked', false);
      consulId = $(eventObj.target).val();
      if (consulId * 1 === 0) {
        isParticipantDisabled = true;
        isVoterDisabled = true;
        isNewsletterDisabled = true;
        isFollowupDisabled = true;
      } else {
        isParticipantDisabled = !consulRecipients[consulId]['hasParticipant'];
        isVoterDisabled = !consulRecipients[consulId]['hasVoter'];
        isNewsletterDisabled = !consulRecipients[consulId]['hasNewsletter'];
        isFollowupDisabled = !consulRecipients[consulId]['hasFollowup'];
      }
      $('.js-consultation-participant').prop('disabled', isParticipantDisabled);
      $('.js-consultation-voter').prop('disabled', isVoterDisabled);
      $('.js-consultation-newsletter').prop('disabled', isNewsletterDisabled);
      $('.js-consultation-followup').prop('disabled', isFollowupDisabled);
    }));
  };

  bindEmailAddAttachment = function() {
    var attachmentId;
    attachmentId = 0;
    return $('.js-email-add-attachment').click(function(ev) {
      var button, html;
      ev.preventDefault();
      button = $(ev.target);
      html = button.next().clone().removeClass('hidden');
      html.find('input[type=hidden]').attr('name', 'attachments[]').removeProp('disabled');
      html = html.prop('outerHTML').replace(/TOKEN_TO_BE_REPLACED_BY_JS/g, attachmentId);
      button.before(html);
      attachmentId++;
    });
  };

  bindTableRowsToggle = function() {
    var checkboxes, rows;
    rows = $('.js-table-rows-toggle tr');
    checkboxes = $('.js-table-rows-toggle input:checkbox');
    rows.click(function(e) {
      if (e.target.type !== 'checkbox') {
        return $(':checkbox', this).trigger('click');
      }
    });
    return checkboxes.change(function(e) {
      var row;
      row = $(this).closest('tr');
      if ($(this).is(':checked')) {
        return row.addClass('info');
      } else {
        return row.removeClass('info');
      }
    });
  };

  bindToggleAll = function() {
    return $('.js-toggle-all').change(function() {
      var form;
      form = $(this).closest('form');
      if ($(this).is(':checked')) {
        return form.find('input:checkbox:not(:checked)').prop('checked', true).change();
      } else {
        return form.find('input:checkbox:checked').prop('checked', false).change();
      }
    });
  };

  bindConsultationCustomPhaseNames = function() {
    return $('.js-enable-custom-consultation-phase-names').change(function(ev) {
      var checkbox, inputs;
      checkbox = $(ev.currentTarget);
      inputs = checkbox.parents('form').find('input[type=text]');
      if (checkbox.prop('checked')) {
        return inputs.prop('disabled', false);
      } else {
        return inputs.prop('disabled', true);
      }
    });
  };

  initCKEditor = function() {
    $('.wysiwyg-standard').ckeditor({
      filebrowserBrowseUrl: baseUrl + '/admin/media/index/targetElId/CKEditor'
    });
    return $('.wysiwyg-email').ckeditor({
      removePlugins: 'horizontalrule,list,justify,indent,indentlist,indentblock,image2,flash,iframe,div',
      removeButtons: 'Underline,Anchor,Strike'
    });
  };

  initDatepicker = function() {
    $('.js-datepicker').datetimepicker({
      'format': 'YYYY-MM-DD',
      'pickTime': false
    });
    $('.js-datetimepicker').datetimepicker({
      'format': 'YYYY-MM-DD HH:mm:ss',
      'sideBySide': true
    });
    return $(document).on('change', '[data-toggle=disable]', function() {
      var $el, $this;
      $this = $(this);
      $el = $($this.data('disable-target') + '.js-datetimepicker');
      $el.data('DateTimePicker', null);
      $el.datetimepicker({
        'format': 'YYYY-MM-DD HH:mm:ss',
        'sideBySide': true
      });
      $el = $($this.data('disable-target') + '.js-datepicker');
      $el.data('DateTimePicker', null);
      $el.datetimepicker({
        'format': 'YYYY-MM-DD',
        'pickTime': false
      });
    });
  };

  initSelect2 = function() {
    return $('.js-select2').select2();
  };

  initSortableFollowupSnippets = function() {
    $('.js-sortable-followup-snippets').disableSelection();
    return $('.js-sortable-followup-snippets').sortable({
      'update': function(ev, ui) {
        var $form, i;
        i = 1;
        $form = ui.item.closest('form');
        $form.find('tr input[type=hidden]').each(function() {
          $(this).val(i);
          return i++;
        });
        $form.find('button[type=submit]').prop('disabled', false);
      }
    });
  };

  initSortableVotingDirs = function() {
    $('.js-sortable-voting-directory').disableSelection();
    return $('.js-sortable-voting-directory').sortable({
      'update': function(ev, ui) {
        var $form, i;
        i = 1;
        $form = ui.item.closest('form');
        $form.find('div input[type=hidden]').each(function() {
          $(this).val(i);
          return i++;
        });
        $form.find('button[type=submit]').prop('disabled', false);
      }
    });
  };

  initI18n = function() {
    $.fn.confirmation.Constructor.prototype.options = {
      'confirm-message': i18n.translate('Are you sure?'),
      'confirm-yes': i18n.translate('Yes'),
      'confirm-no': i18n.translate('No')
    };
  };

  mediaSelectPopup = (function() {

    /**
     * Inserts the image data back into the element that triggered the display of this popup
     * @param    {string}  filename              The media filename
     * @param    {string}  targetElId            The id of the element that triggered this popup
     * @param    {string}  imgPathPrefixInput    The path to be used in media element in input field
     * @param    {string}  imgPathImage          The path to be used in media element in image src attrib.
                                                It points to the cache folder.
     * @param    {numeric} CKEditorFuncNum       The CKEditor callbeck identifier
     */
    function mediaSelectPopup() {}

    mediaSelectPopup.insertValue = function(filename, targetElId, imgPathPrefixInput, imgPathImage, CKEditorFuncNum) {
      var inputEl;
      if (imgPathPrefixInput.length > 0) {
        imgPathPrefixInput = imgPathPrefixInput + '/';
      }
      if (targetElId === 'CKEditor') {
        window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum, imgPathPrefixInput + filename);
      } else {
        inputEl = $(window.opener.document.getElementById(targetElId));
        inputEl.val(imgPathPrefixInput + filename);
        inputEl.parent().find('img').attr('src', imgPathImage);
      }
      return window.close();
    };

    return mediaSelectPopup;

  })();

  document.mediaSelectPopup = mediaSelectPopup;

}).call(this);
