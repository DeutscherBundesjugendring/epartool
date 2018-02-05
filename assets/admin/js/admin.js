(function() {
  var bindConsultationCustomPhaseNames;
  var bindContributionForm;
  var bindContributionMap;
  var bindContributionVideoSelect;
  var bindEmailAddAttachment;
  var bindEmailConsultationSelect;
  var bindEmailTemplateSelect;
  var bindTableRowsToggle;
  var bindToggleAll;
  var changeContributionStatus;
  var contributorAgesSettings;
  var groupsSizesSettings;
  var initMap;
  var initMediaIndexFileLazyLoad;
  var initSortableFollowupSnippets;
  var initSortableGeneral;
  var initSortablePartners;
  var initSortableVotingDirs;
  var mediaSelectPopup;
  var bindPreviewVotingButtons;
  var themeSettings;
  var bindToggleAnonymousContributionSwitch;
  var bindAutoVotingInfo;
  var bindGeoFenceForContributionLocation;

  var map;

  $(document).ready(function() {
    bindContributionForm();
    bindContributionMap();
    bindEmailTemplateSelect();
    bindEmailConsultationSelect();
    bindEmailAddAttachment();
    bindTableRowsToggle();
    bindToggleAll();
    bindConsultationCustomPhaseNames();
    bindContributionVideoSelect();
    bindToggleAnonymousContributionSwitch();
    themeSettings();
    contributorAgesSettings();
    groupsSizesSettings();
    initSortableFollowupSnippets();
    initSortableVotingDirs();
    initSortablePartners();
    initMediaIndexFileLazyLoad();
    changeContributionStatus();
    bindPreviewVotingButtons();
    bindAutoVotingInfo();
    bindGeoFenceForContributionLocation();
    $('[data-toggle="tooltip"]').tooltip();
  });

  bindEmailTemplateSelect = function() {
    return $('.js-template-selector').change((function(eventObj) {
      var bodyHtmlEditor, bodyTextField, subjectField, templateId;
      templateId = $(eventObj.target).val();
      subjectField = $('form.js-send-mail .js-subject');
      bodyTextField = $('form.js-send-mail .js-body-text');
      bodyHtmlEditor = CKEDITOR.instances['body_html'];
      if (templateId * 1 === 0) {
        subjectField.val('');
        bodyTextField.val('');
        return bodyHtmlEditor.setData('');
      } else {
        return $.get(baseUrl + 'mail-send/template-json', {
          templateId: templateId
        }, function(data) {
          subjectField.val(data.subject);
          bodyTextField.val(data.body_text);
          return bodyHtmlEditor.setData(data.body_html);
        }, 'json');
      }
    }));
  };

  bindEmailConsultationSelect = function() {
    var consulRecipients;
    consulRecipients = $('#mail_consultation').data('consultations');
    return $('.js-consultation-selector').change((function(eventObj) {
      var consulId, isFollowupDisabled, isNewsletterDisabled, isParticipantDisabled, isVoterDisabled;
      $('.js-consultation-participant').prop('checked', false);
      $('.js-consultation-voter').prop('checked', false);
      $('.js-consultation-newsletter').prop('checked', false);
      $('.js-consultation-followup').prop('checked', false);
      consulId = $(eventObj.target).val();
      if (consulId * 1 === 0) {
        isParticipantDisabled = true;
        isVoterDisabled = true;
        isNewsletterDisabled = true;
        isFollowupDisabled = true;
      } else {
        isParticipantDisabled = !consulRecipients[consulId]['hasParticipant'];
        isVoterDisabled = !consulRecipients[consulId]['hasVoter'];
        isNewsletterDisabled = !consulRecipients[consulId]['hasNewsletter'];
        isFollowupDisabled = !consulRecipients[consulId]['hasFollowup'];
      }
      $('.js-consultation-participant').prop('disabled', isParticipantDisabled);
      $('.js-consultation-voter').prop('disabled', isVoterDisabled);
      $('.js-consultation-newsletter').prop('disabled', isNewsletterDisabled);
      $('.js-consultation-followup').prop('disabled', isFollowupDisabled);
    }));
  };

  bindEmailAddAttachment = function() {
    var attachmentId;
    attachmentId = 0;
    return $('.js-email-add-attachment').click(function(ev) {
      var button, html;
      ev.preventDefault();
      button = $(ev.target);
      html = button.next().clone().removeClass('hidden');
      html.find('input[type=hidden]').attr('name', 'attachments[]').removeProp('disabled');
      html = html.prop('outerHTML').replace(/TOKEN_TO_BE_REPLACED_BY_JS/g, attachmentId);
      button.before(html);
      attachmentId++;
    });
  };

  bindTableRowsToggle = function() {
    var checkboxes, rows;
    rows = $('.js-table-rows-toggle tr');
    checkboxes = $('.js-table-rows-toggle input:checkbox');
    rows.click(function(e) {
      if (e.target.type !== 'checkbox' && e.target.classList[0] !== 'label') {
        return $(':checkbox', this).trigger('click');
      }
    });
    return checkboxes.change(function(e) {
      var row;
      row = $(this).closest('tr');
      if ($(this).is(':checked')) {
        return row.addClass('info');
      } else {
        return row.removeClass('info');
      }
    });
  };

  bindToggleAll = function() {
    return $('.js-toggle-all').change(function() {
      var form;
      form = $(this).closest('form');
      if ($(this).is(':checked')) {
        return form.find('input:checkbox:not(:checked)').prop('checked', true).change();
      } else {
        return form.find('input:checkbox:checked').prop('checked', false).change();
      }
    });
  };

  bindConsultationCustomPhaseNames = function() {
    return $('.js-enable-custom-consultation-phase-names').change(function(ev) {
      var checkbox, inputs;
      checkbox = $(ev.currentTarget);
      inputs = checkbox.parents('form').find('input[type=text]');
      if (checkbox.prop('checked')) {
        return inputs.prop('disabled', false);
      } else {
        return inputs.prop('disabled', true);
      }
    });
  };

  initSortableFollowupSnippets = function() {
    return initSortableGeneral('.js-sortable-followup-snippets');
  };

  initSortableVotingDirs = function() {
    return initSortableGeneral('.js-sortable-voting-directory');
  };

  initSortablePartners = function() {
    return initSortableGeneral('.js-sortable-partners');
  };

  initSortableGeneral = function(sortableClass) {
    $(sortableClass).disableSelection();
    return $(sortableClass).sortable({
      'update': function(ev, ui) {
        var $form, i;
        i = 1;
        $form = ui.item.closest('form');
        $form.find('div input[type=hidden]').each(function() {
          $(this).val(i);
          return i++;
        });
        $form.find('button[type=submit]').prop('disabled', false);
      }
    });
  };

  initMediaIndexFileLazyLoad = function() {
    var $container, batchSize, offset, wait;
    $container = $('#media-thumbnail-container');
    if ($container.length > 0) {
      batchSize = $container.data('batchSize');
      offset = batchSize;
      wait = false;
      return $(window).scroll(function() {
        var containerBottom, url, viewBottom;
        viewBottom = $(window).scrollTop() + $(window).height();
        containerBottom = $container.scrollTop() + $container.height();
        if (viewBottom + 500 > containerBottom && wait === false) {
          wait = true;
          url = baseUrl + '/admin/media/lazy-load-images/offset/' + offset;
          if ($container.data('kid')) {
            url = url + '/kid/' + $container.data('kid');
          }
          if ($container.data('folder')) {
            url = url + '/folder/' + $container.data('folder');
          }
          if ($container.data('targetElId')) {
            url = url + '/targetElId/' + $container.data('targetElId');
          }
          if ($container.data('lockDir')) {
            url = url + '/lockDir/' + $container.data('lockDir');
          }
          if ($container.data('ckeditorFuncNum')) {
            url = url + '/CKEditorFuncNum/' + $container.data('ckeditorFuncNum');
          }
          return $.get(url, [], function(data) {
            $container.append(data);
            if (data) {
              wait = false;
              return offset = offset + batchSize;
            }
          });
        }
      });
    }
  };

  bindContributionVideoSelect = function() {
    var select;
    select = $('.js-video-service select');
    select.change(function() {
      var addon;
      addon = $(this).closest('.js-video-service').find('.js-video-service-url');
      return addon.html($(this).data('url')[$(this).children(':selected').attr('value')]);
    });
    return select.trigger('change');
  };

  themeSettings = function() {
    var colorAccent1, colorAccent2, colorPrimary;
    $('#themes').data('presetTheme', $("input[name='theme_id']:checked").val());
    colorAccent1 = $('#color_accent_1');
    colorPrimary = $('#color_primary');
    colorAccent2 = $('#color_accent_2');
    colorAccent1.data('oldValue', colorAccent1.val());
    colorPrimary.data('oldValue', colorPrimary.val());
    colorAccent2.data('oldValue', colorAccent2.val());
    $('.js-theme-preset').click(function() {
      var colors;
      if (!$('#themes').data('presetTheme')) {
        if (!confirm(jsTranslations['theme_confirm_override'])) {
          return false;
        }
      }
      colors = $(this).data('colors');
      colorAccent1 = $('#color_accent_1');
      colorPrimary = $('#color_primary');
      colorAccent2 = $('#color_accent_2');
      colorAccent1.closest('.colorpicker-component').colorpicker('setValue', colors['color_accent_1']);
      colorPrimary.closest('.colorpicker-component').colorpicker('setValue', colors['color_primary']);
      colorAccent2.closest('.colorpicker-component').colorpicker('setValue', colors['color_accent_2']);
      $('#themes').data('presetTheme', true);
      colorAccent1.data('oldValue', colorAccent1.val());
      colorPrimary.data('oldValue', colorPrimary.val());
      colorAccent2.data('oldValue', colorAccent2.val());
      return true;
    });
    return $('.colorpicker-component').colorpicker({
      format: "hex"
    }).on('focusin showPicker', function() {
      if ($('#themes').data('presetTheme')) {
        if (!confirm(jsTranslations['theme_confirm_override'])) {
          $(this).find('.js-color-input').blur();
          $(this).colorpicker('hide');
          return true;
        }
      }
      $(this).find('.js-color-input').data('oldValue', $(this).colorpicker('getValue'));
      $('#themes').data('presetTheme', false);
      $("input[name='theme_id']:checked").attr('checked', false);
      return true;
    });
  };

  contributorAgesSettings = function() {
    $('#js-contributor-ages-intervals').on('click', '.js-contributor-ages-delete-row', function() {
      if (!confirm(jsTranslations['contribution_interval_confirm_delete'])) {
        return false;
      }
      $(this).closest('.js-row').remove();
      return false;
    });
    return $('#js-contributor-ages-add-row').click(function() {
      var el, timestamp;
      timestamp = new Date().getUTCMilliseconds();
      el = $('#js-contributor-ages-new-row').clone().removeClass('hidden').attr('id', '');
      el.find('#js-contributor-ages-new-row-from').attr('name', 'contributorAges[_' + timestamp + '][from]').attr('id', '');
      el.find('#js-contributor-ages-new-row-to').attr('name', 'contributorAges[_' + timestamp + '][to]').attr('id', '');
      $('#js-contributor-ages-intervals tr:last').before(el);
      return false;
    });
  };

  groupsSizesSettings = function() {
    $('#js-groups-sizes-intervals').on('click', '.js-groups-sizes-delete-row', function() {
      if (!confirm(jsTranslations['contribution_interval_confirm_delete'])) {
        return false;
      }
      $(this).closest('.js-row').remove();
      return false;
    });
    return $('#js-groups-sizes-add-row').click(function() {
      var el, timestamp;
      timestamp = new Date().getUTCMilliseconds();
      el = $('#js-groups-sizes-new-row').clone().removeClass('hidden').attr('id', '');
      el.find('#js-groups-sizes-new-row-from').attr('name', 'groupSizes[_' + timestamp + '][from]').attr('id', '');
      el.find('#js-groups-sizes-new-row-to').attr('name', 'groupSizes[_' + timestamp + '][to]').attr('id', '');
      $('#js-groups-sizes-intervals tr:last').before(el);
      return false;
    });
  };

  changeContributionStatus = function() {
    return $('.js-contribution-change-status').on('click', function(e) {
      var buttonIcon, buttonLabel, container, dataAttributeName, kid, property, thisButton, tid, tokenEl;
      e.preventDefault();
      tokenEl = $('#contribution-table');
      if (tokenEl.data('token') === '') {
        return;
      }
      kid = $(this).data('kid');
      tid = $(this).data('tid');
      property = $(this).data('property');
      dataAttributeName = 'voting';
      if (property === 'blocking') {
        dataAttributeName = 'admin-confirmation';
      }
      container = $(this).closest('tr');
      thisButton = $(this);
      buttonLabel = $(this).find('.label');
      buttonIcon = $(this).find('.glyphicon').clone();
      $.ajax({
        url: baseUrl + '/admin/input/change-status/kid/' + kid + '/tid/' + tid + '/token/' + tokenEl.data('token') + '/property/' + property,
        type: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        cache: false,
        async: true,
        error: function() {
          buttonLabel.attr('class', 'label label-default');
          buttonLabel.html(' ' + jsTranslations['contribution_label_unknown']);
          buttonIcon.attr('class', 'glyphicon glyphicon-question-sign');
          buttonLabel.prepend(buttonIcon);
          thisButton.attr('style', '');
        },
        beforeSend: function() {
          tokenEl.data('token', '');
          $('.js-contribution-change-status').attr('style', 'opacity:0.7');
          buttonLabel.attr('class', 'label label-info');
          buttonLabel.html(' ' + jsTranslations['contribution_label_loading']);
          buttonIcon.attr('class', 'glyphicon glyphicon-refresh');
          buttonLabel.prepend(buttonIcon);
        },
        success: function(response) {
          var newStatus;
          buttonIcon.attr('class', 'glyphicon glyphicon-' + response.iconClass);
          buttonLabel.attr('class', 'label label-' + response.labelClass);
          buttonLabel.html(' ' + response.label);
          buttonLabel.prepend(buttonIcon);
          tokenEl.data('token', response.token);
          $('.js-contribution-change-status').attr('style', '');
          newStatus = response.status;
          if (dataAttributeName === 'admin-confirmation') {
            if (newStatus === '1') {
              newStatus = 'n';
            }
            if (newStatus === '0') {
              newStatus = 'y';
            }
            if (newStatus === null) {
              newStatus = 'u';
            }
          }
          if (dataAttributeName === 'voting') {
            if (newStatus === '1') {
              newStatus = 'y';
            }
            if (newStatus === '0') {
              newStatus = 'n';
            }
            if (newStatus === null) {
              newStatus = 'u';
            }
          }
          container.data(dataAttributeName, newStatus);
        }
      });
    });
  };

  mediaSelectPopup = (function() {

    /**
     * Inserts the image data back into the element that triggered the display of this popup
     * @param    {string}  filename              The media filename
     * @param    {string}  targetElId            The id of the element that triggered this popup
     * @param    {string}  imgPathPrefixInput    The path to be used in media element in input field
     * @param    {string}  imgPathImage          The path to be used in media element in image src attribute.
                                                It points to the cache folder.
     * @param    {numeric} CKEditorFuncNum       The CKEditor callback identifier
     */
    function mediaSelectPopup() {}

    mediaSelectPopup.insertValue = function(filename, targetElId, imgPathPrefixInput, imgPathImage, CKEditorFuncNum) {
      var inputEl;
      if (targetElId === 'CKEditor') {
        window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum, imgPathPrefixInput + filename);
      } else {
        inputEl = $(window.opener.document.getElementById(targetElId));
        inputEl.val(imgPathPrefixInput + filename);
        inputEl.parent().find('img').attr('src', imgPathImage);
      }
      return window.close();
    };

    return mediaSelectPopup;

  })();

  bindToggleAnonymousContributionSwitch = function() {
    return $('.js-toggle-anonymous-contribution-switch').on('click', function(e) {
      $('#js-anonymous-off-form').toggle();
      $('#js-anonymous-on-form').toggle();
    });
  };

  document.mediaSelectPopup = mediaSelectPopup;

  initMap = function () {
    var $latField = $('#latitude');
    var $lngField = $('#longitude');
    var marker = null;
    var geoFence = $('#js-contribution-map-canvas').data('geoFence') || [];
    var polygon = null;

    map = L.map('js-contribution-map-canvas').setView([
      $latField.val() ? $latField.val() : osmConfig.defaultLocation.latitude,
      $lngField.val() ? $lngField.val() : osmConfig.defaultLocation.longitude
    ], osmConfig.defaultLocation.zoom);

    L.tileLayer(osmConfig.dataServerUrl, {
      attribution: osmConfig.attribution,
    }).addTo(map);

    if (geoFence.length) {
      polygon = L.polygon(geoFence, {color: 'red'}).addTo(map);
      map.fitBounds(polygon.getBounds());
    }

    if ($latField.val()) {
      marker = L.marker([$latField.val(), $lngField.val()]);
      marker.addTo(map);
    }

    map.on('click', function (e) {
      if (polygon !== null && !polygon.contains(e.latlng)) {
        alert(jsTranslations['point_is_not_in_polygon']);

        return;
      }

      if (marker !== null) {
        marker.remove();
      }

      marker = L.marker(e.latlng);
      marker.addTo(map);
      $latField.val(e.latlng.lat);
      $lngField.val(e.latlng.lng);
    });
  }

  bindContributionMap = function() {
    var $map = $('#js-contribution-map');
    if ($map.hasClass('in') && !$map.hasClass('js-contribution-map-initialized')) {
      initMap();
      $map.addClass('js-contribution-map-initialized');
    }

    $('#js-contribution-map-toggle-location').on('click', function () {
      if ($(this).is(':checked')) {
        var $map = $('#js-contribution-map');
        if (!$map.hasClass('js-contribution-map-initialized')) {
          setTimeout(function () {
            initMap();
            $map.addClass('js-contribution-map-initialized');
          }, 1000);
        }
      }
    });

    $('#js-contribution-map-button-my-location').on('click', function (e) {
      e.preventDefault();
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
          map.remove();
          $('#latitude').val(position.coords.latitude);
          $('#longitude').val(position.coords.longitude);
          initMap();
        });
      } else {
        alert(jsTranslations['navigator_geolocation_not_available']);
      }
    });
  }

  bindContributionForm = function () {
    $('#js-contribution-form').on('submit', function () {
      var checkboxAddLocation = $('#js-contribution-map-toggle-location');
      if (!checkboxAddLocation.is(':checked')) {
        $('#latitude').val(null);
        $('#longitude').val(null);
      }
    });
  }

  bindPreviewVotingButtons = function () {
    $('#button_type-element input[type="radio"]').on('click', function () {
      $this = $(this);
      $('#votingButtonsPreviewContent').html($('#votingButtonsPreviewType'
        + $this.val().charAt(0).toUpperCase() + $this.val().slice(1)).html());
    });

    $('#btn_no_opinion-element input[type="radio"]').on('click', function () {
      if ($(this).val() === '1') {
        $('#votingButtonsPreviewContent > ul > li.voting-button-no-opinion').show();
        $('#votingButtonsPreview > div > ul > li.voting-button-no-opinion').show();

        return;
      }
      $('#votingButtonsPreviewContent > ul > li.voting-button-no-opinion').hide();
      $('#votingButtonsPreview > div > ul > li.voting-button-no-opinion').hide();
    });

    $('#is_btn_important-element input[type="radio"]').on('click', function () {
      if ($(this).val() === '1') {
        $('#votingButtonsPreviewContent > ul > li.voting-button-superbutton').show();
        $('#votingButtonsPreview > div > ul > li.voting-button-superbutton').show();

        return;
      }
      $('#votingButtonsPreviewContent > ul > li.voting-button-superbutton').hide();
      $('#votingButtonsPreview > div > ul > li.voting-button-superbutton').hide();
    });

    var $previewOfSelected = $('#button_type-element input[type="radio"]:checked');
    if ($previewOfSelected.length > 0) {
      $('#votingButtonsPreviewContent').empty().append($('#votingButtonsPreviewType'
        + $previewOfSelected.val().charAt(0).toUpperCase() + $previewOfSelected.val().slice(1)).children().clone());
    }

    if ($('#is_btn_important-element input[type="radio"]:checked').val() === '0') {
      $('#votingButtonsPreviewContent > ul > li.voting-button-superbutton').hide();
      $('#votingButtonsPreview > div > ul > li.voting-button-superbutton').hide();
    }

    if ($('#btn_no_opinion-element input[type="radio"]:checked').val() === '0') {
      $('#votingButtonsPreviewContent > ul > li.voting-button-no-opinion').hide();
      $('#votingButtonsPreview > div > ul > li.voting-button-no-opinion').hide();
    }
  }

  bindAutoVotingInfo = function () {
    $('.js-is-votable-radio').on('click', function () {
      $('#js-is-votable-auto-info').remove();
      $('#is_votable_edited').val(true);
    });
  }

  bindGeoFenceForContributionLocation = function () {
    if (!$('#js-contribution-geo-fence-map-canvas').length) {
      return;
    }

    var $polygonCoords = $('#geo_fence_polygon');
    var polygonCoords = ($polygonCoords.val() ? $.parseJSON($polygonCoords.val()) : false) || [];
    var geoFenceMap = L.map('js-contribution-geo-fence-map-canvas').setView([
      osmConfig.defaultLocation.latitude,
      osmConfig.defaultLocation.longitude
    ], osmConfig.defaultLocation.zoom);
    var polygon = null;
    var renderPolygon = function () {
      if (polygon !== null) {
        polygon.removeFrom(geoFenceMap);
      }
      if (polygonCoords.length) {
        polygon = L.polygon(polygonCoords, {color: 'red'}).addTo(geoFenceMap);
      }
    };

    L.tileLayer(osmConfig.dataServerUrl, {
      attribution: osmConfig.attribution,
    }).addTo(geoFenceMap);

    renderPolygon();
    if (polygon !== null) {
      geoFenceMap.fitBounds(polygon.getBounds());
    }

    geoFenceMap.on('click', function (e) {
      polygonCoords.push([e.latlng.lat, e.latlng.lng]);
      renderPolygon();
    });

    var $map = $('#js-contribution-geo-fence-map');
    var $geoFenceEnabled = $('#geo_fence_enabled');
    var $locationEnabled = $('#location_enabled');
    var $noHttpsInfo = $('.js-no-https-info');

    $('#js-question-submit').on('click', function () {
      $polygonCoords.val(JSON.stringify(polygonCoords));
    });

    $('#js-geo-fence-destroy').on('click', function (e) {
      e.preventDefault();
      polygonCoords = [];
      renderPolygon();
    });

    $locationEnabled.on('click', function () {
      if ($(this).is(':checked')) {
        if ($noHttpsInfo.length) {
          $noHttpsInfo.show();
        }
        $geoFenceEnabled.prop('disabled', false);
        if ($geoFenceEnabled.is(':checked')) {
          $map.show();
        }

        return;
      }

      if ($noHttpsInfo.length) {
        $noHttpsInfo.hide();
      }
      $geoFenceEnabled.prop('disabled', true);
      $map.hide();
    });

    $geoFenceEnabled.on('click', function () {
      if ($(this).is(':checked')) {
        $map.show();

        return;
      }

      $map.hide();
    });

    if (!$geoFenceEnabled.is(':checked')) {
      $map.hide();
    }
    if (!$locationEnabled.is(':checked')) {
      $geoFenceEnabled.prop('disabled', true);
      $map.hide();
      if ($noHttpsInfo.length) {
        $noHttpsInfo.hide();
      }
    }
  }

}).call(this);
