jQuery(document).ready(function($) {
    "use strict";

    // AV Logo - areavoices.com
    $('#wp-admin-bar-av-logo a').click(function() {
        ga('send', 'event', 'Global Nav', 'click', 'Logo - areavoices.com');
    });

    // Channels - areavoices.com
    $('#wp-admin-bar-areavoices a').click(function() {
        ga('send', 'event', 'Global Nav', 'click', 'Channels - areavoices.com');
    });

    // Channels - northlandoutdoors.com
    $('#wp-admin-bar-northlandoutdoors a').click(function() {
        ga('send', 'event', 'Global Nav', 'click', 'Channels - northlandoutdoors.com');
    });

    // Channels - sayanythingblog.com
    $('#wp-admin-bar-sayanything a').click(function() {
        ga('send', 'event', 'Global Nav', 'click', 'Channels - sayanythingblog.com');
    });

    // Channels - bisonmedia.areavoices.com
    $('#wp-admin-bar-bisonmedia a').click(function() {
        ga('send', 'event', 'Global Nav', 'click', 'Channels - bisonmedia.areavoices.com');
    });

});
