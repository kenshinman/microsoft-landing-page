$(document).ready(function(){
	$('.next').on('click', function(e){
		e.preventDefault();
		var thisSlide = $(this).closest('li');
		var nextSlide = thisSlide.next();

		var product_id = $('#product-id').val();
		var serial_no = $('#serial-no').val();
		var error_div = $('.error');

		if(product_id && serial_no){

			thisSlide.addClass('animated bounce').removeClass('active');
			nextSlide.addClass('animated flipInY active');
		}else{
			error_div.show();
			error_div.html('<p class="red">Please fill both fields below</p>').addClass('animated shake');
			setTimeout(function(){
				error_div.fadeOut('slow')
			}, 5000)
			return false;
		}
	});

	$('#activation-form').on('submit', function(e){
		e.preventDefault();
		var spinner = $('.loader-container'),
			success_div = $('.success-div');


		spinner.show();
		var formData = {
			product_id: e.target.product_id.value,
			serial_no: e.target.serial_no.value,
			email: e.target.email.value,
			full_name: e.target.full_name.value
		}

		$.ajax({
			type: "POST",
			url: "scripts/insert.php",
			data: formData,
			dataType: "json",
			success: function (data) {
				if(data.status == "success"){
					console.log(data)
					e.target.product_id.value = "";
					e.target.serial_no.value = "";
					e.target.email.value = "";
					e.target.full_name.value = "";
					setTimeout(function(){
						success_div.show();
						$('.form-wrap').hide();
						spinner.hide();
					}, 5000);
					$('span#span-id').html(data.userId);
				}else if(data.status == 'error'){
					setTimeout(function(){
						spinner.hide();
					}, 500);
					var error_div = $('.error');
					error_div.show();
					error_div.html(data.error_reason).addClass('animated shake');
					setTimeout(function(){
						error_div.fadeOut('slow')
					}, 5000)
				}

			},
			error: function(data){
				console.log(data);
				setTimeout(function(){
					success_div.show();
					$('.form-wrap').hide();
					spinner.hide();
				}, 5000);


				return false;
			}

		  });
		//console.log(formData);
		return false;
	})
})
