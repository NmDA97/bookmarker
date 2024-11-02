<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Http\Requests\StoreBookmarkRequest;
use App\Services\UrlPreviewService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class BookmarkController extends Controller
{
    /**
     * URL preview service instance.
     */
    private UrlPreviewService $previewService;

    /**
     * Create a new controller instance.
     */
    public function __construct(UrlPreviewService $previewService)
    {
        $this->previewService = $previewService;
    }

    /**
     * Display a listing of bookmarks with preview data.
     */
    public function index(): View
    {
        $bookmarks = auth()->user()
            ->bookmarks()
            ->latest()
            ->get()
            ->map(function ($bookmark) {
                $bookmark->preview = $this->previewService->getPreview($bookmark->url);
                return $bookmark;
            });

        return view('home', compact('bookmarks'));
    }

    /**
     * Store a newly created bookmark in storage.
     */
    public function store(StoreBookmarkRequest $request): RedirectResponse
    {
        auth()->user()->bookmarks()->create($request->validated());

        return redirect()
            ->route('home')
            ->with('success', 'Bookmark saved successfully!');
    }
    public function destroy(Bookmark $bookmark): RedirectResponse
    {
        // $this->authorize('delete', $bookmark);
        // dd($bookmark);
        $bookmark->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Bookmark saved successfully!');
    }

    /**
     * Get preview data for a URL.
     */
    public function getUrlPreview(Request $request): JsonResponse
    {
        $preview = $this->previewService->getPreview(
            $request->validate(['url' => 'required|url'])['url']
        );

        return response()->json($preview);
    }
}
