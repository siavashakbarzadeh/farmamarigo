@php


    // UPDATE BRANDS

    $brands=DB::connection('mysql2')->select('select * from acq_fornitore');
    dump($brands);
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
