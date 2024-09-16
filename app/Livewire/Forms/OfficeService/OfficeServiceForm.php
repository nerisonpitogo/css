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

    #[Validate('nullable|boolean')]
    public $is_simple = false;

    #[Validate('nullable|boolean')]
    public $has_sqd0 = true;

    #[Validate('nullable|boolean')]
    public $has_sqd1 = true;

    #[Validate('nullable|boolean')]
    public $has_sqd2 = true;

    #[Validate('nullable|boolean')]
    public $has_sqd3 = true;

    #[Validate('nullable|boolean')]
    public $has_sqd4 = true;

    #[Validate('nullable|boolean')]
    public $has_sqd5 = false;

    #[Validate('nullable|boolean')]
    public $has_sqd6 = true;

    #[Validate('nullable|boolean')]
    public $has_sqd7 = true;

    #[Validate('nullable|boolean')]
    public $has_sqd8 = true;

    #[Validate('nullable|boolean')]
    public $allow_na = true;

    #[Validate('nullable|boolean')]
    public $is_external = false;

    #[Validate('nullable|boolean')]
    public $is_internal = false;


    public function setOfficeService($officeservice)
    {
        $this->officeservice = $officeservice;
        $this->service_id = $officeservice->service_id;
        $this->is_simple = $officeservice->is_simple ? true : false;

        $this->is_internal = $officeservice->is_internal ? true : false;
        $this->is_external = $officeservice->is_external ? true : false;
        $this->has_sqd0 = $officeservice->has_sqd0 ? true : false;
        $this->has_sqd1 = $officeservice->has_sqd1 ? true : false;
        $this->has_sqd2 = $officeservice->has_sqd2 ? true : false;
        $this->has_sqd3 = $officeservice->has_sqd3 ? true : false;
        $this->has_sqd4 = $officeservice->has_sqd4 ? true : false;
        $this->has_sqd5 = $officeservice->has_sqd5 ? true : false;
        $this->has_sqd6 = $officeservice->has_sqd6 ? true : false;
        $this->has_sqd7 = $officeservice->has_sqd7 ? true : false;
        $this->has_sqd8 = $officeservice->has_sqd8 ? true : false;
        $this->allow_na = $officeservice->allow_na ? true : false;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'office_id' => $this->office_id,
            'service_id' => $this->service_id,
            'is_simple' => $this->is_simple,
            'is_internal' => $this->is_internal,
            'is_external' => $this->is_external,
            'has_sqd0' => $this->has_sqd0,
            'has_sqd1' => $this->has_sqd1,
            'has_sqd2' => $this->has_sqd2,
            'has_sqd3' => $this->has_sqd3,
            'has_sqd4' => $this->has_sqd4,
            'has_sqd5' => $this->has_sqd5,
            'has_sqd6' => $this->has_sqd6,
            'has_sqd7' => $this->has_sqd7,
            'has_sqd8' => $this->has_sqd8,
            'allow_na' => $this->allow_na,

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
            'is_simple' => $this->is_simple,
            'is_internal' => $this->is_internal,
            'is_external' => $this->is_external,
            'has_sqd0' => $this->has_sqd0,
            'has_sqd1' => $this->has_sqd1,
            'has_sqd2' => $this->has_sqd2,
            'has_sqd3' => $this->has_sqd3,
            'has_sqd4' => $this->has_sqd4,
            'has_sqd5' => $this->has_sqd5,
            'has_sqd6' => $this->has_sqd6,
            'has_sqd7' => $this->has_sqd7,
            'has_sqd8' => $this->has_sqd8,
            'allow_na' => $this->allow_na,
            'updated_by' => Auth::id(),
        ];



        $this->officeservice->update($data);
    }
}
