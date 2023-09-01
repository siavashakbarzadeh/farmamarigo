<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Tables\QuestionnaireTable;
use Aws\DocDB\DocDBClient;
use Botble\Ecommerce\Tables\OrderTable;
use Botble\Ecommerce\Tables\ProductTable;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Botble\Ecommerce\Models\Questionnaire;
use Botble\Ecommerce\Models\Question;
use Botble\Ecommerce\Models\Answer;
use Illuminate\Support\Facades\Validator;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questonary = Questionnaire::query()->active()->first();
        $questions = $questonary->questions()->whereDoesntHave('answers', function ($q) {
            $q->where('customer_id', auth('customer')->user()->id);
        })->get();
        if ($questions->count())
            return view('questionnaire.questionaryForm', compact('questions', 'questonary'));
        return redirect('/');
    }

    public function thankYou()
    {
        session()->reflash();
        if (session()->has('success'))
            return view('questionnaire.thanks');
        return redirect('/');
    }

    public function create()
    {
        return view('plugins/ecommerce::questionnaire.create');
    }

    public function edit($id)
    {
        $questionnaire = Questionnaire::query()->findOrFail($id);
        return view('plugins/ecommerce::questionnaire.edit', compact('questionnaire'));
    }

    public function getView()
    {
        return view('questionnaire.create');
    }


    public function viewQuestionnaire()
    {
        return view('plugins/ecommerce::questionnaire.view');
    }

    public function createQuestions(Request $request)
    {
        $questions = $request->input('questions');
        $questionnaireId = $request->input('questionnaire_id');
        foreach ($questions as $q) {
            $Question = new Question();
            $Question->question_text = $q;
            $Question->question_type = 'text';
            $Question->questionnaire_id = $questionnaireId;
            $questionnaire = Questionnaire::find($questionnaireId); // Retrieve the questionnaire model
            $Question->questionnaire()->associate($questionnaire);
            $Question->save();
        }
        return redirect()->route('admin.ecommerce.questionnaires.index')->with('success', "Questionario aggiunto con successo!");
    }

    public function questionnaires()
    {
        $questionnaires = Questionnaire::query()->latest()->paginate();
        return view('plugins/ecommerce::questionnaire.list', compact('questionnaires'));
    }

    public function active($questionnaire)
    {
        $questionnaire = Questionnaire::query()->findOrFail($questionnaire);
        Questionnaire::query()->update(['is_active' => false]);
        $questionnaire->update(['is_active' => true]);
        return redirect()->route('admin.ecommerce.questionnaires.index')->with('success', "La modifica dello stato ha avuto successo");
    }

    public function inactive($questionnaire)
    {
        $questionnaire = Questionnaire::query()->findOrFail($questionnaire);
        $questionnaire->update(['is_active' => false]);
        return redirect()->route('admin.ecommerce.questionnaires.index')->with('success', "La modifica dello stato ha avuto successo");
    }

    public function delete($questionnaire)
    {
        $questionnaire = Questionnaire::query()->findOrFail($questionnaire);
        $questionnaire->delete();
        return redirect()->route('admin.ecommerce.questionnaires.index')->with('success', "Eliminazione riuscita");
    }


    public function saveAnswers(AnswerRequest $request)
    {
        Answer::query()->insert(collect($request->answers)->map(function ($item) {
            return [
                'customer_id' => auth('customer')->user()->id,
                'question_id' => $item['question_id'],
                'question_option_id' => array_key_exists('answer_option_id', $item) ? $item['answer_option_id'] : null,
                'answer_text' => array_key_exists('answer_text', $item) ? $item['answer_text'] : null,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        })->toArray());
        return redirect()->route('questionnaire.thank-you')->with(['success' => "Grazie per la risposta"]);
    }

    public function getAnswers(Request $request)
    {

        return 'getAnswers';

    }

    public function checkActiveChanges(Request $request)
    {
        $this->validate($request, [
            'start_at' => ['required', 'date', 'after:today'],
            'end_at' => ['required', 'date', 'after:' . $request->start_at],
        ]);
        $start_at = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
        $end_at = Carbon::createFromFormat('Y-m-d', $request->end_at)->startOfDay();
        $questionnaireActive = Questionnaire::query()
            ->active()
            /*->where(function ($q) use ($start_at, $end_at) {
                $q->whereBetween('start_at', [$start_at, $end_at])
                    ->orWhereBetween('end_at', [$start_at, $end_at]);
            })*/
            ->first();
        $questionnaire = [
            'el' => $questionnaireActive,
        ];
        if ($questionnaireActive) {
            $activeQuestionnaireDates = $this->activeQuestionnaireDates($questionnaireActive->start_at, $questionnaireActive->end_at, $start_at, $end_at);
            if (count($activeQuestionnaireDates))
                $questionnaire['activeQuestionnaireDates'] = collect($activeQuestionnaireDates)->mapWithKeys(function ($item, $key) {
                    return [$key => $item->format('Y-m-d')];
                })->toArray();
        }
        return response()->json([
            'status' => 200,
            'questionnaire' => $questionnaire,
        ]);
    }

    private function activeQuestionnaireDates($questionnaireActiveStartAt, $questionnaireActiveEndAt, $start_at, $end_at)
    {
        if ($start_at->between($questionnaireActiveStartAt, $questionnaireActiveEndAt) && $end_at->between($questionnaireActiveStartAt, $questionnaireActiveEndAt)) {
            $dates = collect(CarbonPeriod::between($questionnaireActiveStartAt, $questionnaireActiveEndAt)->toArray())->filter(function ($date) use ($start_at, $end_at) {
                return !$date->between($start_at, $end_at);
            });
        } else {
            $dates = collect(CarbonPeriod::between($start_at, $end_at)->toArray())->filter(function ($date) use ($questionnaireActiveStartAt, $questionnaireActiveEndAt) {
                return !$date->between($questionnaireActiveStartAt, $questionnaireActiveEndAt);
            });
        }
        $reserved_dates = $this->getActiveElements($dates->toArray());
        if ($reserved_dates->count()) {
            return [
                'start_at' => $reserved_dates->first(),
                'end_at' => $reserved_dates->last(),
            ];
        } else {
            return [];
        }
    }

    public function show($id)
    {
        dd($id);
    }

    public function update(Request $request, $id)
    {
        $questionnaire = Questionnaire::query()->with('questions')->findOrFail($id);
        $this->validate($request, [
            'title' => ['required', 'string'],
            'desc' => ['required', 'string'],
            'end_at' => ['required', 'date', 'after:' . $request->start_at],
            'start_at' => ['required', 'date', 'after:today'],
            'questions' => ['nullable', 'array'],
            'questions.*.value' => ['required', 'string'],
            'questions.*.option' => ['required', 'array'],
            'questions.*.option.*' => ['required', 'string'],
        ]);
        $start_at = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
        $end_at = Carbon::createFromFormat('Y-m-d', $request->end_at)->startOfDay();
        $questionnaireActive = Questionnaire::query()
            ->active()
            /*->where(function ($q) use ($start_at, $end_at) {
                $q->whereBetween('start_at', [$start_at, $end_at])
                    ->orWhereBetween('end_at', [$start_at, $end_at]);
            })*/
            ->whereNot('id', $questionnaire->id)
            ->first();
        if ($questionnaireActive) {
            $activeQuestionnaireDates = $this->activeQuestionnaireDates($questionnaireActive->start_at, $questionnaireActive->end_at, $start_at, $end_at);
            if (count($activeQuestionnaireDates))
                $questionnaireActive->update($activeQuestionnaireDates);
        }
        try {
            return DB::transaction(function () use ($questionnaire, $request, $start_at, $end_at) {
                /** @var Questionnaire $questionnaire */
                $questionnaire->update([
                    'title' => $request->title,
                    'desc' => $request->desc,
                    'start_at' => $start_at,
                    'end_at' => $end_at,
                ]);
                if ($request->filled('questions')) {
                    $delete_ids = $questionnaire->questions->pluck('id')->filter(function ($value) use ($request) {
                        return !array_key_exists($value, $request->questions);
                    });
                    if ($delete_ids && $delete_ids->count()) {
                        $questionnaire->questions()->whereIn('id', $delete_ids->toArray())->delete();
                    }
                    $questions = collect($request->questions)->filter(function ($value, $key) use ($delete_ids) {
                        return !in_array($key, $delete_ids->toArray());
                    })->toArray();
                    foreach ($questions as $i => $question) {
                        $questionItem = $questionnaire->questions()->updateOrCreate([
                            'id' => $i,
                        ], [
                            'question_text' => $question['value'],
                            'question_type' => 'text',
                        ]);
                        if (array_key_exists('option', $question) && is_array($question['option']) && count($question['option'])) {
                            $delete_option_ids = $questionItem->options()->pluck('id')->filter(function ($value) use ($question) {
                                return !array_key_exists($value, $question['option']);
                            });
                            if ($delete_option_ids && $delete_option_ids->count()) {
                                $questionItem->options()->whereIn('id', $delete_option_ids->toArray())->delete();
                            }
                            $questionOptions = collect($question['option'])->filter(function ($value, $key) use ($delete_option_ids) {
                                return !in_array($key, $delete_option_ids->toArray());
                            })->toArray();
                            foreach ($questionOptions as $k => $questionOption) {
                                $questionItem->options()->updateOrCreate([
                                    'id' => $k,
                                ], [
                                    'value' => $questionOption,
                                ]);
                            }
                        } else {
                            $questionItem->options()->delete();
                        }
                    }
                } else {
                    $questionnaire->questions()->delete();
                }
                return redirect()->route('admin.ecommerce.questionnaires.index')->with('success', "Questionario aggiunto con successo!");
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string'],
            'desc' => ['required', 'string'],
            'start_at' => ['required', 'date', 'after:today'],
            'end_at' => ['required', 'date', 'after:' . $request->start_at],
            'questions' => ['nullable', 'array'],
            'questions.*.value' => ['required', 'string'],
            'questions.*.option' => ['required', 'array'],
            'questions.*.option.*' => ['required', 'string'],
        ]);
        $start_at = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
        $end_at = Carbon::createFromFormat('Y-m-d', $request->end_at)->startOfDay();
        $questionnaireActive = Questionnaire::query()
            ->active()
            ->where(function ($q) use ($start_at, $end_at) {
                $q->whereBetween('start_at', [$start_at, $end_at])
                    ->orWhereBetween('end_at', [$start_at, $end_at]);
            })
            ->first();
        if ($questionnaireActive) {
            $activeQuestionnaireDates = $this->activeQuestionnaireDates($questionnaireActive->start_at, $questionnaireActive->end_at, $start_at, $end_at);
            if (count($activeQuestionnaireDates))
                $questionnaireActive->update($activeQuestionnaireDates);
        }
        try {
            return DB::transaction(function () use ($questionnaireActive, $request, $start_at, $end_at) {
                /** @var Questionnaire $questionnaire */
                $questionnaire = Questionnaire::query()->create([
                    'title' => $request->title,
                    'desc' => $request->desc,
                    'start_at' => $start_at,
                    'end_at' => $end_at,
                ]);
                if ($request->filled('questions')) {
                    foreach ($request->questions as $question) {
                        $q = $questionnaire->questions()->create([
                            'question_text' => $question['value'],
                            'question_type' => 'text',
                        ]);
                        if (array_key_exists('option', $question) && is_array($question['option']) && count($question['option'])) {
                            $q->options()->createMany(array_map(function ($item) {
                                return [
                                    'value' => $item,
                                ];
                            }, $question['option']));
                        }
                    }
                }
                return redirect()->route('admin.ecommerce.questionnaires.index')->with('success', "Questionario aggiunto con successo!");
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function getActiveElements($arr)
    {
        $result = [];
        $continue = true;
        foreach ($arr as $key => $item) {
            if ($continue) {
                $result[] = $item;
                next($arr);
                $continue = $key + 1 == key($arr);
            }
        }
        return collect($result);
    }

}
