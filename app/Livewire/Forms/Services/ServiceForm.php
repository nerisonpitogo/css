<?php

namespace App\Livewire\Forms\Services;

use App\Models\Service;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ServiceForm extends Form
{
    public ?Service $service;

    #[Validate('required')]
    public $service_name = '';

    #[Validate('nullable', 'min:10240')]
    public $photo;

    public function setService(Service $service)
    {
        $this->service = $service;
        $this->service_name = $service->service_name;
        $this->photo = $service->photo;
    }

    public function store()
    {
        $this->validate();

        $data = ['service_name' => $this->service_name];

        if ($this->photo) {
            $this->photo->store('public/photos');
            $data['photo'] = $this->photo->hashName();
        }

        Service::create($data);
    }

    public function update()
    {
        $this->validate();

        $data = ['service_name' => $this->service_name];

        if ($this->photo) {
            $this->photo->store('public/photos');
            $data['photo'] = $this->photo->hashName();
        }

        $this->service->update($data);
    }
}
