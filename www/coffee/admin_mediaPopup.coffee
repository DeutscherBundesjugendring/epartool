class mediaSelectPopup
    this.insertValue = (value, targetElId, imgPathPrefix) ->
        inputEl = $(window.opener.document.getElementById(targetElId))
        inputEl.val(value)
        inputEl.parent().find('img').attr('src', imgPathPrefix + value)
        window.close()

document.mediaSelectPopup = mediaSelectPopup
