define([
    'Magento_Ui/js/grid/columns/select'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Blog/ui/grid/cells/status'
        },
        getColorClass: function (record) {
            return record.status;
        }
    });
});
