define([
    'M2ePro/Grid'
], function (modal) {

    window.EbayListingProductAddCategorySummaryGrid = Class.create(Grid, {

        // ---------------------------------------

        prepareActions: function () {
            this.actions = {
                removeAction: this.remove.bind(this)
            };
        },

        // ---------------------------------------

        remove: function () {
            if (!confirm(M2ePro.translator.translate('Are you sure?'))) {
                return;
            }

            var url = M2ePro.url.get('ebay_listing_product_add/removeSessionProductsByCategory');
            new Ajax.Request(url, {
                parameters: {
                    ids: this.getSelectedProductsString()
                },
                onSuccess: this.unselectAllAndReload.bind(this)
            });
        },

        // ---------------------------------------

        confirm: function () {
            return true;
        }

        // ---------------------------------------
    });
});