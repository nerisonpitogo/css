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
        $images = get_images($lastOffice);

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
        $this->generate_reports_for_office($currentOffice, $viewContent, $data, $images);

        // Generate the PDF from the accumulated HTML content
        $pdf = PDF::loadHTML($viewContent)
            ->setPaper('a4', 'portrait')
            ->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        return $pdf->stream('report.pdf');
    }

    private function generate_reports_for_office($office, &$viewContent, $data, $images)
    {
        $feedbackService = new FeedbackService();

        // check first if the office has officeService
        if ($office->services->count() > 0) {
            $include_sub_office = 0;
            $data2 = [];

            $data2['total_responses'] = $feedbackService->get_total_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
            $data2['cc1_awareness_total'] = $feedbackService->get_cc1_awareness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
            $data2['cc2_visibility_total'] = $feedbackService->get_cc2_visibility_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
            $data2['cc3_helpfulness_total'] = $feedbackService->get_cc3_helpfulness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);

            $viewContent .= view('reports.report', compact('images', 'data', 'office', 'include_sub_office', 'data2'))->render();
        }
        //if $office->children is not empty
        if ($office->children->count() > 0) {

            $include_sub_office = 1;
            $data2 = [];

            $data2['total_responses'] = $feedbackService->get_total_responses($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
            $data2['cc1_awareness_total'] = $feedbackService->get_cc1_awareness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
            $data2['cc2_visibility_total'] = $feedbackService->get_cc2_visibility_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);
            $data2['cc3_helpfulness_total'] = $feedbackService->get_cc3_helpfulness_total($data['dateFrom'], $data['dateTo'], $office->id, $include_sub_office);

            $viewContent .= view('reports.report', compact('images', 'data', 'office', 'include_sub_office', 'data2'))->render();
        }



        // Recursively generate reports for sub-offices
        foreach ($office->children as $child) {
            $this->generate_reports_for_office($child, $viewContent, $data, $images);
        }
    }
}
