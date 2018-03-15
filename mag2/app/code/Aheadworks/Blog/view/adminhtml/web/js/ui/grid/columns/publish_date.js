define([
    'Magento_Ui/js/grid/columns/date'
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
