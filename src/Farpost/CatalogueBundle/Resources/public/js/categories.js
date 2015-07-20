/**
 * Created by kalita on 20/07/15.
 */

function toggle(objects) {
    objects.forEach(function($object) {
        $object.toggle();
    });
}

$(function() {
    var $isOrganization = $('[name*="[is_organization]"]'),
        $isOrganizationClicker = $isOrganization.next('ins'),
        objects = [
            $('[name*="[description]"]'),
            $('[name*="[logoStandard][file]"]'),
            $('[name*="[logoStandard][_delete]"]'),
            $('[name*="[logoStandard][_delete]"]').closest('.form-group').parent().closest('.form-group'),
            $('[name*="[phone]"]'),
            $('[name*="[site]"]')
        ]
    ;

    objects = objects.map(function(object) {
        return object.closest('.form-group');
    });

    $isOrganizationClicker.click(function() {
        toggle(objects);
    });

    if (!$isOrganization.prop('checked')) {
        toggle(objects);
    }
});
