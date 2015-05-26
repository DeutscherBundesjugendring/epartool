$(document).ready () ->
    bindEmailTemplateSelect()
    bindEmailConsultationSelect()
    bindEmailAddAttachment()
    bindTableRowsToggle()
    bindToggleAll()
    bindConsultationCustomPhaseNames()

    initCKEditor()
    initI18n()
    initDatepicker()
    initSortableFollowupSnippets()
    initSortableVotingDirs()
    initSelect2()

    return

i18n = {
    translate: (key) ->
        if exports.I18N['en'][key]
            return exports.I18N['en'][key]
        else
            return key
}

# Binds ajax loading template data to a template selctor box
bindEmailTemplateSelect = () ->
    $('.js-template-selector')
        .change ((eventObj) ->
            templateId = $(eventObj.target).val()
            subjectField = $('form.js-send-mail .js-subject')
            bodyTextField = $('form.js-send-mail .js-body-text')
            bodyHtmlEditor = CKEDITOR.instances['body_html']
            if templateId * 1 == 0
                subjectField.val('')
                bodyTextField.val('')
                bodyHtmlEditor.setData('')
            else
                $.get(
                    baseUrl + 'mail-send/template-json'
                    {templateId : templateId}
                    (data) ->
                        subjectField.val(data.subject)
                        bodyTextField.val(data.body_text)
                        bodyHtmlEditor.setData(data.body_html)
                    'json'
                )
        )

# Binds form element enable/disable to Consulattions selection
bindEmailConsultationSelect = () ->
    consulRecipients = $('#mail_consultation').data('consultations')
    $('.js-consultation-selector')
        .change ((eventObj) ->
            $('.js-consultation-participant').prop('checked', false)
            $('.js-consultation-voter').prop('checked', false)
            $('.js-consultation-newsletter').prop('checked', false)
            $('.js-consultation-followup').prop('checked', false)

            consulId = $(eventObj.target).val()
            if consulId * 1 == 0
                isParticipantDisabled = true;
                isVoterDisabled = true;
                isNewsletterDisabled = true;
                isFollowupDisabled = true;
            else
                isParticipantDisabled = !consulRecipients[consulId]['hasParticipant']
                isVoterDisabled = !consulRecipients[consulId]['hasVoter']
                isNewsletterDisabled = !consulRecipients[consulId]['hasNewsletter']
                isFollowupDisabled = !consulRecipients[consulId]['hasFollowup']

            $('.js-consultation-participant').prop('disabled', isParticipantDisabled)
            $('.js-consultation-voter').prop('disabled', isVoterDisabled)
            $('.js-consultation-newsletter').prop('disabled', isNewsletterDisabled)
            $('.js-consultation-followup').prop('disabled', isFollowupDisabled)
            return
        )

# Binds appearance of new media selection element to clickng the button
bindEmailAddAttachment = () ->
    attachmentId = 0
    $('.js-email-add-attachment').
        click((ev) ->
            ev.preventDefault()
            button = $(ev.target)
            html = button.next()
                .clone()
                .removeClass('hidden')
            html.find('input[type=hidden]')
                .attr('name', 'attachments[]')
                .removeProp('disabled')
            html = html.prop('outerHTML')
                .replace(/TOKEN_TO_BE_REPLACED_BY_JS/g, attachmentId)
            button.before(html)
            attachmentId++;
            return
        )

bindTableRowsToggle = () ->
    rows = $('.js-table-rows-toggle tr')
    checkboxes = $('.js-table-rows-toggle input:checkbox')

    rows.click (e) ->
        if (e.target.type != 'checkbox')
            $(':checkbox', this).trigger('click')

    checkboxes.change (e) ->
        row = $(this).closest('tr')
        if $(this).is(':checked')
            row.addClass('info')
        else
            row.removeClass('info')

bindToggleAll = () ->
    $('.js-toggle-all').change () ->
        form = $(this).closest('form')

        if $(this).is(':checked')
            form.find('input:checkbox:not(:checked)').prop('checked', true).change()
        else
            form.find('input:checkbox:checked').prop('checked', false).change()

bindConsultationCustomPhaseNames = () ->
    $('.js-enable-custom-consultation-phase-names').change (ev) ->
        checkbox = $(ev.currentTarget)
        inputs = checkbox.parents('form').find('input[type=text]')
        if checkbox.prop('checked')
            inputs.prop('disabled', false);
        else
            inputs.prop('disabled', true);

initCKEditor = () ->
    $('.wysiwyg-standard').ckeditor({
        filebrowserBrowseUrl: baseUrl + '/admin/media/index/targetElId/CKEditor'
        customConfig: '/js/ckeditor.web_config.js'
    })
    $('.wysiwyg-email').ckeditor({
        customConfig: '/js/ckeditor.email_config.js'
        removeButtons: 'Underline,Anchor,Strike'
    })

initDatepicker = () ->
    $('.js-datepicker').datetimepicker({
        'format': 'YYYY-MM-DD',
        'pickTime': false
    })
    $('.js-datetimepicker').datetimepicker({
        'format': 'YYYY-MM-DD HH:mm:ss',
        'sideBySide': true
    })

    # An ugly hack added so that datetimepicker can be triggered if the field was enabled by js after page load
    $(document).on('change', '[data-toggle=disable]', () ->
        $this = $(this)
        $el = $($this.data('disable-target') + '.js-datetimepicker');
        $el.data('DateTimePicker', null);
        $el.datetimepicker({
            'format': 'YYYY-MM-DD HH:mm:ss',
            'sideBySide': true
        })
        $el = $($this.data('disable-target') + '.js-datepicker')
        $el.data('DateTimePicker', null);
        $el.datetimepicker({
            'format': 'YYYY-MM-DD',
            'pickTime': false
        })
        return
    );

initSelect2 = () ->
    $('.js-select2').select2()

initSortableFollowupSnippets = () ->
    $('.js-sortable-followup-snippets').disableSelection()
    $('.js-sortable-followup-snippets').sortable({
        'update': (ev, ui) ->
            i = 1
            $form = ui.item.closest('form')
            $form.find('tr input[type=hidden]').each () ->
                $(this).val(i)
                i++
            $form.find('button[type=submit]').prop('disabled', false)
            return
    });

initSortableVotingDirs = () ->
    $('.js-sortable-voting-directory').disableSelection()
    $('.js-sortable-voting-directory').sortable({
        'update': (ev, ui) ->
            i = 1
            $form = ui.item.closest('form')
            $form.find('div input[type=hidden]').each () ->
                $(this).val(i)
                i++
            $form.find('button[type=submit]').prop('disabled', false)
            return
    });

initI18n = () ->
    $.fn.confirmation.Constructor.prototype.options = {
        'confirm-message': i18n.translate('Are you sure?'),
        'confirm-yes': i18n.translate('Yes'),
        'confirm-no': i18n.translate('No')
    }

    return

