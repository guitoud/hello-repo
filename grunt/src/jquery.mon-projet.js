/*
 * mon-projet
 * https://github.com/guitoud/grunt
 *
 * Copyright (c) 2026 guitoud
 * Licensed under the MIT license.
 */

(function(factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module.
    define(['jquery'], factory);
  } else if (typeof module === 'object' && module.exports) {
    // Node/CommonJS
    module.exports = function(root, jQuery) {
      if (jQuery === undefined) {
        // require('jQuery') returns a factory that requires window to
        // build a jQuery instance, we normalize how we use modules
        // that require this pattern but the window provided is a noop
        // if it's defined (how jquery works)
        if (typeof window !== 'undefined') {
          jQuery = require('jquery')(window);
        } else {
          jQuery = require('jquery');
        }
      }
      factory(jQuery);
      return jQuery;
    };
  } else {
    // Browser globals
    factory(jQuery);
  }
}(function($) {

  // Collection method.
  $.fn.mon_projet = function() {
    return this.each(function(i) {
      // Do something awesome to each selected element.
      $(this).html('awesome' + i);
    });
  };

  // Static method.
  $.mon_projet = function(options) {
    // Override default options with passed-in options.
    options = $.extend({}, $.mon_projet.options, options);
    // Return something awesome.
    return 'awesome' + options.punctuation;
  };

  // Static method default options.
  $.mon_projet.options = {
    punctuation: '.'
  };

  // Custom selector.
  $.expr[':'].mon_projet = function(elem) {
    // Is this element awesome?
    return $(elem).text().indexOf('awesome') !== -1;
  };

}));
