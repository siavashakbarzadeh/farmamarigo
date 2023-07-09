@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="container">
        <div class="questionnaire_section">
            @if(session()->has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('admin.ecommerce.questionnaires.store') }}" method="post" class="w-100 js-form">
                @csrf
                <div class="mb-3">
                    <label for="questionnaire_title">Title</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                           id="questionnaire_title">
                    @error('title')
                    <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="questionnaire_desc">Description</label>
                    <textarea name="desc" class="form-control" id="questionnaire_desc" cols="30"
                              rows="10">{{ old('desc') }}</textarea>
                    @error('desc')
                    <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row mb-2">
                    <div class="col-12 col-md-6 mb-2">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <label for="start_at">Start at</label>
                            <input type="date" name="start_at" value="{{ old('start_at') }}" id="start_at">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-2">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <label for="end_at">End at</label>
                            <input type="date" name="end_at" value="{{ old('end_at') }}" id="end_at">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="py-2 border-bottom">Queistions</div>
                    <div class="questions js-questions d-flex flex-column">
                        @if(old('questions') && count(old('questions')))
                            @foreach(old('questions') as $i => $question)
                                <div class="question-item py-2">
                                    <label class="d-block w-100">
                                        <textarea class="form-control"
                                                  name="questions[{{ $i }}]">{{ $question }}</textarea>
                                        @error('questions.'.$i)
                                        <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </label>
                                    <div class="mt-1 d-flex justify-content-end">
                                        <button type="button" class="btn btn-danger js-btn-remove-question">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mt-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-success js-btn-add-question">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary col-12">
                    Create
                </button>
            </form>
        </div>

    </div>
    @push('header')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    @endpush
    @push('footer')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function () {
                $(document).on('submit', '.js-form', function (e) {
                    let form = this;
                    e.preventDefault();
                    const start_at = $('#start_at').val();
                    const end_at = $('#end_at').val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: "{{ route('admin.ecommerce.questionnaires.check-active-changes') }}",
                        data: {
                            start_at: start_at,
                            end_at: end_at,
                        },
                        type: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            if (response.questionnaire.activeQuestionnaireDates && response.questionnaire.activeQuestionnaireDates.end_at && response.questionnaire.activeQuestionnaireDates.start_at) {
                                Swal.fire({
                                    html:
                                        '<div>ATTENZIONE! abbiamo rilevato un questionario attivo oppure previsto per intervallo di tempo selezionato:' + response.questionnaire.el.title + '</div>' +
                                        '<div>Puoi cambiare le date del questionario' + response.questionnaire.el.title + '</div>' +

                                        '<div> premendo CONFERMA oppure ritornare all editing di questo questionario premendo CANCELLA </div>' +
                                        '<div>Inizio: ' + response.questionnaire.activeQuestionnaireDates.start_at + '</div>' +
                                        '<div>Scadenza: ' + response.questionnaire.activeQuestionnaireDates.end_at + '</div>',
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    confirmButtonText: 'Comfirma',
                                    cancelButtonText: 'Cancella',
                                }).then((result) => {
                                    if(result.isConfirmed){
                                        form.submit();
                                    }
                                });
                            } else {
                                form.submit();
                            }
                        }
                    });
                });
                $(document).on('click', '.js-btn-add-question', function () {
                    const questions = $('.js-questions');
                    const i = Math.abs(questions.find('.question-item').length + 1) * -1;
                    questions.append('<div class="question-item py-2">' +
                        '<label class="d-block w-100">' +
                        '<textarea class="form-control" name="questions[' + i + ']"></textarea>' +
                        '</label>' +
                        '<div class="mt-1 d-flex justify-content-end">' +
                        '<button type="button" class="btn btn-danger js-btn-remove-question">' +
                        '<i class="fa fa-minus"></i>' +
                        '</button>' +
                        '</div>' +
                        '</div>');
                });
                $(document).on('click', '.js-btn-remove-question', function (e) {
                    $(this).closest('.question-item').remove();
                });
            });
        </script>
    @endpush
@stop
