
class @cController
    constructor: (@pElement, @pComponent) ->
        @className = @constructor.name.replace /^c/, ""
        @className = @className.replace /Controller$/, ""

        console.log @pElement
        console.log @pComponent

        if (typeof @onClick == "function")
            $(@pElement).click (@onClick)

    onClick: =>
        alert "Controller"
