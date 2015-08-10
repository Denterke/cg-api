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
        category: '/admin/catalogue/category'
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
                    .selector('node[selected="true"]')
                    .css({
                        'background-color': '#f56954'
                    })
                    .selector('node[type="object"]')
                    .css({
                        'background-color': '#3c8dbc'
                    })
                    .selector('edge')
                    .css({
                        'target-arrow-shape': 'triangle',
                        'width': 2,
                        'line-color': '#000',
                        'target-arrow-color': '#000'
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
            editor.cy.on('click', '*', { editor: editor }, editor.rightClickHandler);
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

Editor.prototype.rightClickHandler = function(e) {
    var isRightMB;
    e = e || window.event;

    if ("which" in e) {// Gecko (Firefox), WebKit (Safari/Chrome) & Opera
        isRightMB = e.which == 3;
    }
    else if ("button" in e) { // IE, Opera
        isRightMB = e.button == 2;
    }
    if (!isRightMB) {
        return;
    }
    console.log('menu');
    //var editor = e.data.editor;
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
}

Editor.prototype.clickNode = function(node) {
    if (!node) {
        console.log('WARN: node is not defined');
    }

    switch (node.data('type')) {
        case 'category':
            this.clickCategory(node);
            break;
        case 'object':
            this.clickObject(node);
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
