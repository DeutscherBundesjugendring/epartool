$(document).ready () ->
    bindCharacterCounters()
    $('.js-has-password-meter').pwstrength({'ui': {'bootstrap2': true, 'showVerdicts': false}})


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
