app.controller('cartCtrl',['$scope', '$sce', 'product_data', function($scope, $sce, product_data){
	$scope.init = function(is_role_client)
	{
		// get data from window object
		$scope.arr_cart_data = window.cart_data;

		$scope.frm_discount = {};
		$scope.frm_discount.code = '';
		$scope.arr_result_discount = {};

		$scope.is_role_client = is_role_client;

		// try get data from twig
		// $scope.arr_cart_data = {{arr_cart_data|json_encode|raw}};

		// try get data from xhr
		// $scope.arr_cart_data = {};
		// $scope.getCartData();

		// $scope.message_one = '';
	}

	$scope.applyDiscountCode = function() {
		$scope.frm_discount._token = angular.element('#apply_coupon__token').val();
		if($scope.frm_discount.code){
			product_data.applyDiscountCode($scope.frm_discount).then(function onSuccess(response){
				$scope.arr_cart_data = response.data.arr_cart_data;
				$scope.arr_result_discount = response.data.arr_result;

				if($scope.arr_result_discount.status){
					$scope.frm_discount.code = '';
				}
				$scope.updateBoxCart();
			}).catch(function onError(response) {
			});
		}
	}

	$scope.removeDiscountCode = function() {
		product_data.removeDiscountCode().then(function onSuccess(response){
			$scope.arr_cart_data = response.data.arr_cart_data;
			$scope.updateBoxCart();
		}).catch(function onError(response) {
		});
	}

	$scope.getRouteProductDetail = function(product_id, product_slug, variant) {
		if (!Array.isArray(variant) || !variant.length) {
			// array does not exist, is not an array, or is empty
			var link_product_detail = Routing.generate('product_detail', { id:product_id, slug:product_slug });
		}else{
			var str_variant = encodeURI(variant.join("-"));
			var link_product_detail = Routing.generate('product_detail', { id:product_id, slug:product_slug, v:str_variant });
		}
		// slugify(product_title)
		return link_product_detail;
	}

	$scope.updateProductQuantity = function($event, quantity, product_id, sku_id, index, mode) {
		// var quantity_element = angular.element($($event.target).closest(".input-group").find(".error_msg").html('error msg'));
		quantity = parseInt(quantity);
		if(mode=='increase'){
			quantity++;
		}else if (mode=='decrease'){
			quantity--;
		}

		if(quantity>0){
			// openDomWindow();
			product_data.updateCart(quantity, product_id, sku_id).then(function onSuccess(response){
				$scope.arr_cart_data = response.data.arr_cart_data;
				$scope.arr_cart_data.products[index].error_msg = response.data.error_msg;
				$scope.updateBoxCart();
				// closeDomWindow();
			}).catch(function onError(response) {
				// closeDomWindow();
			});
		}
	}

	$scope.removeProduct = function(product_id, sku_id, index) {
		if (confirm("Remove from cart. Item(s) will be removed from order")){
			// openDomWindow();
			product_data.updateCart(0, product_id, sku_id).then(function onSuccess(response){
				$scope.arr_cart_data = response.data.arr_cart_data;
				$scope.updateBoxCart();
				// closeDomWindow();
			}).catch(function onError(response) {
				// closeDomWindow();
			});
		}
	}

	$scope.getCartData = function() {
		product_data.getCart().then(function onSuccess(response){
			$scope.arr_cart_data = response.data.arr_cart_data;
		}).catch(function onError(response) {
		});
	}

	$scope.updateBoxCart = function() {
		if($scope.arr_cart_data.summary.total){
			angular.element($( "#cart_summary_item_count" ).html('<span class="cart-item">'+$scope.arr_cart_data.summary.item_count+'</span>'));
			angular.element($( "#cart_summary_item_count_sub" ).html($scope.arr_cart_data.summary.item_count));
			if(!$scope.is_role_client){
				angular.element($( "#cart_summary_total" ).html("- ฿"+numeral( $scope.arr_cart_data.summary.total).format('0,0.00') ));
			}
		}else{
			//remove all product in cart
			angular.element($( "#cart_summary_item_count" ).html(''));
			angular.element($( "#cart_summary_item_count_sub" ).html(''));
			angular.element($( "#cart_summary_total" ).html(''));
		}

		if($scope.arr_cart_data.products){
			angular.element($( "#cart_list_products_item" ).html(''));
			$scope.arr_cart_data.products.forEach(function(element) {
				updateCartBoxData(element, $scope.is_role_client);
			});
		}
	}

	// // // Listen to 'updateDeliveryInformationEvent' coming from controller deliveryAddress
	$scope.$on('updateDeliveryInformationEvent', function(event, data){
		$scope.arr_cart_data.delivery_information = data.delivery_information;
		$scope.arr_cart_data.summary = data.summary;
	});
	// // // Listen to 'updatePaymentEvent' coming from controller paymentCtrl
	$scope.$on('updatePaymentEvent', function(event, data){
		$scope.arr_cart_data.summary = data.summary;
	});

	// // // Send an event all the way down.
	// $scope.broadcastEvent = function(){
	// 	$scope.$broadcast('broadcastEvent', {message: 'Hello!'});
	// }

}]);
