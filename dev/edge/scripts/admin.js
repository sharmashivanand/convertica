jQuery(document).ready(function($) {
	if(typeof vars !== 'undefined' && vars.font_family_fields){

		selector_array = JSON.parse(vars.font_family_fields);
		all_fonts = JSON.parse(vars.all_fonts);
		//alert(selector_array);
		jQuery.each(selector_array,function(key, value){
			//alert('key'+key);
			//alert('value'+value);
			//jQuery("."+key).chosen();

			jQuery( "[data-customize-setting-link='"+key+"']" ).chosen({width:"100%"});
			jQuery("[data-customize-setting-link='"+key+"']").on('change', function(evt, params) {
					selected_font = params.selected;
					//alert(selected_font);
					//lander_fonts = JSON.parse(lander.lander_fonts);
					//options = get_font_variant_html(lander_fonts[selected_font].font_weights);
					//jQuery("."+value).empty().append(weightoptions)
					
					variant_field = "[data-customize-setting-link='"+key.replace('_family','_variant')+"']";

					options = convertica_get_font_variant_html(all_fonts[selected_font].font_weights);
					jQuery(variant_field).empty().append(options);
					jQuery(variant_field).trigger("change");
					//alert(variant_field.value);
				});

		});
		
	}
	
	/* Mobile Settings global option toggle */
	
	if( jQuery( "#mobile_use_global" ).is( ':checked' ) ) {
		jQuery( "#mobile-lp-options" ).css( "display", "none" );
	}
	else {
		jQuery( "#mobile-lp-options" ).css( "display", "block" );
	}
	
	jQuery( "#mobile_use_global" ).change(function() {
		var ischecked = this.checked;
		if(ischecked) {
			jQuery( "#mobile-lp-options" ).css( "display", "none" );
		}
		else{
			jQuery( "#mobile-lp-options" ).css( "display", "block" );
		}
	});
	
	/* Footer Widgets Settings option toggle */
	
	if( jQuery( "#convertica-theme-settings-convertica_footer_widgets option:selected" ).val() == 0 ) {
		jQuery( "#convertica-conditional-hide" ).css( "display", "none" );
	}
	else {
		jQuery( "#convertica-conditional-hide" ).css( "display", "block" );
	}
	
	jQuery( "#convertica-theme-settings-convertica_footer_widgets" ).change(function() {
		var isselected = jQuery(this).val();
		if(isselected == 0) {
			jQuery( "#convertica-conditional-hide" ).css( "display", "none" );
		}
		else{
			jQuery( "#convertica-conditional-hide" ).css( "display", "block" );
		}
	});
	
	jQuery('#convertica-theme-settings-convertica_gfonts_key').mouseover(function() {
		
		if(jQuery('#convertica-theme-settings-convertica_gfonts_key').val().length != 0) {
			jQuery('#convertica-theme-settings-convertica_gfonts_key').removeClass('validated').addClass('validating');

			jQuery.ajax({
				method: 'GET',
				cache: false,
				url: 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key='+jQuery('#convertica-theme-settings-convertica_gfonts_key').val(),
				dataType: 'json',
				success: function(data){
					jQuery('#convertica-theme-settings-convertica_gfonts_key').removeClass('validating valid invalid empty').addClass('validated valid');
				},
				error: function(xhr, type, exception) { 
					// if ajax fails display error alert
					//alert("Oops... ajax error response type "+type);
					jQuery('#convertica-theme-settings-convertica_gfonts_key').removeClass('validating valid invalid empty').addClass('validated invalid');
				}
			});
		}
		else {
			jQuery('#convertica-theme-settings-convertica_gfonts_key').addClass('empty');
		}
		
	});

});

function convertica_get_font_variant_html(weights) {
	weightoptions = '';
	if(weights === 'undefined'){
		weightoptions += "<option value=\""+400+"\">"+400+"</option>";
	}
	else {
		jQuery.each(weights,function(key, value){
			selected = (value == 'regular')? "selected=\"selected\"":'';
			//weightoptions += "<option "+selected+" value=\""+value+"\">"+value+"</option>";
			weightoptions += "<option value=\""+value+"\">"+value+"</option>";
		})
	}
	
	return weightoptions;
}