<?php

namespace App\Livewire\Forms\LibService;

use App\Models\LibService\LibService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LibServiceForm extends Form
{

    public ?LibService $libservice;

    #[Validate('required|min:3')]

    public $service_name;
    #[Validate('nullable')]

    public $service_description;


    public function setLibService($libservice)
    {
        $this->libservice = $libservice;
        $this->service_name = $libservice->service_name;
        $this->service_description = $libservice->service_description;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'service_name' => $this->service_name,
            'service_description' => $this->service_description,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];


        LibService::create($data);
    }

    public function update()
    {
        $this->validate();

        $data = [
            'service_name' => $this->service_name,
            'service_description' => $this->service_description,
            'updated_by' => Auth::id(),
        ];



        $this->libservice->update($data);
    }
}
