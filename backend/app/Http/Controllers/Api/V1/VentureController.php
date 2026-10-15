<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentureResource;
use App\Models\Venture;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VentureController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return VentureResource::collection(Venture::active()->ordered()->get());
    }
}
