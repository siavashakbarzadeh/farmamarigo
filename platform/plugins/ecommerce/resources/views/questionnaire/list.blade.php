@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="py-3 d-flex justify-content-end">
        <a href="{{ route('admin.ecommerce.questionnaires.create') }}" class="btn btn-primary">Nuovo Questionario</a>
    </div>
    @if(session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>
                Titolo
            </th>
            <th>
                Stato
            </th>
            <th>
                Data di inizio
            </th>
            <th>
                Data di scadenza
            </th>
            <th>
                Azioni
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($questionnaires as $questionnaire)
            <tr>
                <td>
                    {{ $questionnaire->title }}
                </td>
                <td>
                    <span
                        class="text-white py-1 px-2 rounded-1 {{ $questionnaire->is_active ? 'bg-success' : 'bg-danger' }}">{{ $questionnaire->is_active ? 'Attivo' : 'Inattivo' }}</span>
                </td>
{{--                <td>{{ $questionnaire->start_at }}</td>--}}
                <td>{{ Carbon\Carbon::parse($questionnaire->start_at)->format('d-m-Y') }}</td>
                <td>{{ Carbon\Carbon::parse($questionnaire->end_at)->format('d-m-Y') }}</td>
{{--                <td>{{ $questionnaire->end_at }}</td>--}}
                <td>
                    {{--                <a class="btn btn-primary mr-2" href="https://dev.marigo.collaudo.biz/admin/ecommerce/questionnaire/viewQuestionnaire?id={{ $Q->id }}" class="viewQuestionnaire"><i class="fa fa-eye"></i></a>--}}
                    <div class="d-flex">
                        <form action="{{ route('admin.ecommerce.questionnaires.delete',$questionnaire->id) }}"
                              method="post" class="js-form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                        @if($questionnaire->is_active)
                            <form action="{{ route('admin.ecommerce.questionnaires.inactive',$questionnaire->id) }}"
                                  method="post" class="js-form-status">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-xmark"></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.ecommerce.questionnaires.active',$questionnaire->id) }}"
                                  method="post" class="js-form-status">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check"></i>
                                </button>
                            </form>
                        @endif
                        <a class="btn btn-primary mr-2"
                           href="{{ route('admin.ecommerce.questionnaires.edit',$questionnaire->id) }}"><i
                                class="fa fa-edit"></i></a>
                        <a class="btn btn-dark mr-2"
                           href="{{ route('admin.ecommerce.questionnaires.show',$questionnaire->id) }}"><i
                                class="fa fa-eye text-white"></i></a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        <div class="">
            {{ $questionnaires->links() }}
        </div>
    </div>
    @push('header')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    @endpush
    @push('footer')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function () {
                $(document).on('submit','.js-form-status',function (e) {
                    let form = this;
                    e.preventDefault();
                    Swal.fire({
                        title: 'Sei sicuro di cambiare lo stato di questo questionario?',
                        showCancelButton: true,
                        cancelButtonText: 'cancella',
                        confirmButtonText: 'si',
                        denyButtonText: `No`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
                $(document).on('submit','.js-form-delete',function (e) {
                    let form = this;
                    e.preventDefault();
                    Swal.fire({
                        title: 'Sei sicuro di eliminare questo questionario?',
                        showCancelButton: true,
                        cancelButtonText: 'cancella',
                        confirmButtonText: 'si',
                        denyButtonText: `No`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@stop
