
@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
@dump($taxes)
@dump($brands)
@dump($linee)
@dump($strumenti)
<a class="btn btn-info" href="https://dev.marigo.collaudo.biz/admin/ecommerce/products">Go back to products</a>
@stop
