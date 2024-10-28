<x-app-layout>

    <div class="mt-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 dark:text-gray-100 text-lg">
                    {{ __('Add a New Bookmark') }}
                </div>
                <div class="mt-4 outline outline-gray-700 outline-1 outline-offset-8 rounded-md">
                    <form action="{{ route('bookmark.store') }}" method="POST" class="flex space-x-4 p-2 items-end">
                        @csrf
                        <div class="flex flex-col w-1/3">
                            <label for="bname" class="text-gray-200 text-sm">Name <span
                                    class="text-red-400">*</span></label>
                            <input type="text" id= "bname" name="bname" class="rounded-md bg-gray-700 h-8 mt-1 text-white"
                                required>
                        </div>
                        <div class="flex flex-col w-1/3">
                            <label for="burl" class="text-gray-200 text-sm">Url <span
                                    class="text-red-400">*</span></label>
                            <input type="text" id= "burl" name="burl" class="rounded-md bg-gray-700 h-8 mt-1 text-white"
                                required>
                        </div>
                        <div class="flex flex-col w-1/3">
                            <label for="bcategory" class="text-gray-200 text-sm">Category</label>
                            <input type="text" id= "bcategory" name="bcategory"
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 dark:text-gray-100 text-lg">
                    {{ __('Your Bookmarks') }}
                </div>
                <div>

                    <div class="overflow-x-auto rounded-lg shadow mt-4">
                        <table class="min-w-full divide-y divide-gray-800">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Url</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-800">
                                @foreach ($bookmarks as $b)
                                    <tr class="hover:bg-gray-400 transition-colors bg-gray-600">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200 hover:text-gray-900">{{ $b->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-200 hover:text-gray-900">
                                            <a href="{{ $b->url }}" target="_blank" class="hover:underline">{{ $b->url }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">{{ $b->category }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
