/*
 * mon-projet
 * https://github.com/guitoud/grunt
 *
 * Copyright (c) 2015 guitoud
 * Licensed under the MIT license.
 */

(function($) {

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

}(jQuery));
