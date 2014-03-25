(function($){
	var initLayout = function() {
		$( '#bgcolorSelector' ).ColorPicker({
			color: '#0000ff',
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#bgcolorSelector div').css('backgroundColor', '#' + hex);
				$('#template_bgcolor' ).val('#' + hex);
			}
		});
		
		$('#ftcolorSelector').ColorPicker({
			color: '#0000ff',
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#ftcolorSelector div').css('backgroundColor', '#' + hex);
				$('#template_ftcolor' ).val('#' + hex);
			}
		});
	};
	
	EYE.register(initLayout, 'init');
})(jQuery)