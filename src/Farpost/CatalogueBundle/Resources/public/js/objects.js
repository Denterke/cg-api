/**
 * Created by kalita on 23/07/15.
 */
function parse(val) {
    var result = null,
        tmp = [];
    location.search
        //.replace ( "?", "" )
        // this is better, there might be a question mark inside
        .substr(1)
        .split("&")
        .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

function setNodeIdIfReferal() {
    var nodeId = parse('node');
    if (!nodeId) {
        return;
    }
    $('input[name*="[node][id]"]').val(nodeId);

}

$(function() {
    //only if we come here from maps page, and want to create object and attach it to node
    var $node = $('input[name*="[node][id]"]');
    $node.closest('.form-group').hide();
    setNodeIdIfReferal();
});