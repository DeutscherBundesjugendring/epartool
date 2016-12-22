$(document).ready () ->
    bindEmailTemplateSelect()
    bindEmailConsultationSelect()
    bindEmailAddAttachment()
    bindTableRowsToggle()
    bindToggleAll()
    bindConsultationCustomPhaseNames()
    bindContributionVideoSelect()
    themeSettings()
    contributorAgesSettings()
    groupsSizesSettings()

    initSortableFollowupSnippets()
    initSortableVotingDirs()
    initSortablePartners()
    initMediaIndexFileLazyLoad()
    changeContributionStatus()

    $('[data-toggle="tooltip"]').tooltip();

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
        if (e.target.type != 'checkbox' && e.target.classList[0] != 'label')
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

initSortableFollowupSnippets = () ->
    return initSortableGeneral('.js-sortable-followup-snippets')

initSortableVotingDirs = () ->
    return initSortableGeneral('.js-sortable-voting-directory')

initSortablePartners = () ->
    return initSortableGeneral('.js-sortable-partners')

initSortableGeneral = (sortableClass) ->
    $(sortableClass).disableSelection()
    $(sortableClass).sortable({
        'update': (ev, ui) ->
            i = 1
            $form = ui.item.closest('form')
            $form.find('div input[type=hidden]').each () ->
                $(this).val(i)
                i++
            $form.find('button[type=submit]').prop('disabled', false)
            return
    });

initMediaIndexFileLazyLoad = () ->
    $container = $('#media-thumbnail-container')
    if ($container).length > 0
        batchSize = $container.data('batchSize');
        offset = batchSize
        wait = false
        $(window).scroll () ->
            viewBottom = $(window).scrollTop() + $(window).height();
            containerBottom = $container.scrollTop() + $container.height()
            if viewBottom + 500 > containerBottom && wait == false
                wait = true
                url = '/admin/media/lazy-load-images/offset/' + offset;
                if $container.data('kid')
                    url = url + '/kid/' + $container.data('kid')
                if $container.data('folder')
                    url = url + '/folder/' + $container.data('folder')
                if $container.data('targetElId')
                    url = url + '/targetElId/' + $container.data('targetElId')
                if $container.data('lockDir')
                    url = url + '/lockDir/' + $container.data('lockDir')
                if $container.data('ckeditorFuncNum')
                    url = url + '/CKEditorFuncNum/' + $container.data('ckeditorFuncNum')
                $.get(url, [], (data) ->
                    $container.append(data)
                    if data
                        wait = false
                        offset = offset + batchSize
                )

bindContributionVideoSelect = () ->
    select = $('.js-video-service select');
    select.change () ->
        addon = $(this).closest('.js-video-service').find('.js-video-service-url');
        addon.html($(this).data('url')[$(this).children(':selected').attr('value')]);
    select.trigger('change');

themeSettings = () ->
    $('#themes').data('presetTheme', $("input[name='theme_id']:checked").val())
    colorAccent1 = $('#color_accent_1')
    colorPrimary = $('#color_primary')
    colorAccent2 = $('#color_accent_2')

    colorAccent1.data('oldValue', colorAccent1.val())
    colorPrimary.data('oldValue', colorPrimary.val())
    colorAccent2.data('oldValue', colorAccent2.val())

    $('.js-theme-preset').click () ->
        if !$('#themes').data('presetTheme')
            if !confirm(i18n.translate('Custom colors are set. Do you want to replace them with predefined theme?'))
                return false;
        colors = $(this).data('colors');
        colorAccent1 = $('#color_accent_1')
        colorPrimary = $('#color_primary')
        colorAccent2 = $('#color_accent_2')

        colorAccent1.closest('.colorpicker-component').colorpicker('setValue', colors['color_accent_1']);
        colorPrimary.closest('.colorpicker-component').colorpicker('setValue', colors['color_primary']);
        colorAccent2.closest('.colorpicker-component').colorpicker('setValue', colors['color_accent_2']);
        $('#themes').data('presetTheme', true)
        colorAccent1.data('oldValue', colorAccent1.val())
        colorPrimary.data('oldValue', colorPrimary.val())
        colorAccent2.data('oldValue', colorAccent2.val())
        return true

    $('.colorpicker-component').colorpicker({format: "hex"}).on('change showPicker', () ->
        if $('#themes').data('presetTheme')
            if !confirm(i18n.translate('Do you want to override predefined theme with custom colors set?'))
                $(this).colorpicker('setValue', $(this).find('input').data('oldValue'))
                $(this).colorpicker('hide')
                return false;
        $(this).find('input').data('oldValue', $(this).colorpicker('getValue'))
        $('#themes').data('presetTheme', false)
        $("input[name='theme_id']:checked").attr('checked', false)
        return true;
    );

contributorAgesSettings = () ->
    $('#js-contributor-ages-intervals').on('click', '.js-contributor-ages-delete-row', () ->
        if !confirm(i18n.translate('Dou you want to delete this interval?'))
            return false
        $(this).closest('.js-row').remove()
        return false
    );

    $('#js-contributor-ages-add-row').click () ->
        timestamp = new Date().getUTCMilliseconds();
        el = $('#js-contributor-ages-new-row').clone().removeClass('hidden').attr('id','')
        el.find('#js-contributor-ages-new-row-from').attr('name', 'contributorAges[_' + timestamp + '][from]').attr('id', '')
        el.find('#js-contributor-ages-new-row-to').attr('name', 'contributorAges[_' + timestamp + '][to]').attr('id', '')
        $('#js-contributor-ages-intervals tr:last').before(el)
        return false

groupsSizesSettings = () ->
    $('#js-groups-sizes-intervals').on('click', '.js-groups-sizes-delete-row', () ->
        if !confirm(i18n.translate('Dou you want to delete this interval?'))
            return false
        $(this).closest('.js-row').remove()
        return false
    );

    $('#js-groups-sizes-add-row').click () ->
        timestamp = new Date().getUTCMilliseconds();
        el = $('#js-groups-sizes-new-row').clone().removeClass('hidden').attr('id','')
        el.find('#js-groups-sizes-new-row-from').attr('name', 'groupSizes[_' + timestamp + '][from]').attr('id', '')
        el.find('#js-groups-sizes-new-row-to').attr('name', 'groupSizes[_' + timestamp + '][to]').attr('id', '')
        $('#js-groups-sizes-intervals tr:last').before(el)
        return false

changeContributionStatus = () ->
    $('.js-contribution-change-status').on 'click', (e) ->
        e.preventDefault()
        tokenEl = $('#contribution-table')
        if (tokenEl.data('token') == '')
            return

        kid = $(this).data('kid')
        tid = $(this).data('tid')
        property = $(this).data('property')

        thisButton = $(this)
        buttonLabel = $(this).find('.label')
        buttonIcon = $(this).find('.glyphicon').clone();

        $.ajax
            url: baseUrl + '/admin/input/change-status/kid/' + kid + '/tid/' + tid + '/token/' + tokenEl.data('token') + '/property/' + property
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            cache: false
            async: true
            error: ->
                buttonLabel.attr('class', 'label label-default')
                buttonLabel.html(' ' + i18n.translate('Unknown'))
                buttonIcon.attr('class', 'glyphicon glyphicon-question-sign')
                buttonLabel.prepend(buttonIcon)
                thisButton.attr('style', '')
                return
            beforeSend: ->
                tokenEl.data('token', '')
                $('.js-contribution-change-status').attr('style', 'opacity:0.7')
                buttonLabel.attr('class', 'label label-info')
                buttonLabel.html(' ' + i18n.translate('Loading…'))
                buttonIcon.attr('class', 'glyphicon glyphicon-refresh')
                buttonLabel.prepend(buttonIcon)
                return
            success: (response) ->
                buttonIcon.attr('class', 'glyphicon glyphicon-' + response.iconClass)
                buttonLabel.attr('class','label label-' + response.labelClass)
                buttonLabel.html(' ' + response.label)
                buttonLabel.prepend(buttonIcon);
                tokenEl.data('token', response.token)
                $('.js-contribution-change-status').attr('style', '')
                return
        return


class mediaSelectPopup
    ###*
    # Inserts the image data back into the element that triggered the display of this popup
    # @param    {string}  filename              The media filename
    # @param    {string}  targetElId            The id of the element that triggered this popup
    # @param    {string}  imgPathPrefixInput    The path to be used in media element in input field
    # @param    {string}  imgPathImage          The path to be used in media element in image src attribute.
                                                It points to the cache folder.
    # @param    {numeric} CKEditorFuncNum       The CKEditor callback identifier
    ###
    this.insertValue = (filename, targetElId, imgPathPrefixInput, imgPathImage, CKEditorFuncNum) ->
        if targetElId == 'CKEditor'
            window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum,  imgPathPrefixInput + filename);
        else
            inputEl = $(window.opener.document.getElementById(targetElId))
            inputEl.val(imgPathPrefixInput + filename)
            inputEl.parent().find('img').attr('src', imgPathImage)
        window.close()

document.mediaSelectPopup = mediaSelectPopup
