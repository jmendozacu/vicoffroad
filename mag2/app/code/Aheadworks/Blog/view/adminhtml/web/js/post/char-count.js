define([
    'jquery',
    'jquery/ui'
], function($) {
    'use strict';

    $.widget("awblog.blogPostMetaCharCount", {
        _create: function() {
            this.noteElement = $(this.options.noteElement);
            this.destElement = $(this.options.charCountDestElement);
            this._bind();
            this.inputChange();
        },
        _bind: function() {
            this._on({'input': this.inputChange});
        },
        inputChange: function() {
            this.destElement.html(this.element.val().length);
            if (this.element.val().length > this.options.warningLength) {
                this.noteElement.addClass('warning');
            } else {
                this.noteElement.removeClass('warning');
            }
        }
    });

    return $.awblog.blogPostMetaCharCount;
});
