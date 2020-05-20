(function ($) {
	"use_strict";
	
	
	$(document).ready(function () {
		
		/**
		* Custom Product Row Action
		* */
		$(document).find('.make_as_default_action').click(function (e) {
			e.preventDefault();
			let _this = $(this),
				parent = _this.parents('tr'),
				default_column = parent.find('.column-default_product'),
				post_id = _this.attr('data-post_id'),
				remove_action_hide = "<style>" +
					"#post-"+post_id+" .row-actions span.trash { display: none !important;}" +
					"#post-"+post_id+" .check-column { pointer-events: none; opacity: 0.5; }" +
					"</style>",
				remove_action_show = "<style>" +
					"#post-"+post_id+" .row-actions span.trash { display: inline-block !important;}" +
					"#post-"+post_id+" .check-column { pointer-events: auto; opacity: 1;}" +
					"</style>";
			const unmark_icon = '<img' +
				' src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABmJLR0QA/wD/AP+gvaeTAAAD5klEQVRYhc3YXYhVVRQH8J+jEzUaheb4Ik3NSxBYYOlTnybTRKlFET5U9IEQGIpCkNPYQ2T2ZNNTEGESQkavWWqNBb3UU9I4OBopvQVR9EU4mtLDXtd75sw5955z7wj+YXM3a++91v/ufdZea22ucMybAx09WIOHMRiy0ziIo7jYrfJusBoTQeaWIHYm+gfxA1Z1aaNjrMd57MXSgvGl2IdzWNepkQUV512Du3FzrFmA3XgtfovwK57Fj/gYO/Cf9KfO4Buc7ZD3JVyLt/B3KDuBSZzCthp6tsWaydBxNnTuDhsdYQDH8ROeQl+FNb3R2mFh6DwdNgbqkuvDMckLr2szdwS3R/+daEK2o83a68PGMSUbUObFo0HscfxZMP4Q+qN/L56L/sJoQnZf9PsxXKDnDzwRREdKuMxCH37PGC3CEXwU/UexOfqDmnfh5hgTc4+00PcCfpOcsS3ukTwtv+XLMRT9O/BFFWWBL2ON0LE8N75Q8vC7qih7EVMF8qFQsrEGsTw2ho6hgrFT2JQXFn2D50vkRyTPu9AFwYuho+i451XVvUVy/QaGMY6VXRDLY2XofDAjmwzbLbEO/5p5Cd+AA9J3NFcYD51LMrLtYbs0LK6W4ma7u6sTLMF3eL7NvJHgMCvB6JG2eG9OvlP6XjZ0Se77aIsz8g2he2du/j7pE5vhB2sxbXZWsgJjKnwbFcgtyY1tCd0rcvJ+aRcfyArHpPxtLtGKXDt8jrdpbuNNOJmb1Is9eFczOtQh13CqtVKUyGMwdO8xO8GYklK7GedcJ/2/Fb9oRoe65GpjDJ/VmN+DD6SYnSXZzbFmcUja2Usoc5LblDtJnmRdclsVO8kyyUnW5I0dl1w8i1HpKlhfYiRLcqIGOZrXTD7N+lDBNUPzon61ooEsyffwbQ1yZRgNDneWTVgvhZvtGdnlCHVHlYe6R9ot3irVIQ1czmQhm2UXJgtFZec/ZqY9h6KR8rkL+KRDYk9Kn8QBuUiBq8L2DBTlfb2KnyuGsB/zOyTXsLdfccKqqu6qKf94DWLjWqf8i9RI+RtFU6vUKFs0Paa4aHopxmhfNG1So2iCXdLzxOKS8WHNsvOwdOHC+9GE7HD0y8pOYeNnvF6VHPUK9x1SxKGzwv0r6YKvvHsNDEjR4TSe1izIW6HO08cz0ilN4Ma65BpYJB33X1KsnpLuq5N4uYae7VL4mgwd06FzV9goRdUU62rJwwalHZovvXq9gTfbrB2V4u0r0h16TvP5bbqi/Y6wLozt03SYLJZJgb+rB8xusUo6unNStBmLdihkx7UI/FUwV4/o95v9iP4pvtblI/oVj/8Bdzvoz00s5FQAAAAASUVORK5CYII="/>';
			const mark_icon = '<img' +
				' src="data:image/png;base64,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"/>';
			
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: KT_Designer_APIAjaxUrl.ajaxURL,
				data: {
					action: "make_as_default_product",
					product_id: post_id
				},
				success: function (response) {
					if ( !response ) {
						_this.text('Unmark as Default');
						parent.append(remove_action_hide);
						default_column.html(mark_icon);
					} else {
						_this.text('Mark as Default');
						parent.append(remove_action_show);
						default_column.html(unmark_icon);
					}
				},
			});
		});
		// end ->> Custom Product Row Action
		
		
	});
	
})(jQuery);


