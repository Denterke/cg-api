$(function(){
   $('#choose_item').change(function(){
      select_plugin_callback($(this).val());
   });
   $('#choose_item').change();
});