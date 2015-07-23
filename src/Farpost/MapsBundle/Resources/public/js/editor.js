/**
 * Created by kalita on 21/07/15.
 */

var MIN_LAYER_NUM = 0;
var MAX_LAYER_NUM = 12;
var INITIAL_LAYER_NUM = 0;

var NT_GYM = 2,
    NT_RECTORATE = 3,
    NT_STUDY_OFFICE = 4,
    NT_LAB = 5,
    NT_AUDITORY = 6,
    NT_FOOD = 7,
    NT_TOILETS = 10,
    NT_FEFU_ORG = 14,
    NT_COMMERCIAL = 15,
    NT_ASSEMBLY_HALL = 17,
    NT_ATM = 18,
    NT_STAND = 19,
    NT_CHAIR = 20
;

var NORMAL_NODE_TYPES = [
    NT_GYM, NT_RECTORATE, NT_STUDY_OFFICE, NT_LAB, NT_AUDITORY, NT_FOOD, NT_TOILETS,
    NT_FEFU_ORG, NT_COMMERCIAL, NT_ASSEMBLY_HALL, NT_ATM, NT_STAND, NT_CHAIR
];

var _graphCache = {};
var _stylesCache = {};

$(function () {
    require(['ol', 'map-config'], function (ol, config) {

        /**
         * returns style of name
         *
         * @returns {ol.style.Style}
         */
        function getStyle(name) {
            if (_stylesCache.hasOwnProperty(name)) {
                return _stylesCache.name;
            }
            var fillColor = [],
                strokeColor = []
            ;
            switch (name) {
                case 'inactive':
                    fillColor = [0, 0, 255, 0.75];
                    strokeColor = [0, 0, 0, 1];
                    break;
                case 'active':
                    fillColor = [255, 0, 0, 1];
                    strokeColor = [0, 0, 0, 1];
                    break;
                default:
                    fillColor = [200, 200, 200, 0.75];
                    strokeColor = [0, 0, 0, 1];
                    break;
            }
            var style = new ol.style.Style({
                fill: new ol.style.Fill({
                    color: fillColor
                }),
                stroke: new ol.style.Stroke({
                    color: strokeColor,
                    width: 1
                })
            });
            _stylesCache.name = style;
            return style;
        }

        /**
         * translates latlng to ol.Coordinate
         *
         * @param arr
         * @returns {ol.Coordinate}
         */
        function transform(arr) {
            return ol.proj.transform(arr, 'EPSG:4326', 'EPSG:3857')
        }


        /**
         * Create map with layers
         *
         * @param target
         * @returns {{layers: Array, vectorLayer: ol.layer.Vector, source: ol.source.Vector, map: ol.Map}}
         */
        function createMap(target, stylishFunction) {
            var layers = [],
                source = new ol.source.Vector(),
                vectorLayer = new ol.layer.Vector({
                    source: source,
                    style: stylishFunction
                })
            ;
            vectorLayer.set('level', 'top')
            vectorLayer.setVisible(true);

            for (var i = MIN_LAYER_NUM; i <= MAX_LAYER_NUM; i++) {
                var layer = new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        url: config.services.tms.replace('{layer}', 'dvfu-' + i),
                        extent: transform([131.8799,43.0127,131.9128,43.0448]),
                        attributions: [ new ol.Attribution({ html: '@ FarPost LLC, 2015' })]
                    })
                });
                layer.set('level', i);
                layers.push(layer);
            }

            layers.push(vectorLayer);

            var map = new ol.Map({
                controls: ol.control.defaults().extend([
                    new ol.control.FullScreen(),
                    new ol.control.ZoomSlider()
                ]),
                interactions: ol.interaction.defaults().extend([
                    new ol.interaction.DragRotateAndZoom()
                ]),
                renderer: "canvas",
                view: new ol.View({
                    minZoom: 17,
                    maxZoom: 22,
                    center: transform([131.893647,43.024502]),
                    zoom: 19
                }),
                layers: layers,
                target: target
            });
            return {
                layers: layers,
                vectorLayer: vectorLayer,
                source: source,
                map: map
            };
        }

        /**
         * Fill source with graph data
         *
         * @param source
         * @param error
         * @param graph
         */
        function fillSource(source, error, graph) {
            if (error) {
                console.log(error);
                return;
            }
            source.clear();
            for (vertexId in graph.vertices) {
                vertex = graph.vertices[vertexId];
                if (typeof vertex.lat === 'undefined' || typeof vertex.lon === 'undefined') {
                    return;
                }
                var point = new ol.geom.Circle(
                    transform([vertex.lon, vertex.lat]),
                    1
                );
                var feature = new ol.Feature({
                    geometry: point,
                    name: vertexId,
                    vertex: vertex,
                    'class': 'inactive'
                });
                source.addFeature(feature);
            }
        }

        /**
         * Loads graph from server or from client cache
         *
         * @param level
         * @param force
         * @param cb
         */
        function loadGraph(level, force, cb) {
            if (force || !_graphCache[level]) {
                $.ajax('/admin/maps/graph?level=' + level)
                    .done(function(data) {
                        _graphCache[level] = data;
                        cb(null, data);
                    })
                    .fail(function(e) {
                        cb(e, null);
                    });
            } else {
                cb(null, _graphCache[level]);
            }
        }

        /**
         * Делает указанный слой базовым
         * @param {Number} level
         */
        function setBaseLayer(map, source, level) {
            var layers = map.getLayers(),
                baseLayer = null,
                vectorLayer = null
            ;

            layers.forEach(function (layer) {
                if (layer.get('level') === 'top') {
                    vectorLayer = layer;
                    return;
                }
                if (level == layer.get('level')) {
                    layer.setVisible(true);
                    baseLayer = layer;
                } else {
                    layer.setVisible(false);
                }
            });

            vectorLayer.setVisible(true);
            layers.remove(baseLayer);
            layers.remove(vectorLayer);
            layers.insertAt(1, vectorLayer);
            layers.insertAt(0, baseLayer);
            loadGraph(level, false, fillSource.bind(null, source));
        }

        /**
         * Loads objects for specified vertex (node)
         * @param vertex
         * @param cb
         */
        function loadObjects(vertex, cb) {
            $.ajax('/admin/maps/objects?node=' + vertex.id)
                .done(function(data) {
                    cb(data.objects);
                })
                .fail(function(e) {
                    console.log(e);
                    cb(null);
                });
        }

        /**
         * Draw information about vertex and it's objects in side-bar
         * @param vertex
         */
        function describeVertex(detachObjectFunction, sidebar, vertex, objects) {
            sidebar.objects.$objects.empty();
            if (!vertex) {
                sidebar.vertex.$description.hide();
                sidebar.vertex.$alias.val('');
                sidebar.vertex.$type.val('');
                sidebar.objects.$description.hide();
                return;
            }
            sidebar.vertex.$alias.val(vertex.alias);
            sidebar.vertex.$type.val(vertex.type.alias);
            sidebar.vertex.$description.show();

            sidebar.objects.$description.show();
            if (objects && Array.isArray(objects) && objects.length > 0) {
                objects.forEach(function(object) {
                    var objectDiv = $('<div>')
                            .addClass('object-description input-group')
                            .data('id', object.id),
                        nameInput = $('<a>')
                            .attr('href', '/admin/farpost/catalogue/catalogueobject/' + object.id + '/edit')
                            .attr('target', '_blank')
                            .addClass('col-md-12 form-control')
                            .text(object.name),
                        span = $('<span>')
                            .addClass('input-group-btn'),
                        unattachBtn = $('<button>')
                            .addClass('btn btn-danger')
                            .data('id', object.id)
                            .click(function() {
                                detachObjectFunction(vertex, $(this).data('id'))
                            }),
                        glyph = $('<span>')
                            .addClass('glyphicon glyphicon-remove')
                    ;
                    unattachBtn.append(glyph);
                    span.append(unattachBtn);
                    objectDiv
                        .append(nameInput)
                        .append(span)
                    ;
                    sidebar.objects.$objects.append(objectDiv);
                });
            }
        }

        /**
         * Entry point
         */
        function main() {
            // cache DOM elements
            var view = {
                sidebar: {
                    levelSelector: $('#select-level'),
                    vertex: {
                        $description: $('#vertex-description'),
                        $alias: $('#vertex-alias'),
                        $type: $('#vertex-type')
                    },
                    objects: {
                        $description: $('#vertex-objects-description'),
                        $objects: $('#vertex-objects'),
                        $selector: $('#object-selector'),
                        $attach: $('#attach')
                    }
                }
            };

            var featureStylishFunction = function(feature, resolution) {
                var vertex = feature.get('vertex'),
                    selected = feature.get('selected'),
                    styleName = ''
                ;
                if (selected) {
                    styleName = 'active';
                } else if (NORMAL_NODE_TYPES.indexOf(vertex.type.id) !== -1) {
                    styleName = 'inactive';
                }
                return [getStyle(styleName)];
            };

            //create layers and map
            var resource = createMap('map', featureStylishFunction),
                source = resource.source,
                layers = resource.layers,
                vectorLayer = resource.vectorLayer,
                map = resource.map,
                activeFeature = null,
                selectedObjectId = null;
            ;

            var describeVertexFunction = describeVertex.bind(
                null,
                function(vertex, objectId) {
                    $.ajax('/admin/catalogue/objects/detach', {
                        data: {
                            objectId: objectId
                        },
                        method: 'POST'
                    }).done(function(data) {
                        loadObjects(vertex, describeVertexFunction.bind(null, vertex));
                    });
                },
                view.sidebar
            );

            //setup view handlers
            view.sidebar.levelSelector.on('change', function(e) {
                var level = $(e.target).val();
                level = level < MIN_LAYER_NUM ? MIN_LAYER_NUM : level;
                level = level > MAX_LAYER_NUM ? MAX_LAYER_NUM : level;
                $(e.target).val(level);
                describeVertexFunction(null);
                setBaseLayer(map, source, level);
                return false;
            });

            //setup map interaction handlers
            map.on('click', function(event) {
                if (activeFeature) {
                    activeFeature.set('selected', false)
                }
                var feature = map.forEachFeatureAtPixel(event.pixel, function(feature, layer) { return feature; });
                if (feature) {
                    activeFeature = feature;
                    feature.set('selected', true);
                    var vertex = feature.get('vertex');
                    loadObjects(vertex, describeVertexFunction.bind(null, vertex));
                } else {
                    describeVertexFunction(null);
                }
            });

            //setup objects selectpicker
            view.sidebar.objects.$selector.typeahead({
                onSelect: function(item) {
                    selectedObjectId = item.value;
                },
                ajax: {
                    url: '/admin/catalogue/objects',
                    timeout: 500,
                    displayField: 'name',
                    triggerLength: 2,
                    method: 'get',
                    //loadingClass: 'loading-circle',
                    preDispatch: function(query) {
                        //showLoadingMask(true);
                        return {
                            search: query
                        }
                    },
                    preProcess: function(data) {
                        return data.objects;
                    }
                }
            });

            view.sidebar.objects.$attach.click(function() {
                if (!selectedObjectId || !activeFeature) {
                    return;
                }
                var vertex = activeFeature.get('vertex');
                $.ajax('/admin/catalogue/objects/attach', {
                    data: {
                        nodeId: vertex.id,
                        objectId: selectedObjectId
                    },
                    method: 'POST'
                }).done(function(data) {
                    if (activeFeature && activeFeature.get('selected')) {
                        loadObjects(vertex, describeVertexFunction.bind(null, vertex));
                    }
                }).fail(function(e) {
                    console.log(e);
                });
            });

            //set initial layer
            setBaseLayer(map, source, INITIAL_LAYER_NUM);
        }


        main();
    });
});
