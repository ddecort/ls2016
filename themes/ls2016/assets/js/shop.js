$(function() {     

    function getUrlVars()
    {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }
    

  var cart;
  var cartLineItemCount;
  if (client){
      if(localStorage.getItem(cartidx)) {
        client.fetchCart(localStorage.getItem(cartidx)).then(function(remoteCart) {
          cart = remoteCart;
          
          var q = getUrlVars();
          if (q.cc && q.cc == 1){
            cart.clearLineItems();
          }
          
          cartLineItemCount = cart.lineItems.length;
          renderCartItems();
          updateCartTabButton();
        });
      } else {
        client.createCart().then(function (newCart) {
          cart = newCart;
          localStorage.setItem(cartidx, cart.id);
          cartLineItemCount = 0;
          updateCartTabButton();
        });
      }
  }
  
  bindEventListeners();



  function objectsEqual(x, y) {
      if (x === y) {
        return true;
      }

      return (x && y && typeof x === 'object' && typeof y === 'object') ?
      (Object.keys(x).length === Object.keys(y).length) &&
        Object.keys(x).every(function(key) {
                return objectsEqual(x[key], y[key]);
        }, true) : (x === y);
  }
  
  function propStrip(obj){
      return JSON.stringify(obj).replace(/"/g,'~');
  }
  
  function propDecode(str){
      return JSON.parse(str.replace(/~/g,'"'));
  }

  function propDescribe(obj){
    var out = '';
    for (var key in obj){
        out = out + (out.length > 0 ? '<br />' : '') + key + ': ' + obj[key];
    }

    return out;
  }

 /* Bind Event Listeners
  ============================================================ */
  function bindEventListeners() {
    /* cart close button listener */
    $('#cart .btn--close').on('click', closeCart);
    
    $('.bigcart a').on('click', function(){
        setPreviousFocusItem(this);
        if ($('#cart').hasClass('js-active')) closeCart();
        else openCart();
    });

    /* click away listener to close cart */
    $(document).on('click', function(evt) {
      if((!$(evt.target).closest('#cart').length) && (!$(evt.target).closest('.js-prevent-cart-listener').length)) {
        if ($('#cart').hasClass('js-active')) closeCart();
      }
    });

    /* escape key handler */
    var ESCAPE_KEYCODE = 27;
    $(document).on('keydown', function (evt) {
      if (evt.which === ESCAPE_KEYCODE) {
        if (previousFocusItem) {
          $(previousFocusItem).focus();
          previousFocusItem = ''
        }
        if ($('#cart').hasClass('js-active')) closeCart();
      }
    });        

    /* checkout button click listener */
    $('.btn--cart-checkout').on('click', function () {
      var config = cart.config;
      var baseUrl = 'https://' + config.domain + '/cart';

      var variantPath = [];
      var hasProps = false;
      if (cart.lineItems && cart.lineItems.length > 0){
          for (var key in cart.lineItems){
              var item = cart.lineItems[key].attrs;
              variantPath.push([item.variant_id, item.quantity, item.properties]);
              if (!$.isEmptyObject(item.properties)){
                  hasProps = true;
              }
          }
          
          if (hasProps){
             var variantOutput = encodeURIComponent(JSON.stringify(variantPath));
             window.open(baseUrl + '?p=' + variantOutput, '_self');
          } else {
              var variantOutput = variantPath.map(function(item){
                  return item[0] + ':' + item[1];
              });
              
              var query = 'api_key=' + config.apiKey;
        
              /* globals ga:true */
              if (typeof ga === 'function') {
                var linkerParam = void 0;
        
                window.ga(function (tracker) {
                  linkerParam = tracker.get('linkerParam');
                });
        
                if (linkerParam) {
                  query += '&' + linkerParam;
                }
              }
    
              window.open(baseUrl + '/' + variantOutput + '?' + query, '_self');
          }
      }
    });

    /* buy button click listener */
    //$('.buy-button').on('click', buyButtonClickHandler);

    /* increment quantity click listener */
    $('#cart').on('click', '.quantity-increment', function () {
      var variantId = $(this).data('variant-id');
      var props = $(this).attr('data-properties');
      incrementQuantity(variantId, props);
    });

    /* decrement quantity click listener */
    $('#cart').on('click', '.quantity-decrement', function() {
      var variantId = $(this).data('variant-id');
      var props = $(this).attr('data-properties');
      decrementQuantity(variantId, props);
    });

    /* update quantity field listener */
    $('#cart').on('keyup', '.cart-item__quantity', debounce(fieldQuantityHandler, 250));

    /* cart tab click listener */
    $('.btn--cart-tab').click(function() {
      setPreviousFocusItem(this);
      openCart();
    });
    
    
  }

  /* Update product variant quantity in cart
  ============================================================ */
  function updateQuantity(fn, variantId, props) {
    var quantity;
    var cartLineItem = findCartItemByVariantId(variantId, props);
    if (cartLineItem) {
      quantity = fn(cartLineItem.quantity);
      updateVariantInCart(cartLineItem, quantity);
    }
  }

  /* Decrease quantity amount by 1
  ============================================================ */
  function decrementQuantity(variantId, props) {
    updateQuantity(function(quantity) {
      return quantity - 1;
    }, variantId, props);
  }

  /* Increase quantity amount by 1
  ============================================================ */
  function incrementQuantity(variantId, props) {
    updateQuantity(function(quantity) {
      return quantity + 1;
    }, variantId, props);
  }

  /* Update producrt variant quantity in cart through input field
  ============================================================ */
  function fieldQuantityHandler(evt) {
    var variantId = parseInt($(this).closest('.cart-item').attr('data-variant-id'), 10);
    var props = $(this).closest('.cart-item').attr('data-properties');
    var cartLineItem = findCartItemByVariantId(variantId,props);
    var quantity = evt.target.value;
    if (cartLineItem) {
      updateVariantInCart(cartLineItem, quantity);
    }
  }

  /* Debounce taken from _.js
  ============================================================ */
  function debounce(func, wait, immediate) {
    var timeout;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    }
  }

  /* Open Cart
  ============================================================ */
  function openCart() {
    $.Velocity.hook($('#cart'), 'translateX','100%');
    $('#cart').css('display','block').addClass('js-active').velocity({ translateX: 0 }, {duration: 200, easing: 'ease-out'});
    if ($('header.desktop').css('display') == 'none'){
        $('.shiftcontainer').css({height: '90vh', 'overflow-y' : 'hidden'}).velocity({left: '-88%', right: '88%'}, { duration: 200, easing: 'ease-out'});
    }
  }

  /* Close Cart
  ============================================================ */
  function closeCart() {
    $('#cart').removeClass('js-active').velocity({ translateX: '100%' }, {duration: 150, easing: 'easeOutCubic'});
    $('.overlay').removeClass('js-active');
    $('.shiftcontainer').css('overflow-y','visible').css('height','auto').velocity({ left: 0, right: 0}, { duration: 150, easing: 'easeOutCubic'});
  }

  /* Find Cart Line Item By Variant Id
  ============================================================ */
  function findCartItemByVariantId(variantId,props) {
    return cart.lineItems.filter(function (item) {
      return (item.variant_id === variantId && objectsEqual(item.properties, propDecode(props)));
    })[0];
  }

  /* Determine action for variant adding/updating/removing
  ============================================================ */
  addOrUpdateVariant = function(variant, props, quantity, clicktarget) {
    if (clicktarget) {
        setPreviousFocusItem(clicktarget);
    }
    openCart();
    
    addVariantToCart(variant, props, quantity);
    updateCartTabButton();
  }

  /* Update details for item already in cart. Remove if necessary
  ============================================================ */
  function updateVariantInCart(cartLineItem, quantity) {
    var variantId = cartLineItem.variant_id;
    var $cartItemContainer = $('.cart-item-container');
    var cartLength = cart.lineItems.length;
    cart.updateLineItem(cartLineItem.id, quantity).then(function(updatedCart) {
      var $cartItem = $('#cart').find('.cart-item[data-variant-id="' + variantId + '"][data-properties="' + propStrip(cartLineItem.properties) + '"]');
      if (updatedCart.lineItems.length >= cartLength) {
        $cartItem.find('.cart-item__quantity').val(cartLineItem.quantity);
        $cartItem.find('.cart-item__price').text(formatAsMoney(cartLineItem.line_price));
      } else {
        $cartItem.addClass('js-hidden').bind('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function() {
           $cartItem.remove();
        });
      }

      updateCartTabButton();
      updateTotalCartPricing();
      if (updatedCart.lineItems.length < 1) {
        $cartItemContainer.find('.empty').removeClass('hidden');
        closeCart();
      }
    }).catch(function (errors) {
      console.log('Fail');
      console.error(errors);
    });
  }

  /* Add 'quantity' amount of product 'variant' to cart
  ============================================================ */
  function addVariantToCart(variant, props, quantity) {
    openCart();
    var $cartItem;
    var oldCartLength = cart.lineItems.length;
    cart.addVariants({ variant: variant, properties: props, quantity: quantity }).then(function() {
      var cartItem = cart.lineItems.filter(function (item) {
        return (item.variant_id === variant.id && objectsEqual(item.properties, props));
      })[0];        
      if (cart.lineItems.length > oldCartLength)
      {
          $cartItem = renderCartItem(cartItem);
          var $cartItemContainer = $('.cart-item-container');
          $cartItemContainer.append($cartItem);
          setTimeout(function () {
            $cartItemContainer.find('.js-hidden').removeClass('js-hidden');
            $cartItemContainer.find('.empty').addClass('hidden');
          }, 0);
      } else {
        $cartItem = $('#cart').find('.cart-item[data-variant-id="' + variant.id + '"][data-properties="' + propStrip(props) + '"]');
        $cartItem.find('.cart-item__quantity').val(cartItem.quantity);
        $cartItem.find('.cart-item__price').text(formatAsMoney(cartItem.line_price));
      }
         

    }).catch(function (errors) {
      console.log('Fail');
      console.error(errors);
    });

    updateTotalCartPricing();
    updateCartTabButton();
  }

  /* Return required markup for single item rendering
  ============================================================ */
  function renderCartItem(lineItem) {
    var lineItemEmptyTemplate = $('#CartItemTemplate').html();
    var $lineItemTemplate = $(lineItemEmptyTemplate);
    var itemImage = lineItem.image ? lineItem.image.src : null;
    $lineItemTemplate.attr('data-variant-id', lineItem.variant_id);
    $lineItemTemplate.attr('data-properties', propStrip(lineItem.properties));
    $lineItemTemplate.addClass('js-hidden');
    $lineItemTemplate.find('.cart-item__img').css('background-image', 'url(' + itemImage + ')');
    $lineItemTemplate.find('.cart-item__title').text(lineItem.title);
    $lineItemTemplate.find('.cart-item__variant-title').text(lineItem.variant_title);
    $lineItemTemplate.find('.cart-item__price').text(formatAsMoney(lineItem.line_price));
    $lineItemTemplate.find('.cart-item__props').html(propDescribe(lineItem.properties));
    $lineItemTemplate.find('.cart-item__quantity').attr('value', lineItem.quantity);
    $lineItemTemplate.find('.quantity-decrement').attr('data-variant-id', lineItem.variant_id).attr('data-properties', propStrip(lineItem.properties));
    $lineItemTemplate.find('.quantity-increment').attr('data-variant-id', lineItem.variant_id).attr('data-properties', propStrip(lineItem.properties));

    return $lineItemTemplate;
  }

  /* Render the line items currently in the cart
  ============================================================ */
  function renderCartItems() {
    var $cartItemContainer = $('.cart-item-container');
    var lineItemEmptyTemplate = $('#CartItemTemplate').html();
    var $cartLineItems = cart.lineItems.map(function (lineItem, index) {
      return renderCartItem(lineItem);
    });
    if (cart.lineItems && cart.lineItems.length > 0){
        $cartItemContainer.find('.empty').addClass('hidden');
        $cartItemContainer.append($cartLineItems);
    } else {
        $cartItemContainer.find('.empty').removeClass('hidden');
    }

    setTimeout(function () {
      $cartItemContainer.find('.js-hidden').removeClass('js-hidden');
    }, 0)
    updateTotalCartPricing();
  }

  /* Update Total Cart Pricing
  ============================================================ */
  function updateTotalCartPricing() {
    $('#cart .pricing').text(formatAsMoney(cart.subtotal));
  }

  /* Format amount as currency
  ============================================================ */
  function formatAsMoney(amount) {
    return '$' + parseFloat(amount, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
  }

  /* Update cart tab button
  ============================================================ */
  function updateCartTabButton() {
    $('.btn--cart-tab .btn__counter').html(cart.lineItemCount);
    if (cart.lineItems.length > 0) {
      $('.btn--cart-tab').addClass('js-active');
    } else {
      $('.btn--cart-tab').removeClass('js-active');
      $('#cart').removeClass('js-active');
    }
  }

  /* Set previously focused item for escape handler
  ============================================================ */
  function setPreviousFocusItem(item) {
    previousFocusItem = item;
  }

  
});
