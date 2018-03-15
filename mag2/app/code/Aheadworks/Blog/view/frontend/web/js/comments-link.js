define([
    'jquery'
], function($) {
    'use strict';

    $.widget("awblog.awBlogCommentsLink", {
        _create: function() {
            this._bind();
        },
        _bind: function() {
            this._on({
                'click': function(event) {
                    $(location).attr('href', this.element.data('url'));
                    event.preventDefault();
                }
            });
        }
    });

    return $.awblog.awBlogCommentsLink;
});
