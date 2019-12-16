define([
    'jquery',
    'jquery/ui',
], function ($) {
    "use strict";

    $(document).on("click", ".button", function () {
        $('.button').show();
        $(".swatch-opt").data("mageSwatchRenderer")._EmulateSelected($(this).data('attr'));
        $(this).hide();
        $(this).closest("tr").addClass("selected").siblings().removeClass("selected");
    });

    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {

            _OnClick: function ($this, $widget) {
                this._super($this, $widget);
                var isku = $widget.options.jsonConfig.sku[this.getProduct()];

                if (isku !== '') {
                    $('[itemprop="sku"]').html(isku);
                    this.updateVariationInfo();
                }

                if ($widget.element.parents($widget.options.selectorProduct)
                    .find(this.options.selectorProductPrice).is(':data(mage-priceBox)')
                ) {
                    $widget._UpdatePrice();
                }

                $widget._loadMedia();
            },

            _OnChange: function ($this, $widget) {
                this._super($this, $widget);
                this.updateVariationInfo();
            },

            updateVariationInfo: function () {
                var products = this._CalcProducts() || [],
                    price = this.element.closest('.product-info-main'),
                    variations = $('.table tr'),
                    sku = $('[itemprop="sku"]').text(),
                    curTR = $("." + sku);
                $('.button').show();
                if (products.length === 1) {
                    curTR.find('button').hide();
                    curTR.addClass("selected").siblings().removeClass("selected");
                } else {
                    variations.removeClass("selected");
                }
            },
        });

        return $.mage.SwatchRenderer;
    };
});
