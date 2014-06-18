var ppa = (function($, w, undefined) {

    var page = 1; //don't redefine this variable each time you click.

    function scrollToTop() {
        $('.nav-links').append('<div class="ppa-nav-top"><a class="ppa-scroll-to-top" href="#">Scroll to top <span class="meta-nav">&uarr;</span></a></div>');
        $(document).on('click', '.ppa-scroll-to-top', function(e) {
            e.preventDefault();
            $('html,body').animate({ scrollTop:0 }, 250);
        })
    }

    function loadPosts() {

        $(document).on('click', '.nav-previous a', function(e) {
            e.preventDefault();
            // if ($('body').hasClass('search-results') && page === 1) {
            //     scrollToTop();
            //     page = 2;
            //     return;
            // }
            var $this = $(this),
                saved_html = $(this).html();
            $this.text('Loading ...');
            var catID = $this.data('catid'),
                year = $this.data('year'),
                month = $this.data('month'),
                search = $this.data('search'),
                no_more_story_text;
            if (page === 1) {
                scrollToTop();
            }

            // increment the page number
            page = page + 1;

            $.post( ppa_ajax.ajaxurl, { action: 'load_more_posts', offset : ppa_ajax.offset, page : page, cat : catID, year: year, month: month, search: search }, function(data) {

                var $data = $(data).filter('article');

                if ($data.length === 0) {
                    noMoreStories();
                }

                else {
                    $('article:last').after( $data );
                    
                    $this.html(saved_html);
                    
                    if ($data.length < ppa_ajax.offset) {
                        noMoreStories();
                    }

                }
            });
        });

    }

    function noMoreStories() {
        $('.nav-previous').html('<span>' + ppa_ajax.no_more_posts_text + '</span>').addClass('ppa-disabled');
    }

    return {
        init: function() {
            loadPosts();
        }
    }

} ( jQuery, window ) );

ppa.init();