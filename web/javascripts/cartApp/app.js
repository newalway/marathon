var app = angular.module('cartApp', []);

app.factory('product_data', ['$http', function($http){
	return {
		getCart: function(){
			return $http({
				method:'GET',
				url: Routing.generate('cart_get_item_cart', {}),
				headers: {
					'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});
		},
		updateCart: function(quantity, product_id, sku_id){
			return $http({
				method:'GET',
				// url: Routing.generate('api_1_get_public_update_cart', {quantity:quantity, product_id:product_id, sku_id:sku_id } ),
				url: Routing.generate('cart_update_item_cart', {quantity:quantity, product_id:product_id, sku_id:sku_id } ),
				headers: {
					'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});
		},
		applyDiscountCode: function(dataObj){
			return $http({
				method:'POST',
				// url: Routing.generate('api_1_post_public_apply_discount_code'),
				url: Routing.generate('cart_apply_discount_code'),
				data: $.param(dataObj),
				headers: {
					'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});
		},
		removeDiscountCode: function(){
			return $http({
				method:'POST',
				url: Routing.generate('cart_remove_discount_code'),
				headers: {
					'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});
		},

		updateCheckoutDeliveryAddress: function(mode, delivery_address_id){
			return $http({
				method:'GET',
				// url: Routing.generate('api_1_get_public_update_cart', {quantity:quantity, product_id:product_id, sku_id:sku_id } ),
				url: Routing.generate('checkout_update_delivery_address', {mode:mode, delivery_address_id:delivery_address_id } ),
				headers: {
					'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});
		},
		addCheckoutDeliveryAddress: function(dataObj){
			return $http({
				method:'POST',
				url: Routing.generate('checkout_add_delivery_address'),
				data: $.param(dataObj),
				headers: {
					'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});
		},
		setPaymentOption: function(code){
			return $http({
				method:'GET',
				url: Routing.generate('checkout_set_payment_option', {code:code} ),
				headers: {
					'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});
		},
	}
}]);
