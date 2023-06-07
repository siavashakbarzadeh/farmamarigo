<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\DB;

class CustomImport extends BaseController
{
    public function sconto(){
        $inputs = request()->all();
        $products=$inputs['products'];
        $users=$inputs['users'];
        $region=$inputs['region'];
        $where='';
        foreach($region as $reg){
            $where.= 'region_id='.$reg.' or';
        }
        $last = strrpos($where, ' or');
        $where = substr($where,0, $last);
        $SourceProducts=DB::connection('mysql')->select('select id from ec_customers where '.$where);

        foreach($SourceProducts as $src){
            array_push($users, $src->id);
        }
        dd($users);
    }




    public function taxes(){
        $taxes=DB::connection('mysql2')->select('select * from arc_codice_iva');
        $taxes_updated=array();
        foreach ($taxes as $tax) {
          $row=DB::connection('mysql')->table('ec_taxes')->updateOrInsert(
            [
                'id' => $tax->pk_codice_iva_id,
                'nome' => $tax->nome,
            ]
            ,
            [
            'codice'=>$tax->codice,
            'desc'=>$tax->descrizione,
            'percentage'=>$tax->percentuale,
            'tipo'=>$tax->tipo,
            'status' => 'published',
            ]
        );
        if($row==true) array_push($taxes_updated, $row);
        }
        if(empty($taxes_updated)) return 'No tax record updated';
        else return $taxes_updated;
    }

    public function brands(){
        $brands=DB::connection('mysql2')->select('select * from acq_fornitore');
        $brands_updated=array();
        foreach ($brands as $brand) {
            $row=DB::connection('mysql')->table('ec_brands')->updateOrInsert(
            [
                'id' => $brand->pk_fornitore_id,
                'name' => $brand->nome,
            ]
            ,
            [
            'status' => 'published',
            'order' => '0',
            ]
        );
        if($row==true){
            array_push($brands_updated, $row);
        }
        }
        if(empty($brands_updated)) return 'No brands record updated';
        else return $brands_updated;
    }


    public function importproduct()
    {

        // UPDATE BRANDS

//    $products=DB::connection('mysql2')->select('SELECT * FROM `art_articolo` WHERE categoria=6 OR categoria=15 OR categoria=17;');
        $products=DB::connection('mysql2')->table("art_articolo")->whereIn('categoria',[6,15,17])->whereIn('fk_linea_id',[443,441,439,383,295,124])->get();
        $products = $products->map(function ($item){
            return (array)$item;
        });
        $variants=$products->filter(function ($item){
            return strlen($item['variante_1']);
        })->groupBy(function ($item){
            $i =explode(" ",$item['nome']);
            return $i[0];
        })->mapWithKeys(function ($item,$key){
            return [$key=>collect($item)->groupBy(function ($item){
                $i =array_filter(explode(" ",$item['nome']));
                return implode(" ",array_slice($i,0,count($i) == 3 ? 1 : 2));
            })];
        });
        $brandsId=DB::connection('mysql2')->table("art_articolo")->select('fk_fornitore_id')->where('fk_fornitore_id',$products->pluck('fk_fornitore_id')->toArray())->get();
        $brandsId = collect($brandsId)->map(function ($item){
            return (array)$item;
        })->pluck('fk_fornitore_id')->unique();
        $brands=DB::connection('mysql2')->table("acq_fornitore")->get();
        $brands=collect($brands)->map(function ($item){
            return (array)$item;
        })->pluck('nome','pk_fornitore_id');
        $items = \Botble\Ecommerce\Models\Product::query()->get()->pluck('name')->toArray();
        $products = $products->unique(function ($item) use($items) {
            return trim($item['nome']);
        });
        try {
            \Illuminate\Support\Facades\DB::transaction(function ()use($products,$variants,$brands,$items){
                foreach ($brands as $brand){
                    $brandItem =\Botble\Ecommerce\Models\Brand::updateOrCreate([
                        'name'=>$brand,
                    ],[
                        'name'=>$brand
                    ]);
//                if (!\Illuminate\Support\Facades\DB::table('')->where('lang_code',"en_US")->where('ec_brands_id')->count()){
//                    \Illuminate\Support\Facades\DB::table('ec_brands_translations')->insert([
//                        'lang_code'=>"en_US",
//                        'ec_brands_id'=>$brandItem->id,
//                        'name'=>$brand,
//                    ]);
//                }

                    \Botble\Slug\Models\Slug::create([
                        'key'=>$brand,
                        'reference_id'=>$brandItem->id,
                        'reference_type'=>$brandItem->getMorphClass(),
                        'prefix'=>"brands"
                    ]);
                }
                foreach ($variants as $variantItems) {
                    foreach ($variantItems as $item) {
                        foreach ($item as $item2) {
                            if ($item2['variante_2']){
                                $order1 = intval(ProductAttribute::query()->where('attribute_set_id',1)->max('order'));
                                ProductAttribute::query()->firstOrCreate([
                                    'title'=>$item2['variante_2'],
                                ],[
                                    'title'=>$item2['variante_2'],
                                    'slug'=>Str::slug($item2['variante_2']),
                                    'attribute_set_id'=>1,
                                    'status'=>"published",
                                    'order'=>$order1 == 0 ? 0 :$order1++,
                                ]);
                            }
                            if ($item2['variante_3']){
                                $order2 = intval(ProductAttribute::query()->where('attribute_set_id',3)->max('order'));
                                ProductAttribute::query()->firstOrCreate([
                                    'title'=>$item2['variante_3'],
                                ],[
                                    'title'=>$item2['variante_3'],
                                    'slug'=>Str::slug($item2['variante_3']),
                                    'attribute_set_id'=>3,
                                    'status'=>"published",
                                    'order'=>$order2 == 0 ? 0 :$order2++,
                                ]);
                            }
                        }
                    }
                }
                foreach ($products as $product) {
                    $variationItems =[];
                    if (strlen($product['variante_2'])){
                        $var2=ProductAttribute::where('title',$product['variante_2'])->first();
                        if($var2){
                            $variantItems[]=$var2;
                        }
                    }
                    if (strlen($product['variante_3'])){
                        $var3=ProductAttribute::where('title',$product['variante_3'])->first();
                        if ($var3){
                            $variantItems[]=$var3;
                        }
                    }
                    if (count($variationItems)){
                        dd($variationItems);
                    }
                    if (in_array(str_replace('&','and',trim($product['nome'])),$items)){
                        $productItem = \Botble\Ecommerce\Models\Product::query()->where('name',str_replace('&','and',trim($product['nome'])))->first();
                        $productItem->update([
                            'description' => 'Description',
                            'price' => $product['prezzo'],
                            'images' => collect([strtolower($product['codice']).'.jpg'])->toJson(),
                        ]);
                    }else{
                        $productItem = \Botble\Ecommerce\Models\Product::query()->create([
                            'name' => str_replace('&','and',trim($product['nome'])),
                            'description' => 'Description',
                            'price' => $product['prezzo'],
                            'brand_id'=>\Botble\Ecommerce\Models\Brand::where('name',$brands->toArray()[$product['fk_fornitore_id']])->first()->id,
                            'images' => collect([strtolower($product['codice']).'.jpg'])->toJson(),
                        ]);
                        \Illuminate\Support\Facades\DB::table('ec_products_translations')->insert([
                            'lang_code'=>"en_US",
                            'ec_products_id'=>$productItem->id,
                            'name'=>str_replace('&','and',trim($product['nome'])),
                        ]);
                        \Botble\Slug\Models\Slug::create([
                            'key'=>\Illuminate\Support\Str::slug(str_replace('&','and',trim($product['nome']))),
                            'reference_id'=>$productItem->id,
                            'reference_type'=>$productItem->getMorphClass(),
                            'prefix'=>"products"
                        ]);
                        $productVariation = ProductVariation::create([
                            'configurable_product_id'=>$productItem->id,
                        ]);
                        $productVariation->productAttributes()->attach([

                        ]);
                    }
                    $productItem->categories()->sync([$product['fk_linea_id']]);
                }
                dd("ok");
            });
        }catch (Throwable $e){
            dd($e);
        }
        return redirect()->back()->withSuccess('IT WORKS!');
    }



}
