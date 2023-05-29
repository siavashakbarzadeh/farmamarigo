@php


    // UPDATE BRANDS

//    $products=DB::connection('mysql2')->select('SELECT * FROM `art_articolo` WHERE categoria=6 OR categoria=15 OR categoria=17;');

    $brandsId=DB::connection('mysql2')->table("art_articolo")->select('fk_fornitore_id')->where('categoria',[6,15,17])->get()->unique();
    $brands=DB::connection('mysql2')->table("acq_fornitore")->where('pk_fornitore_id',34222)->get();
     foreach ($brands as $brand){
          $productItem = \Botble\Ecommerce\Models\Brand::query()->create([
                        'name' => $brand->nome,

                    ]);
     }

     @dd($brands);


@endphp
