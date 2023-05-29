@php


    // UPDATE BRANDS

//    $products=DB::connection('mysql2')->select('SELECT * FROM `art_articolo` WHERE categoria=6 OR categoria=15 OR categoria=17;');

    $products=DB::connection('mysql2')->table("art_articolo")->where('categoria',[6,15,17])->get();
//    $fornitore=$products->fk_fornitore_id;
//     @dd($fornitore);
//    $products=DB::connection('mysql2')->table("art_articolo")->where('categoria',[6,15,17])->whereIn('fk_fornitore_id',[443,441,439,383,295,124])->get();

    $items = \Botble\Ecommerce\Models\Product::query()->get()->pluck('name')->toArray();
    $products = $products->map(function ($item){
            return (array)$item;
        })->unique(function ($item) use($items) {
            return trim($item['nome']);
        });
    try {
        \Illuminate\Support\Facades\DB::transaction(function ()use($products,$items){
            foreach ($products as $product) {
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
                        'images' => collect([strtolower($product['codice']).'.jpg'])->toJson(),
                    ]);
                }
                $productItem->categories()->sync([$product['fk_linea_id']]);
            }
        });
    }catch (Throwable $e){
        dd($e);
    }
dd($products,count($products));
    @dd($products);

    // foreach ($brands as $brand) {
    //     dump(DB::connection('mysql')->table('ec_brands')->updateOrInsert(
    //         [
    //             'id' => $brand->pk_fornitore_id,
    //             'name' => $brand->nome,
    //         ]
    //         ,
    //         [
    //         'status' => 'published',
    //         'order' => '0',
    //         ]
    //     ));
    // }

    // UPDATE taxes
    // $taxes=DB::connection('mysql2')->select('select * from arc_codice_iva');
    // foreach ($taxes as $tax) {
    //     dump(DB::connection('mysql')->table('ec_taxes')->updateOrInsert(
    //         [
    //             'id' => $tax->pk_codice_iva_id,
    //             'nome' => $tax->nome,
    //         ]
    //         ,
    //         [
    //         'codice'=>$tax->codice,
    //         'desc'=>$tax->descrizione,
    //         'percentage'=>$tax->percentuale,
    //         'tipo'=>$tax->tipo,
    //         'status' => 'published',
    //         ]
    //     ));
    // }


    // $taxes=DB::connection('mysql2')->select('select * from arc_codice_iva');
    // foreach ($taxes as $tax) {
    //     dump(DB::connection('mysql')->table('ec_taxes')->updateOrInsert(
    //         [
    //             'id' => $tax->pk_codice_iva_id,
    //             'nome' => $tax->nome,
    //         ]
    //         ,
    //         [
    //         'codice'=>$tax->codice,
    //         'desc'=>$tax->descrizione,
    //         'percentage'=>$tax->percentuale,
    //         'tipo'=>$tax->tipo,
    //         'status' => 'published',
    //         ]
    //     ));
    // }


        // $impegnos= App\Models\Impegno_articolo::all();
        // foreach ($impegnos as $impegno ) {
        //     dump($impegno->consumabili->name);
        // }


        // $c_i= App\Models\Customer_strument::all();
        // foreach ($c_i as $el ) {
        //     dump($el->customer->name);
        //     dump($el->strument->name);
        // }



@endphp
