
# Example Controller
class cExampleController extends cController
    constructor: ->
        super @AttachedElement = "#example", @ComponentName = "example"

    onClick: =>
        console.log 'Class: ' + @className

# Example Anchor Controller
class cExampleAnchorController extends cExampleController
    constructor: ->
        super "#example a", "example"

    onClick: =>
        @internalFunction()
        return false

    internalFunction: ->
        @json = $('meta[name=appleseed-language-data]').attr("content")
        @data = $.parseJSON @json
        console.log 'Data: ' + @data['name']
        return false

# Create instances.
example = new cExampleController
exampleAnchor = new cExampleAnchorController

include "includes/test"
require "includes/test"

class newClass extends testClass
    constructor: ->
        alert "New Class!"

# n = new newClass
