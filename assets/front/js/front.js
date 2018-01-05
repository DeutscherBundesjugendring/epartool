(function() {
  var bindAddContribution;
  var bindAnimatedScrolling;
  var bindCharacterCounters;
  var bindContributionForm;
  var bindContributionMaps;
  var bindHelpTextModal;
  var bindLoadMoreConsultations;
  var bindRemoveSupervote;
  var bindSaveAndContinueContributing;
  var bindSaveAndFinishContributing;
  var bindSupportContribution;
  var bindToggleExtendedInput;
  var bindToggleGroupRegister;
  var bindToggleVotingContributionExplanation;
  var bindVotingRate;
  var hideOverlay;
  var indicateLoginInProgress;
  var initMap;
  var initFB;
  var loginProcessEnd;
  var onClickButtonMyLocation;
  var showOverlay;
  var toggleMap;

  var maps = [];

  $(document).ready(function() {
    bindCharacterCounters();
    bindContributionForm();
    bindContributionMaps();
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
    bindAddContribution();
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
    return $('#js-contribution-create-form').pageLeaveConfirmation();
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
    return $(document).on('click', '.js-click-support', function(event) {
      var kid, tid, isOnMapPopup;
      var $this = $(this);
      event.preventDefault();
      kid = $this.data('kid');
      tid = $this.attr('rel');
      isOnMapPopup = $this.hasClass('js-map-click-support');
      $.post(baseUrl + '/input/support/kid/' + kid + '/format/json', {
        'tid': tid
      }).done(function(data) {
        if (data.count) {
          $('#click-support-wrap-' + tid).html('<span class="glyphicon glyphicon-ok-sign icon-offset icon-shift-down text-accent" aria-hidden="true"></span>' + ' <small id="badge-' + tid + '" class="badge badge-accent">' + data.count + '</small><small class="hidden-print"> ' + jsTranslations['label_supporters'] + '</small>');
          if (isOnMapPopup && markersData && markersData['marker' + tid]) {
            markersData['marker' + tid].spprts = data.count;
            markersData['marker' + tid].supportEnabled = false;
            markers['marker' + tid]._popup.setContent(buildPopup(markersData['marker' + tid]));
          }
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
    if (!appId || appId === '') {
      return;
    }
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
        error: function(response) {
          if (response.responseText) {
            $('#thes-' + tid).html('<td colspan="2">' + response.responseText + '</td>');
          }
          $(e.target).html(jsTranslations['message_general_error']);
        },
        beforeSend: function() {
          $(e.target).html(jsTranslations['label_loading']);
        },
        success: function(response) {
          votesUsed -= 1;
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

  bindAddContribution = function() {
    return $('.js-add-contribution').on('click', function() {
      var formFieldsets = $('#js-contribution-create-form').find('fieldset');

      var newFieldset = formFieldsets.first().clone();
      var newIndex = formFieldsets.length;
      var prefixId = 'inputs-0-';
      var newPrefixId = 'inputs-' + newIndex + '-';
      var newPrefixName = 'inputs[' + newIndex + ']';
      var buttonsDiv = $(this).closest('.js-contribution-add-buttons');

      newFieldset.find('#' + prefixId + 'thes_counter').attr('id', newPrefixId + 'thes_counter');
      newFieldset.find('#' + prefixId + 'thes')
        .attr('id', newPrefixId + 'thes')
        .attr('name', newPrefixName + '[thes]')
        .val('');
      newFieldset.find('label[for="' + prefixId + 'video_service"]').attr('for', newPrefixId + 'video_service');
      newFieldset.find('#' + prefixId + 'video_service')
        .attr('id', newPrefixId + 'video_service')
        .attr('name', newPrefixName + '[video_service]');
      newFieldset.find('label[for="' + prefixId + 'video_id"]').attr('for', newPrefixId + 'video_id');
      newFieldset.find('#' + prefixId + 'video_id')
        .attr('id', newPrefixId + 'video_id')
        .attr('name', newPrefixName + '[video_id]')
        .val('');
      newFieldset.find('#' + prefixId + 'expl')
        .attr('id', newPrefixId + 'expl')
        .attr('name', newPrefixName + '[expl]')
        .val('');
      newFieldset.find('#' + prefixId + 'expl_counter').attr('id', newPrefixId + 'expl_counter');
      newFieldset.find('#' + prefixId + 'latitude')
        .attr('id', newPrefixId + 'latitude')
        .attr('name', newPrefixName + '[latitude]')
        .val('');
      newFieldset.find('#' + prefixId + 'longitude')
        .attr('id', newPrefixId + 'longitude')
        .attr('name', newPrefixName + '[longitude]')
        .val('');
      newFieldset.find('#' + prefixId + 'longitude')
        .attr('id', newPrefixId + 'longitude')
        .attr('name', newPrefixName + '[longitude]')
        .val('');

      newFieldset.find('#js-contribution-add-location-0')
        .attr('id', 'js-contribution-add-location-' + newIndex)
        .attr('checked', false)
        .attr('data-index', newIndex);
      newFieldset.find('label[for="js-contribution-add-location-0"]')
        .attr('for', 'js-contribution-add-location-' + newIndex)
        .attr('data-index', newIndex);
      newFieldset.find('#js-contribution-map-0')
        .attr('id', 'js-contribution-map-' + newIndex)
        .attr('class', 'js-contribution-map-collapsed')
        .attr('data-index', newIndex)
        .attr('style', 'display: none;');
      newFieldset.find('#js-contribution-map-canvas-0')
        .attr('id', 'js-contribution-map-canvas-' + newIndex)
        .html('')
      newFieldset.find('#js-contribution-map-button-my-location-0')
        .attr('id', 'js-contribution-map-button-my-location-' + newIndex)
        .attr('data-index', newIndex);

      $('<hr />').insertBefore(buttonsDiv);
      newFieldset.insertBefore(buttonsDiv);

      $('.js-toggle-extended-input').unbind('click');
      $('textarea.js-has-counter').unbind('change').unbind('keyup');

      $('#js-contribution-add-location-' + newIndex).on('click', function() {
        var index = $(this).data('index');
        var mapEl = $('#js-contribution-map-' + index);
        toggleMap(maps, mapEl, index);
      });

      $('#js-contribution-map-button-my-location-' + newIndex).on('click', function(e) {
        e.preventDefault();
        onClickButtonMyLocation(maps, $(this).data('index'));
      });

      bindToggleExtendedInput();
      bindCharacterCounters();
    });
  };

  window.addModalOpenToBody = function() {
    return $('body').addClass('modal-open');
  };

  window.removeModalOpenFromBody = function() {
    return $('body').removeClass('modal-open');
  };

  initMap = function (index) {
    var latField = $('#inputs-' + index + '-latitude');
    var lngField = $('#inputs-' + index + '-longitude');
    var marker = null;

    var map = L.map('js-contribution-map-canvas-' + index).setView([
      latField.val() ? latField.val() : osmConfig.defaultLocation.latitude,
      lngField.val() ? lngField.val() : osmConfig.defaultLocation.longitude
    ], osmConfig.defaultLocation.zoom);

    L.tileLayer(osmConfig.dataServerUrl, {
      attribution: osmConfig.attribution,
    }).addTo(map);

    if (latField.val()) {
      marker = L.marker([latField.val(), lngField.val()]);
      marker.addTo(map);
    }

    map.on('click', function (e) {
      if (marker !== null) {
        marker.remove();
      }

      marker = L.marker(e.latlng);
      marker.addTo(map);
      latField.val(e.latlng.lat);
      lngField.val(e.latlng.lng);
    });

    return map;
  }

  toggleMap = function (maps, mapEl, index) {
    if (mapEl.hasClass('js-contribution-map-collapsed') && !mapEl.hasClass('js-contribution-map-initialized')) {
      mapEl.removeClass('js-contribution-map-collapsed').show();
      maps[index] = initMap(index);
      mapEl.addClass('js-contribution-map-initialized');

      return;
    }

    if (mapEl.hasClass('js-contribution-map-collapsed')) {
      mapEl.removeClass('js-contribution-map-collapsed').show();

      return;
    }

    mapEl.addClass('js-contribution-map-collapsed').hide();
  }

  onClickButtonMyLocation = function (maps, index) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        maps[index].remove();
        $('#inputs-' + index + '-latitude').val(position.coords.latitude);
        $('#inputs-' + index + '-longitude').val(position.coords.longitude);
        maps[index] = initMap(index);
      });
    } else {
      alert(jsTranslations['navigator_geolocation_not_available']);
    }
  }

  bindContributionMaps = function() {
    $('.js-contribution-map-collapsed').hide();
    $('.js-contribution-map').each(function (i, map) {
      var mapEl = $(map);
      var index = mapEl.data('index');
      if (!mapEl.hasClass('js-contribution-map-collapsed') && !mapEl.hasClass('js-contribution-map-initialized')) {
        maps[index] = initMap(index);
        mapEl.addClass('js-contribution-map-initialized');
      }
    });

    $('.js-contribution-add-location').on('change', function() {
      var index = $(this).data('index');
      var mapEl = $('#js-contribution-map-' + index);

      toggleMap(maps, mapEl, index);
    });

    $('.js-contribution-map-button-my-location').on('click', function(e) {
      e.preventDefault();
      onClickButtonMyLocation(maps, $(this).data('index'));
    });
  }

  bindContributionForm = function () {
    $('#js-contribution-create-form').on('submit', function () {
      $('.js-contribution-add-location').each(function (i, checkbox) {
        var checkboxObj = $(checkbox);
        var index = checkboxObj.data('index');
        if (!checkboxObj.is(':checked')) {
          $('#inputs-' + index + '-latitude').val(null);
          $('#inputs-' + index + '-longitude').val(null);
        }
      });

      return true;
    });
  }

}).call(this);
