$(function()
{


  var last_map_query = { w: 0, e: 0, s: 0, n: 0 };
  var last_map_data = null;
  var infowindow = null;
  var origbounds = null;
  var map = null;
  var requery = true;
  var doing_geocode = false;
  
  
  function geocodeLatLng(geocoder,lat,lng,field) {
    var i, cmp;
    var local = [];
    var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
    var output = '';
    
    geocoder.geocode({'location': latlng}, function(results, status) {
      if (status === google.maps.GeocoderStatus.OK) {
        doing_geocode = true;
        if (results[0]) {        
            local_1 = local_2 = local_3 = cc = false;
            cmp = results[0].address_components;            
            for (i = 0; i < cmp.length; i ++)
            {
                if ((cmp[i].types[0] && cmp[i].types[0] == 'administrative_area_level_3') ||  (cmp[i].types[1] && cmp[i].types[1] == 'administrative_area_level_3'))
                {
                    local_3 = cmp[i].short_name;
                }
                else if ((cmp[i].types[0] && cmp[i].types[0] == 'administrative_area_level_2') ||  (cmp[i].types[1] && cmp[i].types[1] == 'administrative_area_level_2'))
                {
                    local_2 = cmp[i].short_name;
                }
                else if ((cmp[i].types[0] && cmp[i].types[0] == 'administrative_area_level_1') ||  (cmp[i].types[1] && cmp[i].types[1] == 'administrative_area_level_1'))
                {
                    local_1 = cmp[i].short_name;
                }
                else if ((cmp[i].types[0] && cmp[i].types[0] == 'country') ||  (cmp[i].types[1] && cmp[i].types[1] == 'country'))
                {
                    cc = cmp[i].short_name;
                }
            }
            if ((local_1 && local_2 && local_3) || (local_1 && local_3)) output = local_3 + ', ' + local_1;
            else if (local_1 && local_2) output = local_2 + ', ' + local_1;
            else if (local_2 && local_3) output = local_3 + ', ' + local_2;
            
            field.value = output;
            searched_name = output;
                        
            doing_geocode = false;
            map.setCenter(latlng);
            map.setZoom(15);
            searched_pos = latlng;
            searched_cc = cc;
            use_default = false;
        }
      }
    });

  }
  
  var clusterOptions  = {
        imagePath: '/themes/common/assets/images/maps/m',
        maxZoom: 9,
        gridSize: 60,
        averageCenter: true,
        styles: [{
            url: '/themes/common/assets/images/maps/pin.png',
            height: 48,
            width: 30,
            textColor: '#ffffff',
            textSize: 15,
            anchorText: [-8,0]
        }]
  };
   
  
    function drawMarkers(data,map){
        var i;
        var expanded = false;
        var bnds = new google.maps.LatLngBounds();
        for (i in data.dl) {
            var mylatlng = new google.maps.LatLng(parseFloat(data.dl[i].latitude),parseFloat(data.dl[i].longitude));
            var marker = new google.maps.Marker({
                position: mylatlng,
                title: data.dl[i].title,
                info: data.dl[i].infowindow
            });
            
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(this.info);
                infowindow.open(map, this);
            });
            
            dealerMarkerArray.push(marker);
            
            if (data.dl[i].expand){
                expanded = true;
                bnds.extend(mylatlng);
            }
        }
        for (i in data.ds) {
           var mylatlng = new google.maps.LatLng(parseFloat(data.ds[i].latitude),parseFloat(data.ds[i].longitude));
           var marker = new google.maps.Marker({
                position: { lat: parseFloat(data.ds[i].latitude), lng: parseFloat(data.ds[i].longitude)},
                icon: '/themes/common/assets/images/maps/ds.png', 
                map: map,
                title: data.ds[i].title,
                info: data.ds[i].infowindow
            });
            
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(this.info);
                infowindow.open(map, this);
            });

            distributorMarkerArray.push(marker);
            
            if (data.ds[i].expand){
                expanded = true;
                bnds.extend(mylatlng);
            }
        }
        
        if (expanded){
            if (searched_pos){ 
                var srch = new google.maps.LatLng(searched_pos.lat,searched_pos.lng);
                bnds.extend(srch);
            } else {
                bnds.extend(map.getCenter());
            }
            requery = false;
            map.fitBounds(bnds);
        }
        
        updateList(data,map);
    }
  
    function updateList(data,map,filterit){
        var lists = $('.lists');
        var distarea = $('#distlist');
        var dealarea = $('#deallist');
        var distcnt = 0;
        var dealcnt = 0;
        
        distarea.hide().removeClass().find('.dealer').remove();
        dealarea.hide().removeClass().find('.dealer').remove();;
        dealarea.find('div').hide();
        
        if (data.dl.length == 0) {
            dealarea.find('.notfound').show();
            dealarea.show();
        } else {
            var dhtml = '';
            for (i in data.dl) {
                if (filterit){
                    var mylatlng = new google.maps.LatLng(parseFloat(data.dl[i].latitude),parseFloat(data.dl[i].longitude));
                    if (!map.getBounds().contains(mylatlng)){
                        continue;
                    }                 
                }
                dealcnt++;
                dhtml += '<div class="dealer">' + data.dl[i].infowindow + '</div>';
            }
            if (dealcnt > 50){
                dealarea.find('.toomany').show();
                dealarea.show();
            } else {
                dealarea.append(dhtml);
                dealarea.show();
            }
        }
        if (data.ds.length > 0) {
            var dhtml = '';
            for (i in data.ds) {
                if (filterit && (data.cc == 'US' || data.cc != data.ds[i].cc)){
                    
                    var mylatlng = new google.maps.LatLng(parseFloat(data.ds[i].latitude),parseFloat(data.ds[i].longitude));
                    if (!map.getBounds().contains(mylatlng)){
                        continue;
                    }
                }
                distcnt++;
                dhtml += '<div class="dealer">' + data.ds[i].infowindow + '</div>';
            }
            if (distcnt > 0){
                distarea.append(dhtml);            
                distarea.show();
            } else {
                distarea.hide();
            }
        } else {
            distarea.hide();
        }
        if (distcnt > 0 && (dealcnt == 0 || dealcnt <= distcnt)) {
            distarea.addClass('equal');
            dealarea.addClass('equal');            
        } else if (distcnt > 0 && dealcnt > 0) {
            distarea.addClass('recessive');
            dealarea.addClass('dominant');
        }
        
        if (data.cc == 'US'){
            //dealers first
            lists.prepend(dealarea);
        } else {
            //distributors first
            lists.prepend(distarea);
        }
        
        if (data.dragged) {
            searched_pos = null;
            searched_name = null;
            searched_cc = null;
        }
    }
  
    function drawMap(){
        bounds = map.getBounds();
        sw = bounds.getSouthWest();
        ne = bounds.getNorthEast();
        swLat = sw.lat();
        swLng = sw.lng();
        neLat = ne.lat();
        neLng = ne.lng();
        
        if (origbounds === null){
            origbounds = bounds;
        } else if( origbounds.getSouthWest().lat() != bounds.getSouthWest().lat()) {
            use_default = false;
        }
        
        if (!requery)
        {
            requery = true;
            return;
        }
        if (doing_geocode)
        {
            return;
        }
        
        //Load the map data
        if (!(swLat > last_map_query.s && neLat < last_map_query.n && swLng > last_map_query.w && neLng < last_map_query.e))
        {
            $.request('onLoadMarkers', {
                data: {
                    s:swLat, w:swLng, n:neLat, e:neLng, 
                    cat: dealerCatId,
                    frm: searched_pos,
                    frm_name: searched_name,
                    frm_cc: searched_cc,
                    do_na: use_default ? 1 : 0
                },
                success: function(data) {
                    var clusterExists = false;
                    var i;
                    if (dealerMarkerArray.length > 0){
                        for (i in dealerMarkerArray) {
                            dealerMarkerArray[i].setMap(null);
                        }
                        dealerMarkerArray.length = 0;
                        mc.clearMarkers();
                        clusterExists = true;
                    }
                    if (distributorMarkerArray.length > 0){
                        for (i in distributorMarkerArray) {
                            distributorMarkerArray[i].setMap(null);
                        }
                        distributorMarkerArray.length = 0;
                        mc.clearMarkers();
                    }                    
                    if (data.ds.length === 0 && data.dl.length === 0)
                    {
                        updateList(data,map);
                    }
                    else if (clusterExists) {
                        drawMarkers(data,map);
                        mc.addMarkers(dealerMarkerArray);
                    } else {
                        drawMarkers(data,map);
                        mc = new MarkerClusterer(map, dealerMarkerArray, clusterOptions);
                    }
                    last_map_query = { s: swLat, n: neLat, w: swLng, e: neLng };
                    last_map_data = data;
                }
            });
        }
        else
        {
            updateList(last_map_data,map,true);
        }
    }

    function pacSelectFirst(input) {
        // store the original event binding function
        var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;

        function addEventListenerWrapper(type, listener) {
            // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
            // and then trigger the original listener.
            if (type == "keydown") {
                var orig_listener = listener;
                listener = function(event) {
                    var suggestion_selected = $(".pac-item-selected").length > 0;
                    if (event.which == 13 && !suggestion_selected) {
                        var simulated_downarrow = $.Event("keydown", {
                            keyCode: 40,
                            which: 40
                        });
                        orig_listener.apply(input, [simulated_downarrow]);
                    }

                    orig_listener.apply(input, [event]);
                };
            }

            _addEventListener.apply(input, [type, listener]);
        }

        input.addEventListener = addEventListenerWrapper;
        input.attachEvent = addEventListenerWrapper;
    }
    
    initGeo = function() {
        
        var mapel = document.getElementById('map');
        var input = document.getElementById('geoinput');    
        var geocoder = new google.maps.Geocoder();
        var mc;
        
        if (mapel)
        {
            map = new google.maps.Map(document.getElementById('map'), {
              center: defaultLoc,
              zoom: defaultZoom,
              mapTypeId: google.maps.MapTypeId.TERRAIN
            });
    
            // Try HTML5 geolocation.
            if (!searched_name && navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                  lat: position.coords.latitude,
                  lng: position.coords.longitude
                };
                
                geocodeLatLng(geocoder, position.coords.latitude, position.coords.longitude, input);
                
              }, function() {
                //console.log('not found');
              });
            }

            pacSelectFirst(input);
    
            var autocomplete = new google.maps.places.Autocomplete(input, { types: ['geocode'] });
            autocomplete.bindTo('bounds', map);
            autocomplete.addListener('place_changed', function() {
              var place = autocomplete.getPlace();
              //console.log('changed auto');
              if (!place.geometry) {
                return;
              }
    
              // If the place has a geometry, then present it on a map.
              var set_search = false;
              if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
                //console.log(place.geometry.viewport);
                if ((place.geometry.viewport.b.f - place.geometry.viewport.b.b < 0.5) && (place.geometry.viewport.f.b - place.geometry.viewport.f.f < 0.5)){
                    set_search = true;
                }
              } else {
                map.setCenter(place.geometry.location);
                map.setZoom(12);
                set_search = true;
              }
              if (set_search)
              {
                  searched_pos = { lat: place.geometry.location.lat(), lng: place.geometry.location.lng() };
                  searched_name = place.formatted_address;
                  use_default = false;
              } else {
                  searched_pos = null;
                  searched_name = null;
              }
              cmp = place.address_components;            
              for (i = 0; i < cmp.length; i ++)
              {
                    if ((cmp[i].types[0] && cmp[i].types[0] == 'country') ||  (cmp[i].types[1] && cmp[i].types[1] == 'country'))
                    {
                        searched_cc = cmp[i].short_name;
                    }
              }
              
              last_map_query = { w: 0, e: 0, s: 0, n: 0 };
    
              var address = '';
              if (place.address_components) {
                address = [
                  (place.address_components[0] && place.address_components[0].short_name || ''),
                  (place.address_components[1] && place.address_components[1].short_name || ''),
                  (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
              }
            });

            infowindow = new google.maps.InfoWindow({
               content: ""
            });            
            
            google.maps.event.addListener(map, 'idle', drawMap);


            $('#dealers-content ul.subcategories a').click(function(){
                if (!$(this).hasClass('selected')) {
                    $(this).parents('ul').find('a').removeClass('selected');
                    $(this).addClass('selected');
                    dealerCatId = $(this).data('catid');
                    
                    last_map_query = { w: 0, e: 0, s: 0, n: 0 };
                    
                    drawMap();
                }
            });

        }
        
        $('.dealersearch').each(function(){
            var field = $(this);
            pacSelectFirst(this);
            var autocomplete_s = new google.maps.places.Autocomplete(this, { types: ['geocode'] });
            autocomplete_s.addListener('place_changed', function() {  
                var place = autocomplete_s.getPlace();
                if (place.geometry){
                    window.location = field.data('prefix')+'/dealers' + '?lat=' + place.geometry.location.lat() + '&lng=' + place.geometry.location.lng() + '&nm=' + place.formatted_address;
                }
            });
         });
    }
    
    function resetShiftcontainer() {
        $('.shiftcontainer').css('overflow-y','visible').css('height','auto').velocity(
              { left: 0, right: 0}, 
              { duration: 150, easing: 'easeOutCubic'}
        );
    }
    
    function openHamburger() {
        $('.hamburger a').addClass('active');
        var submenu = $('#mobile-menu');
        submenu.addClass('js-active').show()
        $('.shiftcontainer').css({height: '90vh', 'overflow-y' : 'hidden'}).velocity({left: '88%'}, { duration: 300, easing: 'easeInOutCubic'});
    }
    
    function closeHamburger() {
        var menu = $('#mobile-menu');
        if (menu.hasClass('js-active')) {
            menu.removeClass('js-active').hide();
            $('.hamburger a').removeClass('active');
            resetShiftcontainer();
        }
    }
    
    function openSubmenu() {
        $('#shopmenulink').addClass('active');
        var submenu = $('#shopmenu-content');
        submenu.addClass('js-active').css('height','auto');
        var orig_outheight = submenu.outerHeight();
        var orig_inheight = submenu.height();
        submenu.height(0).show().velocity({height: orig_outheight, translateZ: 0}, { duration: 300, easing: 'easeOutCubic'});
    }
    
    function closeSubmenu() {
        var menu = $('#shopmenu-content');
        if (menu.hasClass('js-active')){
            menu.velocity({height:0},{ duration: 100, complete: function(){ $(this).hide().height('auto').removeClass('js-active'); }});
            $('#shopmenulink').removeClass('active');
        }
    }
    
    $(document).on('click', function(evt) {
        if((!$(evt.target).closest('#shopmenu-content').length) && $('#shopmenu-content:visible').length && (!$(evt.target).closest('.js-prevent-listener').length) ){
            closeSubmenu();
            return false;
        } else if (!$(evt.target).closest('#shopmenu-content li, #shopmenu-content h3').length && $('#shopmenu-content:visible').length){
            closeSubmenu();
            return false;
        }
        if((!$(evt.target).closest('#mobile-menu').length) && $('#mobile-menu:visible').length && (!$(evt.target).closest('.js-prevent-listener').length) ){
            closeHamburger();
            return false;
      }
    });
        
    $('#shopmenulink').click(function(el){
        if ($(this).hasClass('active') && $('#shopmenu-content').hasClass('js-active')){
            closeSubmenu();
        } else {
            openSubmenu();
        }
        return false;
    });
    
    $('.hamburger a').click(function(el){
        if ($(this).hasClass('active')){
            closeHamburger();
        } else {
            openHamburger();
        }
    });

    $('.row.caption-left .imgwrap').smoove({ offset: '10%', moveX: '20%', rotateY: '-30deg', duration: 400});
    $('.row.caption-right .imgwrap').smoove({ offset: '10%', moveX: '-20%', rotateY: '30deg', duration: 400});
    $('.row.nocaption img').smoove({ offset: '10%', rotateX: '90deg', duration: 400});
    $('.row.caption-left .caption').smoove({ offset: '10%', moveZ: 0, moveX: '-60%', moveY: '-15%', duration: 400});
    $('.row.caption-right .caption').smoove({ offset: '10%', moveZ: 0, moveX: '60%', moveY: '-15%', duration: 400});
        
    $('.row.blogpost a.showblogcontent').click( function(){
        var row = $(this).parents('.row');
        if ($(this).hasClass('opened')){
            if (!$(this).hasClass('closing')){
                row = row.first();
                $(this).addClass('closing').removeClass('opened');
                row.find('.blogcontent').velocity({'margin-top': 0, height: 0, 'padding-top': 0, 'padding-bottom': 0},{duration: 500, easing: 'easeOutCubic', complete: function(){
                        row.find('.slide-inner-wrap').css('height','');
                        $(this).height('auto').css('padding','');
                        row.removeClass('blogpost-opened');
                        row.parents('.row.split').removeClass('blogpost-opened').find('.row').css('padding-bottom','');
                        row.find('a.showblogcontent').removeClass('closing');
                    }});
                $(this).text('Read Story...');
            }
        } else {
            if (!$(this).hasClass('opening'))
            {
                $(this).addClass('opened').addClass('opening');
                $(this).text('Hide Story...');
                var splitrow = false;
                if (row.hasClass('split')){
                    var splitrow = row.last();
                    row = row.first();
                    var rowpad = splitrow.css('padding-bottom');
                    splitrow.find('.row').css('padding-bottom',rowpad);
                }
                row.find('.blogcontent').height('auto');
                var origheight = row.find('.blogcontent').outerHeight();
                var imgheight = row.find('.slide-inner-wrap').height();
                row.find('.slide-inner-wrap').css('height', imgheight).css('perspective','');
                var diff = imgheight - $(this).parent().position().top - $(this).parent().outerHeight() + $(this).outerHeight()/2;
                row.find('.blogcontent').outerHeight(diff);
                row.find('.blogcontent').velocity({'margin-top': -diff, height: origheight},{duration: 500, easing: 'easeOutCubic', complete: function(){
                        row.find('a.showblogcontent').removeClass('opening');
                    }});
                if (splitrow) {
                    splitrow.addClass('blogpost-opened');
                }
                row.addClass('blogpost-opened');
            }
        }
        return false;
    });
    
    $('.product a.fulllink').hover(
        function () {
            $(this).next().addClass("hover")
        },
        function () {
            $(this).next().removeClass("hover")
        }
     );
     
     $('.videoslide .imgwrap .playoverlay, .videoslide a.button').click( function(){
        var delay = 0;
        row = $(this).parents('.row');
        row.find('.playoverlay').hide();
        $.Velocity.hook(row.find('.caption'), 'translateY','-50%');
        row.find('.caption').velocity({opacity: 0}, 250);
        delay += 50;
        if (row.find('.videoslide.videoborder'))
        {
            row.find('.imgwrap').velocity({top:0,bottom:0,left:0,right:0,width: '100%'}, {delay: delay, duration: 250});
            //row.find('.imgwrap img').width(row.find('.imgwrap img').width()).height('auto');//.velocity({ width: '100%' }, { delay: delay, duration: 700, easing: 'easeInOutSine'});
            delay += 100;
        }
        //expand slide to be 16:9
        if ($(this).parents('.row.split').length === 0){
            row.velocity({'padding-bottom':'56%', 'translateZ': 0}, {delay: delay, duration: 150, easing: 'easeOutSine'});
            delay += 150;
        }
        
        row.find('.videoplayer').show().velocity({opacity:1}, { delay: delay, duration: 500, complete: function(){
            $(this).show().html($(this).attr('data-iframe-html'));
        }});
     });


    var colorboxer = function(el)
    {
        var ret = el.text;
        if (el.element && $(el.element).data('hexcode')){
            ret = $('<div />').html('<div style="display: inline-block; width: 1em; height: 1em; border: 1px solid #000; margin-right: 1em; background-color: #' + $(el.element).data('hexcode') + ';"></div>');
            ret.append('<span>' + el.text + '</span>');
        } else if (el.element && $(el.element).data('thumb-small')){
            ret = $('<div />').html('<div style="display: inline-block; width: 1em; height: 1em; border: 1px solid #000; margin-right: 1em; background: url(' + $(el.element).data('thumb-small') + ') no-repeat; background-size: cover;"></div>');
            ret.append('<span>' + el.text + '</span>');
        }
        return ret;
    }
    
    var selects = $(".categoryfilter select.color");
    if (selects.length > 0){
        selects.select2({ 
            minimumResultsForSearch: 20,
            templateResult: colorboxer,
            width: '175px'
        });
    }
    selects = $(".categoryfilter select.subcategory");
    if (selects.length > 0){
        selects.select2({ 
            minimumResultsForSearch: 20,
            width: '200px'
        });
    }
    $('.categoryfilter select').change(function(el){
        var sels = $(this).parent().find('select');
        var baseurl = window.location.pathname;
        var changed = false;
        var hasparams = false;
        var vals = {};
        
        for (var i = 0; i < sels.length; i++){
            var item = $(sels[i]);
            if ((item.val() != item.data('origvalue')) || (item.val() == 'all' && item.data('origvalue') !== ''))
            {
                changed = true;
            }
            if (item.val() !== '' && item.val() != 'all')
            {
                if (item.hasClass('paramopt')) {
                    hasparams = true;
                    vals[sels[i].name] = item.val();
                } else if (item.attr('name') == 'ft') {
                    baseurl = item.val();
                }
            }
        }
        
        //check for changed subcat
        if (changed) {
            window.location.href = baseurl + ( hasparams ? "?" + $.param(vals) : '');
        }
    });
   
    //set up scroller button
    $('.products .productscroller').each(function(){
        var me = $(this);
        if (me.width() < me.find('.productscroller-inner .product:last').position().left)
         {
             me.parent().find('.relatedscroll.next').removeClass('disabled');
             me.find('.productscroller-inner').data({'pagenum': 0, 'num_items': me.find('.productscroller-inner .product').length});
         }
    }); 
    $('.products .relatedscroll').click(function(){
        if (!$(this).hasClass('disabled'))
        {
            inner = $(this).parents('.products').find('.productscroller-inner');
            control = $(this).parent();
            pagewidth = inner.width();
            current_page = inner.data('pagenum');
            numpages = Math.ceil(inner.data('num_items') * inner.find('.product:first').width() / pagewidth);
            
            if ($(this).hasClass('previous')){
                current_page = Math.max(0, current_page - 1);
                if (current_page == 0) $(this).addClass('disabled');
                control.find('.relatedscroll.next').removeClass('disabled');
            } else {
                current_page = Math.min(current_page + 1, numpages);
                if (current_page +1 == numpages) $(this).addClass('disabled');
                control.find('.relatedscroll.previous').removeClass('disabled');
            }
            
            //update position
            inner.data("pagenum", current_page);
            inner.velocity({ left: current_page * -100 + '%'}, { duration: 500, easing: 'easeInOutCubic' });
        }
     });

    var FlashMessage = function (options, el) {
        var
            options = $.extend({}, FlashMessage.DEFAULTS, options),
            $element = $(el)
    
        $('body > div.flash-message').remove()
    
        if ($element.length == 0)
            $element = $('<div/>').addClass(options.class).html(options.text)
    
        $element.addClass('flash-message fade')
        $element.attr('data-control', null)
        $element.append('<button type="button" class="close" aria-hidden="true">×</button>')
        $element.on('click', 'button', remove)
        $element.on('click', remove)
    
        $(document.body).append($element)
    
        setTimeout(function(){
            $element.addClass('in')
        }, 1)
    
        var timer = window.setTimeout(remove, options.interval*1000)
    
        function removeElement() {
            $element.remove()
        }
    
        function remove() {
            window.clearInterval(timer)
    
            $element.removeClass('in')
            $.support.transition && $element.hasClass('fade') ?
                $element
                    .one($.support.transition.end, removeElement)
                    .emulateTransitionEnd(500) :
                removeElement()
        }
    }
    
    FlashMessage.DEFAULTS = {
        class: 'success',
        text: 'Default text',
        interval: 2
    }
    
    // FLASH MESSAGE PLUGIN DEFINITION
    // ============================
    
    if ($.oc === undefined)
        $.oc = {}
    
    $.oc.flashMsg = FlashMessage
});
