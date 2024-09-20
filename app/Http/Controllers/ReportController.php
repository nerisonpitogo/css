<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

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




        return $this->selType;

        return view('reports.report');
    }
}
