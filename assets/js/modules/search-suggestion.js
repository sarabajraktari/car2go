jQuery(document).ready(function($) {
    let currentFocus = -1; 

    $('#search-input').on('keydown', function(e) {
        let suggestions = $('#suggestions li.suggestion-item');
        if (suggestions.length === 0) return; 

        if (e.keyCode == 40) { 
            e.preventDefault();
            currentFocus++;
            if (currentFocus >= suggestions.length) {
                currentFocus = suggestions.length - 1; 
            }
            addActive(suggestions);
        } else if (e.keyCode == 38) { 
            e.preventDefault();
            currentFocus--;
            if (currentFocus < 0) {
                currentFocus = 0; 
            }
            addActive(suggestions);
        } else if (e.keyCode == 13) { 
            e.preventDefault();
            if (currentFocus > -1) {
                suggestions.eq(currentFocus).click();
            } else if ($('#search-input').val().trim() !== '') { 
                $('#search-form').submit();
            }
        }
    });

    $(document).on('mouseenter', '#suggestions li.suggestion-item', function() {
        let suggestions = $('#suggestions li.suggestion-item');
        removeActive(suggestions); 
        $(this).addClass('highlighted'); 
        currentFocus = suggestions.index(this); 
    });

    $(document).on('mouseleave', '#suggestions li.suggestion-item', function() {
        $(this).removeClass('highlighted'); 
    });

    function addActive(suggestions) {
        if (!suggestions) return false;
        removeActive(suggestions);
        if (currentFocus >= 0 && currentFocus < suggestions.length) {
            suggestions.eq(currentFocus).addClass('highlighted'); 
            $('#search-input').attr('placeholder', suggestions.eq(currentFocus).text()); 
        }
    }

    function removeActive(suggestions) {
        suggestions.removeClass('highlighted'); 
    }

    $('#search-input').on('input', function() {
        let query = $(this).val();
        if (query.length > 2) {
            $.ajax({
                url: '../wp-admin/admin-ajax.php', 
                method: 'GET',
                data: {
                    action: 'get_car_suggestions',
                    query: query
                },
                success: function(response) {
                    let suggestions = JSON.parse(response);
                    let suggestionsList = $('#suggestions');
                    suggestionsList.empty().show();
                    currentFocus = -1; 

                    let currentUrl = window.location.pathname;

                    suggestions.forEach(function(suggestion) {
                        if (suggestion.title) {
                            let searchUrl = '/cars/?search=' + encodeURIComponent(suggestion.title) + '&brand=&city=';
                            if (currentUrl.includes('/cars/')) {
                                searchUrl = '/?search=' + encodeURIComponent(suggestion.title) + '&brand=&city=';
                            }

                            suggestionsList.append(
                                '<li class="p-2 suggestion-item cursor-pointer"><a href="'+ searchUrl +'">' + suggestion.title + '</a></li>'
                            );
                        }
                    });
                }
            });
        } else {
            $('#suggestions').hide();
        }
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#search-input, #suggestions').length) {
            $('#suggestions').hide();
        }
    });

    $(document).on('click', '#suggestions li', function(event) {
        event.preventDefault();  // Prevent default action if no valid link
        let text = $(this).text();
        if (text) {
            $('#search-input').val(text);
            $('#suggestions').hide();
            $('#search-form').submit(); 
        }
    });

    $('#search-input').on('blur', function() {
        setTimeout(function() {
            $('#search-input').attr('placeholder', 'Search a car...'); 
            $('#suggestions').hide();
        }, 200); 
    });

    $('#search-input').on('keydown', function(event) {
        if (event.key === 'Enter') {
            if (currentFocus === -1) { 
                event.preventDefault(); 
                if ($('#search-input').val().trim() !== '') { 
                    $('#search-form').submit();
                }
            }
        }
    });
});
