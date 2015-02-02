class mediaSelectPopup
    ###*
    # Inserts the image data back into the element that triggered the display of this popup
    # @param    {string}  filename              The media filename
    # @param    {string}  targetElId            The id of the element that triggered this popup
    # @param    {string}  imgPathPrefixInput    The path to be used in media element in input field
    # @param    {string}  imgPathImage          The path to be used in media element in image src attrib.
                                                It points to the cache folder.
    # @param    {numeric} CKEditorFuncNum       The CKEditor callbeck identifier
    ###
    this.insertValue = (filename, targetElId, imgPathPrefixInput, imgPathImage, CKEditorFuncNum) ->
        if imgPathPrefixInput.length > 0
            imgPathPrefixInput = imgPathPrefixInput + '/'

        if targetElId == 'CKEditor'
            window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum,  imgPathPrefixInput + filename);
        else
            inputEl = $(window.opener.document.getElementById(targetElId))
            inputEl.val(imgPathPrefixInput + filename)
            inputEl.parent().find('img').attr('src', imgPathImage)
        window.close()

document.mediaSelectPopup = mediaSelectPopup
