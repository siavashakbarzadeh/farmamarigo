<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\ProductCategory;



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
                $query->whereExists(function ($subQuery) use ($categoryIds) {
                    $subQuery->select(DB::raw(1))
                              ->from('ec_product_categories')
                              ->join('ec_product_category_product', 'ec_product_categories.id', '=', 'ec_product_category_product.category_id')
                              ->whereRaw('ec_products.id = ec_product_category_product.product_id')
                              ->whereIn('ec_product_categories.id', $categoryIds) // Specify the table name
                              ->where('status', "published");
                });
            })->get();
        } else {
            $brands = Brand::whereIn('id',$brandIds);
        }

        // Similarly, update categories based on selected brands if any brand is selected
        // If no brands are selected, get all categories
        if (!empty($brandIds)) {
            $categories = ProductCategory::whereHas('products', function ($query) use ($brandIds) {
                $query->whereIn('brand_id', $brandIds)
                      ->where('status', "published");
            })->get();
        } else {
            $categories = ProductCategory::whereIn('id',$categoryIds);
        }

        // Compact both brands and categories into the response
        return response()->json([
            'html' => view('ecommerce.includes.filters', compact('brands','categories'))->render(),
        ]);
    }

}
