<div>
    <div class="flex justify-end justify-self-end pr-2 pb-2" x-data="{ open: false }">
        <!-- Three dots button - hidden when menu is open -->
        <button @click.stop="open = true" x-show="!open" class="p-1 hover:bg-gray-100 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                fill="currentColor">
                <path
                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
            </svg>
        </button>

        <!-- Popup menu -->
        <div x-show="open" @click.away="open = false"
            class="relative bottom-2 right-2 w-48 py-2 bg-gray-200 rounded-lg shadow-xl border" style="display: none;">
            <a href="" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                Edit
            </a>
            <form action="{{ route('bookmark.delete', $bookmark) }}" method="POST" class="block">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100"
                    onclick="return confirm('Are you sure you want to delete this bookmark?')">
                    Delete
                </button>
            </form>
        </div>
</div>