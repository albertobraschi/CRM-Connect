/* 
 * CRM Connect
 * by Bryce Hamrick
 *
 * crmconnect_admin_re.js
 *
 * This file initializes the drag and drop functionality
 * of the Registration Form admin page
 */

jQuery(function($) {
	$( ".crmconnect_module_fields, .crmconnect_form_fields" ).sortable({
		items: "li.sortable",
		connectWith: ".connectedSortable"
	}).disableSelection();

	var heightA = $(".crmconnect_form_fields").height();
	var heightB = $(".crmconnect_module_fields").height();
	if(heightA>heightB){
		$(".connectedSortable").height(heightA);
	} else {
		$(".connectedSortable").height(heightB);
	}

	$("#crmconnect_reg_update #username_select, #crmconnect_reg_update #email_select").change(function(){
		var className = $(this).attr("id") + "_hidden";
		var fieldName = $(this).val();
		$("#crmconnect_reg_update li." + className).removeClass(className);
		if(fieldName.length>0)
			$("#" + fieldName).addClass(className).appendTo(".crmconnect_module_fields");
	}).each(function(){
		var className = $(this).attr("id") + "_hidden";
		var fieldName = $(this).val();
		if(fieldName.length>0)
			$("#" + fieldName).addClass(className).appendTo(".crmconnect_module_fields");
	});

	$("#crmconnect_reg_update").submit(function(){
		var regForm = new Object();
		var username_select = $("#crmconnect_reg_update #username_select").val()
		var email_select = $("#crmconnect_reg_update #email_select").val()
		if(username_select.length>0)
			regForm.username_select = username_select;
		if(email_select.length>0)
			regForm.email_select = email_select;

		regForm.fields = [];
		$("#crmconnect_reg_update .crmconnect_form_fields .sortable").each(function(){
			regForm.fields.push($(this).attr("id"));
		});
		
		var encoded = JSON.stringify(regForm);
		$("#crmconnect_fields").val(encoded);
		return true;
	});
});