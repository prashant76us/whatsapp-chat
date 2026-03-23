jQuery(document).ready(function($) {
    // Toggle popup on button click
    $('#wcp-main-button').on('click', function(e) {
        e.stopPropagation();
        $('#wcp-popup').toggleClass('active');
    });
    
    // Close popup when close button is clicked
    $('.wcp-close').on('click', function() {
        $('#wcp-popup').removeClass('active');
    });
    
    // Close popup when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.wcp-floating-button').length) {
            $('#wcp-popup').removeClass('active');
        }
    });
    
    // Prevent closing when clicking inside popup
    $('#wcp-popup').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Track clicks on chat links (optional analytics)
    $('.wcp-chat-link, .wcp-group-link').on('click', function() {
        var linkType = $(this).hasClass('wcp-chat-link') ? 'chat' : 'group';
        console.log('WhatsApp ' + linkType + ' link clicked');
        // You can add analytics tracking here
    });
});