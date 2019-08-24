// jQuery to collapse the navbar on scroll //
$(window).scroll(function() {

	if ($(".navbar").offset().top > 50) {
			$(".navbar-fixed-top").addClass("top-nav-collapse");
	} else {
			$(".navbar-fixed-top").removeClass("top-nav-collapse");
	}

	// HIDE MOBILE MENU AFTER CLIKING ON A LINK
	$('.navbar-collapse a').click(function(){
			$(".navbar-collapse").collapse('hide');
	});

});
