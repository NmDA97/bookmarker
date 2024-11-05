<x-app-layout>

    <!-- resources/views/search.blade.php -->
    <div class="flex justify-end p-4">
        <input class="rounded-md" type="text" id="search" placeholder="Search...">
    </div>

    <div class="mt-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 dark:text-gray-100 text-lg">
                    {{ __('Add a New Bookmark') }}
                </div>
                <div class="mt-4 outline outline-gray-700 outline-1 outline-offset-8 rounded-md">
                    <form action="{{ route('bookmark.store') }}" method="POST" class="flex space-x-4 p-2 items-end">
                        @csrf
                        <div class="flex flex-col w-1/3">
                            <label for="name" class="text-gray-200 text-sm">Name <span
                                    class="text-red-400">*</span></label>
                            <input type="text" id= "name" name="name"
                                class="rounded-md bg-gray-700 h-8 mt-1 text-white" required>
                        </div>
                        <div class="flex flex-col w-1/3">
                            <label for="url" class="text-gray-200 text-sm">Url <span
                                    class="text-red-400">*</span></label>
                            <input type="text" id= "url" name="url"
                                class="rounded-md bg-gray-700 h-8 mt-1 text-white" required>
                        </div>
                        <div class="flex flex-col w-1/3">
                            <label for="category" class="text-gray-200 text-sm">Category</label>
                            <input type="text" id= "category" name="category"
                                class="rounded-md bg-gray-700 h-8 mt-1 text-white">
                        </div>
                        <div class="mt-4 flex justify-end px-2">
                            <button type="submit" class="bg-blue-500 px-6 py-1 rounded-md text-gray-100">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 px-8 py-4">
            @foreach ($bookmarks as $bookmark)
                <x-bookmark-preview :bookmark="$bookmark" />
            @endforeach
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                let query = $(this).val().trim(); // Trim whitespace

                // Only make the AJAX call if there is input
                if (query.length > 0) {
                    $.ajax({
                        url: "{{ route('bookmark.search') }}",
                        type: "GET",
                        data: {
                            query: query
                        },
                        success: function(response) {
                            // Clear previous results
                            $('.grid').empty();

                            // Check if the response contains data
                            if (response.length > 0) {
                                // Loop through the data and append the Blade component
                                response.forEach(function(bookmark) {
                                    let bookmarkHtml = `
                                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">${bookmark.name}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">${bookmark.category || 'No category'}</p>
                                        <a href="${bookmark.url}" class="text-blue-500" target="_blank">Visit</a>
                                    </div>

                            `;
                                    $('.grid').append(bookmarkHtml);
                                });
                            } else {
                                // Display a message if no results are found
                                $('.grid').append(
                                    '<p class="text-gray-400">No results found</p>');
                            }
                        },
                        error: function() {
                            console.error("An error occurred while fetching search results.");
                        }
                    });
                } else {
                    // Clear results if the query is empty
                    $('.grid').empty();
                }
            });
        });
    </script>



</x-app-layout>
