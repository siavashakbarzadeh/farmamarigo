@php


    // UPDATE BRANDS

//    $products=DB::connection('mysql2')->select('SELECT * FROM `art_articolo` WHERE categoria=6 OR categoria=15 OR categoria=17;');

    $brandsId=DB::connection('mysql2')->table("art_articolo")->select('fk_fornitore_id')->where('categoria',[6,15,17])->get();
    dd(collect($brandsId)->map(function ($item){
        return (array)$item;
    })->pluck('fk_fornitore_id')->union());
    $brands=DB::connection('mysql2')->table("acq_fornitore")->whereIn('pk_fornitore_id',$brandsId->toArray())->get();
    @dd($brands);
     foreach ($brands as $brand){
//         @dd($brand->nome);

          $cbrand = \Botble\Ecommerce\Models\Brand::updateOrCreate([
                        'name' => $brand->nome,
                        'description' => 'Description',

                    ]);
     }

     @dd($brands);


@endphp
