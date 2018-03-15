/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'mageUtils',
    'uiRegistry',
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/modal/confirm'
], function (_, utils, registry, Column, confirm) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'amasty_oaction/grid/shipping',
            sortable: false,
            draggable: false,
            actions: [],
            rows: [],
            rowsProvider: '${ $.parentName }',
            fieldClass: {
                'data-grid-actions-cell': true
            },
            templates: {
                actions: {}
            },
            imports: {
                rows: '${ $.rowsProvider }:rows'
            },
            listens: {
                rows: 'updateActions'
            }
        },

        /**
         * Overrides base method, because this component
         * can't have global field action.
         *
         * @returns {Boolean} False.
         */
        hasFieldAction: function () {
            return false;
        },

        preselectCheckbox: function (row) {
            var id = row['entity_id'];
            if (id) {
                var checked = jQuery('#check' + id).prop('checked');
                if (!checked) {
                    jQuery('#check' + id).click()
                }
            }
        }
    });
});