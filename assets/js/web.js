(function() {
  var bindCharacterCounters, bindLoadMoreConsultations, bindToggleGroupRegister;

  $(document).ready(function() {
    bindCharacterCounters();
    bindToggleGroupRegister();
    bindLoadMoreConsultations();
    return $('.js-has-password-meter').pwstrength({
      'ui': {
        'bootstrap2': true,
        'verdicts': [i18n['Weak'], i18n['Normal'], i18n['Medium'], i18n['Strong'], i18n['Very Strong']]
      }
    });
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
    if (groupTypeChecked !== "group") {
      labelName.show();
      elementName.show();
      container.hide();
      $('select#age_group option').filter("[value='4']").remove();
    }
    return $('input[name="group_type"]').change(function() {
      groupTypeChecked = $('input[name="group_type"]:checked').val();
      if (groupTypeChecked === "group") {
        labelName.hide();
        elementName.hide();
        container.slideDown();
        return $('select#age_group').append($('<option></option>').val('4').html('Alle Altersgruppen'));
      } else {
        labelName.show();
        elementName.show();
        container.slideUp();
        return $('select#age_group option').filter("[value='4']").remove();
      }
    });
  };

  bindLoadMoreConsultations = function() {
    return $('.js-load-more-consultations').click(function(e) {
      e.preventDefault();
      return $.get(baseUrl + '/index/ajax-consultation', {}, function(data) {
        $('.js-load-more-consultations').remove();
        return $('.js-consultations-container').append(data);
      });
    });
  };

}).call(this);
