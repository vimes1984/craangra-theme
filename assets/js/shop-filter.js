jQuery(document).ready(function($) {
    // Check if we are on the WooCommerce shop page/archive page containing the product collection block
    const $collection = $('.wp-block-woocommerce-product-collection');
    if ($collection.length === 0) {
        return; // Not on the shop page.
    }

    const $grid = $('.wc-block-product-template');
    if ($grid.length === 0) {
        return; // Grid not found.
    }

    // 1. Initialize Isotope on the product grid
    $grid.isotope({
        itemSelector: '.wc-block-product',
        layoutMode: 'fitRows',
        transitionDuration: '0.4s',
        percentPosition: true
    });

    // Run layout again once images are fully loaded to prevent overlapping layout bugs
    if (typeof $.fn.imagesLoaded !== 'undefined') {
        $grid.imagesLoaded().progress(function() {
            $grid.isotope('layout');
        });
    }

    // 2. Generate and prepend the Isotope Category Filter Pill Buttons
    if (typeof shop_filter_params !== 'undefined' && shop_filter_params.categories) {
        const $filterBar = $('<div class="shop-filter-bar"></div>');
        $filterBar.append('<button class="filter-btn active" data-filter="*">All Plants</button>');

        shop_filter_params.categories.forEach(function(cat) {
            $filterBar.append('<button class="filter-btn" data-filter=".product_cat-' + cat.slug + '">' + cat.name + '</button>');
        });

        // Insert filter bar above the main product collection block
        $collection.first().before($filterBar);

        // Click handler for category filters
        $filterBar.on('click', '.filter-btn', function() {
            const filterValue = $(this).attr('data-filter');
            $grid.isotope({ filter: filterValue });
            $(this).addClass('active').siblings().removeClass('active');
        });
    }

    // 3. Infinite Scrolling Logic
    const $pagination = $('.wp-block-query-pagination');
    if ($pagination.length > 0) {
        // Hide standard query pagination links
        $pagination.hide();

        // Create and append the loading spinner below the product collection
        const $loader = $('<div class="infinite-scroll-loader"><span class="spinner"></span></div>');
        $collection.first().after($loader);

        let loading = false;
        let nextUrl = $('.wp-block-query-pagination-next').attr('href');

        if (nextUrl) {
            $(window).on('scroll', function() {
                if (loading || !nextUrl) return;

                // Trigger AJAX load when user scrolls to within 300px of page bottom
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 300) {
                    loadNextPage();
                }
            });
        }

        function loadNextPage() {
            loading = true;
            $loader.fadeIn(200);

            $.ajax({
                url: nextUrl,
                method: 'GET',
                dataType: 'html',
                success: function(data) {
                    const $html = $(data);
                    
                    // Retrieve new product items from the response
                    const $newProducts = $html.find('.wc-block-product-template .wc-block-product');
                    
                    if ($newProducts.length > 0) {
                        // Append items to grid and run Isotope
                        $grid.append($newProducts).isotope('appended', $newProducts);

                        // Recalculate layout as images load
                        if (typeof $.fn.imagesLoaded !== 'undefined') {
                            $grid.imagesLoaded().progress(function() {
                                $grid.isotope('layout');
                            });
                        } else {
                            $grid.isotope('layout');
                        }

                        // Retrieve the updated "Next Page" link from the AJAX response
                        nextUrl = $html.find('.wp-block-query-pagination-next').attr('href');
                        
                        $loader.fadeOut(200);
                        loading = false;

                        // Remove the loader if there are no more pages to load
                        if (!nextUrl) {
                            $loader.remove();
                        }
                    } else {
                        $loader.fadeOut(200);
                        loading = false;
                    }
                },
                error: function() {
                    $loader.fadeOut(200);
                    loading = false;
                }
            });
        }
    }
});
