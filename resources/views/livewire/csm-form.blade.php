<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\Office;
use App\Models\Sqd\sqd;

new #[Layout('components.layouts.form')] #[Title('CSM')] class extends Component {
    public int $step = 4;
    public string $language = 'english';

    public $errorFields = [];

    // STEP1
    public $clientType = 'citizen';
    public $clientSex = 'male';
    public $clientAge = '40';
    public $clientRegion = 'Caraga';

    public $hasErrorClientType = false;
    public $hasErrorSex = false;
    public $hasErrorAge = false;
    public $hasErrorRegion = false;

    // STEP2
    public $serViceAvailedOffice = '';
    public $serViceAvailed;

    public $hasErrorServiceAvailed = false;

    // STEP3
    public $cc1;
    public $cc1_hasError = false;
    public $cc2;
    public $cc2_hasError = false;
    public $cc3;
    public $cc3_hasError = false;

    // STEP4
    public $sqd0;
    public $sqd0_hasError = false;
    public $sqd1;
    public $sqd1_hasError = false;
    public $sqd2;
    public $sqd2_hasError = false;
    public $sqd3;
    public $sqd3_hasError = false;
    public $sqd4;
    public $sqd4_hasError = false;
    public $sqd5;
    public $sqd5_hasError = false;
    public $sqd6;
    public $sqd6_hasError = false;
    public $sqd7;
    public $sqd7_hasError = false;
    public $sqd8;
    public $sqd8_hasError = false;

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

    //STEP 1
    clientType: @entangle('clientType'),
    clientSex: @entangle('clientSex'),
    clientAge: @entangle('clientAge'),
    clientRegion: @entangle('clientRegion'),

    hasErrorClientType: @entangle('hasErrorClientType'),
    hasErrorSex: @entangle('hasErrorSex'),
    hasErrorAge: @entangle('hasErrorAge'),
    hasErrorRegion: @entangle('hasErrorRegion'),

    //STEP2
    serViceAvailedOffice: @entangle('serViceAvailedOffice'),
    serViceAvailed: @entangle('serViceAvailed'),

    hasErrorServiceAvailed: @entangle('hasErrorServiceAvailed'),

    //STEP3
    cc1: @entangle('cc1'),
    cc1_hasError: @entangle('cc1_hasError'),
    cc2: @entangle('cc2'),
    cc2_hasError: @entangle('cc2_hasError'),
    cc3: @entangle('cc3'),
    cc3_hasError: @entangle('cc3_hasError'),

    //STEP4
    sqd0: @entangle('sqd0'),
    sqd0_hasError: @entangle('sqd0_hasError'),
    sqd1: @entangle('sqd1'),
    sqd1_hasError: @entangle('sqd1_hasError'),
    sqd2: @entangle('sqd2'),
    sqd2_hasError: @entangle('sqd2_hasError'),
    sqd3: @entangle('sqd3'),
    sqd3_hasError: @entangle('sqd3_hasError'),
    sqd4: @entangle('sqd4'),
    sqd4_hasError: @entangle('sqd4_hasError'),
    sqd5: @entangle('sqd5'),
    sqd5_hasError: @entangle('sqd5_hasError'),
    sqd6: @entangle('sqd6'),
    sqd6_hasError: @entangle('sqd6_hasError'),
    sqd7: @entangle('sqd7'),
    sqd7_hasError: @entangle('sqd7_hasError'),
    sqd8: @entangle('sqd8'),
    sqd8_hasError: @entangle('sqd8_hasError'),



    errorFields: @entangle('errorFields'),

    errorMessage: @entangle('errorMessage'),
    servicesArrayAll: @entangle('servicesArrayAll'),
    servicesArrayByOffice: @entangle('servicesArrayByOffice'),

    handle_sqd0_click(sqd0) {
        this.sqd0 = sqd0;
        this.sqd0_hasError = false;

        if (this.errorFields.includes('SQD0')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD0');
        }
    },


    handle_cc1_click(cc1) {
        this.cc1 = cc1;
        this.cc1_hasError = false;

        if (cc1 === '4') {
            this.cc2 = null;
            this.cc3 = null;

            //remove errorFields for cc2 and cc3
            if (this.errorFields.includes('CC2')) {
                this.errorFields = this.errorFields.filter(field => field !== 'CC2');
            }
            if (this.errorFields.includes('CC3')) {
                this.errorFields = this.errorFields.filter(field => field !== 'CC3');
            }

        }


    },

    handle_cc2_click(cc2) {
        this.cc2 = cc2;
        this.cc2_hasError = false;

        if (this.errorFields.includes('CC2')) {
            this.errorFields = this.errorFields.filter(field => field !== 'CC2');
        }

    },

    handle_cc3_click(cc3) {
        this.cc3 = cc3;
        this.cc3_hasError = false;

        if (this.errorFields.includes('CC3')) {
            this.errorFields = this.errorFields.filter(field => field !== 'CC3');
        }
    },


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


        } else if (this.step === 2) {
            if (this.serViceAvailed === '' || this.serViceAvailed === null) {
                this.hasErrorServiceAvailed = true;
                if (!this.errorFields.includes('Service Availed')) {
                    this.errorFields.push('Service Availed');
                }
            } else {
                this.hasErrorServiceAvailed = false;
                this.errorFields = this.errorFields.filter(field => field !== 'Service Availed');
            }
        } else if (this.step === 3) {
            if (this.cc1 === null) {
                this.cc1_hasError = true;
                if (!this.errorFields.includes('CC1')) {
                    this.errorFields.push('CC1');
                }
            } else {
                this.cc1_hasError = false;
                this.errorFields = this.errorFields.filter(field => field !== 'CC1');
            }

            if (this.cc1 !== '4') {
                if (this.cc2 === null) {
                    this.cc2_hasError = true;
                    if (!this.errorFields.includes('CC2')) {
                        this.errorFields.push('CC2');
                    }
                } else {
                    this.cc2_hasError = false;
                    this.errorFields = this.errorFields.filter(field => field !== 'cc2');
                }

                if (this.cc2 !== '5') {
                    if (this.cc3 === null) {
                        this.cc3_hasError = true;
                        if (!this.errorFields.includes('CC3')) {
                            this.errorFields.push('CC3');
                        }
                    } else {
                        this.cc3_hasError = false;
                        this.errorFields = this.errorFields.filter(field => field !== 'cc3');
                    }
                }
            }
        } else if (this.step === 4) {
            if (this.sqd0 === null) {
                this.sqd0_hasError = true;
                if (!this.errorFields.includes('SQD0')) {
                    this.errorFields.push('SQD0');
                }
            } else {
                this.sqd0_hasError = false;
                this.errorFields = this.errorFields.filter(field => field !== 'SQD0');
            }

            if (this.sqd0 === '1') {
                if (this.sqd1 === null) {
                    this.sqd1_hasError = true;
                    if (!this.errorFields.includes('SQD1')) {
                        this.errorFields.push('SQD1');
                    }
                } else {
                    this.sqd1_hasError = false;
                    this.errorFields = this.errorFields.filter(field => field !== 'SQD1');
                }
            }

            if (this.sqd0 === '2') {
                if (this.sqd2 === null) {
                    this.sqd2_hasError = true;
                    if (!this.errorFields.includes('SQD2')) {
                        this.errorFields.push('SQD2');
                    }
                } else {
                    this.sqd2_hasError = false;
                    this.errorFields = this.errorFields.filter(field => field !== 'SQD2');
                }
            }

            if (this.sqd0 === '3') {
                if (this.sqd3 === null) {
                    this.sqd3_hasError = true;
                    if (!this.errorFields.includes('SQD3')) {
                        this.errorFields.push('SQD3');
                    }
                } else {
                    this.sqd3_hasError = false;
                    this.errorFields = this.errorFields.filter(field => field !== 'SQD3');
                }
            }

            if (this.sqd0 === '4') {
                if (this.sqd4 === null) {
                    this.sqd4_hasError = true;
                    if (!this.errorFields.includes('SQD4')) {
                        this.errorFields.push('SQD4');
                    }
                } else {
                    this.sqd4_hasError = false;
                    this.errorFields = this.errorFields.filter(field => field !== 'SQD4');
                }
            }

            if (this.sqd0 === '5') {
                if (this.sqd5 === null) {
                    this.sqd5_hasError = true;
                    if (!this.errorFields.includes('SQD5')) {
                        this.errorFields.push('SQD5');
                    }
                } else {
                    this.sqd5_hasError = false;
                    this.errorFields = this.errorFields.filter(field => field !== 'SQD5');
                }
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
            <div class="flex flex-wrap justify-center sm:space-x-2">
                <button
                    :class="{
                        'btn-primary': clientType === 'citizen',
                        'btn-primary btn-outline': clientType !== 'citizen',
                        'btn-error': clientType === null && hasErrorClientType === true,
                        'btn w-36 h-24 flex flex-col items-center justify-center': true
                    }"
                    @click="handleClientTypeClick('citizen')">
                    <x-fas-person class="h-12" />
                    <span x-text="sqd_language[language].citizen"></span>
                </button>
                <button
                    :class="{
                        'btn-primary': clientType === 'business',
                        'btn-primary btn-outline': clientType !== 'business',
                        'btn-error': clientType === null && hasErrorClientType === true,
                        'btn w-36 h-24 flex flex-col items-center justify-center': true
                    }"
                    @click="handleClientTypeClick('business')">
                    <x-mary-icon name="s-briefcase" class="h-10" />
                    <span x-text="sqd_language[language].business"></span>
                </button>
                <button
                    :class="{
                        'btn-primary': clientType === 'government',
                        'btn-primary btn-outline': clientType !== 'government',
                        'btn-error': clientType === null && hasErrorClientType === true,
                        'btn w-36 h-24 flex flex-col items-center justify-center': true
                    }"
                    @click="handleClientTypeClick('government')">
                    <div class="col">
                        <x-mary-icon name="s-building-library" class="h-10" />
                    </div>
                    <div class="col"><span x-text="sqd_language[language].government"></span></div>
                </button>
            </div>
            {{-- END CLIENT TYPE --}}
            {{-- SEX --}}
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" :class="{ 'text-error': hasErrorSex }"
                    x-text="sqd_language[language].sex"></span>
            </div>

            <div class="flex flex-wrap justify-center sm:space-x-2 xs:mt-1">
                <button
                    :class="{
                        'btn-primary': clientSex === 'male',
                        'btn-primary btn-outline': clientSex !== 'male',
                        'btn-error': clientSex === null && hasErrorSex === true,
                        'btn w-36 h-24 flex flex-col items-center justify-center': true
                    }"
                    @click="handleClientSexClick('male')">
                    <x-fas-male class="h-12" />
                    <span x-text="sqd_language[language].male"></span>
                </button>
                <button
                    :class="{
                        'btn-primary': clientSex === 'female',
                        'btn-primary btn-outline': clientSex !== 'female',
                        'btn-error': clientSex === null && hasErrorSex === true,
                        'btn w-36 h-24 flex flex-col items-center justify-center': true
                    }"
                    @click="handleClientSexClick('female')">
                    <x-fas-female class="h-12" />
                    <span x-text="sqd_language[language].female"></span>
                </button>
            </div>
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
        <div x-show="step === 2" class="flex flex-col max-w-4xl">
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" :class="{ 'text-error': hasErrorServiceAvailed }"
                    x-text="sqd_language[language].service_availed"></span>
            </div>

            <div class="grid grid-cols-2">
                <div class="overflow-y-auto col max-h-96">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="text-sm card-title" x-text="sqd_language[language].office_transacted"></h1>
                            @foreach ($offices as $office)
                                <livewire:form.form-office-list :office="$office" :key="$office->id" />
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="overflow-y-auto col max-h-96">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="text-sm card-title" x-text="sqd_language[language].service_availed_header">
                            </h1>
                            <template x-for="service in servicesArrayAll">
                                <button @click="handleCLickService(service[0])"
                                    class="items-center justify-start w-full h-auto p-1 text-left btn btn-sm"
                                    :class="{
                                        'btn-primary ': serViceAvailed === service[0],
                                        'btn-primary btn-outline': serViceAvailed !== service[0],
                                        'btn-error': serViceAvailed === null && hasErrorServiceAvailed === true,
                                        'btn w-full': true
                                    }">
                                    <span x-text="service[1]"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- END STEP 2 --}}

    {{-- STEP3 --}}
    <div class="flex items-center justify-center">
        <div x-show="step === 3" class="flex flex-col max-w-3xl">
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" :class="{ 'text-error': hasErrorServiceAvailed }"
                    x-text="sqd_language[language].cc_instruction"></span>
            </div>

            <h1 class="mt-4 text-xl font-semibold" x-text="sqd_language[language].cc1"></h1>

            <button
                :class="{
                    'btn-primary': cc1 === '1',
                    'btn-primary btn-outline': cc1 !== '1',
                    'btn-error': cc1 === null && cc1_hasError === true,
                    'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                }"
                @click="handle_cc1_click('1')">
                <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                <span x-text="sqd_language[language].cc1_1"></span>
            </button>
            <button
                :class="{
                    'btn-primary': cc1 === '2',
                    'btn-primary btn-outline': cc1 !== '2',
                    'btn-error': cc1 === null && cc1_hasError === true,
                    'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                }"
                @click="handle_cc1_click('2')">
                <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                <span x-text="sqd_language[language].cc1_2"></span>
            </button>
            <button
                :class="{
                    'btn-primary': cc1 === '3',
                    'btn-primary btn-outline': cc1 !== '3',
                    'btn-error': cc1 === null && cc1_hasError === true,
                    'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                }"
                @click="handle_cc1_click('3')">
                <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                <span x-text="sqd_language[language].cc1_3"></span>
            </button>
            <button
                :class="{
                    'btn-primary': cc1 === '4',
                    'btn-primary btn-outline': cc1 !== '4',
                    'btn-error': cc1 === null && cc1_hasError === true,
                    'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                }"
                @click="handle_cc1_click('4')">
                <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                <span x-text="sqd_language[language].cc1_4"></span>
            </button>


            <div x-show="cc1 !== '4'">
                <h1 class="mt-4 text-xl font-semibold" x-text="sqd_language[language].cc2"></h1>
                <button
                    :class="{
                        'btn-primary': cc2 === '1',
                        'btn-primary btn-outline': cc2 !== '1',
                        'btn-error': cc2 === null && cc2_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc2_click('1')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc2_1"></span>
                </button>
                <button
                    :class="{
                        'btn-primary': cc2 === '2',
                        'btn-primary btn-outline': cc2 !== '2',
                        'btn-error': cc2 === null && cc2_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc2_click('2')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc2_2"></span>
                </button>
                <button
                    :class="{
                        'btn-primary': cc2 === '3',
                        'btn-primary btn-outline': cc2 !== '3',
                        'btn-error': cc2 === null && cc2_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc2_click('3')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc2_3"></span>
                </button>
                {{-- 4 --}}
                <button
                    :class="{
                        'btn-primary': cc2 === '4',
                        'btn-primary btn-outline': cc2 !== '4',
                        'btn-error': cc2 === null && cc2_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc2_click('4')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc2_4"></span>
                </button>
                {{-- 5 --}}
                <button
                    :class="{
                        'btn-primary': cc2 === '5',
                        'btn-primary btn-outline': cc2 !== '5',
                        'btn-error': cc2 === null && cc2_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc2_click('5')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc2_5"></span>
                </button>
            </div>

            <div x-show="cc1 !== '4'">
                <h1 class="mt-4 text-xl font-semibold" x-text="sqd_language[language].cc3"></h1>
                <button
                    :class="{
                        'btn-primary': cc3 === '1',
                        'btn-primary btn-outline': cc3 !== '1',
                        'btn-error': cc3 === null && cc3_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc3_click('1')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc3_1"></span>
                </button>
                <button
                    :class="{
                        'btn-primary': cc3 === '2',
                        'btn-primary btn-outline': cc3 !== '2',
                        'btn-error': cc3 === null && cc3_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc3_click('2')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc3_2"></span>
                </button>

                <button
                    :class="{
                        'btn-primary': cc3 === '3',
                        'btn-primary btn-outline': cc3 !== '3',
                        'btn-error': cc3 === null && cc3_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc3_click('3')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc3_3"></span>
                </button>

                <button
                    :class="{
                        'btn-primary': cc3 === '4',
                        'btn-primary btn-outline': cc3 !== '4',
                        'btn-error': cc3 === null && cc3_hasError === true,
                        'btn btn-sm w-full sm:ml-10 mt-1 flex items-center justify-start space-x-2 h-auto sm:h-auto md:h-auto lg:h-auto xl:h-auto': true
                    }"
                    @click="handle_cc3_click('4')">
                    <x-mary-icon name="o-squares-2x2" class="hidden h-4 sm:block" />
                    <span x-text="sqd_language[language].cc3_4"></span>
                </button>



            </div>
        </div>
    </div>
    {{-- END STEP 3 --}}

    {{-- STEP 4 --}}
    <div class="flex items-center justify-center">
        <div x-show="step === 4" class="flex flex-col items-center max-w-3xl">

            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" x-text="sqd_language[language].sqd_instruction"></span>
            </div>

            <h1 class="mt-4 text-xl font-semibold" x-text="sqd_language[language].sqd0"
                :class="{ 'text-error': sqd0_hasError }"></h1>

            <div class="flex mt-4 space-x-1">
                {{-- STRONGLY DISAGREE --}}
                <button class="flex items-center justify-center w-20 h-auto p-1 btn"
                    :class="{
                        'btn-primary': sqd0 === '1',
                        'btn-primary btn-outline': sqd0 !== '1',
                        'btn-error': sqd0 === null && sqd0_hasError === true,
                    }"
                    @click="handle_sqd0_click('1')">
                    <x-far-angry class="w-10 h-10 text-red-700" />
                    <span x-text="sqd_language[language].label_sd" class="text-xs"></span>
                </button>
                {{-- DISAGREE --}}
                <button class="flex items-center justify-center w-20 h-auto p-1 btn"
                    :class="{
                        'btn-primary': sqd0 === '2',
                        'btn-primary btn-outline': sqd0 !== '2',
                        'btn-error': sqd0 === null && sqd0_hasError === true,
                    }"
                    @click="handle_sqd0_click('2')">
                    <x-far-frown class="w-10 h-10 text-red-500" />
                    <span x-text="sqd_language[language].label_d" class="text-xs"></span>
                </button>
                {{-- 3 --}}
                <button class="flex items-center justify-center w-20 h-auto p-1 btn"
                    :class="{
                        'btn-primary': sqd0 === '3',
                        'btn-primary btn-outline': sqd0 !== '3',
                        'btn-error': sqd0 === null && sqd0_hasError === true,
                    }"
                    @click="handle_sqd0_click('3')">
                    <x-far-meh class="w-10 h-10 text-secondary" />
                    <span x-text="sqd_language[language].label_n" class="text-xs"></span>
                </button>
                {{-- 4 --}}
                <button class="flex items-center justify-center w-20 h-auto p-1 btn"
                    :class="{
                        'btn-primary': sqd0 === '4',
                        'btn-primary btn-outline': sqd0 !== '4',
                        'btn-error': sqd0 === null && sqd0_hasError === true,
                    }"
                    @click="handle_sqd0_click('4')">
                    <x-far-smile-beam class="w-10 h-10 text-green-500" />
                    <span x-text="sqd_language[language].label_a" class="text-xs"></span>
                </button>
                {{-- STRONGLY AGREE --}}
                <button class="flex items-center justify-center w-20 h-auto p-1 btn"
                    :class="{
                        'btn-primary': sqd0 === '5',
                        'btn-primary btn-outline': sqd0 !== '5',
                        'btn-error': sqd0 === null && sqd0_hasError === true,
                    }"
                    @click="handle_sqd0_click('5')">
                    <x-far-grin-stars class="w-10 h-10 text-green-700" />
                    <span x-text="sqd_language[language].label_sa" class="text-xs"></span>
                </button>
                {{-- N/A --}}
                <button class="flex items-center justify-center w-20 h-auto p-1 btn"
                    :class="{
                        'btn-primary': sqd0 === '6',
                        'btn-primary btn-outline': sqd0 !== '6',
                        'btn-error': sqd0 === null && sqd0_hasError === true,
                    }"
                    @click="handle_sqd0_click('6')">
                    <x-far-question-circle class="w-10 h-10 text-gray-500" />
                    <span x-text="sqd_language[language].label_na" class="text-xs"></span>
                </button>

            </div>

        </div>
    </div>

    {{-- END STEP4 --}}

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
