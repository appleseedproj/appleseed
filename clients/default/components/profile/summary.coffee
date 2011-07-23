
class cProfileSummaryController extends cController
    constructor: (@element) ->
        super @element

    onClick: =>
        @model = new cProfileSummaryModel
        delete @model

class cProfileSummaryModel extends cModel


# ProfileSummaryController = new cProfileSummaryController ".profile .summary"
ProfileSummaryController = new cProfileSummaryController "#profile-summary"
