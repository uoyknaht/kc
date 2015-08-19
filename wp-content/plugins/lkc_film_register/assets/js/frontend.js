jQuery(document).ready(function(){
	
  jQuery('.film-register-search-form').delegate('.input-type-date', 'focusin', function(){
    jQuery(this).datepicker({ 
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      onClose: function() {
        jQuery('.input-type-date').valid();
      }   
    });
  });

  jQuery.extend(jQuery.validator.messages, {
    required: jsVars.validateMessageRequired,
    email: jsVars.validateMessageEmail,
    number: jsVars.validateMessageNumber
  })



  // jQuery('.film-register-search-form input').tipsy({trigger: 'focus', gravity: 'w'});

  // jQuery.validator.addMethod(
  //   "dateFormat", 
  //   function(val, el) {
  //     console.log(val)
  //     if(val == ''){
  //       return true;
  //     } else {
  //       // return val.match(/^\d\d\d\d?\/\d\d?\/\d\d$/);
  //       return val.match(/^\d\d\d\d-\d\d-\d\d$/);
  //     }
  //   },
  //   'Prašome įvesti data yyyy-mm-dd formatu'
  // );

  jQuery('.film-register-search-form').validate({
    // errorPlacement: function(error, el) {
    //   if(el.hasClass('input-type-date')) {
    //     var td = el.closest('td');
    //     if(td.find('label.error').length < 1){
    //       td.append(error); 
    //       error.show();
    //     }
    //   }
    // }
    errorPlacement: function(error, el) {
        var td = el.closest('td');
        //if(td.find('label.error').length < 1){
          td.append(error); 
          // error.show();
        //}
    },
    groups: {
      duration_from: "duration_from, duration_to"
    }
  });

  // jQuery.validator.addClassRules({
  //     'input-type-date': {
  //         dateFormat: true
  //     }
  // });


  // jQuery(".input-type-date").rules("add", { 
  //   dateFormat: true 
  // });

  // jQuery("input[name=duration_from], input[name=duration_to]").rules("add", { 
  jQuery("input[name=duration_from]").rules("add", { 
    number: true 
  });
  jQuery("input[name=duration_to]").rules("add", { 
    number: true 
  });






	jQuery('.film-register-search-results-header select').on('change', function(){
		jQuery(this).closest('form').submit();
	});

  jQuery( ".film-register-search-form input[name=title]" ).autocomplete({
    source: function(request, response){  
        jQuery.getJSON(jsVars.ajaxUrl + "?action=filmRegisterSearchAutocompleteRequest", request, function(data) {                          
            response(data);
    	});
    },
  	select: function(event, ui) {
  		if (typeof ui.item.permalink != 'undefined') { 
  			window.location = ui.item.permalink;
  			return false;
  		} 		
  	},
  	minLength: 3,
    search: function(event, ui) { 
      jQuery('.spinner').show();
    },
    response: function(event, ui) {
      jQuery('.spinner').hide();
    }
  });





  jQuery('#dynamic-search-fields-add-wrap a').on('click', function(){
    var $this = jQuery(this),
      $select = jQuery('#dynamic-search-fields-add-wrap select'),
      newOption = $select.val();

    if(newOption != 0){
      //console.log(newOption)

        jQuery.ajax({
          url: jsVars.ajaxUrl + "?action=getNewFieldRowRequest",
          type: 'GET',
          data: 'newOption='+newOption,
          success: function(data){
              jQuery('#dynamic-search-fields-add-wrap').before(data);
              $select.find('option[value="0"]').attr("selected",true);
              $select.find('option[data-name='+newOption+']').attr('disabled', 'disabled');
          },
          error: function(data){
              alert('Atsiprašome, dėl serverio problemų nepavyko atlikti veiksmo'); 
          }
        });


      // jQuery('#dynamic-search-fields-add-wrap').before('<tr><td>wgrg</td><td>ggr</td></tr>');
      
    }
    // console.log(newOption)
    return false;
  });

  jQuery('.film-register-search-form').delegate('.remove-row-btn', 'click', function(){
    var $this = jQuery(this);
    
    jQuery('#dynamic-search-fields-add-wrap option[data-name='+$this.data('name')+']').removeAttr('disabled');
    $this.closest('tr').remove();

    return false;
  });





});