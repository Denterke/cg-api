var specializations = {};

$(function() {
   $('.school_select').change(function() {
      var school_id = $(this).find('option:selected').val();
      if (!(school_id in specializations)) {
         $.ajax({
            url: '/method/departments.get.json?school=' + school_id,
            type: 'POST',
            dataType: 'json',
            async: false
         })
         .done(function(data) {
            specializations[school_id] = data;
            console.log("success");
         })
         .fail(function() {
            console.log("error");
         })
      }
      var $department = $('.department_select');
      $department.empty();
      if (school_id in specializations) {
         for (var i = 0; i < specializations[school_id].length; i++) {
            $department.append($('<option>', {
               value: specializations[school_id][i].id,
               text: specializations[school_id][i].alias
            }));
         }
      }
   });
})
