<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Traits\ControllerTrait;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\Cat;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CampaignController extends BaseApiController
{
    use ControllerTrait;

    protected $table = 'campaigns';
    protected $model = Campaign::class;
    protected $resource = CampaignResource::class;

    protected $columns = [
        'campaign_id',
        'campaign_title',
        'campaign_description',
        'start_at',
        'close_at',
        'conditions',
        'product_image',
        'ugc_no',
        'ugc_videos_no',
        'video_seconds_min',
        'video_seconds_max',
        'video_price',
        'total_price',
        'is_usg_show',
        'is_brand_show',
        'is_tiktok',
        'is_instagram',
        'is_youtube',
        'is_sent_to_content_creator',

        'deleted_at',
        'created_at',
        'updated_at',
    ];


    public function create()
    {
        try {
            $city_names = City::orderBy('city_name', 'ASC')->pluck('city_name')->toArray();
            $cat_names = Cat::orderBy('cat_name', 'ASC')->pluck('cat_name')->toArray();
            return $this->sendResponse(true, [
                'city_names' => $city_names,
                'cat_names' => $cat_names,
            ], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'campaign_title' => 'required|min:10|max:190|unique:campaigns,campaign_title',
                'campaign_description' => 'required|min:20|max:200',
                'start_at' => 'required|date|date_format:Y-m-d H:i:s',
                'close_at' => 'required|date|date_format:Y-m-d H:i:s',
                'conditions' => 'required|min:20|max:200',
                'product_image' => 'required|min:10|max:190',
                'ugc_no' => 'required|numeric|min:1|max:100',
                'ugc_videos_no' => 'required|numeric|min:1|max:1000',
                'video_seconds_min' => 'required|numeric|min:1|max:10800',
                'video_seconds_max' => 'required|numeric|min:1|max:10800',
                'video_price' => 'required|min:1|max:10000',
                'is_usg_show' => 'required|in:0,1',
                'is_brand_show' => 'required|in:0,1',
                'is_tiktok' => 'required|in:0,1',
                'is_instagram' => 'required|in:0,1',
                'is_youtube' => 'required|in:0,1',
                'is_sent_to_content_creator' => 'required|in:0,1',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
                'city_names' => 'required|array',
                'city_names.*' => 'required|exists:cities,city_name',

            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            DB::beginTransaction();
            $item = Campaign::create([
                'campaign_title' => $request->campaign_title,
                'campaign_description' => $request->campaign_description,
                'start_at' => $request->start_at,
                'close_at' => $request->close_at,
                'conditions' => $request->conditions,
                'product_image' => $request->product_image,
                'ugc_no' => $request->ugc_no,
                'ugc_videos_no' => $request->ugc_videos_no,
                'video_seconds_min' => $request->video_seconds_min,
                'video_seconds_max' => $request->video_seconds_max,
                'video_price' => $request->video_price,
                'is_usg_show' => $request->is_usg_show,
                'is_brand_show' => $request->is_brand_show,
                'is_tiktok' => $request->is_tiktok,
                'is_instagram' => $request->is_instagram,
                'is_youtube' => $request->is_youtube,
                'is_sent_to_content_creator' => $request->is_sent_to_content_creator,
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
                'city_names' => 'required|array',
                'city_names.*' => 'required|exists:cities,city_name',
            ]);

            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();
            $item->cats()->sync($cat_ids);

            $city_ids = City::withTrashed()->whereIn('city_name', $request->city_names)->pluck('city_id')->toArray();
            $item->cities()->sync($city_ids);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new CampaignResource($item),
            ], trans('CampaignegoryHasBeenCreated'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make([...$request->all(), 'campaign_id' => $id], [
                'campaign_id' => 'required|exists:campaigns,campaign_id',
                'campaign_title' => 'required|min:10|max:190|unique:campaigns,campaign_title,' . $id . ',campaign_id',
                'campaign_description' => 'required|min:20|max:200',
                'start_at' => 'required|date|date_format:Y-m-d H:i:s',
                'close_at' => 'required|date|date_format:Y-m-d H:i:s',
                'conditions' => 'required|min:20|max:200',
                'product_image' => 'required|min:10|max:190',
                'ugc_no' => 'required|numeric|min:1|max:100',
                'ugc_videos_no' => 'required|numeric|min:1|max:1000',
                'video_seconds_min' => 'required|numeric|min:1|max:10800',
                'video_seconds_max' => 'required|numeric|min:1|max:10800',
                'video_price' => 'required|min:1|max:10000',
                'is_usg_show' => 'required|in:0,1',
                'is_brand_show' => 'required|in:0,1',
                'is_tiktok' => 'required|in:0,1',
                'is_instagram' => 'required|in:0,1',
                'is_youtube' => 'required|in:0,1',
                'is_sent_to_content_creator' => 'required|in:0,1',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
                'city_names' => 'required|array',
                'city_names.*' => 'required|exists:cities,city_name',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = Campaign::withTrashed()->where('campaign_id', $id)->first();

            DB::beginTransaction();
            $item->update([
                'campaign_title' => $request->campaign_title,
                'campaign_description' => $request->campaign_description,
                'start_at' => $request->start_at,
                'close_at' => $request->close_at,
                'conditions' => $request->conditions,
                'product_image' => $request->product_image,
                'ugc_no' => $request->ugc_no,
                'ugc_videos_no' => $request->ugc_videos_no,
                'video_seconds_min' => $request->video_seconds_min,
                'video_seconds_max' => $request->video_seconds_max,
                'video_price' => $request->video_price,
                'is_usg_show' => $request->is_usg_show,
                'is_brand_show' => $request->is_brand_show,
                'is_tiktok' => $request->is_tiktok,
                'is_instagram' => $request->is_instagram,
                'is_youtube' => $request->is_youtube,
                'is_sent_to_content_creator' => $request->is_sent_to_content_creator,
            ]);


            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();
            $item->cats()->sync($cat_ids);

            $city_ids = City::withTrashed()->whereIn('city_name', $request->city_names)->pluck('city_id')->toArray();
            $item->cities()->sync($city_ids);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new CampaignResource($item),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
