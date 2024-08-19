<?php

namespace App\Livewire\Forms\OfficeService;

use App\Models\OfficeService\OfficeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class OfficeServiceForm extends Form
{

    public ?OfficeService $officeservice;


    public $office_id;

    #[Validate('required|numeric')]
    public $service_id;

    #[Validate('required|numeric')]
    public $has_cc = 1;


    public function setOfficeService($officeservice)
    {
        $this->officeservice = $officeservice;
        $this->service_id = $officeservice->service_id;
        $this->has_cc = $officeservice->has_cc;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'office_id' => $this->office_id,
            'service_id' => $this->service_id,
            'has_cc' => $this->has_cc,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];


        OfficeService::create($data);
    }

    public function update()
    {
        $this->validate();

        $data = [
            'office_id' => $this->office_id,
            'service_id' => $this->service_id,
            'has_cc' => $this->has_cc,
            'updated_by' => Auth::id(),
        ];



        $this->officeservice->update($data);
    }
}
