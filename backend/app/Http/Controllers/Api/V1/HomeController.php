<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutResource;
use App\Http\Resources\ExpertiseResource;
use App\Http\Resources\GalleryItemResource;
use App\Http\Resources\MilestoneResource;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\StatResource;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\VentureResource;
use App\Models\About;
use App\Models\Expertise;
use App\Models\GalleryItem;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Stat;
use App\Models\Testimonial;
use App\Models\Venture;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public const CACHE_KEY = 'home';

    public function __invoke(SettingsService $settings): JsonResponse
    {
        $data = Cache::remember(self::CACHE_KEY, now()->addHour(), fn () => [
            'settings' => $settings->all(),
            'about' => ($about = About::active()->ordered()->first()) ? AboutResource::make($about)->resolve() : null,
            'stats' => $this->collect(StatResource::class, Stat::active()->ordered()->get()),
            'ventures' => $this->collect(VentureResource::class, Venture::active()->ordered()->get()),
            'expertise' => $this->collect(ExpertiseResource::class, Expertise::active()->ordered()->get()),
            'milestones' => $this->collect(MilestoneResource::class, Milestone::active()->ordered()->get()),
            'gallery' => $this->collect(GalleryItemResource::class, GalleryItem::active()->ordered()->get()),
            'partners' => $this->collect(PartnerResource::class, Partner::active()->ordered()->get()),
            'testimonials' => $this->collect(TestimonialResource::class, Testimonial::active()->ordered()->get()),
        ]);

        return response()->json(['data' => $data]);
    }

    /**
     * @param  class-string<JsonResource>  $resource
     */
    private function collect(string $resource, iterable $items): array
    {
        return $resource::collection($items)->resolve();
    }
}
