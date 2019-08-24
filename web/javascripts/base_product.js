function initJueryThailandEnTh(db, local, frm_id, dst_id, amp_id, pro_id, zip_id) {
	var data_form = $('#'+frm_id),
		obj_district = data_form.find("#"+dst_id),
		obj_amphoe = data_form.find("#"+amp_id),
		obj_province = data_form.find("#"+pro_id),
		obj_zipcode = data_form.find("#"+zip_id);

	$.ThaiAddressEnTh({
        lang: local,
        database: db,
        district: obj_district,
        amphoe: obj_amphoe,
        province: obj_province,
        zipcode: obj_zipcode
    });
}

function initJueryThailand(db, frm_id, dst_id, amp_id, pro_id, zip_id) {
	var data_form = $('#'+frm_id),
		obj_district = data_form.find("#"+dst_id),
		obj_amphoe = data_form.find("#"+amp_id),
		obj_province = data_form.find("#"+pro_id),
		obj_zipcode = data_form.find("#"+zip_id);

	$.Thailand({
		database: db,
		$district: obj_district,
		$amphoe: obj_amphoe,
		$province: obj_province,
		$zipcode: obj_zipcode,

		onDataFill: function(data){
			// console.info('Data Filled', data);
		},
		onLoad: function(){
			// console.info('Autocomplete is ready!');
			// $('#loader').toggle();
		}
	});

	obj_district.change(function(){
		// console.log('district', this.value);
	});
	obj_amphoe.change(function(){
		// console.log('amphoe', this.value);
	});
	obj_province.change(function(){
		// console.log('province', this.value);
	});
	obj_zipcode.change(function(){
		// console.log('zipcode', this.value);
	});
}

function updateCartBoxData(element, is_role_client){
	var link_product_detail = img_box_html = variant_option = title_amount_html = price_html = quantity_html = '';
	var newLiElement = angular.element('<li></li>');
	var TitleBoxEle = angular.element('<div class="text-box"></div>');

	if (!Array.isArray(element.variant_option) || !element.variant_option.length) {
		link_product_detail = Routing.generate('product_detail', { id:element.product_id, slug:element.slug });
	}else{
		var str_variant = encodeURI(element.variant_option.join("-"));
		link_product_detail = Routing.generate('product_detail', { id:element.product_id, slug:element.slug, v:str_variant });
	}

	// slugify(element.title)

	if(element.image_small){
		img_box_html = '<div class="img-box"><img src="'+element.image_small+'" alt="'+element.title+'" /></div>';
	}else{
		img_box_html = '<div class="img-box"><img src="/template/img/resources/header-cart-1.jpg" alt="'+element.title+'" /></div>';
	}

	if(element.variant_option.length>0){
		variant_option = element.variant_option.join(" · ");
	};

	title_amount_html = '<a href="'+link_product_detail+'"><h3>'+element.title+'</h3> '+variant_option+' </a>';
	if(is_role_client){
		price_html = '<span class="price"></span>';
	}else{
		price_html = '<span class="price">฿'+ numeral(element.amount).format('0,0.00') +'</span>';
	}

	TitleBoxEle.append(title_amount_html);
	TitleBoxEle.append(price_html);

	if(element.quantity>1){
		if(is_role_client){
			quantity_html = '<div class="review-box"> Qty: '+element.quantity+' </div>';
		}else{
			quantity_html = '<div class="review-box"> ฿<i>'+numeral(element.price).format('0,0.00')+'</i> (Qty: '+element.quantity+') </div>';
		}
		TitleBoxEle.append(quantity_html);
	}

	newLiElement.append(img_box_html);
	newLiElement.append(TitleBoxEle);
	angular.element($( "#cart_list_products_item" ).append(newLiElement));
}
