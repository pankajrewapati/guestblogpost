var loadFile = (event) =>  {
    var output = document.getElementById('feature_image_show');
	var uploadimg = extension = '';

	if(typeof(event.target.files[0]) != "undefined" && event.target.files[0] !== null) {
	    jQuery('.feature_img_div').addClass('showimg');
	    output.src = URL.createObjectURL(event.target.files[0]);

	    var fileInput = document.getElementById('feature_image');
	    var reader = new FileReader();
		reader.readAsDataURL(fileInput.files[0]);

		extension = fileInput.files[0].name.split('.').pop().toLowerCase();
		reader.onload = function () {
			uploadimg = reader.result;
			jQuery('#feature_image_hidden').val(uploadimg);
			jQuery('#feature_image_extension').val(extension);
		};

	    output.onload = function() {
	      URL.revokeObjectURL(output.src) // free memory
	    }
	}

}

jQuery("#addblog_form").submit(function(e) {
    e.preventDefault();     
	var uploadimg = jQuery('#feature_image_hidden').val();
	var extension = jQuery('#feature_image_extension').val();
	var formdata = jQuery(this).serialize();


	jQuery.ajax({ 
	    type: 'POST',
	    url: my_ajax_object.ajaxurl,
	    data: {'action': 'addBlogAjax',formdata:formdata,uploadimg:uploadimg,extension:extension},
	    success: function(response) {
	         alert('Added Successfully!');
	         location.reload();
	    },
	    error: function(xhr, status){
	         alert('Please try again!');
	     }
	});
	console.log('uploadimg',uploadimg);
});

jQuery(document).on('click','.remove_img',function(){
	jQuery('#feature_image_hidden').val();
	jQuery('.feature_img_div').removeClass('showimg');
	jQuery('#feature_image_show').attr('src','');
});

jQuery(document).on('click','.approve',function(){
	var data_type = jQuery(this).attr('data');
	var post_id = jQuery(this).attr('post_id');
	var thisvar = jQuery(this);
	jQuery.ajax({ 
	    type: 'POST',
	    url: my_ajax_object.ajaxurl,
	    data: {'action': 'approvedUnApproved',data_type:data_type,post_id:post_id},
	    success: function(response) {
	    	console.log('response',response);
	    	if(response == 'publish'){
	    		jQuery(thisvar).text('Un Approved');
	    		jQuery(thisvar).addClass('approvdone');
	    		jQuery(thisvar).removeClass('approvpending');
	    		jQuery(thisvar).attr('data','unapproved');
	    	}else{
	    		jQuery(thisvar).text('Approved');
	    		jQuery(thisvar).removeClass('approvdone');
	    		jQuery(thisvar).addClass('approvpending');
	    		jQuery(thisvar).attr('data','approved');
	    	}

	    },
	    error: function(xhr, status){
	         alert('Please try again!');
	     }
	});
});

jQuery(document).on('click','.mypagination li',function(){

	

	jQuery('.mypagination li').removeClass('active');
	jQuery(this).addClass('active');
	var pn = jQuery(this).attr('pn');
	var post_per_page = jQuery('#post_per_page').val();
	jQuery.ajax({ 
	    type: 'POST',
	    url: my_ajax_object.ajaxurl,
	    data: {'action': 'getPaginationPostRecord',pn:pn,post_per_page:post_per_page},
	    success: function(response) {
	    	console.log('response',response);
	    	jQuery('.addblog-form').html(response);

	    	setTimeout(function(){
		    	jQuery('html, body').animate({
			        scrollTop: jQuery("#outer_div").offset().top
			    }, 50);
	    	},100)

	    },
	    error: function(xhr, status){
	         alert('Please try again!');
	     }
	});

	
});


function disallowhtml(fieldid){
 var textVal= jQuery('#'+fieldid).val();
   var result=(/<img.*|<script.*|<style.*|<embeded.*/ig).test(textVal);
   	if(result) {
	    var ErrorText ='do not allow Scriot';
	    jQuery('#'+fieldid).val('');
	    alert(ErrorText);
	    return false;
	}	
}