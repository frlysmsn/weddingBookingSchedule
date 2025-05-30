$(document).ready(function() {
    // Desktop toggle
    $('#desktopToggle').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-collapsed');
        
        // Save state in cookie 
        document.cookie = `sidebarState=${$('body').hasClass('sidebar-collapsed') ? 'collapsed' : 'expanded'}; path=/; max-age=31536000`;
    });

    // Mobile toggle
    $('#mobileToggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('body').toggleClass('sidebar-active');
        
        if ($('body').hasClass('sidebar-active')) {
            $('.sidebar-overlay').fadeIn(300);
        } else {
            $('.sidebar-overlay').fadeOut(300);
        }
    });

    // Close sidebar on overlay click
    $('.sidebar-overlay').on('click', function() {
        $('body').removeClass('sidebar-active');
        $(this).fadeOut(300);
    });

    // Close sidebar when clicking outside
    $(document).on('click', function(e) {
        if ($(window).width() <= 768 && 
            !$(e.target).closest('.sidebar').length && 
            !$(e.target).closest('#mobileToggle').length) {
            $('body').removeClass('sidebar-active');
            $('.sidebar-overlay').fadeOut(300);
        }
    });

    // Handle window resize
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if ($(window).width() > 768) {
                $('body').removeClass('sidebar-active');
                $('.sidebar-overlay').fadeOut(300);
            }
        }, 250);
    });

    // Handle keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Close sidebar on ESC
        if (e.key === 'Escape' && $('body').hasClass('sidebar-active')) {
            $('body').removeClass('sidebar-active');
            $('.sidebar-overlay').fadeOut(300);
        }
        
        // Toggle sidebar on Ctrl + B
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            if ($(window).width() <= 768) {
                $('#mobileToggle').click();
            } else {
                $('#desktopToggle').click();
            }
        }
    });

    // Prevent sidebar content clicks from closing
    $('.sidebar').on('click', function(e) {
        e.stopPropagation();
    });
}); 
