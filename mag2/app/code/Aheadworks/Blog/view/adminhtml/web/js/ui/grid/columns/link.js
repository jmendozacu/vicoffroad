define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Blog/ui/grid/cells/link'
        },
        hasLink: function(row) {
            return row[this.index + '_url'];
        },
        getPlainText: function(row) {
            return row[this.index];
        },
        getLinkText: function(row) {
            return row[this.index + '_text'];
        },
        getLinkHint: function(row) {
            return row[this.index + '_hint'];
        },
        getLinkUrl: function(row) {
            return row[this.index + '_url'];
        }
    });
});
