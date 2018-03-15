require(['jquery'], function ($) {
    $(document).ready(function () {
        var widthwindow = $(window).width();
        /* page list */
        if ($('.sidebar-main').length) {
            $('body').addClass('page-layout-2columns-left');
        }
        if ($('.page-blog').length) {
            $('body').addClass('page-layout-blog');
        }
        if ($('.page-checkout').length) {
            $('body').addClass('page-layout-checkout');
        }
        if ($('.page-cart').length) {
            $('body').addClass('page-layout-cart');
        }
        $('.navigation li').children('.sub-menu').parent().addClass('parent');
        $('.navigation li.parent > a').click(function () {
            $(this).parent().toggleClass('active');
            $(this).parent().children('.sub-menu').slideToggle();
        });
        $('.product.info.detailed  .product-item').each(function () {
            if ($(this).children().children().children().children('.price-container').children().children('.price-was').html() == "") {
                $(this).children().children().children().children('.price-container').children().children('.price-was').parent().parent().addClass('price-save-hide');
            }
        });
        $('.products-grid .product-item').each(function () {
            if ($(this).children().children().children().children('.price-container').children().children('.price-was').html() == "") {
                $(this).children().children().children().children('.price-container').children().children('.price-was').parent().parent().addClass('price-save-hide');
            }
        });

        $(window).on("orientationchange load resize", function () {
            var widthwindow1 = $('.testimonial').width();
            var window_fle = widthwindow1 - 30;
            var flexisel = $(".nbs-flexisel-container");
            flexisel.css({"width": window_fle + "px"});


            if ($(window).width() > 768) {

                $('.page-layout-2columns-left .columns .categories-menu').show();

            } else if ($(window).width() <= 768) {
                /* page list */
                $('.page-layout-2columns-left .columns .refine').hide();
                $('.page-layout-2columns-left .columns .categories-menu').hide();
                $('.page-layout-2columns-left .columns .categories-menu.active').show();

                $('.catalog-category-view #maincontent .column.main').before($('.catalog-category-view #maincontent .sidebar.sidebar-main'));
                $('.catalog-category-view #maincontent .categories-menu').before('<div class="refine mobile"><h3>Refine By <a href="#" title="">Clear All</a></h3></div>');

                $('.page-layout-2columns-left .columns .refine').click(function () {
                    $(this).parent().toggleClass('active');
                    $('.page-layout-2columns-left .columns .categories-menu').toggleClass('active');
                    $('.page-layout-2columns-left .columns .categories-menu').slideToggle();
                });
            }
        });

        if (widthwindow < 768) {
            $('.page-header .nav-toggle').click(function () {
                $(this).toggleClass('active');
                $('.nav-sections').toggleClass('active');
                $('.nav-sections').slideToggle();
                $('.nav-sections .navigation').before($('.page-header .nav-toggle'));

                $('.nav-toggle.active').click(function () {
                    $('.page-header .logo').before($('.section-items .nav-toggle'));
                });
            });
            $('.header-top .minicart-wrapper').insertAfter('.header-main-right .header-form');
            $('.icon-search').click(function () {
                $('.block-search').slideToggle();
                $(this).toggleClass('active');
                $('.header-main').toggleClass('active');
            });
        }
        $(window).on("orientationchange load resize", function () {
            var width1 = $(window).width();
            $('.cart-summary #block-shipping h2').click(function () {
                $(this).parent().toggleClass('active');
                $('.cart-summary #block-shipping #block-summary').toggleClass('active');
                $('.cart-summary #block-shipping #block-summary').slideToggle();
            });

        });
        $(".testimonial ul").flexisel({
            visibleItems: 1,
            enableResponsiveBreakpoints: true,
            responsiveBreakpoints: {
                portrait: {
                    changePoint: 480,
                    visibleItems: 1
                },
                landscape: {
                    changePoint: 640,
                    visibleItems: 1
                },
                tablet: {
                    changePoint: 768,
                    visibleItems: 1
                }
            }
        });
        if ($(window).width() < 768)
        {
            $('.nav-sections-item-content > .navigation > ul').after('<ul class="utility-links">' + $('.header-top ul').html() + '</ul>');

        }

        //$('.block-static-block .toolbar .pages').before($('.block-static-block .toolbar .limiter'));
        

    });
});