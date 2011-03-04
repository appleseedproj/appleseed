    dojo.require("dijit.layout.TabContainer");
    dojo.require("dijit.layout.ContentPane");

   dojo.addOnLoad(function() {
        var tc = new dijit.layout.TabContainer({
            style: "height: 100%; width: 100%;"
        },
        "tc1-prog");

        var cp1 = new dijit.layout.ContentPane({
            title: "Food",
            content: "We offer amazing food"
        });
        tc.addChild(cp1);

        var cp2 = new dijit.layout.ContentPane({
            title: "Drinks",
            content: "We are known for our drinks."
        });
        tc.addChild(cp2);

        tc.startup();
    });
