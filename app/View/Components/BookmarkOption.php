<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BookmarkOption extends Component
{
    /**
     * Create a new component instance.
     * 
     */
    public $bookmark;
     public function __construct($bookmark)
    {
        $this->bookmark = $bookmark;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.bookmark-option');
    }
}
