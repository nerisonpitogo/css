<?php

use Livewire\Volt\Component;

new class extends Component {
    public $selType;
    public $dateFrom;
    public $dateTo;
    public $officeDropdowns = [];
    public $selectedOffices = [];
    public $includeSubOffice;

    public function mount()
    {
        // from the query param
        $this->selType = request()->query('selType');
        $this->dateFrom = request()->query('dateFrom');
        $this->dateTo = request()->query('dateTo');
        $this->includeSubOffice = request()->query('includeSubOffice');
    }
}; ?>

<div x-cloak>
    <div class="w-full card bg-base-100">

        <div class="card-body">
            wew
        </div>
    </div>
</div>
