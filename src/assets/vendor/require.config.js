var components = {
    "packages": [
        {
            "name": "tooltipsy",
            "main": "tooltipsy-built.js"
        },
        {
            "name": "jquery",
            "main": "jquery-built.js"
        },
        {
            "name": "jquery-ui",
            "main": "jquery-ui-built.js"
        },
        {
            "name": "select2",
            "main": "select2-built.js"
        },
        {
            "name": "x-editable",
            "main": "x-editable-built.js"
        },
        {
            "name": "vue",
            "main": "vue-built.js"
        }
    ],
    "shim": {
        "jquery-ui": {
            "deps": [
                "jquery"
            ],
            "exports": "jQuery"
        }
    },
    "baseUrl": "components"
};
if (typeof require !== "undefined" && require.config) {
    require.config(components);
} else {
    var require = components;
}
if (typeof exports !== "undefined" && typeof module !== "undefined") {
    module.exports = components;
}