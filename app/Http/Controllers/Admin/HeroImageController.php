<?php

namespace App\Http\Controllers\Admin;

use App\Models\HeroImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heroImages = HeroImage::orderBy('order')->get();
        return view('admin.hero_images.index', compact('heroImages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.hero_images.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // If cropped image data is present, process it
        if ($request->filled('cropped_image')) {
            $base64Data = $request->input('cropped_image');
            
            try {
                @list($type, $imageData) = explode(';', $base64Data);
                @list(, $imageData)      = explode(',', $imageData);
                
                $extension = 'jpg';
                if (strpos($type, 'png') !== false) {
                    $extension = 'png';
                } elseif (strpos($type, 'gif') !== false) {
                    $extension = 'gif';
                }
                
                $filename = 'hero-images/' . time() . '_' . Str::random(10) . '.' . $extension;
                Storage::disk('public')->put($filename, base64_decode($imageData));
                
                HeroImage::create([
                    'image_path' => '/storage/' . $filename,
                    'order' => (HeroImage::max('order') ?? 0) + 1,
                ]);

                return redirect()->route('admin.hero-images.index')->with('success', 'Image cropped and uploaded successfully.');
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to process the cropped image: ' . $e->getMessage()]);
            }
        }

        // Fallback to standard upload
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hero-images', 'public');

            HeroImage::create([
                'image_path' => '/storage/' . $path,
                'order' => (HeroImage::max('order') ?? 0) + 1,
            ]);

            return redirect()->route('admin.hero-images.index')->with('success', 'Image uploaded successfully.');
        }

        return back()->withErrors(['image' => 'Please upload an image.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $heroImage = HeroImage::findOrFail($id);

        // Remove the file from storage
        // image_path is like /storage/hero-images/xyz.jpg
        // we need public/hero-images/xyz.jpg
        $relativePath = str_replace('/storage/', '', $heroImage->image_path);
        Storage::disk('public')->delete($relativePath);

        $heroImage->delete();

        return redirect()->route('admin.hero-images.index')->with('success', 'Image deleted successfully.');
    }

    /**
     * Reorder the hero images.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:hero_images,id',
        ]);

        foreach ($request->input('ids') as $index => $id) {
            HeroImage::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Hero images reordered successfully.']);
    }
}
