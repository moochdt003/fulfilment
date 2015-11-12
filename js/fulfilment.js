/*
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
*/
/* 
    Created on : 27 Oct 2015, 3:48:58 PM
    Author     : David Muchatibaya
    Title      : Fulfilment Javascript
*/


jQuery(function($){
        
    //handles hiding/show effect of fullfilment items in the fulfiled orders ection    
       $(".hide_show").hide();
        $(".show_fulfilments").on("click", function () {
            
            $(this).closest('tr').next('tr').toggle();
        });
       
 
    
    selected_id = null;
        $('.edit_fulfilment').click(function(){
            selected_id = $(this).attr('id');
           $('input[name="fulfilmentID"]').val(selected_id);

        }); 

        $(".edit_fulfilment").click(function() {
            var id = $(this).data("id");
            
            
        });
   });