/*
 * imgZoom jQuery plugin
 * version 0.1
 *
 * Copyright (c) 2009 Michal Wojciechowski (odyniec.net)
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)

 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://odyniec.net/projects/imgzoom/
 *
 */

(function($) {

$.imgZoom = function (img, options) {
    var width, height, thumbWidth, thumbHeight, src,
        image,
        svgns = "http://www.w3.org/2000/svg",
        imgOfs, endX, endY, $bigImg = $('<img/>'),
        Z = $.imgZoom, M = Math,
        imgZoom = this,
        winWidth, winHeight,
        time,

        fx = $.extend($('<div/>')[0], { imgZoom: this, pos: 0 });

    function setOptions(newOptions) {
        if (!Z.loaded)
            return $(window).load(function () { setOptions(newOptions); });

        options = $.extend(options, newOptions);
        $('<img/>').attr('src', options.loadingImg);
        Z.$loading.css({
            backgroundImage: 'url(' + options.loadingImg + ')',
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat'
        });
    }

    function getImgOfs() {
        imgOfs = { left: M.round($(img).offset().left + ($(img).outerWidth() - thumbWidth) / 2),
                top: M.round($(img).offset().top + ($(img).outerHeight() - thumbHeight) / 2) };
    }

    function reset() {
        winWidth = $(window).width();
        winHeight = $.browser.opera && $.browser.version < 9.5 ?
            window.innerHeight : $(window).height();

        getImgOfs();

        Z.$overlay.css({
            backgroundColor: options.showOverlay ? '#000' : '',
            position: 'absolute',
            left: 0,
            top: 0,
            width: M.max(winWidth, document.documentElement.scrollWidth),
            height: M.max(winHeight, document.documentElement.scrollHeight),
            overflow: 'hidden'
        });

        if (Z.svg) {
            Z.svg.setAttribute('width', Z.$overlay.width());
            Z.svg.setAttribute('height', Z.$overlay.height());
        }
        else {
            $(Z.vmlGroup).css({
                width: Z.$overlay.width(),
                height: Z.$overlay.height()
            });
            Z.vmlGroup.coordsize = Z.$overlay.width() + ',' + Z.$overlay.height();
        }
    }

    function animate(pos) {
        var w = M.round(thumbWidth + (width - thumbWidth) * pos),
            h = M.round(thumbHeight + (height - thumbHeight) * pos),
            angle = M.round(pos * parseInt(options.rotate + 0) * 360),
            opacity = $.isArray(options.opacity) ?
                options.opacity[0] * (1 - pos) + pos * options.opacity[1] :
                options.opacity * (1 - pos) + pos,
            overlayOpacity = pos * options.overlayOpacity;

        fx.pos = pos;

        getImgOfs();

        var x = M.round(imgOfs.left * (1 - pos) + endX * pos),

            y = M.round(imgOfs.top * (1 - pos) + endY * pos);

        if (options.showOverlay)
            Z.$overlay.css('opacity', overlayOpacity);

        if (Z.svg) {
            image.setAttribute('width', w);
            image.setAttribute('height', h);
            image.setAttribute('x', x);
            image.setAttribute('y', y);

            if (options.rotate)
                image.setAttribute('transform', 'rotate(' + angle +
                        ',' + M.round(x + w/2) + ', ' + M.round(y + h/2) + ')');

            $(image).css('opacity', opacity);

            if ($.browser.safari) {
                var rect = document.createElementNS(svgns, "rect");
                rect.setAttribute('x', -Z.$overlay.width());
                rect.setAttribute('y', -Z.$overlay.height());
                rect.setAttribute('width', 3 * Z.$overlay.width());
                rect.setAttribute('height', 3 * Z.$overlay.height());
                rect.setAttribute('fill', 'none');
                $(Z.svg).append(rect);
                setTimeout(function () { $(rect).remove(); }, 0);
            }
        }
        else {
            $(image).css({
                width: w,
                height: h,
                left: x,
                top: y,
                opacity: opacity
            });

            if (options.rotate)
                image.style.rotation = angle;
        }
    }

    function zoom(duration, callback, out) {
        if (isNaN(duration))
            duration = options.duration;

        Z.animating++;

        if (!out) {
            Z.$anim.show();

            endX = M.round((winWidth - width) / 2 + $(document).scrollLeft());
            endY = M.round((winHeight - height) / 2 + $(document).scrollTop());

            if (options.hideThumbnail)
                $(img).css('visibility', 'hidden');

            if (Z.svg) {
                image.setAttributeNS("http://www.w3.org/1999/xlink", "href", src);

                $(Z.svg).append(image);
            }
            else {
                $(image).css('z-index', 1000);
            }
        }

        reset();
        $(image).show();

        $(fx).animate({ pos: out ? 0 : 1 }, duration, 'swing', function () {
            if (!out)
                $bigImg.css({
                    left: endX,
                    top: endY,
                    opacity: $.isArray(options.opacity) ? options.opacity[1] : 1
                })
                .click(function () {
                    Z.$anim.show();
                    $bigImg.remove();
                    zoomOut();
                })
                .appendTo('body')
                .show();
            else
                $(img).css('visibility', '');

            $(image).css('z-index', '').hide();

            Z.animating--;

            if ((!options.showOverlay || out) && !Z.animating)
                Z.$anim.hide();

            if (callback)
                callback.call();
        });
    };

    function doZoomIn(duration, callback) {
        if (Z.zoomed)
            Z.zoomed.zoomOut();

        zoom(duration, callback);
        Z.zoomed = imgZoom;
    }

    function zoomIn(duration, callback) {
        if (Z.zoomed == imgZoom)
            return;

        if (width != null)
            doZoomIn(duration, callback);
        else {
            Z.$loading.css({
                position: 'absolute',
                left: imgOfs.left,
                top: imgOfs.top,
                width: thumbWidth,
                height: thumbHeight
            }).appendTo('body').show();

            time = (new Date()).getTime();

            $bigImg.one('load', function () {
                    $bigImg.hide().appendTo('body');
                    width = $bigImg.width();
                    height = $bigImg.height();

                    time = (new Date()).getTime() - time;

                    if (Z.svg) {
                        Z.$loading.hide();
                        doZoomIn(duration, callback);
                    }
                    else {
                        image.src = src;

                        setTimeout(function () {
                            Z.$loading.hide();
                            doZoomIn(duration, callback);
                        }, time*2 + 50);
                    }
                })
                .attr('src', src)
                .css('position', 'absolute');
        }
    }

    function zoomOut(duration, callback) {
        if (Z.zoomed != imgZoom)
            return;

        Z.$anim.show();
        $bigImg.remove();

        zoom(duration, callback, true);

        if (Z.zoomed == imgZoom)
            Z.zoomed = null;
    }

    function init() {
        if (!Z.$overlay) {
            Z.$overlay = $('<div/>');
            Z.$loading = $('<div/>');
        }

        setOptions(options = $.extend({
            duration: 500,
            loadingImg: 'css/loading.gif',
            opacity: 1,
            overlayOpacity: .75
        }, options));

        src = options.src || $(img).parent().attr('href');

        thumbWidth = img.clientWidth;
        thumbHeight = img.clientHeight;

        if (window.SVGAngle) {
            if (!Z.svg) {
                $(Z.svg = document.createElementNS(svgns, "svg"))
                    .css({
                        position: 'absolute',
                        left: 0,
                        top: 0
                    });

                Z.$anim = Z.$overlay.add($(Z.svg));
            }

            $(image = document.createElementNS(svgns, "image")).hide();

            $(Z.svg).append(image);
        }
        else {
            if (!Z.vmlGroup) {
                document.createStyleSheet().addRule(".imgzoom-vml", "behavior:url(#default#VML)");

                if (!document.namespaces.izvml)
                    document.namespaces.add("izvml", "urn:schemas-microsoft-com:vml");

                Z.vmlElem = function (tagName) {
                    return document.createElement('<izvml:' + tagName + ' class="imgzoom-vml">');
                };

                $(Z.vmlGroup = Z.vmlElem('group'))
                    .css({
                        position: 'absolute',
                        left: 0,
                        top: 0
                    });

                Z.$anim = Z.$overlay.add($(Z.vmlGroup));
            }

            $(image = Z.vmlElem('image')).hide();

            $(Z.vmlGroup).append(image);
        }

        Z.$anim.hide().appendTo('body');

        reset();

        $(img).click(function () {

            if (!Z.animating)
                if (Z.zoomed == imgZoom)
                    zoomOut();
                else
                    zoomIn();

            return false;
        });

        Z.fxStepDefault = $.fx.step._default;

        $.fx.step._default = function (fx) {
            return fx.elem.imgZoom ? fx.elem.imgZoom.animate(fx.now) :
                Z.fxStepDefault(fx);
        };

        $(window).resize(reset);
    }

    this.setOptions = setOptions;
    this.animate = animate;
    this.zoomIn = zoomIn;
    this.zoomOut = zoomOut;
    this.init = init;
    if (Z.loaded)
        init();
    else
        $(window).load(this.init);
};

$.imgZoom.animating = 0;

$(window).load(function () {
    $.imgZoom.loaded = true;
});

$.fn.imgZoom = function (options) {
    options = options || {};

    this.filter('img').each(function () {
        if ($(this).data('imgZoom'))
            $(this).data('imgZoom').setOptions(options);
        else
            $(this).data('imgZoom', new $.imgZoom(this, options));
    });

    if (options.instance)
        return $(this).filter('img').data('imgZoom');

    return this;
};

})(jQuery);