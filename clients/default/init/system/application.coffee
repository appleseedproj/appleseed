
# Application class definition.
class cApplication
    constructor: ->
    
    client: "default"

# Instantiate the Application class as a global object.
@zApp = new cApplication

# Dynamically load and execute a coffeescript file.
@include = (@pFilename) ->

    @extension = ".coffee"
    @client = zApp.client

    @path = "/clients/" + @client + "/" + @pFilename + @extension
    
    @script = null

    window.__loaded_script ?= []

    # File has already been loaded.
    if @path in window.__loaded_scripts
        return true

    window.__require_script = null

    @script = $.ajax {
        url: @path,
        method: "get",
        async: false,
        dataType: "text",
        success: (@data) ->
            window.__require_script = CoffeeScript.compile @data
            window.__loaded_scripts.push path
    }

    if (!window.__require_script)
        return false

    eval window.__require_script

    delete window.__require_script

# Dynamically load and execute a coffeescript file, exit on failure.
@require = (@pFilename) ->
    @result = @include @pFilename

    @extension = ".coffee"
    @client = zApp.client

    @path = "/clients/" + @client + "/" + @pFilename + @extension
    
    if (!@result)
        document.write "Require failed: " + @path

    return false
