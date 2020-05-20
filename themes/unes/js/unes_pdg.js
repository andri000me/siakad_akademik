$(document).ready(function(){
//ganti tombol
	$("input[type=button]").attr("class","btn bg-olive btn-flat margin");
    $("input[type=submit]").attr("class","btn bg-olive btn-flat margin");
    $("input[type=reset]").attr("class","btn bg-navi btn-flat margin");
    $("form").attr("autocomplete","off");
    $("select").attr("data-rel","chosen");
	$(".nones").attr("data-rel","");
	$('a[rel*=facebox]').facebox();
	//highlight current / active link
	$('ul.main-menu li a').each(function(){
		if($($(this))[0].href==String(window.location))
			$(this).parent().addClass('active');
	});
	
});
	

