/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */
// Copyright 2014-2015 Twitter, Inc.
// Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
// @see http://getbootstrap.com/getting-started/#support-ie10-width
if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
  var msViewportStyle = document.createElement('style')
  msViewportStyle.appendChild(
    document.createTextNode('@-ms-viewport{width:auto!important}')
  )
  document.querySelector('head').appendChild(msViewportStyle)
}

$(document).ready(function() {
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
});