app.controller('paymentCtrl',['$scope', '$sce', 'product_data', function($scope, $sce, product_data){

	$scope.payment_init = function()
	{

	}

	$scope.validatePlaceOrder = function()
	{
		if($scope.arr_cart_data.summary.payment_option && $scope.arr_cart_data.delivery_information.shipping_address.id){
			return false;
		}else{
			return true;
		}
	}

	$scope.getPaymentOptionCode = function($event, code)
	{
		// var element = angular.element($($event.target).find('a') );
		// angular.element(element).triggerHandler('click');

		// $event.stopPropagation();
		// var element = angular.element($($event.target));

		//reset payment_option
		$scope.arr_cart_data.summary.payment_option = '';

		if(code){
			product_data.setPaymentOption(code).then(function onSuccess(response){
				if(response.data.arr_result.status){
					$scope.emitPaymentEvent(response.data.arr_cart_data.summary);
				}else{
					//error
				}

			}).catch(function onError(response) {
			});
		}
	}

	// // // Send an event all the way up.
	$scope.emitPaymentEvent = function(summary){
		$scope.$emit('updatePaymentEvent', {summary: summary});
	}
}]);
