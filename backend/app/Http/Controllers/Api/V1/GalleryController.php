<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryItemResource;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GalleryController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $items = GalleryItem::active()->ordered()
            ->when($request->string('category')->toString(), fn ($q, $category) => $q->where('category', $category))
            ->get();

        return GalleryItemResource::collection($items);
    }
}
