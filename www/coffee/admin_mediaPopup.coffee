class mediaSelectPopup
    this.insertValue = (value, targetElId, imgPathPrefix, CKEditorFuncNum) ->
        if targetElId == 'CKEditor'
            window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum,  imgPathPrefix + value);
        else
        inputEl = $(window.opener.document.getElementById(targetElId))
        inputEl.val(value)
        inputEl.parent().find('img').attr('src', imgPathPrefix + '/' + value)
        window.close()

document.mediaSelectPopup = mediaSelectPopup
