<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SettingHelper;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show the edit form for global settings.
     */
    public function edit()
    {
        // Enforce admin permission (we can also apply this middleware in routes)
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $languageHelperEnabled = SettingHelper::get('global_language_helper_enabled', '1') === '1';

        return view('admin.settings', compact('languageHelperEnabled'));
    }

    /**
     * Update global settings in the database.
     */
    public function update(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $enabled = $request->has('global_language_helper_enabled') ? '1' : '0';
        SettingHelper::set('global_language_helper_enabled', $enabled);

        return redirect()->route('admin.settings.edit')->with('success', __('Settings updated successfully.'));
    }
}
