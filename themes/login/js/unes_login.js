$(document).ready(function(){
// ==========================MULTIpost start here by Arisal Yanuarafi
$("#multiform").submit(function(e)
{
		$("#multi-msg").html("<img src='themes/unes/img/ajax-loaders/ajax-loader-7.gif'/>");

	var formObj = $(this);
	var formURL = formObj.attr("action");

if(window.FormData !== undefined)  // for HTML5 browsers
//	if(false)
	{
	
		var formData = new FormData(this);
		$.ajax({
        	url: formURL,
	        type: 'POST',
			data:  formData,
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					$("#multi-msg").html(data);
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				$("#multi-msg").html("<div class=\"box-content alerts\"><div class=\"alert alert-error\"><strong>Akses Gagal</strong> coba periksa kembali koneksi Anda !</code></pre>");
	    	} 	        
	   });
        e.preventDefault();
   }
   else  //for olden browsers
	{
		//generate a random id
		var  iframeId = 'unique' + (new Date().getTime());

		//create an empty iframe
		var iframe = $('<iframe src="javascript:false;" name="'+iframeId+'" />');

		//hide it
		iframe.hide();

		//set form target to iframe
		formObj.attr('target',iframeId);

		//Add iframe to body
		iframe.appendTo('body');
		iframe.load(function(e)
		{
			var doc = getDoc(iframe[0]);
			var docRoot = doc.body ? doc.body : doc.documentElement;
			var data = docRoot.innerHTML;
			$("#multi-msg").html(data);
		});
	
	}

});


$("#multi-post").click(function()
	{
	$("#multiform").submit();
});
$('input[type=text]').on('keyup', function(e) {
    if (e.which == 13) {
        $("#multiform").submit();
    }
});

// ==========================multi post
});