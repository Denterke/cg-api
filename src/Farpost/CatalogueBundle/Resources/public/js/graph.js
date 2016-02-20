function Editor(id, cb) {
    this.options = {
        name: 'breadthfirst',
        directed: false,
        roots: '#c1',
        padding: 300,
        animation: false,
        //avoidOverlap: true,
        fit: true
    };
    this.id = id;

    this.meny = $('#context-menu');

    this.state = {
        selectedNode: null,
        initialized: false
    };

    this.urls = {
        categories: '/admin/catalogue/graph',
        category: '/admin/catalogue/category',
        edge: {
            create: '/admin/catalogue/editor/edge/create',
            delete: '/admin/catalogue/editor/edge/delete',
            revert: '/admin/catalogue/editor/edge/revert'
        },
        findObject: '/admin/catalogue/editor/object'
    };

    this.init(cb);
};

Editor.prototype.init = function(cb) {
    var editor = this;
    $.ajax(editor.urls.categories)
        .done(function(data) {
            editor.cy = cytoscape({
                style: cytoscape.stylesheet()
                    .selector('node')
                    .css({
                        'background-color': '#00a65a',
                        'content': 'data(name)',
                        'width': '200px',
                        'height': '200px',
                        'text-wrap': 'wrap',
                        'text-max-width': '90px',
                        'text-halign': 'center',
                        'text-valign': 'center'
                    })
                    .selector('node.source')
                    .css({
                        'background-color': '#FF0000',
                        'border-style': 'double',
                        'border-width': 30
                    })
                    .selector('node[selected="true"]')
                    .css({
                        'background-color': '#f56954'
                    })
                    .selector('node[type="object"]')
                    .css({
                        'background-color': '#3c8dbc',
                    })
                    .selector('node.inherits')
                    .css({
                        'border-color': '#f56954',
                        'border-width': 5
                    })
                    .selector('edge')
                    .css({
                        'target-arrow-shape': 'triangle',
                        'width': 2,
                        'line-color': '#000',
                        'target-arrow-color': '#000'
                    })
                    .selector('edge.selected')
                    .css({
                        'width': 8,
                        'line-color': '#f56954',
                        'target-arrow-color': '#f56954'
                    })
                    .selector('.highlighted')
                    .css({
                        'background-color': '#61bffc',
                        'line-color': '#61bffc',
                        'target-arrow-color': '#61bffc'
                    }),
                container: document.getElementById(editor.id),
                elements: {
                    nodes: data.nodes,
                    edges: data.edges
                },
                layout: editor.options
            });

            editor.cy.on('tap', 'node', { editor: editor }, editor.tapNodeHandler);

            editor.initContextMenus();
            editor.state.initialized = true;
            if (cb) {
                cb(null);
            }
        })
        .fail(function(e) {
            if (cb) {
                cb(e);
            }
        });
};

Editor.prototype.gotoCataloguePage = function(node) {
    if (!node) {
        console.log('WARN: Node is not defined');
    }
    var prefix = '/admin/farpost/catalogue/',
        suffix = '/edit';
    if (node.data('type') === 'category') {
        prefix += 'cataloguecategory/'
    } else if (node.data('type') === 'object') {
        prefix += 'catalogueobject/';
    }
    var url = prefix + node.data('realId') + suffix;
    window.open(url, '_blank');
};

Editor.prototype.resetSource = function(node) {
    if (!node) {
        console.log('WARN: Node is not defined');
    }
    if (this.state.sourceNode) {
        this.state.sourceNode.removeClass('source');
    }
    this.state.sourceNode = node;
    node.addClass('source');
};

Editor.prototype.linkWithSource = function(node) {
    this.linkNodes(this.state.sourceNode, node);
};

Editor.prototype.deleteEdge = function(edge) {
    if (!edge) {
        console.log('WARN: edge is not defined');
        return;
    }

    var editor = this;

    $.ajax(this.urls.edge.delete, {
        method: 'POST',
        data: {
            id: edge.data('realId'),
            type: edge.data('type')
        }
    }).done(function(data) {
        editor.cy.remove('edge#' + edge.id());
    });
};

Editor.prototype.revertEdge = function(edge) {
    if (!edge) {
        console.log('WARN: edge is not defined');
        return;
    }

    var editor = this;

    $.ajax(this.urls.edge.revert, {
        method: 'POST',
        data: {
            id: edge.data('realId')
        }
    }).done(function(data) {
        editor.cy.remove('edge#' + edge.id());
        editor.cy.add({
            group: 'edges',
            data: data.data
        });
        editor.applyStyleForSelected();
    })
};

Editor.prototype.linkWithSelected = function(node) {
    this.linkNodes(this.state.selectedNode, node);
};

Editor.prototype.linkNodes = function(source, target) {
    if (!source) {
        console.log('WARN: source is not defined');
        return;
    }
    if (!target) {
        console.log('WARN: target is not defined');
        return;
    }
    if (this.cy.collection('edge[source="' + source.id() + '"][target="' + target.id() + '"]').length > 0) {
        console.log('WARN: edge from source to target already exists');
        return;
    }
    if (source.data('type') !== 'category') {
        console.log('WARN: source is not category');
        return;
    }

    var editor = this;

    $.ajax(this.urls.edge.create, {
        method: 'POST',
        data: {
            sourceId: source.data('realId'),
            targetId: target.data('realId'),
            targetType: target.data('type')
        }
    }).done(function(data) {
        editor.cy.add({
            group: 'edges',
            data: data.data
        });
        editor.applyStyleForSelected();
    });
};

Editor.prototype.applyStyleForSelected = function() {
    if (!this.state.selectedNode) {
        console.log('WARN: selectedNode is not defined');
        return;
    }

    var connectedEdges = editor.cy.elements('edge[source="' + this.state.selectedNode.id() + '"]');
    connectedEdges.addClass('selected');
    connectedEdges.targets().addClass('inherits');
};

Editor.prototype.linkObject = function(category) {
    var objectsListUrl = '/admin/farpost/catalogue/catalogueobject/list?filter%5Bname%5D%5Btype%5D=&filter%5Bname%5D%5Bvalue%5D=&filter%5Bwithout_node%5D%5Btype%5D=&filter%5Bstrange%5D%5Btype%5D=&filter%5Bwithout_parents%5D%5Btype%5D=&filter%5Bwithout_parents%5D%5Bvalue%5D=1&filter%5B_page%5D=1&filter%5B_sort_by%5D=id&filter%5B_sort_order%5D=ASC&filter%5B_per_page%5D=25';
    this.loadModalData(objectsListUrl, function() {
        $('#modal-form').modal('show');
    });
};

Editor.prototype.selectObject = function(objectId) {
    var id = 'o' + objectId,
        node = this.cy.$('node#' + id),
        editor = this;
    if (node.length == 0) {
        $.ajax(this.urls.findObject + '?id=' + objectId)
            .done(function(data) {
                if (!data.data) {
                    console.log('WARN: no node found');
                    return;
                }
                var position = {
                    x: editor.state.selectedNode.position().x,
                    y: editor.state.selectedNode.position().y - 200
                };
                //position.y -= 100;
                editor.cy.add({
                    group: 'nodes',
                    data: data.data,
                    position: position
                });
                editor.linkWithSelected(editor.cy.$('node#' + id)[0]);
            });
    } else {
        this.linkWithSelected(node[0]);
    }
};

Editor.prototype.patchModal = function(data) {
    var $modal = $('#modal-form');
    var editor = this;
    $('#modal-form .modal-body').html(data);
    $('#modal-form a').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        var href = $(this).attr('href');
        editor.loadModalData(href);
    });
    $('#modal-form td.sonata-ba-list-field-select a').off('click');
    $('#modal-form td.sonata-ba-list-field-select a').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        editor.selectObject($(this).closest('td').attr('objectid'));
        $('#modal-form').modal('hide');
    });
    $('#modal-form button').closest('form').ajaxForm(function(a) {
        editor.patchModal(a);
    });

};

Editor.prototype.loadModalData = function(url, cb) {
    var editor = this;
    $.ajax(url)
        .done(function(data) {
            editor.patchModal(data);
            if (cb) {
                cb();
            }
        });
};

Editor.prototype.initContextMenus = function() {
    var editor = this;
    this.menus = {};

    var settings = {
        menuRadius: 100,
        fillColor: 'rgba(0, 0, 0, 0.75)', // the background colour of the menu
        activeFillColor: 'rgba(92, 194, 237, 0.75)', // the colour used to indicate the selected command
        activePadding: 20, // additional size in pixels for the active command
        indicatorSize: 24, // the size in pixels of the pointer to the active command
        separatorWidth: 3, // the empty spacing in pixels between successive commands
        spotlightPadding: 4, // extra spacing in pixels between the element and the spotlight
        minSpotlightRadius: 24, // the minimum radius in pixels of the spotlight
        maxSpotlightRadius: 38, // the maximum radius in pixels of the spotlight
        itemColor: 'white', // the colour of text in the command's content
        itemTextShadowColor: 'black', // the text shadow colour of the command's content
        zIndex: 9999 // the z-index of the ui div
    };

    var categoryMenuSettings = settings;
    categoryMenuSettings.selector = 'node[type="category"]';
    categoryMenuSettings.commands = [
        {
            content: 'В справочник',
            select: function() {
                editor.gotoCataloguePage(this);
            }
        },
        {
            content: 'Пометить как источник',
            select: function() {
                editor.resetSource(this);
            }
        },
        {
            content: 'Связать с помеченным',
            select: function() {
                editor.linkWithSource(this);
            }
        },
        {
            content: 'Связать с открытым',
            select: function() {
                editor.linkWithSelected(this);
            }
        },
        {
            content: 'Привязать объект',
            select: function() {
                editor.clickCategory(this);
                editor.linkObject(this);
            }
        }
    ];

    this.menus.category = this.cy.cxtmenu(categoryMenuSettings);

    var objectSettings = settings;
    objectSettings.selector = 'node[type="object"]';
    objectSettings.commands = [
        {
            content: 'В справочник',
            select: function() {
                editor.gotoCataloguePage(this);
            }
        },
        {
            content: 'Связать с помеченным',
            select: function() {
                editor.linkWithSource(this);

            }
        }
    ];

    this.menus.object = this.cy.cxtmenu(objectSettings);

    var categoryEdgeSettings = settings;
    categoryEdgeSettings.selector = 'edge[type="categoryedge"]';
    categoryEdgeSettings.commands = [
        {
            content: 'Удалить',
            select: function() {
                editor.deleteEdge(this);
            }
        },
        {
            content: 'Перевернуть',
            select: function() {
                editor.revertEdge(this);
            }
        }
    ];

    this.menus.edge = this.cy.cxtmenu(categoryEdgeSettings);

    var categoryNodeEdgeSettings = settings;
    categoryNodeEdgeSettings.selector = 'edge[type="categorynodeedge"]';
    categoryNodeEdgeSettings.commands = [
        {
            content: 'Удалить',
            select: function() {
                editor.deleteEdge(this);
            }
        }
    ];

    this.menus.nodeEdge = this.cy.cxtmenu(categoryNodeEdgeSettings);
};

Editor.prototype.tapNodeHandler = function(e) {
    var editor = e.data.editor,
        node = e.cyTarget;

    editor.clickNode(node);
};

Editor.prototype.unselectNode = function(node) {
    if (!node) {
        console.log('WARN: node is not defined');
        return;
    }

    var wasSelected = node.data('selected') === 'true';
    node.data('selected', false);
    //this.cy.collection('edge[source="' + node.id() + '"]').removeClass('selected');
    var connectedEdges = editor.cy.elements('edge[source="' + node.id() + '"]');
    connectedEdges.removeClass('selected');
    connectedEdges.targets().removeClass('inherits');
    this.cy.remove("edge[type = 'categorynodeedge']");
    this.cy.remove("node[type = 'object']");

    return wasSelected;
};

Editor.prototype.clickCategory = function(node) {
    if (this.unselectNode(this.state.selectedNode)) {
        if (node === this.state.selectedNode) {
            return;
        }
    };

    node.data('selected', 'true');
    this.state.selectedNode = node;
    this.loadCategoryItems(node);
};

Editor.prototype.addElements = function(basic, edges, nodes) {
    var editor = this,
        basicPosition = basic.position();

    var w = 300;
    var y = basicPosition.y,
        nodesInRow = 8;

    for (var i = 0; i < nodes.length; i++) {
        if (i % nodesInRow === 0) {
            var modifier = 1;
            if (nodes.length - i - 1 < nodesInRow) {
                modifier = nodes.length - i - 1;
            } else {
                modifier = nodesInRow - 1;
            }
            x = basicPosition.x - modifier * w / 2;
            y += 300;
        }
        editor.cy.add({
            group: 'nodes',
            data: nodes[i].data,
            position: {
                x: x,
                y: y
            }
        });
        x += w;
    }

    edges.forEach(function(edge) {
        editor.cy.add({
            group: 'edges',
            data: edge.data
        });
    });

    editor.applyStyleForSelected();
};

Editor.prototype.loadCategoryItems = function(node, cb) {
    var editor = this;
    editor.startLoading();
    $.ajax(this.urls.category + '?id=' + node.data('realId')).done(function(data) {
        editor.addElements(node, data.edges, data.nodes);
        editor.endLoading();
        if (cb) {
            cb(null);
        }
    });
};

Editor.prototype.startLoading = function() {
    var target = $('#' + this.id);
    target.addClass('loading');
    console.log('STATUS: loading started');
};

Editor.prototype.endLoading = function() {
    var target = $('#' + this.id);
    target.removeClass('loading');
    console.log('STATUS: loading ended');
};

Editor.prototype.clickNode = function(node) {
    if (!node) {
        console.log('WARN: node is not defined');
    }

    switch (node.data('type')) {
        case 'category':
            this.clickCategory(node);
            break;
        case 'object':
            //this.clickObject(node);
            break;
        default:
            console.log('WARN: node type is unknown');
            return;
    }
};

var editor;

$(function() {
    editor = new Editor('graph-canvas');
});
