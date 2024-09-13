<?php

use Livewire\Volt\Component;
use App\Services\FeedbackService;
use Livewire\Attributes\Reactive;

new class extends Component {
    #[Reactive]
    public $selType;

    #[Reactive]
    public $dateFrom;

    #[Reactive]
    public $dateTo;

    #[Reactive]
    public $includeSubOffice;

    #[Reactive]
    public $selectedOffices = [];

    public function mount($selType, $dateFrom, $dateTo, $includeSubOffice, $selectedOffices)
    {
        $this->selType = $selType;

        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->includeSubOffice = $includeSubOffice;
        $this->selectedOffices = $selectedOffices;
    }

    public function with(FeedbackService $feedbackService)
    {
        $sqd0s = $feedbackService->get_sqd_all_grouped_by_answer($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd0');

        // index 0 = NA
        // index 1 = Strongly Agree
        // index 2 = Agree
        // index 3 = Neutral
        // index 4 = Disagree
        // index 5 = Strongly Disagree
        $sqd0_array = [['rating' => 6, 'count' => 0], ['rating' => 5, 'count' => 0], ['rating' => 4, 'count' => 0], ['rating' => 3, 'count' => 0], ['rating' => 2, 'count' => 0], ['rating' => 1, 'count' => 0]];

        foreach ($sqd0s as $sqd) {
            foreach ($sqd0_array as &$item) {
                if ($item['rating'] == $sqd->sqd0) {
                    $item['count'] = $sqd->count;
                    break;
                }
            }
        }

        $final_rating = $feedbackService->get_score($sqd0_array[1]['count'], $sqd0_array[2]['count'], $sqd0s->sum('count'), $sqd0_array[0]['count']);
        $final_rating_word = $feedbackService->get_rating_in_words($final_rating);

        $sqd1_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd1');
        $sqd2_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd2');
        $sqd3_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd3');
        $sqd4_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd4');
        $sqd5_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd5');
        $sqd6_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd6');
        $sqd7_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd7');
        $sqd8_scores = $feedbackService->get_sqd_score_overall_with_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd8');

        $overall = $feedbackService->get_sqd1_to_sqd8_overall_and_word($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice);

        // dd($overall);

        // dd('TEST');

        $all_sqd_scores = [
            'sqd1' => $sqd1_scores,
            'sqd2' => $sqd2_scores,
            'sqd3' => $sqd3_scores,
            'sqd4' => $sqd4_scores,
            'sqd5' => $sqd5_scores,
            'sqd6' => $sqd6_scores,
            'sqd7' => $sqd7_scores,
            'sqd8' => $sqd8_scores,
        ];

        return [
            'sqd0_array' => $sqd0_array,
            'final_rating' => $final_rating,
            'final_rating_word' => $final_rating_word,
            'all_sqd_scores' => $all_sqd_scores,
            'overall' => $overall,
        ];
    }

    public function placeholder()
    {
        return generate_placeholder(2, 1, 'grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-2', 96);
    }
    //
}; ?>


<div class="mt-2">

    <div wire:loading class="w-full">

        {!! generate_placeholder(2, 1, 'grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-2', 96) !!}
    </div>

    <div wire:loading.remove class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-4">
        <div class="col-span-2 stats text-base-100">
            <div class="p-4 rounded-lg shadow-lg bg-base-100 stat">
                <div class="mb-2 text-lg font-semibold stat-title">Service Satisfaction (SQD0)</div>
                <div class="mb-4 text-3xl font-bold {{ get_percentage_color($final_rating) }} stat-value">

                    {{ $final_rating !== 'N/A' ? number_format($final_rating, 2) . '% [' . $final_rating_word . ']' : 'N/A' }}
                </div>



                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        Strongly Agree
                    </div>
                    <div class="p-2 text-xl font-bold text-center rounded text-base-content bg-base-200">
                        {{ $sqd0_array[1]['count'] }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        Agree
                    </div>
                    <div class="p-2 text-xl font-bold text-center rounded text-base-content bg-base-200">
                        {{ $sqd0_array[2]['count'] }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        Neutral
                    </div>
                    <div class="p-2 text-xl font-bold text-center rounded text-base-content bg-base-200">
                        {{ $sqd0_array[3]['count'] }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        Disagree
                    </div>
                    <div class="p-2 text-xl font-bold text-center rounded text-base-content bg-base-200">
                        {{ $sqd0_array[4]['count'] }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        Strongly Disagree
                    </div>
                    <div class="p-2 text-xl font-bold text-center rounded text-base-content bg-base-200">
                        {{ $sqd0_array[5]['count'] }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        Not Applicable
                    </div>
                    <div class="p-2 text-xl font-bold text-center rounded text-base-content bg-base-200">
                        {{ $sqd0_array[0]['count'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-2 stats text-base-100">
            <div class="p-4 rounded-lg shadow-lg bg-base-100 stat">
                <div class="mb-2 text-lg font-semibold stat-title">Overall Score (SQD1 - SQD8)</div>
                <div class="mb-4 text-3xl font-bold {{ get_percentage_color($overall['overall']) }} stat-value">
                    {{ $overall['overall'] !== 'N/A' ? number_format($overall['overall'], 2) . '% [' . $overall['word'] . ']' : 'N/A' }}
                </div>

                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD1 - Responsiveness
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd1'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd1'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd1'][0], 2) . '% [' . $all_sqd_scores['sqd1'][1] . ']' : 'N/A' }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD2 - Reliability
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd2'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd2'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd2'][0], 2) . '% [' . $all_sqd_scores['sqd2'][1] . ']' : 'N/A' }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD3 - Access and Facilities
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd3'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd3'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd3'][0], 2) . '% [' . $all_sqd_scores['sqd3'][1] . ']' : 'N/A' }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD4 - Communication
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd4'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd4'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd4'][0], 2) . '% [' . $all_sqd_scores['sqd4'][1] . ']' : 'N/A' }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD5 - Costs
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd5'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd5'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd5'][0], 2) . '% [' . $all_sqd_scores['sqd5'][1] . ']' : 'N/A' }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD6 - Integrity
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd6'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd6'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd6'][0], 2) . '% [' . $all_sqd_scores['sqd6'][1] . ']' : 'N/A' }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD7 - Assurance
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd7'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd7'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd7'][0], 2) . '% [' . $all_sqd_scores['sqd7'][1] . ']' : 'N/A' }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div class="p-2 text-lg font-semibold text-center rounded text-base-content bg-base-200">
                        SQD8 - Outcome
                    </div>
                    <div
                        class="p-2 text-xl font-bold text-center rounded {{ get_percentage_color($all_sqd_scores['sqd8'][0]) }} bg-base-200">
                        {{ $all_sqd_scores['sqd8'][0] !== 'N/A' ? number_format($all_sqd_scores['sqd8'][0], 2) . '% [' . $all_sqd_scores['sqd8'][1] . ']' : 'N/A' }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
