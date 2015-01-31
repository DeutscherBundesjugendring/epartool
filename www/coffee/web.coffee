$(document).ready () ->
    bindCharacterCounters()
    bindToggleGroupRegister()
    bindLoadMoreConsultations()

    $('.js-has-password-meter').pwstrength({'ui': {
        'bootstrap2': true,
        'verdicts': [i18n['Weak'], i18n['Normal'], i18n['Medium'], i18n['Strong'], i18n['Very Strong']]
    }})


bindCharacterCounters = () ->
    updateCounter = (el) ->
        pad = (number, length) ->
            str = '' + number
            str = '0' + str while str.length < length
            str

        charsLeft = el.attr('maxlength') - el.val().length
        charsLeft = pad(charsLeft, el.attr('maxlength').length)
        digits = charsLeft.split('')
        HTMLOutput = ''
        $.each(digits, (index, value) ->
            HTMLOutput += '<span class="counter-digit">' + value + '</span>';
        )
        $('#' + el.attr('id') + '_counter').html(HTMLOutput)

    updateCounterHandler = (event) ->
        el = $(event.target)
        updateCounter(el)

    countees = $('textarea.js-has-counter');
    countees.each (index, el) ->
        el = $(el)
        updateCounter(el)
        el.change(updateCounterHandler)
        el.keyup(updateCounterHandler)

bindToggleGroupRegister = () ->
    container = $("#group_specs-element")
    labelName = $("#name-label")
    elementName = $("#name-element")
    groupTypeChecked = $('input[name="group_type"]:checked').val()

    if groupTypeChecked != "group"
        labelName.show()
        elementName.show()
        container.hide()
        $('select#age_group option').filter("[value='4']").remove()

    $('input[name="group_type"]').change(() ->
        groupTypeChecked = $('input[name="group_type"]:checked').val()
        if groupTypeChecked == "group"
            labelName.hide()
            elementName.hide()
            container.slideDown()
            $('select#age_group').append($('<option></option>').val('4').html('Alle Altersgruppen'))
        else
            labelName.show()
            elementName.show()
            container.slideUp()
            $('select#age_group option').filter("[value='4']").remove()
    )

bindLoadMoreConsultations = () ->
    $('.js-load-more-consultations').click (e) ->
        e.preventDefault()
        $.get(
            baseUrl + '/index/ajax-consultation',
            {},
            (data) ->
                $('.js-load-more-consultations').remove()
                $('.js-consultations-container').append(data)
        )
