<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(string $locale)
    {
        if (in_array($locale, ['ta', 'en'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }
}
