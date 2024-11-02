<div>
    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow shadow-lg shadow-cyan-500/50 ">
        <a href="{{ $bookmark->url }}" target="_blank" class="block">
            @if ($bookmark->preview['image'])
                <div class="h-36 bg-gray-100">
                    <img src="{{ $bookmark->preview['image'] }}" alt="{{ $bookmark->name }}"
                        class="w-full h-36 object-cover">
                </div>
            @endif

            <div class="p-4 h-38">
                <div class="flex items-center gap-2 mb-2">
                    @if ($bookmark->preview['favicon'])
                        <img src="{{ $bookmark->preview['favicon'] }}" alt="favicon" class="w-4 h-4">
                    @endif
                    <h3 class="font-medium truncate text-gray-200">
                        {{ $bookmark->name }}
                    </h3>
                </div>

                @if ($bookmark->preview['description'])
                    <p class="text-gray-400 text-sm line-clamp-2">
                        {{ $bookmark->preview['description'] }}
                    </p>
                @endif

                @if ($bookmark->category)
                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded mt-2">
                        {{ $bookmark->category }}
                    </span>
                @endif

                <div class="mt-2 text-sm text-gray-500">
                    {{ $bookmark->preview['domain'] }}
                </div>
            </div>
        </a>
        <x-bookmark-option :bookmark="$bookmark"/>

        </div>
    </div>
</div>
