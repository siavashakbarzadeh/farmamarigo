@php


    // UPDATE BRANDS

//    $products=DB::connection('mysql2')->select('SELECT * FROM `art_articolo` WHERE categoria=6 OR categoria=15 OR categoria=17;');
    $products=DB::connection('mysql2')->table("art_articolo")->where('categoria',6)->where('fk_linea_id',439)->get();
    foreach ($products as $product) {

        $productItem = \Botble\Ecommerce\Models\Product::updateOrCreate([
                        'name' => $product->nome
],[
            'description' => 'Description',
            'price' => $product->prezzo


]);
        $productItem->categories()->sync([439]);
    }
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
