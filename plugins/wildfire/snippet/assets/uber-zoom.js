/*! Copyright (c) 2013 Brandon Aaron (http://brandon.aaron.sh)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * NOTE -- THIS FILE HAS BEEN SIGNIFICANTLY MODIFIED TO ADD NEW FUNCTIONALITY. DO NOT UPGRADE.
 * dave@wildfireinteractive.ca
 *
 * Version: 3.1.12
 *
 * Requires: jQuery 1.2.2+
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});

// Plugin
;(function ( $, window, document, undefined ) {
    // CUSTOM VARIABLES

    var zoomables = new Array();
    var activeZoomable = undefined;

    var imageInteraction = false;
    var interfaceInteraction = false;

    var windowScrollX = $(window).scrollLeft();
    var windowScrollY = $(window).scrollTop();
    var windowWidth = $(window).width();
    var windowHeight = $(window).height();

    // -END- CUSTOM VARIABLES

    // FUNCTIONS

    function hexToRgb(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    function ndd_init_global_events() {
        $(document).on("mousemove", function(e) {
            if (activeZoomable != undefined) {
                activeZoomable.handle_event(e);
            }
        });

        $(document).on("mouseup", function(e) {
            if (activeZoomable != undefined) {
                activeZoomable.handle_event(e);
                activeZoomable = undefined;
            }
        });

        // touch

        $(window).on("touchmove", function(e) {
            if (e.originalEvent.touches.length == 1) {
                if (activeZoomable != undefined) {
                    activeZoomable.handle_event(e);
                }
            }

            if (e.originalEvent.touches.length == 2) {
                e.preventDefault();
                if (activeZoomable != undefined) {
                    activeZoomable.handle_event(e);
                }
            }
        });

        $(window).on("touchend", function(e) {
            if (activeZoomable != undefined) {
                activeZoomable.handle_event(e);
                activeZoomable = undefined;
            }
        });

        $(window).on('resize', function() {
            windowWidth = $(window).width();
            windowHeight = $(window).height();
        })
    }
    function log(obj) { console.log(obj); }

    function lerp(v0, v1, t) { return (1-t)*v0 + t*v1; }

    ndd_init_global_events();

    // -END- FUNCTIONS


    // Create the defaults once
    var uberZoom = "uberZoom",
        defaults = {
            width : "100%",
            height : "100%",
            maxZoom : "auto",
            navigator : false,
            navigatorImagePreview : false,
            fullscreen : false,
            startInFullscreen : false,
            rubberband : true,
            rotationImages: []
        }

    // The actual plugin constructor
    function UberZoom( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = uberZoom;

        // CUSTOM PROPERTIES

        // general

        this.obj = undefined;
        this.obj_image = undefined;
        this.obj_content = undefined;
        this.img = undefined;

        this.frameWidth = 0;
        this.frameHeight = 0;
        this.contentWidth = 0;
        this.contentHeight = 0;

        this.frameOffsetLeft = 0;
        this.frameOffsetTop = 0;

        // zooming

        this.minZoom = 0.85;
        this.maxZoom = 4;
        this.currentZoom = 0.85;
        this.targetZoom = 1;

        this.zoomStep = 0.25;
        this.zoomSpeed = 0.15; // 1 = instant, 0.01 = slowest

        // moving

        this.defaultPosX = 0;
        this.defaultPosY = 0;
        this.targetPosX = 0;
        this.targetPosY = 0;
        this.currentPosX = 0;
        this.currentPosY = 0;

        this.dragOutOfBoundsX = 0;
        this.dragOutOfBoundsY = 0;

        this.intertia = 0.9; // 1 = endless, 0 = no intertia

        this.dragEventOriginX = 0;
        this.dragEventOriginY = 0;
        this.dragInitialPositionX = 0;
        this.dragInitialPositionY = 0;
        this.dragInitialScrollTop = 0;
        this.scrollDebounce = false;
        this.eventQ = [];

        this.dragLastEventX = 0; // for velocity, intertia
        this.dragLastEventY = 0;
        this.dragMomentumX = 0;
        this.dragMomentumY = 0;
        this.dragMomentumCalculateTimer = 5;
        this.vx = 0;
        this.vy = 0;

        this.lastMomentumCalculateTime = 0;

        this.dragTimeout = undefined;
        this.zoomTimeout = undefined;

        // gestures
        this.lastTouchTime = 0;
        this.didDoubleTap = false;
        this.lastTouchX = 0;
        this.lastTouchY = 0;

        this.initialPinchDistance = 0;
        this.pinchDelta = 0;
        this.initialZoom = 0;
        this.lastZoom = 0;

        this.pinchZooming = false;
        this.pinchZoomOffsetX = 0;
        this.pinchZoomOffsetY = 0;

        this.dragging = false;

        // interface
        this.obj_interface = undefined;
        this.ui_hide_timeout = undefined;
        this.ui_visible = false;

        // navigator
        this.obj_navigator = undefined;
        this.obj_nav_window = undefined;
        this.navigatorWidth = 0;
        this.navigatorHeight = 0;

        this.navigator_dragging = false;

        this.nav_window_width = 0;
        this.nav_window_height = 0;

        // fullscreen
        this.obj_fullscreen = undefined;
        this.is_fullscreen = false;

        // -END- CUSTOM PROPERTIES
        this.currentRotationImage = 0;
        this.rotationLoaded = false;
        this.rotationSpinning = true;
        this.rotationLoadingCounter = 0;
        this.rotationImages = [];

        this.init();
    }

    UberZoom.prototype = {

        init : function() {
            var self = this;

            // store reference
            self.id = zoomables.length;
            zoomables[self.id] = self;

            // store img reference
            self.obj_image = $(self.element);
            self.obj_image.wrap('<div class="ndd-uberzoom-container"></div>');
            self.obj_image.addClass("ndd-uberzoom-main-image");

            // prepare container
            self.obj = $(self.element).parent();
            self.obj.attr('id', 'ndd-uberzoom-container-' + self.id);
            self.obj.css({
                "width" : self.options.width,
                "height" : self.options.height
            });

            // is fullscreen?
            if (self.options.starting_in_fullscreen) {
                self.is_fullscreen = true;
                self.obj.addClass('ndd-uberzoom-container-fullscreen');
            }

            // set frame width and height
            self.frameWidth = self.obj.width();
            self.frameHeight = self.obj.height();

            self.img = new Image();
            self.img.onload = function() {

                // FIT IMAGE
                var imgRatio = self.img.width / self.img.height;
                var frameRatio = self.frameWidth / self.frameHeight;

                if (imgRatio < frameRatio) {
                    // wider
                    self.obj_image.css({
                        "width" : "auto",
                        "height" : "100%"
                    });

                    // self.contentHeight = self.frameHeight;
                } else {
                    // taller
                    self.obj_image.css({
                        "width" : "100%",
                        "height" : "auto"
                    });

                    // self.contentWidth = self.frameWidth;
                }

                self.contentWidth = self.obj_image.width();
                self.contentHeight = self.obj_image.height();

                // create image wrapper and set size
                self.obj_image.wrap('<div class="ndd-uberzoom-content"></div>');
                self.obj_content = self.obj.find(".ndd-uberzoom-content");

                self.obj_content.css({
                    "position" : "absolute",
                    "width" : self.contentWidth * self.currentZoom,
                    "height" : self.contentHeight * self.currentZoom,
                    "left" : -(self.contentWidth * self.currentZoom)/2 + self.frameWidth/2 + "px",
                    "top" : -(self.contentHeight * self.currentZoom)/2 + self.frameHeight/2 + "px"
                });

                // show image and fit to wrapper
                self.obj_image.css({"width" : "100%", "height" : "100%" });

                self.defaultPosX = self.obj_content.position().left;
                self.defaultPosY = self.obj_content.position().top;
                self.currentPosX = self.defaultPosX;
                self.currentPosY = self.defaultPosY;

                self.frameOffsetLeft = self.obj.offset().left;
                self.frameOffsetTop = self.obj.offset().top;

                // OPTIONS

                // max zooming
                if (self.options.maxZoom == "auto") {
                    if (imgRatio < frameRatio) {
                        self.maxZoom = self.img.height / self.frameHeight;
                    } else {
                        self.maxZoom = self.img.width / self.frameWidth;
                    }
                } else {
                    self.maxZoom = self.options.maxZoom;
                }

                // UI
                self.obj.append('<div class="ndd-uberzoom-interface"></div>');
                self.obj_interface = self.obj.find('.ndd-uberzoom-interface');

                // navigator
                if (self.options.navigator) {
                    // create navigator
                    self.obj_interface.append('<div class="ndd-uberzoom-navigator"><div class="ndd-uberzoom-navigator-window"></div></div>');
                    self.obj_navigator = self.obj.find('.ndd-uberzoom-navigator');
                    self.obj_nav_window = self.obj.find('.ndd-uberzoom-navigator-window');

                    self.options.navigatorMaxWidth = self.frameWidth/4;
                    self.options.navigatorMaxHeight = self.frameHeight/4;

                    // calculate size
                    var navigatorRectRatio = self.options.navigatorMaxWidth / self.options.navigatorMaxHeight;
                    var imgRectRatio = self.img.width / self.img.height;

                    var navigatorScale = 1;
                    if (imgRectRatio > navigatorRectRatio) {
                        // wider
                        navigatorScale = self.contentWidth / self.options.navigatorMaxWidth;
                    } else {
                        // taller
                        navigatorScale = self.contentHeight / self.options.navigatorMaxHeight;
                    }

                    self.navigatorWidth = self.contentWidth / navigatorScale;
                    self.navigatorHeight = self.contentHeight / navigatorScale;

                    var px = 10;
                    var py = self.frameHeight - self.navigatorHeight - 10;

                    self.obj_navigator.css({
                        "width" : self.navigatorWidth,
                        "height" : self.navigatorHeight,
                        "left" : 10,
                        "top" : "100%",
                        "margin" : 0,
                        "margin-top" : -self.navigatorHeight - 10
                    });

                    if (self.options.navigatorImagePreview) {
                        self.obj_navigator.append('<img class="ndd-uberzoom-navigator-image" src="' + self.img.src + '">');
                    }

                    self.redraw_navigator();
                }

                // fullscreen
                if (self.options.fullscreen) {
                    self.obj_interface.append('<div class="ndd-uberzoom-fullscreen"></div');
                    self.obj_fullscreen = self.obj_interface.find('.ndd-uberzoom-fullscreen');

                    self.obj_fullscreen.css({
                        "width" : 44,
                        "height" : 44,
                        "left" : "100%",
                        "top" : "100%",
                        "margin-top" : -10 - 44,
                        "margin-left" : -10 - 44,
                        "margin-right" : 0,
                        "margin-bottom" : 0
                    });

                    if (self.is_fullscreen) {
                        self.obj_fullscreen.append('<img alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADgAAAA4CAYAAACohjseAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RkFDRjUwRjI0QzczMTFFNEJEMURCQTlDMEQ1NjUwMjMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RkFDRjUwRjM0QzczMTFFNEJEMURCQTlDMEQ1NjUwMjMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpGQUNGNTBGMDRDNzMxMUU0QkQxREJBOUMwRDU2NTAyMyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpGQUNGNTBGMTRDNzMxMUU0QkQxREJBOUMwRDU2NTAyMyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmMmKIIAAALISURBVHja7JvfTxNBEMevBF/Q8mAMf4D27/EPMD6REKI1tKVHCm3PYH9BVCoawpP2Sf4t/gNNND4R4zhL9sKx3u7Obgfbvdwk34QcM7vzgb2dZTtEABApGqHe5zynKkY99oh7gtqdY16R80R9rjrtwI31PSY5l7EbHrEbMvaTR2wvk3dDB/gC/rV9h0kGmbg1jyTXMvGHjitGtboK2AC99RzhhFU9AKvKGANCTMeQdysFHILdTMs1L54DEOTYOv8DQt4j4TgGmu3lTPJG48sFCHIOyrLMs6M0YNcDcmDw4wRUIfeIucbqJtMgBm6iXll8uAFBzrlJzLGlKxNN4LG7AARXuDxAoXbAgLGt0OcV/FAAW3njmiZ8GRBgXTfuaqS3c9QP1Ndoue056kL3zRVL8KNo+c2co2HJHAW0RCe6cXWTTQLcZMZUwFHAZWJoAxwxFdtFFvqhDnAMfLZIwFvLNR38DHht3QNwnTmHs7QOtlF15q37z3+KMZlguqyIyx784jvqN+Pgv0QFcoypoB4w5iB+eQ8r1+u0wLYSFdxKwBKwBCwBS8ASsAQsAcM1cSCtob4V8LB9Txy2BeBT1AnzD64qIV3sPuoncx5NsUSnqI9LsPS5X5eTa67MX9SHBbqySHSXTkkBABPbtWEvYMAe9eK3HyBg3/XTpdcBASauV/eptgMA3DaNa9uarwI4rFz51h5xXzoLAHAmc3X6+KwT4CbTob6D3YDLRNcGGDMV20UW+lgHSO0geobaWgDglpzbqSPLdVnuEJvhuAH3PTqyDlLAU2JA0+FYxwmY185J7ciaCucvnp2GpgM6F2Bi8Ke8Up9T52ODE6WPun8HgJSWalOX5LG6yUws75xN2fd43pbmrkNcg/IRdqp3Gae2R5JTGTtPU/rUIzbbQPjWVuhnqA/g394vJqt5xNVgvn8rOJW533r+V4ABANsFyxkv8SUEAAAAAElFTkSuQmCC" />');
                    } else {
                        self.obj_fullscreen.append('<img alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADgAAAA4CAYAAACohjseAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RTVDRDgxOTM0QzczMTFFNEE1ODBEN0VENTU4QTFBQjgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RTVDRDgxOTQ0QzczMTFFNEE1ODBEN0VENTU4QTFBQjgiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFNUNEODE5MTRDNzMxMUU0QTU4MEQ3RUQ1NThBMUFCOCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFNUNEODE5MjRDNzMxMUU0QTU4MEQ3RUQ1NThBMUFCOCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PnEA7z4AAAIuSURBVHja7Jpda8IwFIajV/sB259zd+IciPWrDj87bDt1X+oYshvvZOwvDraLLgUDnbSuOTk5KSOB9zLpeUxMznkTFuG3NhcDqocdDEMe71kBTuilqIAeApzQrGiAmHBCXlEAJxrghCamAaca4YSmpgA9AjihW2rAOiGc0DUk0DKDtW9G375AvRSWqEs4ezemNpkRAdzE9DHhFXFzwT7oBxrghhQH/RVXJedYQwNwlUOMIMBkwA7hcs2bojmJPiNZwLT0q53zwysFuBUA7uRmxCRnoa85fcuTlrkys88AwXU1Zzj1E+N3ZX+kJOBYIoi0/2QVcZOp5lyWWRofA64V7YWahmOiJjlzx4qZolIcKM/Y1sD88J7rk8vXlH/OuM64hsD+bgzI/nMrs3/eLKAFtIAW0AJaQAtoAS2gBTQJ2FPoH5dZO43x7RRKubj1RcH7CigoB4mCdKOh4N0o+q5vx5bFTKKzm2IpPCLCPSnehQRZplMgOXOYluEvq0HBQQ/+sg1DRcd5rwC3V3TQw7zGrw/0ROO2VAB8yPmNfkpfX9a69wH3cx2EJdoBQAbQy5dmhkcZKfqWEN81yvBimxTXZ46GY8LBCAwDsB3puwDtmAZsEVxht0wBNggfITSgQZYimLV9yfVBnDfH33ynqiYuDBQG59TvZBaES3RpapOZE8AtTB8ToUa4u6K8kwmLCIcJiL1c51hBYT9K3yLAbTEDwgaMDnf3UDgXO5gfAQYA/kaa5uk6WEcAAAAASUVORK5CYII=" />');
                    }
                }

                // EVENTS
                $(self.obj).on("mousedown", function(e) {
                    activeZoomable = self;
                    self.handle_event(e);
                });
                $(self.obj).mousewheel(function(e, d) {
                    //activeZoomable = self;
                    self.handle_event(e);
                });

                $(window).on("resize", function(e) {
                    self.handle_event(e);
                });

                // touch events
                $(self.obj).on("touchstart", function(e) {
                    if (e.originalEvent.touches.length == 1) {
                        activeZoomable = self;

                        var timestamp = new Date().getTime();

                        var touchX = e.originalEvent.touches[0].screenX;
                        var touchY = e.originalEvent.touches[0].screenY;

                        if (timestamp - self.lastTouchTime < 350 && timestamp - self.lastTouchTime > 100 && Math.abs(self.lastTouchX - touchX) < 50 && Math.abs(self.lastTouchY - touchY) < 50) {
                            self.lastTouchTime = 0;
                            self.didDoubleTap = true;
                        } else {
                            self.lastTouchTime = timestamp;
                        }

                        self.lastTouchX = touchX;
                        self.lastTouchY = touchY;

                        self.handle_event(e);
                    }

                    if (e.originalEvent.touches.length == 2) {
                        activeZoomable = self;
                        self.handle_event(e);
                    }

                    //e.preventDefault();
                });

                // start in fullscreen (user option)
                if (self.options.startInFullscreen) {
                    self.toggle_fullscreen();
                }

                if (self.options.rotationImages && self.options.rotationImages.length > 1)
                {
                    $('#imagezone .rotateinfo').css('opacity',0.5);
                    $('#imagezone .rotateinfo h3').css('opacity',1);
                    $('#imagezone .rotateinfo img').css('opacity',0);
                    for (var j=0; j < self.options.rotationImages.length; j++){
                        var img = new Image();
                        img.onload = function() { self.increment_rotation_load(self); };
                        img.src = self.options.rotationImages[j];
                        self.rotationImages.push(img);
                    }
                } 
            }

            // start loading image
            self.img.src = self.obj_image.attr("src");
        },

        increment_rotation_load : function(self){
            self.rotationLoadingCounter ++;
            if (self.rotationLoadingCounter == self.options.rotationImages.length)
            {
                self.rotationLoaded = true;
                $('#imagezone .rotateinfo h3').css('opacity','0');
                $('#imagezone .rotateinfo img').css('opacity','1');
                if (give_spin_demo)
                {
                    self.rotationSpinning = true;
                    self.switch_rotation_image(true);
                    self.rotation_demo();
                    give_spin_demo = false;
                }
            }
        },

        rotation_demo: function() {
            var self = this;
            if (self.rotationSpinning && self.currentRotationImage > 0)
            {
                self.switch_rotation_image(true);
                setTimeout(function() { self.rotation_demo(); }, 40);
            }
        },

        show_ui : function() {
            var self = this;

            if (!self.ui_visible && self.currentZoom > 1) {
                self.ui_visible = true;
                self.obj_interface.css({ "opacity" : 1 });
            }
        },

        hide_ui : function() {
            var self = this;

            if (self.ui_visible) {
                self.obj_interface.css({ "opacity" : 0 });
                self.ui_visible = false;
            }
        },

        schedule_hide_ui : function() {
            var self = this;

            if (self.ui_visible) {
                clearTimeout(self.ui_hide_timeout);

                self.ui_hide_timeout = setTimeout(function() {
                    self.hide_ui();
                }, 1000);
            }
        },

        toggle_fullscreen : function() {
            var self = this;

            if (self.is_fullscreen) {
                self.obj.remove();
            } else {
                $('body').prepend('<img src="' + self.img.src + '" id="ndd-uberzoom-fullscreen-temp-image">');

                var options = self.options;
                options.startInFullscreen = false;
                options.starting_in_fullscreen = true;
                options.width = "100%";
                options.height = "100%";

                $('#ndd-uberzoom-fullscreen-temp-image').uberZoom(options);
            }
        },

        handle_event : function(e) {
            var self = this;

            self.rotationSpinning = false;

            if (e.type == 'mousewheel' && (self.currentZoom == self.maxZoom || $(window).scrollTop() > 0) && e.deltaY > 0) {
                return;
            }

            if (e.type == 'mousewheel' && self.currentZoom == self.minZoom && e.deltaY < 0) {
                return;
            }
            if (e.type != 'touchstart' && e.type != 'touchmove')
            {
                e.stopPropagation();
                e.preventDefault();
            }
            // UI EVENTS ========================================================
            if (e.type != "mousewheel" &&
                !imageInteraction &&
                ($(e.target).hasClass('ndd-uberzoom-fullscreen') || $(e.target).parent().hasClass('ndd-uberzoom-fullscreen') ||
                $(e.target).hasClass('ndd-uberzoom-navigator') ||
                $(e.target).hasClass('ndd-uberzoom-navigator-image') ||
                $(e.target).hasClass('ndd-uberzoom-navigator-window')))
            {
                // mouse events
                if (e.type == "mousedown") {
                    interfaceInteraction = true;
                    if ($(e.target).hasClass('ndd-uberzoom-navigator') || $(e.target).hasClass('ndd-uberzoom-navigator-image') || $(e.target).hasClass('ndd-uberzoom-navigator-window')) {
                        self.navigator_window_start_dragging(e.pageX, e.pageY);
                        self.navigator_dragging = true;
                    }
                }

                if (e.type == "mouseup") {
                    interfaceInteraction = false;

                    if ($(e.target).hasClass('ndd-uberzoom-fullscreen') || $(e.target).parent().hasClass('ndd-uberzoom-fullscreen')) {
                        self.toggle_fullscreen();
                    }
                }

                if (e.type == "mousemove") {
                    interfaceInteraction = true;
                    if (self.navigator_dragging) {
                        self.navigator_window_drag(e.pageX, e.pageY);
                    }
                }

                // touch events

                if (e.type == "touchstart" && e.originalEvent.touches.length == 1) {
                    e.stopPropagation();
                    e.preventDefault();
                   
                    self.show_ui();
                    interfaceInteraction = true;

                    if ($(e.target).hasClass('ndd-uberzoom-navigator') || $(e.target).hasClass('ndd-uberzoom-navigator-image') || $(e.target).hasClass('ndd-uberzoom-navigator-window')) {
                        self.navigator_window_start_dragging(e.originalEvent.touches[0].pageX, e.originalEvent.touches[0].pageY);
                        self.navigator_dragging = true;
                    }
                }

                if (e.type == "touchmove" && e.originalEvent.touches.length == 1) {
                    e.stopPropagation();
                    e.preventDefault();
                   
                    self.show_ui();
                    interfaceInteraction = true;

                    if (self.navigator_dragging) {
                        self.navigator_window_drag(e.originalEvent.touches[0].pageX, e.originalEvent.touches[0].pageY);
                    }
                }

                if (e.type == "touchend" && e.originalEvent.touches.length == 0) {
                    self.schedule_hide_ui();
                    interfaceInteraction = false;
                    if ($(e.target).hasClass('ndd-uberzoom-fullscreen') || $(e.target).parent().hasClass('ndd-uberzoom-fullscreen')) {
                        self.toggle_fullscreen();
                    }
                }

                return;
            }

            // corner cases
            if (interfaceInteraction && (e.type == "touchend" || e.type == "mouseup")) {
                self.schedule_hide_ui();
                interfaceInteraction = false;
            }

            if (interfaceInteraction && e.type == "mousemove") {
                if (self.navigator_dragging) {
                    self.navigator_window_drag(e.pageX, e.pageY);
                }
            }

            if (interfaceInteraction && e.type == "touchmove") {
                self.show_ui();

                if (self.navigator_dragging) {
                    self.navigator_window_drag(e.originalEvent.touches[0].pageX, e.originalEvent.touches[0].pageY);
                }
            }

            // IMAGE EVENTS ========================================================

            if (interfaceInteraction) return;

            // mouse events
            if (e.type == "mousedown") {
                imageInteraction = true;
                self.dragging = true;
                self.start_moving(e.screenX, e.screenY);
            }

            if (e.type == "mousemove") {
                imageInteraction = true;
                if (self.dragging) {
                    self.move(e.screenX, e.screenY);
                }
            }

            if (e.type == "mouseup") {
                imageInteraction = false;
                self.stop_moving();
                self.dragging = false;
            }

            if (e.type == "mousewheel") {
                if (e.deltaY > 0) {
                    self.zoom_in(e.offsetX, e.offsetY);
                } else if (e.deltaY < 0) {
                    self.zoom_out(e.offsetX, e.offsetY);
                }
            }

            if (e.type == "resize") {
                self.update_size();
            }

            // touch events
            if (e.type == "touchstart" && e.originalEvent.touches.length == 2) {
                e.stopPropagation();
                e.preventDefault();
               
                self.show_ui();
                imageInteraction = true;
                this.pinchZooming = true;

                var t1x = e.originalEvent.touches[0].screenX;
                var t1y = e.originalEvent.touches[0].screenY;
                var t2x = e.originalEvent.touches[1].screenX;
                var t2y = e.originalEvent.touches[1].screenY;

                self.initialPinchDistance = Math.sqrt(Math.pow((t1y - t2y), 2) + Math.pow((t1x - t2x), 2));
                self.initialZoom = self.currentZoom;
            }

            if (e.type == "touchmove" && e.originalEvent.touches.length == 2) {
                e.stopPropagation();
                e.preventDefault();
               
                self.show_ui();
                imageInteraction = true;

                var t1x = e.originalEvent.touches[0].screenX;
                var t1y = e.originalEvent.touches[0].screenY;
                var t2x = e.originalEvent.touches[1].screenX;
                var t2y = e.originalEvent.touches[1].screenY;

                self.pinchDelta = Math.sqrt(Math.pow((t1y - t2y), 2) + Math.pow((t1x - t2x), 2)) - self.initialPinchDistance;
                self.pinchDelta = (self.pinchDelta * 7) / (Math.sqrt(Math.pow(self.contentWidth, 2) + Math.pow(self.contentHeight, 2)));

                var t1ox = e.originalEvent.touches[0].pageX - self.obj.offset().left;
                var t1oy = e.originalEvent.touches[0].pageY - self.obj.offset().top;
                var t2ox = e.originalEvent.touches[1].pageX - self.obj.offset().left;
                var t2oy = e.originalEvent.touches[1].pageY - self.obj.offset().top;

                self.pinchZoomOffsetX = (t1ox + t2ox) / 2;
                self.pinchZoomOffsetY = (t1oy + t2oy) / 2;

                self.pinch_zoom(self.pinchZoomOffsetX, self.pinchZoomOffsetY);
            }
            if (e.type == "touchstart") {
                if (self.didDoubleTap) {
                    e.stopPropagation();
                    e.preventDefault();
                    imageInteraction = true;
                    this.dragging = true;

                    var offsetX = e.originalEvent.touches[0].pageX - self.obj.offset().left;
                    var offsetY = e.originalEvent.touches[0].pageY - self.obj.offset().top;

                    if (self.currentZoom < self.maxZoom / 2) {
                        self.zoom_in(offsetX, offsetY, true);
                    } else {
                        self.zoom_out(offsetX, offsetY, true);
                    }

                    self.show_ui();
                    self.didDoubleTap = false;
                } else {
                    e.stopPropagation();
                    e.preventDefault();
                    self.show_ui();
                    imageInteraction = true;
                    this.dragging = true;
                    self.start_moving(e.originalEvent.touches[0].screenX, e.originalEvent.touches[0].screenY);
                }
            }
            if (e.type == "touchend") {
                self.schedule_hide_ui();
                imageInteraction = false;
                self.stop_moving();

                if (this.pinchZooming) {
                    this.pinchZooming = false;

                    if (self.currentZoom > self.maxZoom) {
                        self.zoom_in(self.pinchZoomOffsetX, self.pinchZoomOffsetY, true);
                    } else if (self.currentZoom < self.minZoom) {
                        self.zoom_out(self.pinchZoomOffsetX, self.pinchZoomOffsetY, true);
                    }
                }
            }
            if (e.type == "touchmove") {
                
                self.show_ui();
                imageInteraction = true;

                if (self.dragging) {
                    self.move(e.originalEvent.touches[0].screenX, e.originalEvent.touches[0].screenY);
                }
            }
        },

        navigator_window_start_dragging : function(docX, docY) {
            var self = this;

            // did the event occur inside the window?
            if (docX > this.obj_nav_window.offset().left &&
                docX < this.obj_nav_window.offset().left + this.nav_window_width &&
                docY > this.obj_nav_window.offset().top &&
                docY < this.obj_nav_window.offset().top + this.nav_window_height)
            {
                self.dragEventOriginX = docX;
                self.dragEventOriginY = docY;
                self.dragInitialPositionX = self.currentPosX;
                self.dragInitialPositionY = self.currentPosY;
            } else {
                self.dragInitialPositionX = self.currentPosX;
                self.dragInitialPositionY = self.currentPosY;

                // move image to current cursor position
                var scale = (self.contentWidth * self.currentZoom) / self.navigatorWidth;

                var dragOriginX = self.obj_nav_window.offset().left + self.nav_window_width/2;
                var dragOriginY = self.obj_nav_window.offset().top + self.nav_window_height/2;

                var dx = (dragOriginX - docX) * scale;
                var dy = (dragOriginY - docY) * scale;

                self.targetPosX = self.dragInitialPositionX + dx;
                self.targetPosY = self.dragInitialPositionY + dy;

                // constrain
                self.constrain_target_position();
                self.currentPosX = self.targetPosX;
                self.currentPosY = self.targetPosY;

                // redraw
                self.redraw();

                // update navigator
                self.redraw_navigator();

                // start dragging as usual
                self.dragEventOriginX = docX;
                self.dragEventOriginY = docY;
                self.dragInitialPositionX = self.currentPosX;
                self.dragInitialPositionY = self.currentPosY;
            }
        },

        navigator_window_drag : function(docX, docY) {
            var self = this;

            // move image
            var scale = (self.contentWidth * self.currentZoom) / self.navigatorWidth;

            var dx = (self.dragEventOriginX - docX) * scale;
            var dy = (self.dragEventOriginY - docY) * scale;

            self.targetPosX = self.dragInitialPositionX + dx;
            self.targetPosY = self.dragInitialPositionY + dy;

            // constrain
            self.constrain_target_position();
            self.currentPosX = self.targetPosX;
            self.currentPosY = self.targetPosY;

            // redraw
            self.redraw();

            // update navigator
            self.redraw_navigator();
        },

        pinch_zoom : function(offsetX, offsetY) {
            var self = this;

            var targetZoom = self.initialZoom * (1 + self.pinchDelta);

            //self.targetZoom = (self.targetZoom < self.maxZoom) ? self.targetZoom : self.maxZoom;
            //self.targetZoom = (self.targetZoom > self.minZoom) ? self.targetZoom : self.minZoom;

            if (targetZoom > self.maxZoom) {
                var multiplier = 1 - (targetZoom - self.maxZoom);
                multiplier = (multiplier < 0) ? 0 : multiplier;

                targetZoom = self.lastZoom + (targetZoom - self.lastZoom) * multiplier;
            }

            if (targetZoom < self.minZoom) {
                var multiplier = 1 - (self.minZoom - targetZoom) * 4;
                multiplier = (multiplier < 0) ? 0 : multiplier;

                targetZoom = self.lastZoom + (targetZoom - self.lastZoom) * multiplier;
            }

            self.targetZoom = targetZoom;
            self.lastZoom = self.targetZoom;

            var fpx = (offsetX - self.currentPosX) / (self.contentWidth * self.currentZoom);
            var fpy = (offsetY - self.currentPosY) / (self.contentHeight * self.currentZoom);

            self.targetPosX = self.currentPosX - ((self.targetZoom - self.currentZoom) * self.contentWidth) * fpx;
            self.targetPosY = self.currentPosY - ((self.targetZoom - self.currentZoom) * self.contentHeight) * fpy;

            self.currentZoom = self.targetZoom;
            self.currentPosX = self.targetPosX;
            self.currentPosY = self.targetPosY;

            // Apply
            self.redraw();
        },

        zoom_in : function(offsetX, offsetY, max) {
            var self = this;

            self.targetZoom += self.zoomStep;
            self.targetZoom = (self.targetZoom < self.maxZoom) ? self.targetZoom : self.maxZoom;
            self.targetZoom = (self.targetZoom > self.minZoom) ? self.targetZoom : self.minZoom;

            if (max == true) {
                self.targetZoom = self.maxZoom;
            }

            var fpx = (offsetX - self.currentPosX) / (self.contentWidth * self.currentZoom);
            var fpy = (offsetY - self.currentPosY) / (self.contentHeight * self.currentZoom);

            if ((self.targetZoom * self.contentWidth) < self.frameWidth) self.targetPosX = (self.frameWidth - (self.targetZoom * self.contentWidth))/2;
            else self.targetPosX = self.currentPosX - ((self.targetZoom - self.currentZoom) * self.contentWidth) * fpx; 
            if ((self.targetZoom * self.contentHeight) < self.frameHeight) self.targetPosY = (self.frameHeight - (self.targetZoom * self.contentHeight))/2;
            else self.targetPosY = self.currentPosY - ((self.targetZoom - self.currentZoom) * self.contentHeight) * fpy;

            // stay in frame
            self.constrain_target_position();

            self.apply_zoom();
        },

        zoom_out : function(offsetX, offsetY, max) {
            var self = this;

            self.targetZoom -= self.zoomStep;
            self.targetZoom = (self.targetZoom < self.maxZoom) ? self.targetZoom : self.maxZoom;
            self.targetZoom = (self.targetZoom > self.minZoom) ? self.targetZoom : self.minZoom;

            if (max == true) {
                self.targetZoom = self.minZoom;
            }

            var fpx = (offsetX - self.currentPosX) / (self.contentWidth * self.currentZoom);
            var fpy = (offsetY - self.currentPosY) / (self.contentHeight * self.currentZoom);
            if ((self.targetZoom * self.contentWidth) < self.frameWidth) self.targetPosX = (self.frameWidth - (self.targetZoom * self.contentWidth)/2);
            else self.targetPosX = self.currentPosX - ((self.targetZoom - self.currentZoom) * self.contentWidth) * fpx;
            if ((self.targetZoom * self.contentHeight) < self.frameHeight) self.targetPosY = (self.frameHeight - (self.targetZoom * self.contentHeight)/2);
            else self.targetPosY = self.currentPosY - ((self.targetZoom - self.currentZoom) * self.contentHeight) * fpy;

            // stay in frame
            self.constrain_target_position();

            self.apply_zoom();
        },

        apply_zoom : function() {
            var self = this;

            clearTimeout(self.dragTimeout);
            clearTimeout(self.zoomTimeout);

            self.currentZoom = lerp(self.currentZoom, self.targetZoom, self.zoomSpeed);
            self.currentPosX = lerp(self.currentPosX, self.targetPosX, self.zoomSpeed);
            self.currentPosY = lerp(self.currentPosY, self.targetPosY, self.zoomSpeed);

            // Apply
            self.redraw();

            if (Math.abs(self.currentZoom - self.targetZoom) > 0.025 || Math.abs(self.currentPosX - self.targetPosX) > 0.5 || Math.abs(self.currentPosY - self.targetPosY) > 0.5) {
                self.zoomTimeout = setTimeout(function() {
                    self.apply_zoom();
                }, 16);
            } else {
                self.currentZoom = self.targetZoom;
                self.currentPosX = self.targetPosX;
                self.currentPosY = self.targetPosY;

                self.redraw();
            }
        },

        start_moving : function(x, y) {
            var self = this;

            self.dragOutOfBoundsX = 0;
            self.dragOutOfBoundsY = 0;

            self.dragEventOriginX = x;
            self.dragEventOriginY = y;
            self.dragInitialPositionX = self.currentPosX;
            self.dragInitialPositionY = self.currentPosY;
            self.dragInitialScrollTop = $(window).scrollTop();

            self.dragLastEventX = self.dragEventOriginX;
            self.dragLastEventY = self.dragEventOriginY;

            self.dragLastEventMomentumX = self.dragEventOriginX;
            self.dragLastEventMomentumY = self.dragEventOriginY;

            activeZoomable = self;

            self.dragMomentumX = 0;
            self.dragMomentumY = 0;

            self.vx = 0;
            self.vy = 0;

            clearTimeout(self.dragTimeout);
            clearTimeout(self.zoomTimeout);
        },

        switch_rotation_image : function(going_left) {
            var self = this;
            
            if (going_left) {
                self.currentRotationImage += 1;
            } else {
                self.currentRotationImage -= 1;
            }

            if (self.currentRotationImage < 0 ) {
                self.currentRotationImage = (self.rotationImages.length - 1);
            } else if (self.currentRotationImage >= self.rotationImages.length) {
                self.currentRotationImage = 0;    
            }
            self.obj_image.attr('src',self.rotationImages[self.currentRotationImage].src);
            if (self.options.navigatorImagePreview) {
                self.obj_navigator.find('img').attr('src', self.obj_image.attr('src'));
            }
            self.redraw();
        },

        move : function(x, y) {
            var self = this;
        
            var diffX = Math.abs(x - self.dragLastEventX);
            var diffY = Math.abs(y - self.dragLastEventY);
            if (Math.round(self.currentZoom,1) == 1.0){
                var rotate_threshold = (self.frameWidth / 2) / self.rotationImages.length;
                if (self.rotationImages.length > 1 && diffX > diffY) {
                   if (self.rotationLoaded && diffX > rotate_threshold)
                    {
                        var going_left = (x - self.dragLastEventX < 0);

                        self.dragLastEventX = x;
                        self.dragLastEventY = y; 

                        self.switch_rotation_image(going_left);
                        $('#imagezone .rotateinfo').css('opacity',0);
                    }
                } else if (self.dragging && diffY > diffX) {
                    var scrolled = y - self.dragEventOriginY;
                    if (Math.abs(scrolled) != 0 && !self.scrollDebounce)
                    {
                        self.scrollDebounce = true;
                        $(window).scrollTop(Math.max(0,self.dragInitialScrollTop-scrolled));
                        setTimeout(function(){ self.scrollDebounce = false;}, 50);
                        self.dragLastEventX = x;
                        self.dragLastEventY = y;
                    }
                }
            } else {

                // calculate resistance
                self.calculate_out_of_bounds();

                // forces
                var dragMultiplierX = (1 - (Math.abs(self.dragOutOfBoundsX) / 100) < 0) ? 0 : 1 - (Math.abs(self.dragOutOfBoundsX) / 100);
                var dragMultiplierY = (1 - (Math.abs(self.dragOutOfBoundsY) / 100) < 0) ? 0 : 1 - (Math.abs(self.dragOutOfBoundsY) / 100);

                self.vx = (x - self.dragLastEventX) * dragMultiplierX;
                self.vy = (y - self.dragLastEventY) * dragMultiplierY;

                var timestamp = new Date().getTime();

                if (timestamp - self.lastMomentumCalculateTime > 16) {
                    self.dragMomentumX = x - self.dragLastEventMomentumX;
                    self.dragMomentumY = y - self.dragLastEventMomentumY;

                    self.dragMomentumX = (self.dragMomentumX > 50) ? 50 : self.dragMomentumX;
                    self.dragMomentumY = (self.dragMomentumY > 50) ? 50 : self.dragMomentumY;

                    self.lastMomentumCalculateTime = timestamp;

                    self.dragLastEventMomentumX = self.dragLastEventX;
                    self.dragLastEventMomentumY = self.dragLastEventY;
                }

                // apply forces
                self.currentPosX += self.vx;
                self.currentPosY += self.vy;

                // redraw
                self.redraw();

                // next frame prep
                self.dragLastEventX = x;
                self.dragLastEventY = y;
            }
        },

        stop_moving : function() {
            var self = this;

            // calculate resistance
            self.calculate_out_of_bounds();

            // force
            self.vx = self.dragMomentumX - (self.dragOutOfBoundsX)/2;
            self.vy = self.dragMomentumY - (self.dragOutOfBoundsY)/2;

            self.vx = (self.vx > 20) ? 20 : self.vx;
            self.vx = (self.vx < -20) ? -20 : self.vx;
            self.vy = (self.vy > 20) ? 20 : self.vy;
            self.vy = (self.vy < -20) ? -20 : self.vy;

            // apply force
            self.currentPosX += self.vx;
            self.currentPosY += self.vy;

            // reduce intertia force
            self.dragMomentumX = self.dragMomentumX * self.intertia;
            self.dragMomentumY = self.dragMomentumY * self.intertia;

            // redraw
            self.redraw();

            // repeat
            if (Math.abs(self.vx) > 0.15 || Math.abs(self.vy) > 0.15) {
                self.dragTimeout = setTimeout(function() {
                    self.stop_moving();
                }, 16);
            }
        },

        calculate_out_of_bounds : function() {
            var self = this;

            var zoomX = self.contentWidth*self.currentZoom;
            var zoomY = self.contentHeight*self.currentZoom;
            var minPosX = zoomX <= self.frameWidth ? (-zoomX + self.frameWidth)/2 : -zoomX + self.frameWidth;
            var minPosY = zoomY <= self.frameHeight ? (-zoomY + self.frameHeight)/2 : -zoomY + self.frameHeight;
            var maxPosX = zoomX <= self.frameWidth ? (-zoomX + self.frameWidth)/2: 0;
            var maxPosY = zoomY <= self.frameHeight ? (-zoomY + self.frameHeight)/2 : 0;

            self.dragOutOfBoundsX = 0;
            self.dragOutOfBoundsY = 0;

            if (self.currentPosX < minPosX) {
                self.dragOutOfBoundsX = self.currentPosX - minPosX;
            }
            if (self.currentPosY < minPosY) {
                self.dragOutOfBoundsY = self.currentPosY - minPosY;
            }
            if (self.currentPosX > maxPosX) {
                self.dragOutOfBoundsX = self.currentPosX - maxPosX;
            }
            if (self.currentPosY > maxPosY) {
                self.dragOutOfBoundsY = self.currentPosY - maxPosY;
            }
        },

        constrain_target_position : function() {
            var self = this;

            var zoomX = self.contentWidth*self.targetZoom;
            var zoomY = self.contentHeight*self.targetZoom;
            var minPosX = zoomX <= self.frameWidth ? (-zoomX + self.frameWidth)/2 : -zoomX + self.frameWidth;
            var minPosY = zoomY <= self.frameHeight ? (-zoomY + self.frameHeight)/2 : -zoomY + self.frameHeight;
            var maxPosX = zoomX <= self.frameWidth ? (-zoomX + self.frameWidth)/2: 0;
            var maxPosY = zoomY <= self.frameHeight ? (-zoomY + self.frameHeight)/2 : 0;

            if (self.targetPosX < minPosX) {
                self.targetPosX = minPosX;
            }
            if (self.targetPosY < minPosY) {
                self.targetPosY = minPosY;
            }
            if (self.targetPosX > maxPosX) {
                self.targetPosX = maxPosX;
            }
            if (self.targetPosY > maxPosY) {
                self.targetPosY = maxPosY;
            }
        },

        constrain_current_position : function() {
            var self = this;
            var minPosX = (-self.contentWidth)*self.currentZoom + self.frameWidth;
            var minPosY = (-self.contentHeight)*self.currentZoom + self.frameHeight;

            self.dragOutOfBoundsX = 0;
            self.dragOutOfBoundsY = 0;

            if (self.currentPosX > 0) {
                self.currentPosX = 0;
            }
            if (self.currentPosY > 0) {
                self.currentPosY = 0;
            }
            if (self.currentPosX < minPosX) {
                self.currentPosX = minPosX;
            }
            if (self.currentPosY < minPosY) {
                self.currentPosY = minPosY;
            }
        },

        redraw : function() {
            var self = this;

            if (!self.options.rubberband) {
                self.constrain_current_position();
            }

            self.obj_content.css({
                "width" : self.contentWidth * self.currentZoom,
                "height" : self.contentHeight * self.currentZoom,
                "left" : self.currentPosX,
                "top" : self.currentPosY
            });

            if (self.options.navigator) {
                self.redraw_navigator();
            }
        },

        redraw_navigator : function() {
            var self = this;

            if (!self.options.navigator) return;

            var scale = (self.contentWidth * self.currentZoom) / self.navigatorWidth;
            var px = -self.currentPosX / scale;
            var py = -self.currentPosY / scale;
            var px2 = ((self.contentWidth*self.currentZoom) - self.frameWidth - self.currentPosX) / scale;
            var py2 = ((self.contentHeight*self.currentZoom) - self.frameHeight - self.currentPosY) / scale;

            self.nav_window_width = self.navigatorWidth + px - px2;
            self.nav_window_height = self.navigatorHeight + py - py2;

            self.obj_nav_window.css({
                "width" : self.nav_window_width,
                "height" : self.nav_window_height,
                "left" : px,
                "top" : py
            });
        },

        update_size : function() {
            var self = this;

            if (self.frameWidth == self.obj.width() && self.frameHeight == self.obj.height()) {
               // return;
            }

            self.frameWidth = self.obj.width();
            self.frameHeight = self.obj.height();

            var imgRatio = self.img.width / self.img.height;
            var frameRatio = self.frameWidth / self.frameHeight;

            var old_width = self.obj_content.css('width');
            var old_height = self.obj_content.css('height');
            var old_left = self.obj_content.css('left');
            var old_top = self.obj_content.css('top');

            self.obj_image.unwrap();

            if (imgRatio < frameRatio) {
                self.obj_image.css({
                    "width" : "auto",
                    "height" : "100%"
                });
            } else {
                self.obj_image.css({
                    "width" : "100%",
                    "height" : "auto"
                });
            }

            self.contentWidth = self.obj_image.width();
            self.contentHeight = self.obj_image.height();

            self.obj_image.wrap('<div class="ndd-uberzoom-content"></div>');
            self.obj_content = self.obj.find(".ndd-uberzoom-content");

            self.obj_content.css({
                "position" : "absolute",
                "width" : old_width,
                "height" : old_height,
                "left" : old_left,
                "top" : old_top
            }).velocity({
                "width" : self.contentWidth * self.currentZoom,
                "height" : self.contentHeight * self.currentZoom,
                "left" : -(self.contentWidth * self.currentZoom)/2 + self.frameWidth/2 + "px",
                "top" : -(self.contentHeight * self.currentZoom)/2 + self.frameHeight/2 + "px"
            }, {duration: 100});

            self.obj_image.css({ "opacity" : "1", "width" : "100%", "height" : "100%" });

            self.defaultPosX = self.obj_content.position().left;
            self.defaultPosY = self.obj_content.position().top;
            self.currentPosX = self.defaultPosX;
            self.currentPosY = self.defaultPosY;

            self.redraw_navigator();
        }
    };

    $.fn[uberZoom] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + uberZoom)) {
                var record = new UberZoom( this, options);
                $.data(this, "plugin_" + uberZoom, record);
            }
        });
    };

})( jQuery, window, document );

