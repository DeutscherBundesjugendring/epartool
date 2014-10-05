$(document).ready () ->
    bindEmailTemplateSelect()
    bindEmailConsultationSelect()
    bindEmailAddAttachment()
    bindTableRowToggle()
    bindToggleAll()
    bindConsultationCustomPhaseNames()

    initDataViewTable()
    initCKEditor()
    # initDatepicker()
    initSortableFollowupSnippets()
    initSelect2()
    initConfirmMsg()
    initFilter()


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

bindTableRowToggle = () ->
    $('.js-table-row-toggle').change(() ->
        row = $(this).closest('tr')

        if $(this).is(':checked')
            row.addClass('info')
        else
            row.removeClass('info')
    )

bindToggleAll = () ->
    $('.js-toggle-all').change(() ->
        form = $(this).closest('form')

        if $(this).is(':checked')
            form.find('input:checkbox:not(:checked)').prop('checked', true).change()
        else
            form.find('input:checkbox:checked').prop('checked', false).change()
    )

bindConsultationCustomPhaseNames = () ->
    $('.js-enable-custom-consultation-phase-names').change (ev) ->
        checkbox = $(ev.currentTarget)
        inputs = checkbox.parents('form').find('input[type=text]')
        if checkbox.prop('checked')
            inputs.prop('disabled', false);
        else
            inputs.prop('disabled', true);

initDataViewTable = () ->
    table = $('[data-view="table"]')
    th = table.find('[data-toggle="sort"]')

    th.prepend('<span class="data-view-table-icon"></span>')
    th.click () ->
        sort(this)
    # Keyboard accessibility for enter and spacebar
    th.keyup (e) ->
        if (e.keyCode == 13 || e.keyCode == 32)
            sort(this)

    sort = (colHeaderEl) ->
        rows = table.find('tbody tr').toArray()
        rows = rows.sort(comparer($(colHeaderEl).index()))

        # Indicate active column and reverse order if the column is already active
        if $(colHeaderEl).hasClass('sorting-active')
            colHeaderEl.asc = !colHeaderEl.asc
        else
            th.removeClass('sorting-active')
            $(colHeaderEl).addClass('sorting-active')

        # Descending order
        if colHeaderEl.asc
            rows = rows.reverse()
            $(colHeaderEl).addClass('sorting-desc')
        else
            $(colHeaderEl).removeClass('sorting-desc')

        # Grouped sorting with navigation
        table.find('thead:gt(0)').remove()
        if table.data('navigation')
            navigation = $(table.data('navigation'))
            navigation.children().remove()
        if $(colHeaderEl).data('group') && $(colHeaderEl).data('group') == 'first-letter'
            isGrouped = true
            colCount = rows[0].childElementCount

        for row in rows
            if isGrouped == true
                letter = getCellValue(row, $(colHeaderEl).index())[0]
                if letter
                    letter = letter.toUpperCase()
                else
                    letter = ''

                if newLetter != letter
                    newLetter = letter
                    if navigation
                        navigation.append('<li><a href="#letter-' + letter + '">' + letter + '</a></li>')
                    table.append($('<thead><tr class="active"><th colspan="' + colCount + '"><h2 class="h3" id="letter-' + newLetter + '">' + newLetter + '</h2></th></tr></thead>'))
                    table.append($('<tbody></tbody>'))

            table.find('tbody:last').append(row)

    comparer = (index) ->
        (a, b) ->
            valA = getCellValue(a, index)
            valB = getCellValue(b, index)
            if $.isNumeric(valA) && $.isNumeric(valB)
                valA - valB
            else
                valA.localeCompare(valB)

    getCellValue = (row, index) ->
        $(row).children('td').eq(index).text()

    defaultSort = table.find('[data-order]')
    if defaultSort.data('order') == 'default'
        sort(defaultSort[0])
    else if defaultSort.data('order') == 'default desc'
        defaultSort[0].asc = !defaultSort[0].asc
        sort(defaultSort[0])

    return

initCKEditor = () ->
    $('.wysiwyg-standard').ckeditor({
        filebrowserBrowseUrl: baseUrl + '/admin/media/index/targetElId/CKEditor'
    })
    $('.wysiwyg-email').ckeditor({
        removePlugins: 'horizontalrule,list,justify,indent,indentlist,indentblock,image2,flash,iframe,div',
        removeButtons: 'Underline,Anchor,Strike'
    })

initDatepicker = () ->
    $('.js-datetimepicker').datepicker()

initSelect2 = () ->
    $('.js-select2').select2()

initConfirmMsg = () ->
    $('[data-toggle=confirm]').click (ev) ->
        el = ev.currentTarget
        msg = $(el).data('confirm-message')
        if (!msg)
            msg = 'Are you sure?'

        return confirm(msg)

initFilter = () ->
    $('[data-toggle=filter]').change (ev) ->
        formEl = $(ev.currentTarget).closest('form')
        $(formEl.data('target')).show()

        formEl.find(':input').each (index, filterEl) ->
            filterEl = $(filterEl)
            if filterEl.val() != '' && filterEl.val() != null
                $(formEl.data('target')).each (dataIndex, dataEl) ->
                    dataElAttribVal = $(dataEl).data(filterEl.data('target-attrib'))
                    filterVal = filterEl.val()
                    if $.type(dataElAttribVal) == 'string' && dataElAttribVal != filterVal
                        $(dataEl).hide()
                    else if dataElAttribVal instanceof Array
                        if $.type(filterVal) == 'string' && dataElAttribVal.indexof(filterVal) == -1
                            $(dataEl).hide()
                        else if $.type(filterVal) == 'array'
                            $.each(filterVal, (index, el) ->
                                if dataElAttribVal.indexOf(el) == -1
                                    $(dataEl).hide()
                            )


    $('[data-toggle=reset-filter]').click (ev) ->
        formEl = $(ev.currentTarget).closest('form')
        formEl.find(':input').val('')
        $(formEl.data('target')).show()

initSortableFollowupSnippets = () ->
    $('.js-sortable').disableSelection()
    $('.js-sortable').sortable({
        'update': (ev, ui) ->
            i = 1
            ui.item.closest('form').find('tr input[type=hidden]').each () ->
                $(this).val(i)
                i++
            $('.js-sortable').closest('form').find('button[type=submit]').prop('disabled', false)
            return
    });
