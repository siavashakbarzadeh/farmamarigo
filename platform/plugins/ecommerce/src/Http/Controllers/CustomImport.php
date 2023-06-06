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

    public function index(){



    }

    public function consumabili()
    {
        $taxes=$this->taxes();
        $brands=$this->brands();
        $linee=$this->linea();
        $strumenti=$this->strumenti();
        $SourceProducts=DB::connection('mysql2')->select('select * from art_articolo where flag_scheda_strumento=0 and categoria=1 ');
        $UpdatedProducts=array();
        foreach($SourceProducts as $SourceProduct){
            $ConnectedStrumenti=$this->ConnectedStrumenti($SourceProduct);
            $row=DB::connection('mysql')->table('ec_products')->updateOrInsert([
            'sku'=>$SourceProduct->codice,
            ],[
                'price'=>$SourceProduct->prezzo,
                'name'=>$SourceProduct->nome,
                'linea_id'=>$SourceProduct->fk_linea_id,
                'brand_id'=>$SourceProduct->fk_fornitore_id,
                'tax_id'=>$SourceProduct->fk_codice_iva_id,
                'confezione'=>$SourceProduct->confezione,
                'status'=>'published',
                'length'=>$SourceProduct->lunghezza,
                'wide'=>$SourceProduct->profondita,
                'height'=>$SourceProduct->altezza,
                'weight'=>$SourceProduct->peso,
                'product_type'=>'physical'
            ]);
            $ec=DB::connection('mysql')->select("select * from ec_products where product_type='physical' ");
            foreach($ec as $e){
                DB::connection('mysql')->table('ec_product_category_product')->updateOrInsert(
                    [
                        'category_id' => 31,
                        'product_id' => $e->id,
                    ]
                );
            }


            if($row==true) array_push($UpdatedProducts, $row);
        }

        return view('plugins/ecommerce::customImport.success',compact('brands','taxes','linee','strumenti','UpdatedProducts'));
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

    public function linea(){
        $linee=DB::connection('mysql2')->select('select * from art_linea');
        $linea_updated=array();
        foreach ($linee as $linea) {
            $row=DB::connection('mysql')->table('ec_lines')->updateOrInsert(
            [
                'id' => $linea->pk_linea_id,
                'nome' => $linea->nome,
                'linea'=>$linea->linea
            ]
            ,
            [

            'categoria' => NULL,
            'gruppo' => NULL,
            'status'=>'published'
            ]
        );
        if($row==true){
            array_push($linea_updated, $row);
        }
        }
        if(empty($linea_updated)) return 'No linee record updated';
        else return $linea_updated;
    }



    public function strumenti(){

        $strumenti=DB::connection('mysql2')->select('select * from art_articolo where flag_scheda_strumento=1');
        $strumentiUpdated=array();
        foreach($strumenti as $strumento){
            $row=DB::connection('mysql')->table('ec_products')->updateOrInsert(
                [
                    'id' => $strumento->pk_articolo_id,
                    'name' => $strumento->nome,
                ]
                ,
                [
                    'status'=>'published',
                    'product_type'=>'digital',
                    'image'=>'products/3.jpg',
                    'price'=>0
                ]
            );
            $ec=DB::connection('mysql')->select("select * from ec_products where product_type='digital'");
            foreach($ec as $e){
                DB::connection('mysql')->table('ec_product_category_product')->updateOrInsert(
                    [
                        'category_id' => 32,
                        'product_id' => $e->id,
                    ]
                );
                DB::connection('mysql')->table('ec_product_tags')->updateOrInsert(
                    [
                        'id' => $e->id,

                    ]
                    ,
                    [
                        'name' => $e->name,
                        'status'=>'published'
                    ]
                );
            }



            if($row==true){
                array_push($strumentiUpdated, $row);
            }
        }
        if(empty($strumentiUpdated)) return 'No strumenti record updated';
        else return $strumentiUpdated;

    }


    public function ConnectedStrumenti($product){
        $Impegnos=[
            $product->fk_impegno1_id,
            $product->fk_impegno2_id,
            $product->fk_impegno3_id,
            $product->fk_impegno4_id,
            $product->fk_impegno5_id,
            $product->fk_impegno6_id,
            $product->fk_impegno7_id,
            $product->fk_impegno8_id
        ];
        if(!empty($Impegnos)){
            $PairUpdated=array();
            foreach($Impegnos as $Impegno){
                if($Impegno!=0){
                    //get struments of that impegno
                    $tags=DB::connection('mysql2')->select("select * from art_articolo where flag_scheda_strumento=1 and fk_impegno1_id=$Impegno or fk_impegno2_id=$Impegno or fk_impegno3_id=$Impegno or fk_impegno4_id=$Impegno or fk_impegno5_id=$Impegno or fk_impegno6_id=$Impegno or fk_impegno7_id=$Impegno or fk_impegno8_id=$Impegno");

                    foreach($tags as $tag){
                        $row=DB::connection('mysql')->table('ec_product_tag_product')->updateOrInsert(
                            [
                                'product_id' => $product->pk_articolo_id,
                                'tag_id' => $tag->pk_articolo_id,
                            ],[]);
                            if($row==true){
                                array_push($PairUpdated, $row);
                            }
                    }
                }
            }

            if(empty($PairUpdated)) return 'No connected record updated';
            else return $PairUpdated;
        }
        else{
            return false;
        }



    }








    public function users(){

    }

    private function user_tags(){

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
                    if ($product['variante_2'] && $var2=ProductAttribute::where('title',$product['variante_2'])->first()){
                        $variantItems[]=$var2;
                    }
                    if ($product['variante_3'] && $var3=ProductAttribute::where('title',$product['variante_3'])->first()){
                        $variantItems[]=$var3;
                    }
                    dump($variationItems);
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
