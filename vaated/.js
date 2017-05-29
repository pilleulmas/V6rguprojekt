$(function() {
	$('#minuvorm').on('submit', function(e) {
		var data = $("#minuvorm :input").serialize();
		$.ajax({
			type: "POST",
			url: "galerii.php?page=makro",
			data: data,
		});
	e.preventDefault();
	});
});