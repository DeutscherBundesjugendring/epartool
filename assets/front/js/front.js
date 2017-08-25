(function() {
  var bindAnimatedScrolling, bindCharacterCounters, bindHelpTextModal, bindLoadMoreConsultations, bindRemoveSupervote, bindSaveAndContinueContributing, bindSaveAndFinishContributing, bindSupportContribution, bindToggleExtendedInput, bindToggleGroupRegister, bindToggleVotingContributionExplanation, bindVotingRate, hideOverlay, indicateLoginInProgress, initFB, loginProcessEnd, showOverlay;

  $(document).ready(function() {
    bindCharacterCounters();
    bindToggleGroupRegister();
    bindLoadMoreConsultations();
    bindAnimatedScrolling();
    bindToggleExtendedInput();
    bindSupportContribution();
    bindSaveAndFinishContributing();
    bindSaveAndContinueContributing();
    bindHelpTextModal();
    bindRemoveSupervote();
    bindVotingRate();
    bindToggleVotingContributionExplanation();
    initFB(document, 'script', 'facebook-jssdk');
    $('.js-has-password-meter').pwstrength({
      'ui': {
        'verdicts': [
          jsTranslations['password_strength_weak'],
          jsTranslations['password_strength_normal'],
          jsTranslations['password_strength_medium'],
          jsTranslations['password_strength_strong'],
          jsTranslations['password_strength_very_strong']
        ]
      }
    });
    $('.js-share').on("click", function(e) {
      $(this).socialPopup(e);
    });
    return $('.js-contribution-create-form').pageLeaveConfirmation();
  });

  bindCharacterCounters = function() {
    var countees, updateCounter, updateCounterHandler;
    updateCounter = function(el) {
      var HTMLOutput, charsLeft, digits, pad;
      pad = function(number, length) {
        var str;
        str = '' + number;
        while (str.length < length) {
          str = '0' + str;
        }
        return str;
      };
      charsLeft = el.attr('maxlength') - el.val().length;
      charsLeft = pad(charsLeft, el.attr('maxlength').length);
      digits = charsLeft.split('');
      HTMLOutput = '';
      $.each(digits, function(index, value) {
        return HTMLOutput += '<span class="counter-digit">' + value + '</span>';
      });
      return $('#' + el.attr('id') + '_counter').html(HTMLOutput);
    };
    updateCounterHandler = function(event) {
      var el;
      el = $(event.target);
      return updateCounter(el);
    };
    countees = $('textarea.js-has-counter');
    return countees.each(function(index, el) {
      el = $(el);
      updateCounter(el);
      el.change(updateCounterHandler);
      return el.keyup(updateCounterHandler);
    });
  };

  bindToggleGroupRegister = function() {
    var container, elementName, groupTypeChecked, labelName;
    container = $("#group_specs-element");
    labelName = $("#name-label");
    elementName = $("#name-element");
    groupTypeChecked = $('input[name="group_type"]:checked').val();
    if (groupTypeChecked === "group") {
      labelName.hide();
      elementName.hide();
      container.slideDown();
      $('select#age_group > option:last-child').show();
    } else {
      labelName.show();
      elementName.show();
      container.hide();
      $('select#age_group > option:last-child').hide();
    }
    return $('input[name="group_type"]').change(function() {
      groupTypeChecked = $('input[name="group_type"]:checked').val();
      if (groupTypeChecked === "group") {
        labelName.hide();
        elementName.hide();
        container.slideDown();
        return $('select#age_group > option:last-child').show();
      } else {
        labelName.show();
        elementName.show();
        container.slideUp();
        return $('select#age_group > option:last-child').hide();
      }
    });
  };

  bindLoadMoreConsultations = function() {
    return $('.js-load-more-consultations').click(function(e) {
      e.preventDefault();
      $(this).addClass('disabled');
      return $.get(baseUrl + '/index/ajax-consultation', {}, function(data) {
        $('.js-load-more-consultations').remove();
        $('.js-top-link').removeClass('hidden');
        return $('.js-consultations-container').append(data);
      });
    });
  };

  bindAnimatedScrolling = function() {
    return $('.js-scroll').click(function(e) {
      e.preventDefault();
      return $('html, body').animate({
        scrollTop: $(this.hash).offset().top
      }, 500);
    });
  };

  bindToggleExtendedInput = function() {
    return $('.js-toggle-extended-input').click(function(event) {
      event.preventDefault();
      $(this).next('textarea').toggle();
      $(this).nextAll('.js-character-counter').toggle();
      $(this).toggleClass('expanded');
      if ($(this).hasClass('expanded')) {
        $(this).html('<span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span><span class="hidden-xs"> ' + jsTranslations['label_shut_back'] + ' </span><span class="glyphicon glyphicon-menu-up hidden-xs" aria-hidden="true"></span>');
      } else {
        $(this).html('<span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span><span class="hidden-xs"> ' + jsTranslations['label_explain_contribution'] + ' </span><span class="glyphicon glyphicon-menu-down hidden-xs" aria-hidden="true"></span>');
      }
    });
  };

  bindSupportContribution = function() {
    return $('.js-click-support').click(function(event) {
      var kid, tid;
      event.preventDefault();
      kid = $(this).data('kid');
      tid = $(this).attr('rel');
      $.post(baseUrl + '/input/support/kid/' + kid + '/format/json', {
        'tid': tid
      }).done(function(data) {
        if (data.count) {
          $('#click-support-wrap-' + tid).html('<span class="glyphicon glyphicon-ok-sign icon-offset icon-shift-down text-accent" aria-hidden="true"></span>' + ' <small id="badge-' + tid + '" class="badge badge-accent">' + data.count + '</small><small class="hidden-print"> ' + jsTranslations['label_supporters'] + '</small>');
        }
      });
    });
  };

  bindSaveAndFinishContributing = function() {
    return $('#finish').click(function() {
      $('#submitmode').val('save_finish');
    });
  };

  bindSaveAndContinueContributing = function() {
    return $('#plus').click(function() {
      $('#submitmode').val('save_plus');
    });
  };

  window.googleAuthenticateCallback = function(authResult) {
    if (authResult.status.method === 'PROMPT') {
      if (authResult['status']['signed_in']) {
        indicateLoginInProgress();
        return $.post(baseUrl + '/index/google-authenticate', {
          token: authResult['code'],
          webserviceLoginCsrf: $('#webserviceLoginCsrf').val()
        }, function(data) {
          if (data === 'true') {
            return location.reload();
          } else {
            loginProcessEnd();
            return $('body').prepend(data);
          }
        });
      }
    }
  };

  window.facebookAuthenticateCallback = function() {
    return FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        indicateLoginInProgress();
        return $.post(baseUrl + '/index/facebook-authenticate', {
          token: response['authResponse']['accessToken'],
          webserviceLoginCsrf: $('#webserviceLoginCsrf').val()
        }, function(data) {
          if (data === 'true') {
            return location.reload();
          } else {
            loginProcessEnd();
            return $('body').prepend(data);
          }
        });
      }
    });
  };

  window.googleRegisterCallback = function(authResult) {
    if (authResult.status.method === 'PROMPT') {
      if (authResult['status']['signed_in']) {
        indicateLoginInProgress();
        return $.post(baseUrl + '/index/google-register', {
          token: authResult['code'],
          webserviceLoginCsrf: $('#webserviceLoginCsrf').val()
        }, function(data) {
          var $emailEl, $emailField;
          loginProcessEnd();
          if (data !== 'false') {
            $emailField = $('#email');
            $emailField.prop('disabled', true).val(data);
            $emailEl = $('<input name="email" type="hidden" />');
            $emailEl.val(data);
            $emailField.closest('form').append($emailEl);
          }
        });
      }
    }
  };

  window.facebookRegisterCallback = function() {
    return FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        indicateLoginInProgress();
        return $.post(baseUrl + '/index/facebook-register', {
          token: response['authResponse']['accessToken'],
          webserviceLoginCsrf: $('#webserviceLoginCsrf').val()
        }, function(data) {
          var $emailEl, $emailField;
          loginProcessEnd();
          if (data !== 'false') {
            $emailField = $('#email');
            $emailField.prop('disabled', true).val(data);
            $emailEl = $('<input name="email" type="hidden" />');
            $emailEl.val(data);
            $emailField.closest('form').append($emailEl);
          }
        });
      }
    });
  };

  indicateLoginInProgress = function() {
    showOverlay();
    return $('body').prepend($('<div id="loginFlashMessage" class="alert alert-info alert-on-overlay"> <span class="icon-offset glyphicon glyphicon-transfer"></span>' + jsTranslations['message_logging_in'] + '</div>'));
  };

  loginProcessEnd = function() {
    $('#loginFlashMessage').remove();
    return hideOverlay();
  };

  showOverlay = function() {
    $('body').append($('<div id="overlay" class="overlay"></div>'));
    return $('body').addClass('has-overlay');
  };

  hideOverlay = function() {
    $('#overlay').remove();
    return $('body').removeClass('has-overlay');
  };

  initFB = function(d, s, id) {
    var appId, fjs, js;
    fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
      return;
    }
    appId = $('.fb-login-button').data('app-id');
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=" + appId + "&version=v2.0";
    return fjs.parentNode.insertBefore(js, fjs);
  };

  window.initGoogle = function() {
    var registerButton = gapi.signin.render('googleRegisterButton', {
      'callback': $('#googleRegisterButton').data('callback'),
      'clientid': $('#googleRegisterButton').data('clientid'),
      'cookiepolicy': 'single_host_origin',
      'scope': 'profile email'
    });

    var signInButton = gapi.signin.render('googleSignInButton', {
      'callback': $('#googleSignInButton').data('callback'),
      'clientid': $('#googleSignInButton').data('clientid'),
      'cookiepolicy': 'single_host_origin',
      'scope': 'profile email'
    });

    return registerButton && signInButton;
  };

  bindHelpTextModal = function() {
    return $('.js-toggle-help').click(function() {
      $.get(this.href, {}, function(data) {
        var modalHtml;
        modalHtml = $(data);
        $('body').append(modalHtml);
        return modalHtml.modal('show');
      });
      return false;
    });
  };

  bindRemoveSupervote = function() {
    var votesUsed;
    votesUsed = $('.js-supervotes-used').html();
    return $('.js-voting-remove-supervote').on('click', function(e) {
      var kid, tid;
      tid = $(this).data('tid');
      kid = $(this).data('kid');
      $.ajax({
        url: baseUrl + '/voting/removethesis/kid/' + kid + '/tid/' + tid,
        type: 'POST',
        data: 'format=html',
        cache: false,
        async: 'true',
        error: function() {
          $(e.target).html = jsTranslations['message_general_error'];
        },
        beforeSend: function() {
          $(e.target).html = jsTranslations['label_loading'];
        },
        success: function(response) {
          votesUsed -= 1;
          $(e.target).html = jsTranslations['label_loading'];
          $('#thes-' + tid).remove();
          $('.js-supervotes-used').html(votesUsed);
        }
      });
    });
  };

  bindVotingRate = function() {
    return $('.js-voting').on('click', function(e) {
      var container, kid, rating, target, tid, url;
      e.preventDefault();
      if (e.target.tagName === 'SPAN') {
        target = e.target.parentNode;
      } else {
        target = e.target;
      }
      tid = $(target).data('tid');
      kid = $(target).data('kid');
      rating = $(target).data('rating');
      container = $('#thesis-' + tid);
      if (rating === 'y') {
        url = baseUrl + '/voting/previewfeedbackpi/kid/' + kid + '/id/' + tid + '/points/' + rating;
      } else {
        url = baseUrl + '/voting/previewfeedback/kid/' + kid + '/id/' + tid + '/points/' + rating;
      }
      if (target.tagName === 'A') {
        $.ajax({
          url: url,
          type: 'POST',
          data: 'format=html',
          cache: false,
          async: 'true',
          error: function() {
            $(container).html = jsTranslations['message_general_error'];
          },
          beforeSend: function() {
            $(container).html = jsTranslations['label_loading'];
          },
          success: function(response) {
            $(container).html(response);
          }
        });
      }
    });
  };

  bindToggleVotingContributionExplanation = function() {
    return $('.js-toggle-voting-contribution-explanation').on('click', function(e) {
      $('#voting-contribution-explanation').toggle();
      return $('.glyphicon', this).toggleClass('hide');
    });
  };

  window.addModalOpenToBody = function() {
    return $('body').addClass('modal-open');
  };

  window.removeModalOpenFromBody = function() {
    return $('body').removeClass('modal-open');
  };

}).call(this);
