<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicPageController extends Controller
{
    /**
     * Render the product-specific public landing page.
     */
    public function home(): View
    {
        return view('public.home');
    }

    /**
     * Render the complete feature and user documentation guide.
     */
    public function docs(): View
    {
        return view('public.docs');
    }
}
