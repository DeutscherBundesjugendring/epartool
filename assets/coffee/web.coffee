$(document).ready () ->
    bindCharacterCounters()
    bindToggleGroupRegister()
    bindLoadMoreConsultations()
    bindAnimatedScrolling()
    bindToggleExtendedInput()
    bindSupportContribution()
    bindSaveAndFinishContributing()
    bindSaveAndContinueContributing()
    initFB(document, 'script', 'facebook-jssdk')

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
        $(this).addClass('disabled');
        $.get(
            baseUrl + '/index/ajax-consultation',
            {},
            (data) ->
                $('.js-load-more-consultations').remove()
                $('.js-top-link').removeClass('hidden')
                $('.js-consultations-container').append(data)
        )

bindAnimatedScrolling = () ->
    $('.js-scroll').click (e) ->
        e.preventDefault()
        $('html, body').animate({ scrollTop: $(this.hash).offset().top }, 500);

<<<<<<< HEAD
bindToggleExtendedInput = () ->
    $('.js-toggle-extended-input').click (event) ->
        event.preventDefault()
        $(this).next('textarea').toggle()
        $(this).nextAll('.js-character-counter').toggle()
        $(this).toggleClass 'expanded'
        if $(this).hasClass('expanded')
            $(this).html '<span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span> Shut back<span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span>'
        else
            $(this).html '<span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span> Click here to explain contribution<span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>'
        return

bindSupportContribution = () ->
    $('.click-support').click (event) ->
        event.preventDefault()
        tid = $(this).attr('rel')
        $.post('<?php echo $this->baseUrl(); ?>/input/support/kid/<?php echo $this->consultation->kid; ?>/format/json', 'tid': tid).done (data) ->
            if data.count
                $('#click-support-wrap-' + tid).html '<span><span class="glyphicon glyphicon-ok-sign icon-orange icon-2x" aria-hidden="true"></span>' + ' <span id="badge-' + tid + '">(' + data.count + ')</span>' + ' <span class="label label-default">supporters</span></span>'
            return
        return

bindSaveAndFinishContributing = () ->
    $('#finish').click ->
        $('#submitmode').val 'save_finish'
        return

bindSaveAndContinueContributing = () ->
    $('#plus').click ->
        $('#submitmode').val 'save_plus'
        return

# function has to be attached to window for google js SDK script to find it
window.googleAuthenticateCallback = (authResult) ->
    if authResult.status.method == 'PROMPT'
        if authResult['status']['signed_in']
            $.post(
                baseUrl + '/index/google-authenticate',
                {
                    token: authResult['code'],
                    webserviceLoginCsrf: $('#webserviceLoginCsrf').val()

                },
                (data) ->
                    if data == 'true'
                        location.reload()
                    else
                        $('body').prepend(data)
        )

# function has to be attached to window for facebook js SDK script to find it
window.facebookAuthenticateCallback = () ->
    FB.getLoginStatus((response) ->
        if response.status == 'connected'
            $.post(
                baseUrl + '/index/facebook-authenticate',
                {
                    token: response['authResponse']['accessToken'],
                    webserviceLoginCsrf: $('#webserviceLoginCsrf').val()
                },
                (data) ->
                    if data == 'true'
                        location.reload()
                    else
                        $('body').prepend(data)
            )
    )

initFB = (d, s, id) ->
    fjs = d.getElementsByTagName(s)[0];
    if d.getElementById(id)
        return

    appId = $('.fb-login-button').data('app-id');
    js = d.createElement(s)
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=" + appId + "&version=v2.0";
    fjs.parentNode.insertBefore(js, fjs)

