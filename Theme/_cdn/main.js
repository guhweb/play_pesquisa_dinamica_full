$(function(){
   
   $('.main_header_content_menu_mobile_obj').on('click', function(){
       $(this).toggleClass('main_header_content_menu_mobile_obj_active');
       $('.main_header_content_menu_mobile_sub').toggleClass('ds_none');
   });
});