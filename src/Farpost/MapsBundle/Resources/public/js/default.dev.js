/**
 * Created by kalita on 21/07/15.
 */
define (
    {
        "about": "Картографический сервис Map.VL.ru",
        "copyright": "© ГИС Владивосток, ООО НКС ВЕКТОР, 2011 | © FarPost LLC, 2015",
        "hostname": "http://map.vl.dev",
        "services": {
            "tiles": "http://wms.map.vl.dev/service",
            "tms": "http://wms.map.vl.dev/tiles/{layer}/{z}/{x}/{-y}.png",
            "traffic": "http://wms2.map.vl.dev/service",
            "api": "http://api.map.vl.dev",
            "search": "http://search.map.vl.dev"
        },
        "defaults": {
            "center": [
                131.8892,
                43.1129
            ],
            "zoom": 12,
            "extent": [
                129.375,
                40.979898,
                140.622253,
                48.920694
            ]
        },
        "icons": {
            "pin": {
                "url": "http://api.map.vl.dev/img/markers/default-single.png",
                "width": 52,
                "height": 45,
                "offsetX": -18,
                "offsetY": -43
            },
            "cluster": {
                "url": "http://api.map.vl.dev/img/markers/default-group.png",
                "width": 52,
                "height": 45,
                "offsetX": -18,
                "offsetY": -43
            }
        },
        "layers": {
            "base": {
                "name": "mapvlru",
                "extent": [
                    129.375,
                    40.979898,
                    140.622253,
                    48.920694
                ],
                "center": [
                    131.8892,
                    43.1129
                ],
                "zoom": 12
            },
            "mobile": {
                "name": "mapvlru_mobile",
                "extent": [
                    129.375,
                    40.979898,
                    140.622253,
                    48.920694
                ],
                "center": [
                    131.8892,
                    43.1129
                ],
                "zoom": 12
            },
            "apec": {
                "name": "apec"
            },
            "drom": {
                "name": "users"
            },
            "traffic": {
                "name": "traffic",
                "extent": [
                    131.7467,
                    42.8297,
                    133.2381,
                    43.4739
                ]
            },
            "roadPoi": {
                "name": "road_poi",
                "extent": [
                    131.835937,
                    42.940339,
                    132.1875,
                    43.325177
                ]
            },
            "busStops": {
                "name": "bus_stops",
                "extent": [
                    131.835937,
                    42.940339,
                    132.1875,
                    43.325177
                ]
            },
            "crimea": {
                "name": "crimea",
                "extent": [
                    32.3673,
                    44.3086,
                    39.5908,
                    46.2753
                ],
                "center": [
                    36.5671,
                    45.331
                ],
                "zoom": 12
            }
        },
        "options": {
            "buses": {
                "activeAge": 1800
            },
            "tracking": {
                "refreshInterval": 30,
                "trackAge": 900,
                "activeAge": 900
            },
            "traffic": {}
        },
        "mode": "development"
    }
)