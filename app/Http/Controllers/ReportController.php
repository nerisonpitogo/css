<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Services\FeedbackService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;



class ReportController extends Controller
{
    public $selType;
    public $dateFrom;
    public $dateTo;
    public $officeDropdowns = [];
    public $selectedOffices = [];
    public $includeSubOffice;



    // Constructor
    public function __construct(Request $request)
    {
        $this->dateFrom = $request->query('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->query('dateTo', date('Y-m-d'));
        $this->selType = $request->query('selType', 'daily');
        $this->includeSubOffice = $request->query('includeSubOffice', false);
        $this->selectedOffices = $request->query('selectedOffices', []);
    }

    public function generate_report()
    {
        // reports.report
        $data = [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'selType' => $this->selType,
            'includeSubOffice' => $this->includeSubOffice,
            'selectedOffices' => $this->selectedOffices,
        ];
        $lastOffice = end($this->selectedOffices);

        // $images = get_images($lastOffice);

        // Format the dates
        $dateFromFormatted = Carbon::parse($this->dateFrom)->format('F d, Y');
        $dateToFormatted = Carbon::parse($this->dateTo)->format('F d, Y');

        // Determine the date covered string
        if ($this->dateFrom === $this->dateTo) {
            $date_covered = $dateFromFormatted;
        } else {
            $date_covered = $dateFromFormatted . ' to ' . $dateToFormatted;
        }

        $data['date_covered'] = $date_covered;

        // Loop through the office and then to the children
        $currentOffice = Office::find($lastOffice);

        // Initialize view content
        $viewContent = "";

        // Generate reports for the current office and its sub-offices
        $this->generate_reports_for_office($currentOffice, $viewContent, $data, $lastOffice);

        // Generate the PDF from the accumulated HTML content
        // $pdf = Pdf::loadHTML($viewContent)
        //     ->setPaper('a4', 'portrait')
        //     ->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadHTML($viewContent)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);


        return $pdf->stream('report.pdf');
    }

    private function generate_reports_for_office($office, &$viewContent, $data, $lastOffice)
    {
        $feedbackService = new FeedbackService();

        $images = get_images($lastOffice);

        if ($office->services->count() > 0) {
            $include_sub_office = 0;
            $data2 = $this->generateReportData($feedbackService, $data, $office, $include_sub_office);

            $viewContent .= view('reports.report', compact('images', 'data', 'office', 'include_sub_office', 'data2'))->render();
        }

        // if $office->children is not empty
        if ($office->children->count() > 0) {
            $include_sub_office = 1;
            $data2 = $this->generateReportData($feedbackService, $data, $office, $include_sub_office);

            $viewContent .= view('reports.report', compact('images', 'data', 'office', 'include_sub_office', 'data2'))->render();
        }



        // Recursively generate reports for sub-offices
        foreach ($office->children as $child) {

            $this->generate_reports_for_office($child, $viewContent, $data, $child->id);
        }
    }

    private function generateReportData($feedbackService, $data, $office, $include_sub_office)
    {
        $data2 = [];

        $data2['total_responses'] = $feedbackService->get_total_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
        $data2['cc1_awareness_total'] = $feedbackService->get_cc1_awareness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
        $data2['cc2_visibility_total'] = $feedbackService->get_cc2_visibility_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
        $data2['cc3_helpfulness_total'] = $feedbackService->get_cc3_helpfulness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);

        // SQD0
        $sqd0s = $feedbackService->get_sqd_all_grouped_by_answer($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd0');
        $sqd0_array = [['rating' => 6, 'count' => 0], ['rating' => 5, 'count' => 0], ['rating' => 4, 'count' => 0], ['rating' => 3, 'count' => 0], ['rating' => 2, 'count' => 0], ['rating' => 1, 'count' => 0]];
        foreach ($sqd0s as $sqd) {
            foreach ($sqd0_array as &$item) {
                if ($item['rating'] == $sqd->sqd0) {
                    $item['count'] = $sqd->count;
                    break;
                }
            }
        }
        $data2['final_rating'] = $feedbackService->get_score($sqd0_array[1]['count'], $sqd0_array[2]['count'], $sqd0s->sum('count'), $sqd0_array[0]['count']);
        $data2['final_rating_word'] = $feedbackService->get_rating_in_words($data2['final_rating']);

        $data2['sqd0_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd0');
        $data2['sqd1_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd1');
        $data2['sqd2_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd2');
        $data2['sqd3_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd3');
        $data2['sqd4_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd4');
        $data2['sqd5_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd5');
        $data2['sqd6_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd6');
        $data2['sqd7_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd7');
        $data2['sqd8_scores'] = $feedbackService->get_sqd_score_overall_with_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office, 'sqd8');

        $data2['overall'] = $feedbackService->get_sqd1_to_sqd8_overall_and_word($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office,);


        $data2['comments'] = $feedbackService->get_response_comments($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);

        $data2['age_brackets_with_percentage'] = $feedbackService->get_age_bracket_with_percentage($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
        $data2['sex_bracket_with_percentage'] = $feedbackService->get_sex_bracket_with_percentage($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
        $data2['region_responses_with_percentage'] = $feedbackService->get_region_responses_with_percentage($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);


        $data2['external_services'] = $feedbackService->get_external_services_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
        $data2['internal_services'] = $feedbackService->get_internal_services_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
        $data2['services_with_no_responses'] = $feedbackService->get_services_with_no_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);



        return $data2;
    }

    // private function generate_reports_for_office($office, &$viewContent, $data, $images)
    // {
    //     $feedbackService = new FeedbackService();

    //     // check first if the office has officeService
    //     if ($office->services->count() > 0) {
    //         $include_sub_office = 0;
    //         $data2 = [];

    //         $data2['total_responses'] = $feedbackService->get_total_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
    //         $data2['cc1_awareness_total'] = $feedbackService->get_cc1_awareness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
    //         $data2['cc2_visibility_total'] = $feedbackService->get_cc2_visibility_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
    //         $data2['cc3_helpfulness_total'] = $feedbackService->get_cc3_helpfulness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);

    //         // SQD0
    //         $sqd0s = $feedbackService->get_sqd_all_grouped_by_answer($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd0');
    //         $sqd0_array = [['rating' => 6, 'count' => 0], ['rating' => 5, 'count' => 0], ['rating' => 4, 'count' => 0], ['rating' => 3, 'count' => 0], ['rating' => 2, 'count' => 0], ['rating' => 1, 'count' => 0]];

    //         foreach ($sqd0s as $sqd) {
    //             foreach ($sqd0_array as &$item) {
    //                 if ($item['rating'] == $sqd->sqd0) {
    //                     $item['count'] = $sqd->count;
    //                     break;
    //                 }
    //             }
    //         }

    //         $data2['final_rating'] = $feedbackService->get_score($sqd0_array[1]['count'], $sqd0_array[2]['count'], $sqd0s->sum('count'), $sqd0_array[0]['count']);
    //         $data2['final_rating_word'] = $feedbackService->get_rating_in_words($data2['final_rating']);



    //         $viewContent .= view('reports.report', compact('images', 'data', 'office', 'include_sub_office', 'data2'))->render();
    //     }
    //     //if $office->children is not empty
    //     if ($office->children->count() > 0) {

    //         $include_sub_office = 1;
    //         $data2 = [];

    //         $data2['total_responses'] = $feedbackService->get_total_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
    //         $data2['cc1_awareness_total'] = $feedbackService->get_cc1_awareness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
    //         $data2['cc2_visibility_total'] = $feedbackService->get_cc2_visibility_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
    //         $data2['cc3_helpfulness_total'] = $feedbackService->get_cc3_helpfulness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);


    //         // SQD0
    //         $sqd0s = $feedbackService->get_sqd_all_grouped_by_answer($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice, 'sqd0');
    //         $sqd0_array = [['rating' => 6, 'count' => 0], ['rating' => 5, 'count' => 0], ['rating' => 4, 'count' => 0], ['rating' => 3, 'count' => 0], ['rating' => 2, 'count' => 0], ['rating' => 1, 'count' => 0]];

    //         foreach ($sqd0s as $sqd) {
    //             foreach ($sqd0_array as &$item) {
    //                 if ($item['rating'] == $sqd->sqd0) {
    //                     $item['count'] = $sqd->count;
    //                     break;
    //                 }
    //             }
    //         }

    //         $data2['final_rating'] = $feedbackService->get_score($sqd0_array[1]['count'], $sqd0_array[2]['count'], $sqd0s->sum('count'), $sqd0_array[0]['count']);
    //         $data2['final_rating_word'] = $feedbackService->get_rating_in_words($data2['final_rating']);



    //         $viewContent .= view('reports.report', compact('images', 'data', 'office', 'include_sub_office', 'data2'))->render();
    //     }



    //     // Recursively generate reports for sub-offices
    //     foreach ($office->children as $child) {
    //         $this->generate_reports_for_office($child, $viewContent, $data, $images);
    //     }
    // }



}
