<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostEditorMediaController extends Controller
{
    public function storeImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $validated['image']->store('posts/content', 'public');

        return response()->json([
            'url' => url('/storage/'.$path),
            'path' => $path,
        ]);
    }
}
