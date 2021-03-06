
jQuery(document).ready(function($){

	var target = $('#target-currency').val();
	var rate = 0;
	getRate();

	$('#target-currency').on('change', function(event) {
		target = $('#target-currency').val();
		getRate();
	});
	$('#amount').on('keyup', function() {
		calculate();
	});

	function getRate() {

		$.post( '', {'target': target}, function(data) {
				if (data.success) {
					rate = roundNumber(data.rate, 6);
					calculate();
				} else {
					console.log(data);
					rate = 0;
				}
				$('#rate').val(rate);
			},
			'json'
		);
	}

	function calculate() {
		let amount = $('#amount').val();
		if (rate>0 && amount>0) {
			$('#result').html('Convert USD to ' + target + ' rate: ' + rate + ' Result amount: ' + roundNumber(amount*rate, 2));
		} else {
			$('#result').html('');
		}
	}

	function roundNumber(number, decimal) {
		return Math.round(number * Math.pow(10, decimal)) / Math.pow(10, decimal);
	}
});
