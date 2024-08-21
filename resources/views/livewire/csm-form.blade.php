<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\Office;
use App\Models\Sqd\sqd;

new #[Layout('components.layouts.form')] #[Title('CSM')] class extends Component {
    public int $step = 2;
    public string $language = 'english';

    public $errorFields = [];

    public $clientType = 'citizen';
    public $clientSex = 'male';
    public $clientAge = '40';
    public $clientRegion = 'Caraga';

    // STEP2
    public $serViceAvailedOffice = '';
    public $serViceAvailed = '';

    public $hasErrorClientType = false;
    public $hasErrorSex = false;
    public $hasErrorAge = false;
    public $hasErrorRegion = false;
    public $hasErrorServiceAvailed = false;
    public $errorMessage = '';

    public $type, $with_sub, $office_id;

    public $servicesArrayByOffice = [];
    public $servicesArrayAll = [];

    public function mount($type, $with_sub, $office_id)
    {
        $this->type = $type; //1 for onsite 0 for online
        $this->with_sub = $with_sub;
        $this->office_id = $office_id;
    }

    public function with(): array
    {
        $sqds = Sqd::where(['is_onsite' => $this->type])->get();
        $sqd_language = [];

        foreach ($sqds as $sqd) {
            $sqd_language[$sqd->language] = $sqd;
        }

        // for the services
        // Offices
        $offices = Office::orderBy('name')
            ->with('allChildren')
            ->where('id', $this->office_id)
            ->get();

        // services

        $services_array = [];
        foreach ($offices as $office) {
            $services_array = $this->getServices($office, $services_array);
        }

        $services_array_by_office = [];
        foreach ($offices as $office) {
            $services_array_by_office = $this->getServicesByOffice($office, $services_array_by_office);
        }

        $this->servicesArrayAll = $services_array;
        $this->servicesArrayByOffice = $services_array_by_office;

        return [
            'sqd_language' => $sqd_language,
            'offices' => $offices,
        ];
    }

    private function getServicesByOffice(Office $office, $services_array = [])
    {
        // Initialize the array for the current office if not already set
        // if (!isset($services_array[$office->id])) {
        //     $services_array[$office->id] = [];
        // }

        // Add services of the current office
        foreach ($office->services as $service) {
            $services_array[$office->id][] = [$service->id, $service->service->service_name];
        }

        // Recursively add services of the child offices
        if ($office->children && $office->children->isNotEmpty()) {
            foreach ($office->children as $child) {
                $services_array = $this->getServicesByOffice($child, $services_array);
            }
        }

        return $services_array;
    }

    private function getServices(Office $office, $services_array = [])
    {
        foreach ($office->services as $service) {
            $services_array[] = [$service->id, $service->service->service_name];
        }

        // Recursively add services of the child offices
        if ($office->children && $office->children->isNotEmpty()) {
            foreach ($office->children as $child) {
                $services_array = $this->getServices($child, $services_array);
            }
        }

        return $services_array;
    }

    public function next()
    {
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prev()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function changeLanguage($language)
    {
        $this->language = $language;
    }
};
?>

<div x-data="{
    step: @entangle('step'),
    language: @entangle('language'),
    sqd_language: @js($sqd_language),
    clientType: @entangle('clientType'),
    clientSex: @entangle('clientSex'),
    clientAge: @entangle('clientAge'),
    clientRegion: @entangle('clientRegion'),
    serViceAvailedOffice: @entangle('serViceAvailedOffice'),
    serViceAvailed: @entangle('serViceAvailed'),

    errorFields: @entangle('errorFields'),
    hasErrorClientType: @entangle('hasErrorClientType'),
    hasErrorSex: @entangle('hasErrorSex'),
    hasErrorAge: @entangle('hasErrorAge'),
    hasErrorRegion: @entangle('hasErrorRegion'),

    //step2
    hasErrorServiceAvailed: @entangle('hasErrorServiceAvailed'),

    errorMessage: @entangle('errorMessage'),
    servicesArrayAll: @entangle('servicesArrayAll'),
    servicesArrayByOffice: @entangle('servicesArrayByOffice'),

    handleCLickService(serviceId) {
        this.serViceAvailed = serviceId;
        this.hasErrorServiceAvailed = false;
        this.errorFields = this.errorFields.filter(field => field !== 'Service Availed');
    },


    handleCLickOffice(officeId) {
        this.servicesArrayAll = [];
        this.servicesArrayAll = this.servicesArrayByOffice[officeId];
        this.serViceAvailedOffice = officeId;
    },


    handleRegionChange(event) {
        let input = event.target.value;

        if (input.length < 1) {
            this.hasErrorRegion = true;
            if (!this.errorFields.includes('Region')) {
                this.errorFields.push('Region');
            }
        } else {
            this.clientRegion = input;
            this.hasErrorRegion = false;
            this.errorFields = this.errorFields.filter(field => field !== 'Region');
        }

        event.target.value = input; // Update the input field value
    },

    handleAgeChange(event) {
        let input = event.target.value;
        if (input > 100) {
            input = 40; // Limit input to 100
        }
        this.clientAge = parseInt(input, 10);

        if (this.clientAge < 10 || this.clientAge > 100 || isNaN(this.clientAge)) {
            this.hasErrorAge = true;
            if (!this.errorFields.includes('Age')) {
                this.errorFields.push('Age');
            }
        } else {
            this.hasErrorAge = false;
            this.errorFields = this.errorFields.filter(field => field !== 'Age');
        }
        event.target.value = input; // Update the input field value
    },

    handleClientTypeClick(clientType) {
        this.clientType = clientType;
        this.hasErrorClientType = false;
        this.errorFields = this.errorFields.filter(field => field !== 'Client Type');
    },

    handleClientSexClick(clientSex) {
        this.clientSex = clientSex;
        this.hasErrorSex = false;
        this.errorFields = this.errorFields.filter(field => field !== 'Sex');
    },

    handleNextClick() {
        if (this.step === 1) {
            if (!this.clientType) {
                this.hasErrorClientType = true;
                if (!this.errorFields.includes('Client Type')) {
                    this.errorFields.push('Client Type');
                }
            }
            if (!this.clientSex) {
                this.hasErrorSex = true;
                if (!this.errorFields.includes('Sex')) {
                    this.errorFields.push('Sex');
                }
            }
            if (!this.clientAge || this.clientAge < 10 || this.clientAge > 100) {
                this.hasErrorAge = true;
                if (!this.errorFields.includes('Age')) {
                    this.errorFields.push('Age');
                }
            }
            if (!this.clientRegion) {
                this.hasErrorRegion = true;
                if (!this.errorFields.includes('Region')) {
                    this.errorFields.push('Region');
                }
            }

            if (this.errorFields.length > 0) {
                this.errorMessage = `Please provide ${this.errorFields.join(', ')}`;
                return;
            } else {
                this.errorMessage = '';
                this.errorFields = [];
                this.hasErrorClientType = false;
                this.hasErrorSex = false;
                this.hasErrorAge = false;
                this.step++;
            }



        } else {
            this.step++;
        }
    },

    handlePreviousClick() {
        this.step--;
    }



}" x-cloak>

    <div class="flex items-center justify-center w-full max-w-full overflow-x-auto">
        <x-mary-steps wire:model="step" steps-color="step-primary">
            <x-mary-step step="1" text="" />
            <x-mary-step step="2" text="" />
            <x-mary-step step="3" text="" />
            <x-mary-step step="4" text="" />
            <x-mary-step step="5" text="" />
            <x-mary-step step="6" text="" />
            <x-mary-step step="7" text="" />
            <x-mary-step step="8" text="" data-content="✓" step-classes="!step-success" />
        </x-mary-steps>
    </div>

    {{-- STEP 1 --}}
    <div id="STEP1">
        <div x-show="step === 1" class="flex flex-col items-center justify-center">
            <div class="flex flex-col items-center justify-center mt-6 mb-4">
                <span class="mb-2 text-lg font-semibold">Select Language</span>
                <div class="flex space-x-2">
                    <button @click="language = 'english'" :class="{ 'btn-primary': language === 'english' }"
                        class="btn btn-sm">English</button>
                    <button @click="language = 'tagalog'" :class="{ 'btn-primary': language === 'tagalog' }"
                        class="btn btn-sm">Tagalog</button>
                    <button @click="language = 'bisaya'" :class="{ 'btn-primary': language === 'bisaya' }"
                        class="btn btn-sm">Bisaya</button>
                </div>
            </div>
            <div class="flex w-full lg:w-1/2">
                <template x-if="sqd_language[language]">
                    <span x-text="sqd_language[language].header"></span>
                </template>
            </div>
            {{-- CLIENT TYPE --}}
            <span class="mt-4 mb-2 text-lg font-semibold" :class="{ 'text-error': hasErrorClientType }"
                x-text="sqd_language[language].client_type"></span>
            <button
                :class="{
                    'btn-primary': clientType === 'citizen',
                    'btn-error': clientType === null && hasErrorClientType === true,
                    'mb-2 btn w-full lg:w-1/2': true
                }"
                @click="handleClientTypeClick('citizen')">
                <span x-text="sqd_language[language].citizen"></span>
            </button>
            <button
                :class="{
                    'btn-primary': clientType === 'business',
                    'btn-error': clientType === null && hasErrorClientType === true,
                    'mb-2 btn w-full lg:w-1/2': true
                }"
                @click="handleClientTypeClick('business')">
                <span x-text="sqd_language[language].business"></span>
            </button>
            <button
                :class="{
                    'btn-primary': clientType === 'government',
                    'btn-error': clientType === null && hasErrorClientType === true,
                    'mb-2 btn w-full lg:w-1/2': true
                }"
                @click="handleClientTypeClick('government')">
                <span x-text="sqd_language[language].government"></span>
            </button>
            {{-- END CLIENT TYPE --}}
            {{-- SEX --}}
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" :class="{ 'text-error': hasErrorSex }"
                    x-text="sqd_language[language].sex"></span>
            </div>
            <button
                :class="{
                    'btn-primary': clientSex === 'male',
                    'btn-error': clientSex === null && hasErrorSex === true,
                    'mb-2 btn w-full lg:w-1/2': true
                }"
                @click="handleClientSexClick('male')">
                <span x-text="sqd_language[language].male"></span>
            </button>
            <button
                :class="{
                    'btn-primary': clientSex === 'female',
                    'btn-error': clientSex === null && hasErrorSex === true,
                    'mb-2 btn w-full lg:w-1/2': true
                }"
                @click="handleClientSexClick('female')">
                <span x-text="sqd_language[language].female"></span>
            </button>
            {{-- END SEX --}}
            {{-- AGE --}}
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" x-text="sqd_language[language].age"
                    :class="{ 'text-error': hasErrorAge }"></span>
            </div>
            <input @keyup="handleAgeChange" type="number" min="10" max="100" placeholder="How old are you?"
                class="w-full text-xl text-center lg:w-1/2 input input-bordered input-xl"
                :class="{ 'input-error': hasErrorAge }" />
            {{-- END AGE --}}
            {{-- REGION --}}
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" x-text="sqd_language[language].region"
                    :class="{ 'text-error': hasErrorRegion }"></span>
            </div>
            <input @keyup="handleRegionChange" type="text" placeholder="Region or your Address"
                class="w-full text-xl text-center lg:w-1/2 input input-bordered input-xl"
                :class="{ 'input-error': hasErrorRegion }" />
            {{-- END REGION --}}
        </div>
    </div>
    {{-- END STEP 1 --}}

    {{-- STEP 2 --}}
    <div class="flex items-center justify-center ">
        <div x-show="step === 2" class="flex flex-col max-w-3xl">
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" :class="{ 'text-error': hasErrorServiceAvailed }"
                    x-text="sqd_language[language].service_availed"></span>
            </div>

            <div class="grid grid-cols-2">
                <div class="overflow-y-auto col max-h-96">
                    <x-mary-card title="" subtitle="Select Office you have transacted." shadow separator>
                        @foreach ($offices as $office)
                            <livewire:form.form-office-list :office="$office" :key="$office->id" />
                        @endforeach
                    </x-mary-card>
                </div>

                <div class="overflow-y-auto col max-h-96">
                    <x-mary-card title="" subtitle="Service you have availed." shadow separator>
                        <template x-for="service in servicesArrayAll">
                            <button @click="handleCLickService(service[0])"
                                class="items-start justify-start w-full h-auto p-2 mt-2 text-left btn btn-sm"
                                :class="{
                                    'btn-primary ': serViceAvailed === service[0],
                                    'btn-primary btn-outline': serViceAvailed !== service[0],
                                    'btn w-full': true
                                }">
                                <span x-text="service[0] + ' ' + service[1]"></span>
                            </button>
                        </template>
                    </x-mary-card>
                </div>
            </div>
        </div>
    </div>
    {{-- END STEP 2 --}}


    <hr class="my-5" />

    <div class="flex flex-col items-center justify-center gap-1">
        <div class="flex">
            <span x-show="errorMessage" class="mt-4 mb-2 text-lg font-semibold text-center text-error"
                x-text="errorMessage"></span>
        </div>
        <div class="flex">
            <x-mary-button class="mr-2 btn btn-primary" x-show="step > 1" @click="handlePreviousClick()">
                <span x-text="sqd_language[language].previous"></span>
            </x-mary-button>
            <x-mary-button class="btn btn-primary" x-show="step < 8" @click="handleNextClick()">
                <span x-text="sqd_language[language].next"></span>
            </x-mary-button>
        </div>
    </div>
</div>

{{-- CODE VERY SHORT --}}
