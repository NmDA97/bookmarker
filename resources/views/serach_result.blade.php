<!-- resources/views/search.blade.php -->
<input type="text" id="search" placeholder="Search...">
<div id="results"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('keyup', function() {
            let query = $(this).val();

            if (query.length > 0) {
                $.ajax({
                    url: "{{ route('search') }}",
                    type: "GET",
                    data: { query: query },
                    success: function(data) {
                        $('#results').empty(); // Clear previous results

                        if (data.length === 0) {
                            $('#results').append('<p>No results found</p>');
                        } else {
                            data.forEach(function(item) {
                                $('#results').append('<div>' + item.title + '</div>'); // Customize this line to display your data
                            });
                        }
                    },
                    error: function() {
                        $('#results').empty();
                        $('#results').append('<p>An error occurred</p>');
                    }
                });
            } else {
                $('#results').empty(); // Clear results if the query is empty
            }
        });
    });
</script>
