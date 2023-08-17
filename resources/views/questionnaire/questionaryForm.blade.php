@php
    SeoHelper::setTitle(__('Questionnaire'));
    Theme::fireEventGlobalAssets();
@endphp

{!! Theme::partial('header') !!}
<div class="container">
    <main class="main page-404">
        {{--        <form action="{{ route('questionary.saveanswers') }}" method="POST">--}}
        <form action="{{ route('questionary.save-answers') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">{{$questonary->title}}</label>

            </div>
            <div class="form-group">
                <label for="title">{{$questonary->desc}}</label>

            </div>


            @foreach( $questions as $question)
                <div id="questions-container">
                    <div class="question-container">
                        <div class="form-group">
                            <label for="answer_{{ $question->id }}">{{$question->question_text}}</label>
                            <input type="hidden" name="answers[{{ $loop->iteration }}][question_id]"
                                   value="{{ $question->id }}">
                            @if($question->options->count())
                                <select name="answers[{{ $loop->iteration }}][answer_option_id]"
                                        id="answer_{{ $question->id }}" class="">
                                    <option value="" disabled selected class="">Not selected</option>
                                    @foreach($question->options as $option)
                                        <option @if(old('answers.'.$loop->iteration.'.answer_option_id') == $option->id) selected @endif value="{{ $option->id }}">{{ $option->value }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="answers[{{ $loop->iteration }}][answer_text]"
                                       id="answer_{{ $question->id }}" value="{{ old('answers.'.$loop->iteration.'.answer_text') }}" class="form-control" required>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
            <button type="submit" class="btn btn-success mb-3">Conferma</button>
        </form>
    </main>
</div>


{!! Theme::partial('footer') !!}

