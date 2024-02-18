<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class FilterController extends BaseController
{

    /**
     * Handle filter update requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateFilter(Request $request)
    {
        // Retrieve category and brand IDs from the request. Ensure they are arrays.
        $categoryIds = $request->categoryIds ?? [];
        $brandIds = $request->brandIds ?? [];

        // Update brands based on selected categories if any category is selected
        // If no categories are selected, get all brands
        if (!empty($categoryIds)) {
            $brands = Brand::whereHas('products', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds)
                      ->where('status', BaseStatusEnum::PUBLISHED); 
            })->get();
        } else {
            $brands = Brand::whereIn('id',$brandIds);
        }

        // Similarly, update categories based on selected brands if any brand is selected
        // If no brands are selected, get all categories
        if (!empty($brandIds)) {
            $categories = Category::whereHas('products', function ($query) use ($brandIds) {
                $query->whereIn('brand_id', $brandIds)
                      ->where('status', BaseStatusEnum::PUBLISHED);
            })->get();
        } else {
            $categories = Category::whereIn('id',$categoryIds);
        }

        // Compact both brands and categories into the response
        return response()->json([
            'html' => view('views.ecommerce.includes.filters', compact('brands','categories'))->render(),
        ]);
    }

}
