define([
    "jquery",
    "jquery/ui",
    'Magento_Catalog/js/catalog-add-to-cart',
    'Magento_Catalog/product/view/validation',
], function ($, ui, mage_addtocart, validation) {

    $.widget('mage.amCart', {
        options: {},
        progressSelector: '#amprogress',
        imageWrapperSelector:    'div.fotorama__active, .product-image-wrapper',
        formParentSelector: '.product-item, .product.info, .item, .main',
        topCartSelector:  '[data-block="minicart"]',
        addToCartButtonDisabledClass: 'disabled',
        addToCartButtonSelector: '.action.tocart',

        _create: function (options) {
            var self = this;
            if (this.element.prop("tagName") == "BUTTON" || this.element.prop("tagName") == "A") {
                this._createButtonObserve(this.element);
            }
            else{
                this.element.unbind( "submit" );
                this.element.on('submit', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    e.stopPropagation();
                    var validator = $(this).validation({ radioCheckboxClosest: '.nested'});
                    if (validator.valid()) {
                        self.submitForm($(this));
                    }
                });
            }
        },

        _createButtonObserve: function(element){
            element.unbind( "click" );
            var self = this;
            element.on('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();

                var dataPost = element.attr('data-post');
                dataPost = jQuery.parseJSON(dataPost);
                var form =  $('<form />', { action: dataPost.action, method: 'POST' });

                $.each( dataPost.data, function( key, value ) {
                    form.append(
                        $('<input />', {name: key, type: 'hidden', value: value })
                    );
                });
                form.append(
                    $('<input />', {name: 'form_key', type: 'hidden', value: $('input[name="form_key"]').val() })
                );
                var parent = element.closest(self.formParentSelector);
                if(parent.find('input[name^="qty"]').length){
                    form.append(
                        parent.find('input[name^="qty"]').clone().hide()
                    );
                }

                parent.append(form);

                self.submitForm(form);
            });
        },

        submitForm: function(form) {
            var self = this;
            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        submitFormInPopup: function() {
            var form = $('#confirmBox form');
            if (form.length) {
                var validator = form.validation({ radioCheckboxClosest: '.nested'});
                if (validator.valid()) {
                    this.submitForm(form);
                }
            }
        },

        ajaxSubmit: function(form) {
            $('#confirmOverlay').fadeOut(function() {
                $(this).remove();
            });
            var self = this;
            $(this.topCartSelector).trigger('contentLoading');
            self.disableButton(form);

            var data = form.serialize();
            data += '&product_page=' + $('body').hasClass('catalog-product-view');
            /*
            * add data from swatches
            * */
            if(form.find('input[name="product"]').length) {
                var input = form.find('input[name="product"]')[0];
                var productId = $(input).val();
                if(productId && $('.swatch-opt-' + productId).length){
                    var swatchesData = '&' + $('.swatch-opt-' + productId + ' :input').serialize();
                    if (swatchesData.indexOf("''") == -1
                        && swatchesData.indexOf("=&") == -1
                    ) {
                        data += swatchesData;
                    }
                }
            }

            if (form.attr('action').length) {
                var idProduct = form.attr('action').match(/product(.*?)uenc/);
                if(idProduct) {
                    idProduct = idProduct[0].replace(/[^\d]/gi, '');
                    if(parseInt(idProduct) > 0) {
                        data += '&product=' + idProduct;
                    }
                }

            }

            var url = self.options['send_url'];
            if(form.attr('action').indexOf('wishlist/index/cart') > 0){
                url = form.attr('action').replace('wishlist/index/cart', 'amasty_cart/wishlist/cart');
            }

            $.ajax({
                url: url,
                data: data,
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    self.showAnimation(form);
                },
                success: function(response) {
                    self.hideAnimation();
                    if (response.error) {
                        alert(response.error);
                    }
                    else{
                        try {
                            self.confirm({
                                'title'      : response.title,
                                'message'    : response.message,
                                'checkout'   : response.checkout,
                                'buttons'    : {
                                    '1'    : {
                                        'name'  :  response.b1_name,
                                        'class' : 'am-btn-left',
                                        'action': function() {
                                            if (response.b1_action.indexOf('document.location') > -1 && window.parent.location != window.location) {
                                                response.b1_action = response.b1_action.replace('document.location', 'window.parent.location');
                                            }
                                            eval(response.b1_action);
                                        }
                                    },
                                    '2'    : {
                                        'name'  :  response.b2_name,
                                        'class' : 'am-btn-right',
                                        'action': function() {
                                            if (response.b2_action.indexOf('document.location') > -1 && window.parent.location != window.location) {
                                                response.b2_action = response.b2_action.replace('document.location', 'window.parent.location');
                                            }
                                            eval(response.b2_action);
                                        }
                                    },
                                    '3'    : {
                                        'name'  :  response.b3_name,
                                        'class' : 'am-btn-right-checkout',
                                        'action': function() {
                                            if (response.b3_action.indexOf('document.location') > -1 && window.parent.location != window.location) {
                                                response.b3_action = response.b3_action.replace('document.location', 'window.parent.location');
                                            }
                                            eval(response.b3_action);
                                        }
                                    }
                                }
                            });
                            
                            var popup = $('#messageBox');
                            var maxHeight = 0.65 * $(window).height();
                            popup.css({
                                overflowY : 'auto',
                                maxHeight : maxHeight + 'px'
                            });

                            eval(response.script);
                            if (popup.find('.related').length) {
                                popup.find('button.tocart').each(function(i, button){
                                    self._createButtonObserve($(button));
                                })

                            }
                        } catch(e) {
                            console.debug(e);
                        }
                    }
                }
            }).always(function() {
                self.enableButton(form);
            });

            return false;
        },


        showAnimation: function(form) {
            var foundImage = false;
            var loadingType = this.options['type_loading'];

            if (loadingType != 0) {
                try {
                    var parent = form.closest(this.formParentSelector);
                    var wrapper = parent.find(this.imageWrapperSelector);
                    wrapper = $(wrapper[0]);
                    var image = wrapper.find('img');
                    var topCart = $(this.topCartSelector);
                    if (image.length && topCart.length) {
                        image = $(image[0]);
                        foundImage = true;

                        var container = $('<div />', {
                            id: "am_loading_container",
                            css: {
                                position: 'absolute',
                                zIndex: '99919',
                                top: 0,
                                left: 0
                            }
                        });

                        container.append(image.clone());
                        wrapper.append(container);

                        var posImage = image.offset();
                        var posLink = topCart.offset();

                        $('body').append(
                            container.css({
                                top: posImage.top,
                                left: posImage.left
                            })
                        );

                        container.animate({
                            opacity: 0.15,
                            left: posLink.left + 'px',
                            top: posLink.top + 'px',
                            width: 0,
                            height: 0
                        }, 1500, function () {
                            container.remove();
                        });
                    }
                }
                catch (ex) {
                    foundImage = false;
                }
            }

            if (loadingType == 0 || !foundImage) {
                    var progress = $('<div />', { id: "amprogress" });
                    var container = $('<div />', { id: "amimg_container" });

                    container.appendTo(progress);

                     var img = $('<img />');
                    img.attr('src', this.options['src_image_progress']);
                    img.appendTo(container);

                    container.width('150px');
                    var width = container.width();
                    width = "-" + width/2 + "px" ;
                    container.css("margin-left", width);
                    progress.hide().appendTo( $('body') ).fadeIn();
            }
        },

        hideAnimation: function() {
            if ($(this.progressSelector).length) {
                $(this.progressSelector).fadeOut(function() {
                    $(this).remove();
                });
            }
        },

        //run every second while time !=0
        oneSec: function() {
            var elem= $('#confirmButtons button:last-child');
            var value = elem.text();
            var sec = 1000;//parseInt(value.replace(/\D+/g,""));
            if (sec) {
                value =  value.replace(sec, sec-1);
                elem.text(value);
                if (sec <= 1) {
                    clearInterval(document.timer);
                    elem.click();
                }
            }
            else{
                clearInterval(document.timer);
            }
        },


        confirm: function(params) {
            if ($('#confirmOverlay').length > 0) {
                // A confirm is already shown on the page:
                return false;
            }

            var buttonHTML = params.checkout? params.checkout: '';
            $.each(params.buttons,function(name,obj) {

                // Generating the markup for the buttons:

                buttonHTML += '<button class="'+obj['class']+'"><span><span>'+obj['name']+'</span></span></button>';

                if (!obj.action) {
                    obj.action = function() {};
                }
            });

            var confirmOverlay = $('<div />', {
                id: "confirmOverlay"
            });

            var hideDiv = $('<div />', {
                id: "hideDiv"
            });
            hideDiv.appendTo(confirmOverlay);

            var confirmBox = $('<div />', {
                id: "confirmBox"
            });
            switch(this.options['align']) {
                case "1":
                    confirmBox.css('top', '130px');
                    confirmBox.css('left', '50%');
                    break;
                case "2":
                    confirmBox.css('top', '130px');
                    confirmBox.css('left', '230px');
                    break;
                case "3":
                    confirmBox.css('top', '130px');
                    confirmBox.css('right', '0px');
                    break;
                case "4":
                    confirmBox.css('top', '30%');
                    confirmBox.css('left', '230px');
                    break;
                case "5":
                    confirmBox.css('top', '30%');
                    confirmBox.css('right', '0px');
                    break;
                case "0":
                default:
                    confirmBox.css('top', '30%');
                    confirmBox.css('left', '50%');
            }
            confirmBox.appendTo(confirmOverlay);

            var confirmButtons = $('<div />', {
                id: "confirmButtons"
            });
            confirmButtons.html(buttonHTML);
            confirmButtons.appendTo(confirmBox);
            
            
            var closeButton = $('<a class="close-box" href="javascript:void(0);">close</a>', {
                id: "close-button"
            });
            closeButton.appendTo(confirmBox);
            closeButton.click(function() {  self.confirmHide();});
            var messageBox = $('<div />', {
                id: "messageBox"
            });
            messageBox.html(params.message);
            messageBox.insertBefore(confirmButtons);

            var title = $('<h1 />');
            title.html(params.title);
            title.insertBefore(messageBox);

            confirmOverlay.hide().appendTo($('body'));

            confirmOverlay.fadeIn();
            var conWidth = confirmBox.width()/2;
            if (conWidth < 160) conWidth = 230;
            confirmBox.css('marginLeft', -conWidth);
            var self = this;
            hideDiv.click(function() {  self.confirmHide();});
            var buttons = $('#confirmButtons button'),
                i = 0;

            $.each(params.buttons,function(name,obj) {
                buttons.eq(i++).click(function() {
                    obj.action();
                    return false;
                });
            });
            this.confirmTimer();
        },

        confirmTimer: function() {
            var elem= $('#confirmButtons button:last-child');
            var value = elem.text();
            var sec = parseInt(value.replace(/\D+/g,""));
            if (sec) {
                var self = this;
                document.timer = setInterval(function() {
                    self.oneSec();
                }, 1000);
            }
        },

        confirmHide: function() {
            $('#confirmOverlay').fadeOut(function() {
                $(this).remove();
            });
        },


        disableButton: function(form) {
            var addToCartButton = $(form).find(this.addToCartButtonSelector);
            addToCartButton.addClass(this.addToCartButtonDisabledClass);
        },

        enableButton: function(form) {
            var self = this,
                addToCartButton = $(form).find(this.addToCartButtonSelector);

            addToCartButton.removeClass(self.addToCartButtonDisabledClass);
        }
    });

    return $.mage.amCart;
});
