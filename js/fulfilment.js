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
       $(".hide_show").hide();
        $(".show_fulfilments").on("click", function () {
            
            $(this).closest('tr').next('tr').toggle();
        });
       
    });