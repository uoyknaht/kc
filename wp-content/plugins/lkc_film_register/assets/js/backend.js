jQuery(document).ready(function(){
	jQuery('.input-type-date').datepicker({ 
		dateFormat: "yy-mm-dd",
	    changeMonth: true,
	    changeYear: true		
	});
	jQuery(".chosen").chosen();

});