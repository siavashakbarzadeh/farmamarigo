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
use Illuminate\Http\Request;
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



}
