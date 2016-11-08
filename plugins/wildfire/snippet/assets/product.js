var zoomimg = null;

$(function() {

    var updateChoicerSelection = function(link){
         //for setting z-index effects
         $(link).parents('.choicer').find('.last-selected').removeClass('last-selected');
         $(link).parents('.choicer').find('.selected').removeClass('selected').addClass('last-selected');
         $(link).addClass('selected');
         $(link).parents('.choicer').find('.indicator').show().velocity({ top: ($(link).position().top - 1), left: ($(link).position().left - 1), width: $(link).outerWidth(true) + 2, height: ($(link).outerHeight(true) + 2)}, { easing: 'easeInOutCubic', duration: 400});
         $(link).parents('.variantchoicer').find('h3').addClass('selected').find('span.val').html($(link).attr('data-name'));

         if (showbuilder) {
            $(link).parents('.buildoption').find('.colorpreview a span').css('background-color',$(link).find('span').css('background-color'));
            if ($(link).data('color')){
                $(link).parents('.buildoption').data('val',$(link).data('color'));
            }
            if ($(link).parents('#custom_plug').length > 0){
                $(link).parents('.buildoption').find('.extraprice').toggle($(link).data('color') !== 'Black');
                updatePrice();
            }

            if (!$(link).hasClass('default') && $(link).parents('#custom_grip, .variantchoicer').length == 0){
                activateBuilderPreview();
            }
            updateBuilderPreview();
         }
    };

    var resetCustomization = function(){
        $('#gripbuilder #custom_plug .colorchoices a.default').click();
        $('#gripbuilder #custom_ring_outer').data('val','').find('.choicer a.selected').removeClass('selected');
        $('#gripbuilder #custom_ring_outer .indicator').hide();
        $('#gripbuilder #custom_ring_inner').data('val','').find('.choicer a.selected').removeClass('selected');
        $('#gripbuilder #custom_ring_inner .indicator').hide();
        $('#custom_engrave_text').val('');
        engrave_graphic.val('None').trigger('change');
        revertBuilderPreview();
    };

    function getImgSize(imgSrc){
    }

    function getImgWidth(imgSrc) {
        var newImg = new Image();

        newImg.onload = function() {
            var height = newImg.height;
            var width = newImg.width;
        }

        newImg.src = imgSrc; // this must be done AFTER setting onload
        return newImg.width;
    }

    function getImgHeight(imgSrc) {
        var newImg = new Image();

        newImg.onload = function() {
            var height = newImg.height;
            var width = newImg.width;
        }

        newImg.src = imgSrc; // this must be done AFTER setting onload
        return newImg.height;
    }

    function extractUrl(input)
    {
     // remove quotes and wrapping url()
     return input.replace(/"/g,"").replace(/url\(|\)$/ig, "");
    }

    var initCustomization = function(){
        $('#gripbuilder').css('display','block');

        if ($(window).width() < 1025){
            $('#gripbuilder').addClass('inline').insertAfter('.infopane .description-box');

        }

        if ($(window).height() < 1000 || $(window).width() < 1024){
            $('#gripbuilder').addClass('autocollapse');
            $('#gripbuilder .buildoption').each(function(){
                $(this).addClass('collapsed').find('.optionbody').data('orig-height',$(this).find('.optionbody').height()).height(0);
            });
        }
        revertBuilderPreview();

        $('#gripbuilder .colorchoices a.default').click();

        var testImg = new Image();
        testImg.onload = function(){
            $('#builderpreview .grip img').css('background-image','url("' + this.src+'")');
        
            var h = this.height;
            var w = this.width;

            if (h < 330) {
                $('#builderpreview').addClass('custom-short');
            } else if (h < 404) {
                $('#builderpreview').addClass('custom-medium');
            }

            var spacing = 2;
            if (w > 2040){
                spacing = 0;
                $('#builderpreview').addClass('custom-wide');
            }

            $('#left-side .grip img').reel({
                frames: 20,
                suffix: '',
                footage: 20,
                spacing: spacing, 
                frame: 3,
                area: '#builderpreview-inner'
            });


        }
        testImg.src = productimages[0].rotation[0].srcfull;

        Cufon.now();
    };

    var activateBuilderPreview = function(){
        if ($('#builderpreview:visible').length == 0 && !$('#builderpreview.showing').length > 0 && productimages[current_image_id].rotation){
            $('#builderpreview').addClass('showing');
            $('#imagezone .wrap .ndd-uberzoom-interface').hide();
            var imgzone = $('#imagezone .wrap');
            var imgwrap = $('#imagezone .wrap .ndd-uberzoom-content');
            var img = $('#imagezone .wrap img');
            var asp = img.width() / img.height();
            var newh = 350;
            var neww = newh*asp;
            var newleft = ((imgzone.width() -  neww) / 2) - 36; //offset because of plugs
            var newtop = (($('#builderpreview').height() -  newh) / 2) - 31; //offset since preview grips are vertically offset
            if (imgwrap.length == 0){
                //not loaded yet
                $('.productpane .previewpane .spinnerbox').addClass('hiding');
                $('#builderpreview').removeClass('showing').show();
                $('#imagezone').hide();
            } else {
                imgwrap.data({
                    'orig-width': imgwrap.width(), 
                    'orig-height': imgwrap.height(),
                    'orig-top': imgwrap.css('top'),
                    'orig-left': imgwrap.css('left')
                }).velocity({
                    'rotateZ': -45, 
                    'height': newh, 
                    'width': neww, 
                    'top': newtop, 
                    'left': newleft 
                }, {duration: 500, easing: 'easeInOutCubic', complete: function(){
                    $('#imagezone').css('opacity',1).velocity({'opacity': 0}, {duration: 100, complete: function(){
                       $(this).css('display','none');
                    }});
                    //position grips in middle
                    var lpos = parseInt($('#builderpreview #left-side').css('left'));
                    var rpos = parseInt($('#builderpreview #right-side').css('left'));
                    $('#builderpreview #left-side').data('orig-left', lpos).css('left', (lpos+rpos)/2);
                    $('#builderpreview #right-side').data('orig-left', rpos).css('left', (lpos+rpos)/2);
                    $('#builderpreview').css('opacity',0).show().velocity({'opacity': 1 }, { duration: 100, complete: function(){
                        $('#builderpreview').removeClass('showing');
                        //animate grips back to normal
                        $('#builderpreview #left-side').velocity({'left': $('#builderpreview #left-side').data('orig-left')}, {duration: 100, easing: 'easeInOutCubic'});
                        $('#builderpreview #right-side').velocity({'left': $('#builderpreview #right-side').data('orig-left')}, {duration: 100, easing: 'easeInOutCubic'});
                    }});
                }}) ;
            }
        }
    };

    var revertBuilderPreview = function(){
        if ($('#builderpreview:visible').length > 0){
            var lpos = parseInt($('#builderpreview #left-side').css('left'));
            var rpos = parseInt($('#builderpreview #right-side').css('left'));
            $('#builderpreview #left-side').velocity({'left': (lpos+rpos)/2}, {duration: 100, easing: 'easeInOutCubic'});
            $('#builderpreview #right-side').velocity({'left': (lpos+rpos)/2}, {duration: 100, easing: 'easeInOutCubic', complete: function(){
                $('#builderpreview').removeClass('showing').velocity({'opacity': 0 }, {duration: 100, complete: function(){
                    $('#builderpreview #left-side').css('left','');
                    $('#builderpreview #right-side').css('left','');
                    $('#builderpreview').hide();
                }});
                $('#imagezone').css('display','block');

                var imgzone = $('#imagezone .wrap');
                var imgwrap = $('#imagezone .wrap .ndd-uberzoom-content');
                var img = $('#imagezone .wrap img');
                var asp = img.width() / img.height();
                var newh = neww = 0;

                //set up the initial position
                newh = 350;
                neww = newh*asp;
                newleft = ((imgzone.width() -  neww) / 2) - 20; //offset because of plugs
                newtop = (($('#builderpreview').height() -  newh) / 2) - 47; //offset since preview grips are vertically offset

                imgwrap.css({
                    'height': newh,
                    'width': neww,
                    'top': newtop,
                    'left': newleft
                });

                $('#imagezone').velocity({'opacity': 1 }, {duration: 100, complete: function(){
                    imgwrap.velocity({'rotateZ': 0}, {duration: 200, easing: 'easeInOutCubic', complete: function(){
                        $('#imagezone .wrap .ndd-uberzoom-interface').show();
                        productImageResize();
                    }});
                }});           
            }});
        }
    };

    var updateBuilderPreview = function(){
        //grip color
        if (productimages[current_image_id].rotation){
            $('#builderpreview .grip img').css('background-image','url("'+productimages[current_image_id].rotation[0].srcfull+'")');
        } else {
            if ($('#builderpreview:visible').length > 0){
                revertBuilderPreview();
            }
        }
        //outer ring color
        if (hide_outer_ring){
            $('#builderpreview .top-ring').css('opacity',0);
        } else {
            var outcol = $('#custom_ring_outer').data('val') == '' ? 'black' : $('#custom_ring_outer').data('val').toLowerCase();
            $('#builderpreview .top-ring img').css('background-image','url("/themes/ls2016/assets/gripbuilder/rings-rotating-top-' + outcol +'.png")');
        }
        //inner ring color
        var incol = $('#custom_ring_inner').data('val') == '' ? 'black' : $('#custom_ring_inner').data('val').toLowerCase();
        $('#builderpreview .bottom-ring img').css('background-image','url("/themes/ls2016/assets/gripbuilder/rings-rotating-bottom-' + incol +'.png")');
        //plug color
        $('#builderpreview #plugs').removeClass().addClass($('#custom_plug').data('val').toLowerCase());
        //text
        $('#builderpreview .monogram-text').text($('#custom_engrave_text').val());
        Cufon.replace('#builderpreview .monogram-text', { fontFamily: $('#custom_engrave_font').find(':selected').data('typeface') });
        Cufon.refresh();
        $('#builderpreview .monogram').attr('class', 'monogram ' + $('#custom_engrave_graphic').find(':selected').data('graphic'));
    }

    var updateBuilderEngraving = function(){
        var text = $('#custom_engrave_text').val();
        var graphic = $('#custom_engrave_graphic');

        if (text.length > 0){
            activateBuilderPreview();
        }
        updateBuilderPreview();
        updatePrice();
        $('#custom_engrave .engravepreview .text').html(text);
        $('#custom_engrave .engravepreview .graphic').removeClass().addClass('engrave_graphic_optioninner graphic');
        if (text.length > 0){
            $('#custom_engrave .engravepreview .graphic').addClass(graphic.find(':selected').data('graphic'));
            if ($('#custom_ring_inner').data('val') == '') $('#custom_ring_inner .choicer a:first').click();
            if ($('#custom_ring_outer').data('val') == '' && !hide_outer_ring) $('#custom_ring_outer .choicer a:first').click();
        } else {
            $('#custom_engrave .engravepreview .graphic').removeClass();
        }
    }

    $('#gripbuilder #custom_engrave_text').on('input',updateBuilderEngraving);
    $('#gripbuilder #custom_engrave_font').on('change',updateBuilderEngraving);
    $('#gripbuilder #custom_engrave_graphic').on('change',updateBuilderEngraving);
    $('#gripbuilder #custom_reset a').click(resetCustomization);
    $('#gripbuilder .preheader').click(function(){
        var contents = $('#gripbuilder .contents');
        if ($(this).hasClass('closed')){
            $(this).removeClass('closed');
            contents.velocity({ height: contents.data('orig-height') }, { duration: 250, easing: 'easeInOutCubic', complete: function() { 
                contents.height('auto'); 
                productImageResize();
            }});
            $('.productpage .productpane').addClass('custom');
        } else {
            $(this).addClass('closed');
            contents.data('orig-height',contents.height()).velocity( { height: 0 }, { duration: 250, easing: 'easeInOutCubic', complete: function(){
                resetCustomization();
                productImageResize();
            }});
        }
    });

    $('#gripbuilder .optionheader').click(function(){
        var par = $(this).parents('.buildoption');
        if (par.hasClass('collapsed')){
            if ($('#gripbuilder').hasClass('autocollapse')){
                $('#gripbuilder .buildoption:not(.collapsed)').each(function(){
                    $(this).addClass('collapsed');
                    cur = $(this).find('.optionbody');
                    cur.data('orig-height', cur.height()).velocity({ height: 0 }, { duration: 250, easing: 'easeInOutCubic' });
                });
            }
            par.removeClass('collapsed');
            par.find('.optionbody').velocity({ height: par.find('.optionbody').data('orig-height') }, { duration: 250, easing: 'easeInOutCubic', complete: function() { 
                par.find('.optionbody').height('auto'); 
                productImageResize();
            }});
        }
        else {
            par.addClass('collapsed');
            par.find('.optionbody').data('orig-height',par.find('.optionbody').height()).velocity({ height: 0 }, { duration: 250, easing: 'easeInOutCubic', complete: function(){
                productImageResize();
            }});
        }
    });

    var fontSelection = function(el){
        var ret = el.text;
        if (el.element){
            ret = $('<span class="engrave_font_option" />').append('<span class="engrave_font_optioninner '+ $(el.element).data('typeface') + '">&nbsp;</span>');
        }
        return ret;
    };
    var graphicSelection = function(el){
        var ret = el.text;
        if (el.element){
            ret = $('<span class="engrave_graphic_option" />').append('<span class="engrave_graphic_optioninner '+ $(el.element).data('graphic') + '">&nbsp;</span>').append('<span>' + ret + '</span>');
        }

        return ret;
    };

    var engrave_font = $('#custom_engrave_font').select2({
        minimumResultsForSearch: 500, 
        width: '80%',
        templateSelection: fontSelection,
        templateResult: fontSelection
    });

    var engrave_graphic = $('#custom_engrave_graphic').select2({
        minimumResultsForSearch: 500, 
        width: '80%',
        templateSelection: graphicSelection,
        templateResult: graphicSelection
    });

    //click function for color and sizes
    $('.productpage .choicer a').click(function(el){
         if ($(this).attr('data-colorway-id')){
             updateVariantSelection($(this).attr('data-colorway-id'), false);
             if (showbuilder){
                 var other = $('.productpage .choicer a[data-colorway-id="' + $(this).attr('data-colorway-id') + '"]').filter(':not(.selected)').not(this);
                 if (other.length) updateChoicerSelection(other);
             }
         }
         else if ($(this).attr('data-size-id')) {
             updateVariantSelection(false, $(this).attr('data-size-id'));
        }
        updateChoicerSelection(this);
     });      
    
   
    selected_variant = sole_variant || false;
    var selected_colorway_id = sole_colorway_id || false;
    var selected_size_id = sole_size_id || false;
    var has_colorways = null;
    var has_sizes = null;
    var current_image_id = false;
    var give_spin_demo = true;
    
    function updatePrice()
    {
        price = selected_variant.price;

        if (showbuilder){
            var text = $('#custom_engrave_text').val();
            var graphic = $('#custom_engrave_graphic');
            if (graphic.val() != "None" || text.length > 0){
                price += 3.99;
            }

            if ($('#custom_plug').data('val') != 'Black'){
                price += 3.99;
            }
        }


        $('#price').html('$' + price); 
    }

    function updateVariantSelection(colorway_id, size_id)
    {
        var selected_colorway_id = (colorway_id ? colorway_id : false);
        var selected_size_id = (size_id ? size_id : false);
        
        //filter images
        updateImage(searchImage(selected_colorway_id,selected_size_id));
        
        //get from table
        found = false;
        for (i =0 ; i < variantinfo.length; i++)
        {
            if (variantinfo[i].colorway_id == selected_colorway_id)
            {
                if (variantinfo[i].size_id == selected_size_id)
                {
                    found = true;
                    selected_variant = variantinfo[i];
                    updatePrice();
                    if (selected_variant.inventory == -99)
                    {
                        $('#addtocart').addClass('disabled').html('Coming Soon')
                    }
                    else if (selected_variant.inventory <= 0)
                    {
                        $('#addtocart').addClass('disabled').html('Sold Out')
                    }
                    else
                    {
                        $('#addtocart').removeClass('disabled').text('Add To Cart');
                    }
                }
            }
        }
    }
    
    var addToCartAction = function (evt)
    {
        if (!selected_variant)
        {
            if (has_colorways && !selected_colorway_id) $.oc.flashMsg({ text: 'Please make a choice from the color options', class: 'error', interval: 6});
            else if (has_sizes && !selected_size_id) $.oc.flashMsg({ text: 'Please make a choice from the size options', class: 'error', interval: 6});
        }
        else if (selected_variant.inventory <= 0)
        {    
            if (variantinfo.length > 0) $.oc.flashMsg({ text: 'Sorry, we are sold out of the product with the options you selected. Try another color/size or check back later.', class: 'error', interval: 10});
            else $.oc.flashMsg({ text: 'Sorry, we are sold out of this product right now. Please try again later.', class: 'error', interval: 10});
        }
        else
        {
            var obj = selected_variant;
            obj.image = { src: productimages[current_image_id].thumb };
            
            var props = {};
            quantity = $('#quantity').val();

            //record cart adding -- for best sellers
            $.request('onCartTrack', {
                data: {variant_id: selected_variant.myid}
            });

            if (showbuilder){
                var outer = $('#custom_ring_outer').data('val');
                var inner = $('#custom_ring_inner').data('val');
                var plug = $('#custom_plug').data('val');
                var engrave_text = $('#custom_engrave_text').val();
                var props = {};

                //add inner ring
                if (engrave_text || inner !== '' || outer !== ''){
                    if (engrave_text || inner) props['Custom Inner Ring Color'] = inner;
                    if (engrave_text || outer) props['Custom Outer Ring Color'] = hide_outer_ring ? 'N/A' : outer;
                }
                addOrUpdateVariant(obj, props, parseInt(quantity), evt.target);
    
                //add plug
                if (plug !== 'Black'){
                    addOrUpdateVariant(plug_variant, {
                        'Plug Color': plug, 
                        'Goes With': obj.productTitle + ' - ' +obj.title 
                    }, parseInt(quantity), evt.target);
                }

                //add engraving
                if (engrave_text){
                    addOrUpdateVariant(engrave_variant, {
                        'Text': engrave_text, 
                        'Font': $('#custom_engrave_font').val(), 
                        'Graphic': $('#custom_engrave_graphic').val(),
                        'Goes With': obj.productTitle + ' - ' + obj.title 
                    }, parseInt(quantity), evt.target);
                }
            } else {
                addOrUpdateVariant(obj, props, parseInt(quantity), evt.target);
            }
        }
    }
    var drawImage = function(imageobj)
    {

        var image_id = imageobj.data('image_id_loading');
        var size = imageobj.data('image_size');
        var rotation = false;
        imageinfo = productimages[image_id];
        if (imageinfo.rotation && imageinfo.rotation.length > 1)
        {
            rotation = [];
            for (var i=0; i < imageinfo.rotation.length; i++) {
                rotation.push(imageinfo.rotation[i]['src'+size]);
            }
        }

        //determine the area size
        area = $('#imagezone .wrap');
        area.append(imageobj);

        area = $('#imagezone .wrap');
        imageobj = area.find('img');
        $('#imagezone .rotateinfo').css('opacity','0');
        if (area && imageobj)
        {
            area.css({ width: 'auto', height: 'auto', top: 0, left: 0, margin: 0});

            //modify the area to not be taller than the screen minus header area
            real_estate = $(window).height() - area.offset().top;
            if (real_estate < area.height() && real_estate > 200)area.height(real_estate + 'px');
            
            img_aspect = imageobj[0].naturalWidth/imageobj[0].naturalHeight;
            div_aspect = area.innerWidth()/area.innerHeight();
            if (area.width() < imageobj[0].naturalWidth || area.height() < imageobj[0].naturalHeight || rotation)
            {
                imageobj.css({'margin-top':0,'margin-left':0,width:'auto',height:'auto', opacity: 1});
                zoomimg = imageobj.uberZoom({ startInFullscreen : false, fullscreen: ($(window).width() < 1024), navigator : true, navigatorImagePreview : true, rubberband : true, maxZoom: (rotation ? 3 : 'auto'), rotationImages: rotation }); 
            }
            else
            {
                if (img_aspect > div_aspect) imageobj.css({ width: Math.min(imageobj[0].naturalWidth, area.innerWidth())}).css({height: imageobj.width/img_aspect});
                else imageobj.css({ width: Math.min(imageobj[0].naturalWidth, area.innerHeight()*img_aspect), height: Math.min(imageobj[0].naturalHeight,area.innerHeight()) });
                imageobj.css({ 'margin-top': (area.innerHeight() - imageobj.height())/2, 'margin-left': (area.innerWidth() - imageobj.width())/2 })
                imageobj.css({opacity: 1});
            }
            
        }

        $('.productpane .previewpane .spinnerbox').addClass('hiding');
    }
    
    function productImageResize()
    {
        area = $('#imagezone .wrap');
        //modify the area to not be taller than the screen minus header area
        real_estate = $(window).height() - area.offset().top;
        if (real_estate < area.height() && real_estate > 200)area.height(real_estate + 'px');

        var vis = $('.ndd-uberzoom-main-image:visible');
        if (vis.length > 0){
            vis.each(function(){ 
                $(this).data('plugin_uberZoom').update_size();
            });
        } else {
            var area = $('#imagezone .wrap');
            var imageobj = area.find('img');
            var img_aspect = imageobj[0].naturalWidth/imageobj[0].naturalHeight;
            var div_aspect = area.innerWidth()/area.innerHeight();
            var params = { 'margin-top': (area.innerHeight() - imageobj.height())/2, 'margin-left': (area.innerWidth() - imageobj.width())/2 };
            if (img_aspect > div_aspect) {
                params.width = Math.min(imageobj[0].naturalWidth, area.innerWidth());
                params.height = imageobj.width/img_aspect;
            } else {
                params.width = Math.min(imageobj[0].naturalWidth, area.innerHeight()*img_aspect);
                params.height = Math.min(imageobj[0].naturalHeight,area.innerHeight());
            }
            imageobj.velocity(params, { duration: 200 });
        }
    }    

    function choicerResize()
    {
        $('.choicer').each(function(){
            var select = $(this).find('a.selected');
            if (select.length > 0){
               $(this).find('.indicator').css({
                   top: select.position().top - 1, 
                   left: select.position().left - 1, 
                   width: select.outerWidth(true) + 2, 
                   height: select.outerHeight(true) + 2
                });
            }
        });
    }

    function drawThumbnails(imageset){
        var zone = $('#imagezone');
        var zonear = zone.width() / zone.height();
        var thumbs = zone.find('.thumbs');

        zone.removeClass();
        thumbs.find('.inner').empty();
        if (zonear < 1) zone.addClass('thumbs bottom');
        else zone.addClass('thumbs right');

        for (var key in imageset){
            var imginfo = productimages[imageset[key]];
            var newitem = $('<a class="thumb" />').data('imgid', imageset[key]).html('<img src="'+imginfo.thumb+'"/><span class="'+ (imginfo.label ? 'label' : '') +'">'+imginfo.label+'</span>');
            thumbs.find('.inner').append(newitem);
        }         

        thumbs.find('a').first().addClass('selected');
        thumbs.find('a').click(function(){
            $('#imagezone .thumbs a.selected').removeClass('selected');
            $(this).addClass('selected');
            setupImage($(this).data('imgid'));
        });
    }

    function hideThumbnails(){
        $('#imagezone').removeClass();
    }

    var setupImage = function(image_id)
    {
        current_image_id = image_id;

        //make sure indicator is showing
        $('#imagezone .wrap').empty();
        $('.productpane .previewpane .spinnerbox').removeClass('hiding');

        loader = new Image();
        var imageinfo = productimages[image_id];
        var src = '';
        if (imageinfo.rotation && imageinfo.rotation.length > 1)
        {
            //determine what size image to use
            if ($(window).width() > 1600 ) size = 2000;
            else if ($(window).width() > 800) size = 1000;
            else size = 500;
            src = imageinfo.rotation[0]['src'+size];
        } else {
            //determine what size image to use
            if ($(window).width() > 1023) size = 2000;
            else size = 1000;
            src = imageinfo['src'+size];
        }
        $(loader).data('image_id_loading',image_id).data('image_size', size);
        $(loader).css('opacity',0);
        loader.onload = function(){
            drawImage($(this));
            current_image_id = image_id;
            this.onload = null;
        };
        loader.src = src;
    }

    var updateImage = function(imageset)
    {
        var image_id = imageset[0];

        if (imageset.length > 1){
            drawThumbnails(imageset)
        } else {
            hideThumbnails();
        }
        if (current_image_id === image_id)
        {
            return false;
        }
        else if (productimages[image_id])
        {
            setupImage(image_id);
        }
    }
    
    var searchImage = function(colorway_id, size_id)
    {
        var imageset = [];
        var has_unmatching = false;
        var fullset = [];

        if (productimages.length == 1) return [0];
        
        
        //find match for both
        for (i = 0; i < productimages.length; i++)
        {
            fullset.push(i);
            if (productimages[i].colorway_id){
                has_unmatching = true;
            }
            if (productimages[i].size_id){
                has_unmatching = true;
            }
            if ((!has_colorways || productimages[i].colorway_id == colorway_id) && (!has_sizes || productimages[i].size_id == size_id))
            {
                imageset.push(i);
            }
        }
        if (imageset.length > 0){
            return imageset;
        }
        
        //find match for one
        for (i = 0; i < productimages.length; i++)
        {
            if ( ((colorway_id && productimages[i].colorway_id == colorway_id) && productimages[i].size_id === false) || (( size_id && productimages[i].size_id == size_id) && productimages[i].colorway_id === false))
            {
                imageset.push(i);
            }
        }                
        if (imageset.length > 0){
            return imageset;
        }
        
        //give up and use first image
        return has_unmatching ? [0] : fullset;
    
    }
    
    //check for zero stock on all variants
    has_stock = false;
    coming_soon = true;
    found_color = null;
    found_size = null;
    for (i =0 ; i < variantinfo.length; i++)
    {
        if (variantinfo[i].inventory > 0){
            has_stock = true;
        }
        if (variantinfo[i].inventory != -99){
            coming_soon = false;
        }
        if (variantinfo[i].colorway_id && found_color != variantinfo[i].colorway_id)
        {
            if (found_color != null) has_colorways = true;
            else found_color = variantinfo[i].colorway_id;
        }
        if (variantinfo[i].size_id && found_size != variantinfo[i].size_id)
        {
            if (found_size != null) has_sizes = true;
            else found_size = variantinfo[i].size_id;
        }
    }
    if (coming_soon) $('#addtocart').addClass('disabled').text('Coming Soon');
    else if (!has_stock) $('#addtocart').addClass('disabled').text('Sold Out');
    
    //choose default variant
    if (variantinfo.length == 1) selected_variant = variantinfo[0];
    
    //choose default image and load
    if (!showbuilder) {
        updateImage(searchImage(false,false));
    }
    
    //set up click event
    $('#addtocart').click(addToCartAction);
     
     //pad sizes to full width if there's more than one row
     var widest_amt = 0;
     var widest_idx = null;
     var rowlengths = [];
     var rowitems = [];
     var lastvert = false;
     var rownum = -1;
     
     var choices = $('.productpage .sizechoices a')
     if (choices.first().height() * 2 <= $('.productpage .sizechoices').first().height() )
     {
         for (var i = 0; i < choices.length; i ++)
         {
             var item = $(choices[i]);
             if (item.position().top > lastvert || !lastvert)
             {
                 rownum++;
                 lastvert = item.position().top;
                 rowlengths[rownum] = item.outerWidth(true);
                 rowitems[rownum] = [];
             } else {
                 rowlengths[rownum] += item.outerWidth(true);
             }
            rowitems[rownum].push(item);
             if (rowlengths[rownum] > widest_amt){
                widest_amt = rowlengths[rownum];
                widest_idx = rownum;
             }
         }

         if (rowlengths.length == 2)
         {  
             while (rowlengths[0] - rowitems[0][rowitems[0].length - 1].outerWidth() > rowlengths[1] + rowitems[0][rowitems[0].length -1].outerWidth())
             {
                 item = rowitems[0].pop();
                 rowlengths[0] -= item.outerWidth();
                 widest_amt = rowlengths[0];
                 rowlengths[1] += item.outerWidth();
                 rowitems[1].unshift(item);
             }
         }
        
         for (var j = 0; j < rowlengths.length; j++)
         {
             if (j > 0 )
             {
                 rowitems[j][0].before('<br />');
             }
             if (j != widest_idx){
                 var amt_to_add = (widest_amt - rowlengths[j])/rowitems[j].length;
                 for (var k = 0; k < rowitems[j].length; k++)
                 {
                     rowitems[j][k].outerWidth(Math.round(rowitems[j][k].outerWidth() + amt_to_add));
                 }
                 
             }
         }

         
     }
     var maxoverall = 0;
     for (i = 0; i < rowitems.length; i++){
         overall = rowitems[i][rowitems[i].length - 1].position().left + rowitems[i][rowitems[i].length - 1].outerWidth(true);
         if (overall > maxoverall) maxoverall = overall;
     }
     for (i = 0; i < rowitems.length; i++){
         var diff = maxoverall - rowitems[i][rowitems[i].length - 1].position().left - rowitems[i][rowitems[i].length - 1].outerWidth(true);
         if (diff > 1)
         {
             rowitems[i][rowitems[i].length - 1].outerWidth(rowitems[i][rowitems[i].length - 1].outerWidth() + diff);
         }
     }             

    $("#quantity").select2({ 
        minimumResultsForSearch: 20, 
        width: '45%',
        templateSelection: function(el){ return 'Quantity: ' + el.text; }
    });

    $(".productpage .description-box .readmore").click(function() {
         
      par = $(this).parent();

        if (par.hasClass('active'))
        {
            par.removeClass('active').outerHeight(par.data('orig-collapsedheight'));
        }
        else
        {
            par.addClass('active').data('orig-collapsedheight', par.outerHeight()).outerHeight(par.data('orig-fullheight'));
        }
    });

    //check for too-long content
    var minOverflow = 50; // number of pixels over-height needed to force read-more box. (prevents read-more from cutting off just 1 line of text)
    var minRemaining = 100; // minimum to keep visible.
    if ($('.productpane').offset().top + $('.productpane').height() > ($(window).height() + minOverflow))
    {
        diff = $('.productpane').offset().top + $('.productpane').height() - $(window).height();
        box = $('.productpage .description-box');
        if (box.outerHeight() > diff + minRemaining)
        {
            $('.productpage .description-box .readmore').show();
            box.data('orig-fullheight', box.outerHeight() + $('.productpage .description-box .readmore').outerHeight()).outerHeight(box.outerHeight() - diff);
            box.data('org-collapsedheight', box.outerHeight());
        }
    }

    if (showbuilder) {
        initCustomization();
    }


    (function($,sr){
    
      // debouncing function from John Hann
      // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
      var debounce = function (func, threshold, execAsap) {
          var timeout;
    
          return function debounced () {
              var obj = this, args = arguments;
              function delayed () {
                  if (!execAsap)
                      func.apply(obj, args);
                  timeout = null;
              };
    
              if (timeout)
                  clearTimeout(timeout);
              else if (execAsap)
                  func.apply(obj, args);
    
              timeout = setTimeout(delayed, threshold || 100);
          };
      }
      // smartresize 
      jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };
    
    })(jQuery,'smartresize');
    
    
    // usage:
    $(window).smartresize(function(){
      productImageResize();
      choicerResize();
    });

});
