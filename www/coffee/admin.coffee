$(document).ready () ->
    bindSelectTemplate()
    bindConsultationSelect()
    initDataViewTable();

# Binds ajax loading template data to a template selctor box
bindSelectTemplate = () ->
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
bindConsultationSelect = () ->
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
                        navigation.append('<a href="#letter-shortcut-' + letter + '">' + letter + '</a>')
                    table.append($('<thead><tr><th colspan="' + colCount + '"><a name="letter-shortcut-' + newLetter + '">' + newLetter + '</a></th></tr></thead>'))
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

    defaultSort = table.find('[data-order="default"]')[0]
    if defaultSort
        sort(defaultSort)
    return
