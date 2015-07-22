/**
 * Created by kalita on 21/07/15.
 */
if (typeof requirejs !== "undefined") {
    requirejs.config({
        paths: {

            "ol":           "http://api.map.vl.dev/assets/ol3/ol-debug.js?203",

            // deprecated, mapConfig at client require clause should be changed to map-config
            // "mapConfig":    "http://api.map.vl.dev/config/default.js?203",
            // deprecated, MapMaker at client require clause should be changed to map-maker
            // "MapMaker":     "http://api.map.vl.dev/js/map-maker.js?203",
            // actual modules
            "map-config":   "http://api.map.vl.dev/config/default.js?203",
            "map-maker":    "http://api.map.vl.dev/js/map-maker.js?203",
            "view-bull-map":"http://api.map.vl.dev/js/farpost/view-bull-map.js?203",
            "view-dir-map": "http://api.map.vl.dev/js/farpost/view-dir-map.js?203",
            "address-map":  "http://api.map.vl.dev/js/farpost/address-map.js?203"
        }
    });
}