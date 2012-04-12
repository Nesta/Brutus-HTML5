/**
 * brutus javascript core
 *
 * - Provides frequently used extensions to base javascript objects
 * - jQuery browser detection tweak
 * - Define functions used in events
 */
 

// Brutus Theme behavior
(function($) {
  Drupal.behaviors.brutus = {
    attach: function(context) {

      $('html').removeClass('no-js');
      $('.sol').click(function() {
        $('.luna').removeClass('active');
        $('.sol').addClass('active');
        $('body').removeClass('black');
        $('header#header').removeClass('black')
      });  
      $('.luna').click(function() {
        $('.sol').removeClass('active');
        $('.luna').addClass('active');
        $('body').addClass('black');
        $('header#header').addClass('black')
      });  
      $('.menu-om li').bind('click', function(event) {
        var $anchor = $(this);
        if ($anchor.attr('id') == 'ellos') {
          $('html, body').stop().animate({
              scrollTop: $anchor.offset().top + 640
          }, 3363,'easeInOutExpo');
        }
        if ($anchor.attr('id') == 'saben') {
          $('html, body').stop().animate({
              scrollTop: $anchor.offset().top + 500
          }, 3363,'easeInOutExpo');
        }
        if ($anchor.attr('id') == 'como') {
          $('html, body').stop().animate({
              scrollTop: $anchor.offset().top + 1320
          }, 3363,'easeInOutExpo');
        }
        event.preventDefault();
      });
      // Add Target _blank metoth
      jQuery("a[href^=http://]").attr("target","_blank");    
    }
  };
})(jQuery);



// Add String.trim() method
String.prototype.trim = function(){
  return this.replace(/\s+$/, '').replace(/^\s+/, '');
}

// Add Array.indexOf() method
if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function (obj, fromIndex) {
    if (fromIndex == null) {
      fromIndex = 0;
    } else if (fromIndex < 0) {
      fromIndex = Math.max(0, this.length + fromIndex);
    }
    for (var i = fromIndex, j = this.length; i < j; i++) {
      if (this[i] === obj){
        return i;
      }
    }
    return -1;
  };
}

// jQuery Browser Detect Tweak For IE7
jQuery.browser.version = jQuery.browser.msie && parseInt(jQuery.browser.version) == 6 && window["XMLHttpRequest"] ? "7.0" : jQuery.browser.version;

// Console.log wrapper to avoid errors when firebug is not present
// usage: log('inside coolFunc',this,arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function() {
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if (this.console) {
    console.log(Array.prototype.slice.call(arguments));
  }
};

// init object
var brutus = brutus || {};

/**
 * Image handling functions
 */
brutus.image = { _cache : [] };

// preload images
brutus.image.preload = function() {
  for (var i = arguments.length; i--;) {
    var cacheImage = document.createElement('img');
    cacheImage.src = arguments[i];
    brutus.image._cache.push(cacheImage);
  }  
}
