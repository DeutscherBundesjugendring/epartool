$(document).ready () ->
    bindCharacterCounters()
    bindToggleGroupRegister()
    bindLoadMoreConsultations()
    bindAnimatedScrolling()
    bindToggleExtendedInput()
    bindSupportContribution()
    bindSaveAndFinishContributing()
    bindSaveAndContinueContributing()
    bindHelpTextModal()
    bindRemoveSupervote()
    bindVotingRate()
    bindToggleVotingContributionExplanation()
    initFB(document, 'script', 'facebook-jssdk')

    $('.js-has-password-meter').pwstrength({'ui': {
        'verdicts': [i18n['Weak'], i18n['Normal'], i18n['Medium'], i18n['Strong'], i18n['Very Strong']]
    }})

    $('.js-contribution-create-form').pageLeaveConfirmation();


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

    if groupTypeChecked == "group"
        labelName.hide()
        elementName.hide()
        container.slideDown()
        $('select#age_group > option:last-child').show()
    else
        labelName.show()
        elementName.show()
        container.hide()
        $('select#age_group > option:last-child').hide();

    $('input[name="group_type"]').change(() ->
        groupTypeChecked = $('input[name="group_type"]:checked').val()
        if groupTypeChecked == "group"
            labelName.hide()
            elementName.hide()
            container.slideDown()
            $('select#age_group > option:last-child').show();
        else
            labelName.show()
            elementName.show()
            container.slideUp()
            $('select#age_group > option:last-child').hide();
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

bindToggleExtendedInput = () ->
    $('.js-toggle-extended-input').click (event) ->
        event.preventDefault()
        $(this).next('textarea').toggle()
        $(this).nextAll('.js-character-counter').toggle()
        $(this).toggleClass 'expanded'
        if $(this).hasClass('expanded')
            $(this).html '<span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span><span class="hidden-xs"> ' + i18n['Shut back'] + ' </span><span class="glyphicon glyphicon-menu-up hidden-xs" aria-hidden="true"></span>'
        else
            $(this).html '<span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span><span class="hidden-xs"> ' + i18n['Click here to explain contribution'] + ' </span><span class="glyphicon glyphicon-menu-down hidden-xs" aria-hidden="true"></span>'
        return

bindSupportContribution = () ->
    $('.js-click-support').click (event) ->
        event.preventDefault()
        kid = $(this).data('kid')
        tid = $(this).attr('rel')
        $.post(baseUrl + '/input/support/kid/' + kid + '/format/json', 'tid': tid).done (data) ->
            if data.count
                $('#click-support-wrap-' + tid).html '<span class="glyphicon glyphicon-ok-sign icon-offset icon-shift-down text-accent" aria-hidden="true"></span>' + ' <small id="badge-' + tid + '" class="badge badge-accent">' + data.count + '</small><small class="hidden-print"> ' + i18n['supporters'] + '</small>'
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
            indicateLoginInProgress()
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
                        loginProcessEnd()
                        $('body').prepend(data)
        )

# function has to be attached to window for facebook js SDK script to find it
window.facebookAuthenticateCallback = () ->
    FB.getLoginStatus((response) ->
        if response.status == 'connected'
            indicateLoginInProgress()
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
                        loginProcessEnd()
                        $('body').prepend(data)
            )
    )

# function has to be attached to window for google js SDK script to find it
window.googleRegisterCallback = (authResult) ->
    if authResult.status.method == 'PROMPT'
        if authResult['status']['signed_in']
            indicateLoginInProgress()
            $.post(
                baseUrl + '/index/google-register',
                {
                    token: authResult['code'],
                    webserviceLoginCsrf: $('#webserviceLoginCsrf').val()
                },
                (data) ->
                    loginProcessEnd()
                    if data != 'false'
                        $emailField = $('#email')
                        $emailField.prop('disabled', true).val(data);
                        $emailEl = $('<input name="email" type="hidden" />')
                        $emailEl.val(data)
                        $emailField.closest('form').append($emailEl)
                        return
        )

# function has to be attached to window for facebook js SDK script to find it
window.facebookRegisterCallback = () ->
    FB.getLoginStatus((response) ->
        if response.status == 'connected'
            indicateLoginInProgress()
            $.post(
                baseUrl + '/index/facebook-register',
                {
                    token: response['authResponse']['accessToken'],
                    webserviceLoginCsrf: $('#webserviceLoginCsrf').val()
                },
                (data) ->
                    loginProcessEnd()
                    if data != 'false'
                        $emailField = $('#email')
                        $emailField.prop('disabled', true).val(data);
                        $emailEl = $('<input name="email" type="hidden" />')
                        $emailEl.val(data)
                        $emailField.closest('form').append($emailEl)
                        return
            )
    )

indicateLoginInProgress = () ->
    showOverlay()
    $('body').prepend($('<div id="loginFlashMessage" class="alert alert-info alert-on-overlay">
        <span class="icon-offset glyphicon glyphicon-transfer"></span>' +
            i18n['You are being logged in. Please wait…'] + '</div>'))

loginProcessEnd = () ->
    $('#loginFlashMessage').remove();
    hideOverlay()

showOverlay = () ->
    $('body').append($('<div id="overlay" class="overlay"></div>'))
    $('body').addClass('has-overlay')

hideOverlay = () ->
    $('#overlay').remove()
    $('body').removeClass('has-overlay')

initFB = (d, s, id) ->
    fjs = d.getElementsByTagName(s)[0];
    if d.getElementById(id)
        return

    appId = $('.fb-login-button').data('app-id');
    js = d.createElement(s)
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=" + appId + "&version=v2.0";
    fjs.parentNode.insertBefore(js, fjs)

window.initGoogle = () ->
    gapi.signin.render('googleSignInButton', {
      'callback': $('#googleSignInButton').data('callback'),
      'clientid': $('#googleSignInButton').data('clientid'),
      'cookiepolicy': 'single_host_origin',
      'scope': 'profile email'
    });

bindHelpTextModal = () ->
    $('.js-toggle-help').click () ->
        $.get(this.href, {}, (data) ->
            modalHtml = $(data)
            $('body').append(modalHtml)
            modalHtml.modal('show')
        )

        return false

bindRemoveSupervote = () ->
    votesUsed = $('.js-supervotes-used').html()

    $('.js-voting-remove-supervote').on 'click', (e) ->
        tid = $(this).data('tid')
        kid = $(this).data('kid')

        $.ajax
            url: baseUrl + '/voting/removethesis/kid/' + kid + '/tid/' + tid
            type: 'POST'
            data: 'format=html'
            cache: false
            async: 'true'
            error: ->
                $(e.target).html = i18n['Something went wrong']
                return
            beforeSend: ->
                $(e.target).html = i18n['Loading…']
                return
            success: (response) ->
                votesUsed -= 1
                $(e.target).html = i18n['Loading…']
                $('#thes-' + tid).remove()
                $('.js-supervotes-used').html votesUsed
                return
        return

bindVotingRate = () ->
    $('.js-voting').on 'click', (e) ->
        e.preventDefault()

        if e.target.tagName == 'SPAN' # deal with superbutton icon
            target = e.target.parentNode
        else
            target = e.target

        tid = $(target).data('tid')
        kid = $(target).data('kid')
        rating = $(target).data('rating')
        container = $('#thesis-' + tid)

        if rating == 'y'
            url = baseUrl + '/voting/previewfeedbackpi/kid/' + kid + '/id/' + tid + '/points/' + rating
        else
            url = baseUrl + '/voting/previewfeedback/kid/' + kid + '/id/' + tid + '/points/' + rating

        if target.tagName == 'A' # only send request when actual link is clicked
            $.ajax
                url: url
                type: 'POST'
                data: 'format=html'
                cache: false
                async: 'true'
                error: ->
                    $(container).html = i18n['Something went wrong']
                    return
                beforeSend: ->
                    $(container).html = i18n['Loading…']
                    return
                success: (response) ->
                    $(container).html response
                    return
            return

bindToggleVotingContributionExplanation = () ->
    $('.js-toggle-voting-contribution-explanation').on 'click', (e) ->
        $('#voting-contribution-explanation').toggle()
        $('.glyphicon', this).toggleClass('hide')

window.addModalOpenToBody = () ->
    $('body').addClass('modal-open')

window.removeModalOpenFromBody = () ->
    $('body').removeClass('modal-open')
