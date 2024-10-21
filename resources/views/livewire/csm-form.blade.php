<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\Office;
use App\Models\Sqd\Sqd;
use App\Models\Feedback;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Validator;
use App\Models\OfficeRegion;
use App\Models\LibRegion\LibRegion;
use Illuminate\Support\Facades\Crypt;

new #[Layout('components.layouts.form')] #[Title('CSM')] class extends Component {
    use Toast;

    public int $step = 1;
    public string $language = 'english';

    public $errorFields = [];

    // STEP1
    public $clientType;
    public $clientSex;
    public $clientAge;
    public $clientRegion;
    public $clientRegionText;

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

    // STEP 5
    public $suggestion;
    public $email;
    public $disagree = [];

    // step6
    public $office_transacted_word = '';
    public $service_availed_word = '';
    public $cc1_selected_word = '';
    public $cc2_selected_word = '';
    public $cc3_selected_word = '';
    public $selected_client_type = '';
    public $selected_sex = '';

    // addition
    public $has_sqd0;
    public $has_sqd1;
    public $has_sqd2;
    public $has_sqd3;
    public $has_sqd4;
    public $has_sqd5;
    public $has_sqd6;
    public $has_sqd7;
    public $has_sqd8;
    public $allow_na;

    public $errorMessage = '';

    public $is_onsite, $with_sub, $office_id;

    public $servicesArrayByOffice = [];
    public $servicesArrayAll = [];

    public $office;
    public $is_external = 1;

    public function mount($is_onsite, $with_sub, $is_external, $office_id)
    {
        try {
            $office_id = Crypt::decrypt($office_id);
        } catch (\Throwable $th) {
            abort(404);
        }

        $this->is_onsite = $is_onsite; //1 for onsite 0 for online
        $this->with_sub = $with_sub;
        $this->office_id = $office_id;
        $this->is_external = $is_external;

        $this->office = Office::findOrFail($office_id);

        // initialize
        // $this->sqd0 = 2;
        // $this->sqd1 = 2;
        // $this->sqd2 = 2;
        // $this->sqd3 = 2;
        // $this->sqd4 = 2;
        // $this->sqd5 = 2;
        // $this->sqd6 = 2;
        // $this->sqd7 = 2;
        // $this->sqd8 = 2;
    }

    public function with(): array
    {
        $sqds = Sqd::where(['is_onsite' => $this->is_onsite])->get();
        $sqd_language = [];

        foreach ($sqds as $sqd) {
            $sqd_language[$sqd->language] = $sqd;
        }

        $images = get_images($this->office_id);

        // for the services
        // Offices
        $offices = Office::orderBy('name');
        if ($this->with_sub) {
            // $offices = $offices->with('allChildren');
            $offices = $offices->with('children');
        }
        $offices = $offices->where('id', $this->office_id)->get();

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
            'regions' => $this->getRegions(),
            'images' => $images,
        ];
    }

    private function getRegions()
    {
        $regions = OfficeRegion::where('office_id', $this->office_id)
            ->orderBy('is_priority', 'desc')
            ->get();

        // get the first row and set the region
        if ($regions->isNotEmpty()) {
            $this->clientRegion = $regions->first()->region_id;
            $this->clientRegionText = $regions->first()->region->name;
        }

        $regions_array = [];
        if ($regions->isNotEmpty()) {
            // return an array with id and region_name

            foreach ($regions as $region) {
                // $regions_array[$region->id] = $region->region->region_name;
                // store in id and name
                $regions_array[] = [
                    'id' => $region->region->id,
                    'name' => $region->region->name,
                ];
            }
            return $regions_array;
        } else {
            //get from the lib_regions
            $regions = LibRegion::all();
            foreach ($regions as $region) {
                $regions_array[] = [
                    'id' => $region->id,
                    'name' => $region->name,
                ];
            }
            $this->clientRegion = $regions->first()->id;
            $this->clientRegionText = $regions->first()->name;
            return $regions_array;
        }
    }

    private function getServicesByOffice(Office $office, $services_array = [])
    {
        // Initialize the array for the current office if not already set
        // if (!isset($services_array[$office->id])) {
        //     $services_array[$office->id] = [];
        // }

        // Add services of the current office
        foreach ($office->services as $service) {
            // $services_array[$office->id][] = [$service->id, $service->service->service_name];

            if ($this->is_external == 1 && $service->is_external == 1) {
                $services_array[$office->id][] = [$service->id, $service->service->service_name, $office->name, $service->has_sqd0, $service->has_sqd1, $service->has_sqd2, $service->has_sqd3, $service->has_sqd4, $service->has_sqd5, $service->has_sqd6, $service->has_sqd7, $service->has_sqd8, $service->allow_na];
            }
            // for internal
            if ($this->is_external == 0 && $service->is_internal == 1) {
                $services_array[$office->id][] = [$service->id, $service->service->service_name, $office->name, $service->has_sqd0, $service->has_sqd1, $service->has_sqd2, $service->has_sqd3, $service->has_sqd4, $service->has_sqd5, $service->has_sqd6, $service->has_sqd7, $service->has_sqd8, $service->allow_na];
            }
        }

        // Recursively add services of the child offices
        if ($this->with_sub) {
            if ($office->children && $office->children->isNotEmpty()) {
                foreach ($office->children as $child) {
                    $services_array = $this->getServicesByOffice($child, $services_array);
                }
            }
        }

        return $services_array;
    }

    private function getServices(Office $office, $services_array = [])
    {
        foreach ($office->services as $service) {
            if ($service->service->service_name == 'Others') {
                continue;
            }
            // dd($service->office_id);
            // $services_array[] = [$service->id, $service->service->service_name, $office->name, $service->has_sqd0, $service->has_sqd1, $service->has_sqd2, $service->has_sqd3, $service->has_sqd4, $service->has_sqd5, $service->has_sqd6, $service->has_sqd7, $service->has_sqd8, $service->allow_na];
            // dd($service->is_external);
            if ($this->is_external == 1 && $service->is_external == 1) {
                // dd($service->is_external);
                $services_array[] = [$service->id, $service->service->service_name, $office->name, $service->has_sqd0, $service->has_sqd1, $service->has_sqd2, $service->has_sqd3, $service->has_sqd4, $service->has_sqd5, $service->has_sqd6, $service->has_sqd7, $service->has_sqd8, $service->allow_na];
            }

            // for internal
            if ($this->is_external == 0 && $service->is_internal == 1) {
                $services_array[] = [$service->id, $service->service->service_name, $office->name, $service->has_sqd0, $service->has_sqd1, $service->has_sqd2, $service->has_sqd3, $service->has_sqd4, $service->has_sqd5, $service->has_sqd6, $service->has_sqd7, $service->has_sqd8, $service->allow_na];
            }
            //$services_array[] = [$service->id, $service->service->service_name, $office->name, $service->has_sqd0, $service->has_sqd1, $service->has_sqd2, $service->has_sqd3, $service->has_sqd4, $service->has_sqd5, $service->has_sqd6, $service->has_sqd7, $service->has_sqd8, $service->allow_na];
        }

        // Recursively add services of the child offices
        if ($this->with_sub) {
            if ($office->children && $office->children->isNotEmpty()) {
                foreach ($office->children as $child) {
                    $services_array = $this->getServices($child, $services_array);
                }
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

    public function save_feedback()
    {
        try {
            $this->validate(
                [
                    'clientType' => 'required',
                    'clientSex' => 'required|in:male,female',
                    'clientAge' => 'nullable|integer|min:10|max:120',
                    'clientRegion' => 'required|exists:lib_regions,id',
                    'serViceAvailed' => 'required',
                    'cc1' => 'required',
                    'cc2' => 'nullable',
                    'cc3' => 'nullable',
                    'sqd0' => 'required|between:1,6',
                    'sqd1' => 'required|between:1,6',
                    'sqd2' => 'required|between:1,6',
                    'sqd3' => 'required|between:1,6',
                    'sqd4' => 'required|between:1,6',
                    'sqd5' => 'required|between:1,6',
                    'sqd6' => 'required|between:1,6',
                    'sqd7' => 'required|between:1,6',
                    'sqd8' => 'required|between:1,6',
                    'suggestion' => 'nullable',
                    'email' => 'nullable',
                ],
                $this->messages(),
            );

            // insert OfficeService
            $insert = new Feedback();
            $insert->client_type = $this->clientType;
            $insert->sex = $this->clientSex;
            $insert->age = $this->clientAge;
            $insert->region_id = $this->clientRegion;
            $insert->office_service_id = $this->serViceAvailed;
            $insert->cc1 = $this->cc1;
            $insert->cc2 = $this->cc2;
            $insert->cc3 = $this->cc3;
            $insert->sqd0 = $this->sqd0;
            $insert->sqd1 = $this->sqd1;
            $insert->sqd2 = $this->sqd2;
            $insert->sqd3 = $this->sqd3;
            $insert->sqd4 = $this->sqd4;
            $insert->sqd5 = $this->sqd5;
            $insert->sqd6 = $this->sqd6;
            $insert->sqd7 = $this->sqd7;
            $insert->sqd8 = $this->sqd8;
            $insert->suggestions = $this->suggestion;
            $insert->email = $this->email;
            $insert->is_external = $this->is_external;

            $insert->save();

            // $this->success('Thank you for your feedback!!!');
            $this->toast(
                type: 'success',
                title: 'Thank you for your feedback!',
                description: 'This will help us gain insights to serve you better.', // optional (text)
                position: 'toast-middle toast-center', // optional (daisyUI classes)
                icon: 'o-information-circle', // Optional (any icon)
                css: 'alert-success', // Optional (daisyUI classes)
                timeout: 5000, // optional (ms)
                redirectTo: null, // optional (uri)
            );

            $this->js('playSuccessSound()');

            // reset everything
            $this->reset('step', 'language', 'clientType', 'clientSex', 'clientAge', 'clientRegion', 'clientRegionText', 'serViceAvailedOffice', 'serViceAvailed', 'cc1', 'cc2', 'cc3', 'sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8', 'suggestion', 'email');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation failure
            $this->warning($e->getMessage());
            return;
        }
    }

    protected function messages()
    {
        return [
            'serViceAvailed.required' => 'The service availed field is required.',
        ];
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
    clientRegionText: @entangle('clientRegionText'),

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

    //STEP5
    suggestion: @entangle('suggestion'),
    email: @entangle('email'),
    disagree: @entangle('disagree'),

    //STEP6
    office_transacted_word: @entangle('office_transacted_word'),
    service_availed_word: @entangle('service_availed_word'),
    cc1_selected_word: @entangle('cc1_selected_word'),
    cc2_selected_word: @entangle('cc2_selected_word'),
    cc3_selected_word: @entangle('cc3_selected_word'),
    selected_client_type: @entangle('selected_client_type'),
    selected_sex: @entangle('selected_sex'),


    //addition
    has_sqd0: @entangle('has_sqd0'),
    has_sqd1: @entangle('has_sqd1'),
    has_sqd2: @entangle('has_sqd2'),
    has_sqd3: @entangle('has_sqd3'),
    has_sqd4: @entangle('has_sqd4'),
    has_sqd5: @entangle('has_sqd5'),
    has_sqd6: @entangle('has_sqd6'),
    has_sqd7: @entangle('has_sqd7'),
    has_sqd8: @entangle('has_sqd8'),
    allow_na: @entangle('allow_na'),


    errorFields: @entangle('errorFields'),

    errorMessage: @entangle('errorMessage'),
    servicesArrayAll: @entangle('servicesArrayAll'),
    servicesArrayByOffice: @entangle('servicesArrayByOffice'),
    audio: null,
    audioError: null,
    audioSuccess: null,

    init() {
        this.audio = new Audio('{{ url('/sounds/click2.mp3') }}');
        this.audioError = new Audio('{{ url('/sounds/error.mp3') }}');
        this.audioSuccess = new Audio('{{ url('/sounds/success5.mp3') }}');
    },

    playClickSound() {
        this.audio.currentTime = 1; // Reset the audio to the beginning
        this.audio.play();
        alert('play');
    },

    playErrorSound() {
        this.audioError.currentTime = 1; // Reset the audio to the beginning
        this.audioError.play();
    },
    playSuccessSound() {
        this.audioSuccess.currentTime = 1; // Reset the audio to the beginning
        this.audioSuccess.play();
    },

    handle_sqd0_click(sqd0) {
        this.sqd0 = sqd0;
        this.sqd0_hasError = false;

        if (this.errorFields.includes('SQD0')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD0');
        }
        this.playClickSound();
    },

    handle_sqd1_click(sqd1) {
        this.sqd1 = sqd1;
        this.sqd1_hasError = false;
        if (this.errorFields.includes('SQD1')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD1');
        }
        this.playClickSound();
    },

    handle_sqd2_click(sqd2) {
        this.sqd2 = sqd2;
        this.sqd2_hasError = false;

        if (this.errorFields.includes('SQD2')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD2');
        }
        this.playClickSound();
    },

    handle_sqd3_click(sqd3) {
        this.sqd3 = sqd3;
        this.sqd3_hasError = false;

        if (this.errorFields.includes('SQD3')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD3');
        }
        this.playClickSound();
    },

    handle_sqd4_click(sqd4) {
        this.sqd4 = sqd4;
        this.sqd4_hasError = false;

        if (this.errorFields.includes('SQD4')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD4');
        }
        this.playClickSound();
    },

    handle_sqd5_click(sqd5) {
        this.sqd5 = sqd5;
        this.sqd5_hasError = false;

        if (this.errorFields.includes('SQD5')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD5');
        }
        this.playClickSound();
    },

    handle_sqd6_click(sqd6) {
        this.sqd6 = sqd6;
        this.sqd6_hasError = false;

        if (this.errorFields.includes('SQD6')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD6');
        }
        this.playClickSound();
    },

    handle_sqd7_click(sqd7) {
        this.sqd7 = sqd7;
        this.sqd7_hasError = false;

        if (this.errorFields.includes('SQD7')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD7');
        }
        this.playClickSound();
    },

    handle_sqd8_click(sqd8) {
        this.sqd8 = sqd8;
        this.sqd8_hasError = false;

        if (this.errorFields.includes('SQD8')) {
            this.errorFields = this.errorFields.filter(field => field !== 'SQD8');
        }
        this.playClickSound();
    },


    handle_cc1_click(cc1) {
        this.cc1 = cc1;
        this.cc1_hasError = false;
        this.cc1_selected_word = this.sqd_language[this.language]['cc1_' + cc1];

        this.playClickSound();

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
        this.playClickSound();
        this.cc2 = cc2;
        this.cc2_hasError = false;
        this.cc2_selected_word = this.sqd_language[this.language]['cc2_' + cc2];
        if (this.errorFields.includes('CC2')) {
            this.errorFields = this.errorFields.filter(field => field !== 'CC2');
        }

    },

    handle_cc3_click(cc3) {
        this.playClickSound();
        this.cc3 = cc3;
        this.cc3_hasError = false;
        this.cc3_selected_word = this.sqd_language[this.language]['cc3_' + cc3];
        if (this.errorFields.includes('CC3')) {
            this.errorFields = this.errorFields.filter(field => field !== 'CC3');
        }
    },


    handleCLickService(serviceId, serviceName, officeName, has_sqd0, has_sqd1, has_sqd2, has_sqd3, has_sqd4, has_sqd5, has_sqd6, has_sqd7, has_sqd8, allow_na) {
        this.service_availed_word = serviceName;
        this.office_transacted_word = officeName;
        this.serViceAvailed = serviceId;
        this.hasErrorServiceAvailed = false;
        this.errorFields = this.errorFields.filter(field => field !== 'Service Availed');

        this.has_sqd0 = has_sqd0;
        this.has_sqd1 = has_sqd1;
        this.has_sqd2 = has_sqd2;
        this.has_sqd3 = has_sqd3;
        this.has_sqd4 = has_sqd4;
        this.has_sqd5 = has_sqd5;
        this.has_sqd6 = has_sqd6;
        this.has_sqd7 = has_sqd7;
        this.has_sqd8 = has_sqd8;
        this.allow_na = allow_na;

        this.playClickSound();

    },


    handleCLickOffice(officeId) {
        this.servicesArrayAll = [];
        this.servicesArrayAll = this.servicesArrayByOffice[officeId];
        this.serViceAvailedOffice = officeId;
        this.playClickSound();
    },


    handleRegionChange(event) {
        let input = event.target.value;
        let selectedOption = event.target.selectedOptions[0];
        this.clientRegionText = selectedOption.text;
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
        this.selected_client_type = this.sqd_language[this.language][clientType];
        this.errorFields = this.errorFields.filter(field => field !== 'Client Type');
        this.playClickSound();
    },



    handleClientSexClick(clientSex) {
        this.clientSex = clientSex;
        this.hasErrorSex = false;
        this.selected_sex = this.sqd_language[this.language][clientSex];
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
            const fields = ['sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8'];

            fields.forEach(field => {
                if (this[field] === null) {

                    if (this[`has_${field}`]) {
                        this[`${field}_hasError`] = true;
                        if (!this.errorFields.includes(field.toUpperCase())) {
                            this.errorFields.push(field.toUpperCase());
                            {{-- if (field === 'sqd0') {
                            this.disagree.push(field.toUpperCase());
                        } --}}
                        }
                    } else {
                        this[`${field}`] = '6';
                    }



                } else {
                    this[`${field}_hasError`] = false;
                    this.errorFields = this.errorFields.filter(errorField => errorField !== field.toUpperCase());
                    {{-- if (field === 'sqd0') {
                        this.disagree = this.disagree.filter(disagreeField => disagreeField !== field.toUpperCase());
                    } --}}
                }
            });
        } else if (this.step === 5) {

            //empty the disagree array
            this.disagree = [];
            //check from sqd0 to sqd8 if there are strong disagree or disagree if so add to disagree
            if (this.sqd0 === '1' || this.sqd0 === '2') {
                this.disagree.push('SQD0');
            }
            if (this.sqd1 === '1' || this.sqd1 === '2') {
                this.disagree.push('SQD1');
            }
            if (this.sqd2 === '1' || this.sqd2 === '2') {
                this.disagree.push('SQD2');
            }
            if (this.sqd3 === '1' || this.sqd3 === '2') {
                this.disagree.push('SQD3');
            }
            if (this.sqd4 === '1' || this.sqd4 === '2') {
                this.disagree.push('SQD4');
            }
            if (this.sqd5 === '1' || this.sqd5 === '2') {
                this.disagree.push('SQD5');
            }
            if (this.sqd6 === '1' || this.sqd6 === '2') {
                this.disagree.push('SQD6');
            }
            if (this.sqd7 === '1' || this.sqd7 === '2') {
                this.disagree.push('SQD7');
            }
            if (this.sqd8 === '1' || this.sqd8 === '2') {
                this.disagree.push('SQD8');
            }

            //check if there are disagree if so require to provide suggestion
            if (this.disagree.length > 0 && !this.suggestion) {
                //get all the disagree fields separe with comma
                let disagreeFields = this.disagree.join(', ');
                this.errorMessage = this.sqd_language[this.language].sqd_error_message + ' ' + disagreeFields;
                return;
            }


        }




        if (this.errorFields.length > 0) {
            this.errorMessage = `Please provide ${this.errorFields.join(', ')}`;
            this.playErrorSound();
            return;
        } else {
            this.errorMessage = '';
            this.errorFields = [];
            this.hasErrorClientType = false;
            this.hasErrorSex = false;
            this.hasErrorAge = false;
            this.step++;
            this.playClickSound();
        }
    },

    handlePreviousClick() {
        this.step--;
        this.playClickSound();
    }



}" x-cloak>

    <div class="flex items-center justify-center w-full max-w-full overflow-x-auto">
        @if ($images['form_header_image'])
            <img class="max-w-full sm:max-w-2xl img-fluid"
                src="{{ asset('storage/header_images/' . $images['form_header_image']) }}" alt="Office Header Image">
        @else
            <p>No header image available.</p>
        @endif
    </div>
    <div class="flex items-center justify-center w-full max-w-full overflow-x-auto">
        <div class="grid grid-cols-1 text-center">
            <h1 class="text-xl">
                @php
                    $client_type = $is_external == 1 ? 'External Client' : 'Internal Client';
                    $form_type = $is_onsite == 1 ? 'Onsite' : 'Online';
                @endphp
                {{ $client_type }} Feedback Form ({{ $form_type }})

            </h1>
            <x-mary-steps wire:model="step" steps-color="step-primary">
                <x-mary-step step="1" text="" />
                <x-mary-step step="2" text="" />
                <x-mary-step step="3" text="" />
                <x-mary-step step="4" text="" />
                <x-mary-step step="5" text="" />
                <x-mary-step step="6" text="" data-content="✓" step-classes="!step-success" />
            </x-mary-steps>
        </div>
    </div>

    {{-- STEP 1 --}}
    <div id="STEP1">
        <div x-show="step === 1" class="flex flex-col items-center justify-center">
            <div class="flex flex-col items-center justify-center mt-6 mb-4">
                <span class="mb-2 text-lg font-semibold">Select Language</span>
                <div class="flex space-x-2">
                    <button @click="language = 'english'; playClickSound()"
                        :class="{ 'btn-primary text-base-100': language === 'english' }"
                        class="btn btn-sm">English</button>
                    <button @click="language = 'tagalog'; playClickSound()"
                        :class="{ 'btn-primary text-base-100': language === 'tagalog' }"
                        class="btn btn-sm">Tagalog</button>
                    <button @click="language = 'bisaya'; playClickSound()"
                        :class="{ 'btn-primary text-base-100': language === 'bisaya' }"
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
                        'btn-primary text-base-100': clientType === 'citizen',
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
                        'btn-primary text-base-100': clientType === 'business',
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
                        'btn-primary text-base-100': clientType === 'government',
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
                        'btn-primary text-base-100': clientSex === 'male',
                        'btn-primary btn-outline': clientSex !== 'male',
                        'btn-error': clientSex === null && hasErrorSex === true,
                        'btn w-36 h-24 flex flex-col items-center justify-center': true
                    }"
                    @click="handleClientSexClick('male'); playClickSound()">
                    <x-fas-male class="h-12" />
                    <span x-text="sqd_language[language].male"></span>
                </button>
                <button
                    :class="{
                        'btn-primary text-base-100': clientSex === 'female',
                        'btn-primary btn-outline': clientSex !== 'female',
                        'btn-error': clientSex === null && hasErrorSex === true,
                        'btn w-36 h-24 flex flex-col items-center justify-center': true
                    }"
                    @click="handleClientSexClick('female'); playClickSound()">
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
            <div class="flex justify-center w-full align-middle md:w-1/4">
                <input wire:model='clientAge' @keyup="handleAgeChange; playClickSound()" type="number" min="10"
                    max="100" placeholder="How old are you?"
                    class="w-full text-xl text-center input input-bordered border-primary input-xl"
                    :class="{ '!input-error': hasErrorAge }" />
            </div>

            {{-- END AGE --}}
            {{-- REGION --}}
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" x-text="sqd_language[language].region"
                    :class="{ 'text-error': hasErrorRegion }"></span>
            </div>
            {{-- <input wire:model='clientRegion' @keyup="handleRegionChange" type="text"
                placeholder="Region or your Address"
                class="w-full text-xl text-center lg:w-1/2 input input-bordered input-xl"
                :class="{ 'input-error': hasErrorRegion }" /> --}}

            <x-mary-select label="" @change="handleRegionChange; playClickSound()" :options="$regions"
                wire:model="clientRegion" />

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

            <div class="grid grid-cols-1 sm:grid-cols-2">
                <div class="overflow-y-auto col max-h-96">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="text-sm card-title" x-text="sqd_language[language].office_transacted"></h1>
                            @foreach ($offices as $office)
                                <livewire:form.form-office-list :office="$office" :with_sub="$with_sub" :key="$office->id" />
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
                                <button
                                    @click="handleCLickService(
                                                service[0],
                                                service[1],
                                                service[2],
                                                service[3], 
                                                service[4],
                                                service[5],
                                                service[6],
                                                service[7],
                                                service[8],
                                                service[9],
                                                service[10],
                                                service[11],
                                                service[12]
                                                )"
                                    class="items-center justify-start w-full h-auto p-1 text-left btn btn-sm"
                                    :class="{
                                        'btn-primary text-base-100': serViceAvailed === service[0],
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

    {{-- STEP 3 --}}
    <div class="flex items-center justify-center">
        <div x-show="step === 3" class="flex flex-col max-w-3xl">
            <div class="flex items-center justify-center">
                <span class="mt-4 mb-2 text-lg font-semibold" :class="{ 'text-error': hasErrorServiceAvailed }"
                    x-text="sqd_language[language].cc_instruction"></span>
            </div>

            <h1 class="mt-4 text-xl font-semibold" x-text="sqd_language[language].cc1"></h1>

            <button
                :class="{
                    'btn-primary text-base-100': cc1 === '1',
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
                    'btn-primary text-base-100': cc1 === '2',
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
                    'btn-primary text-base-100': cc1 === '3',
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
                    'btn-primary text-base-100': cc1 === '4',
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
                        'btn-primary text-base-100': cc2 === '1',
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
                        'btn-primary text-base-100': cc2 === '2',
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
                        'btn-primary text-base-100': cc2 === '3',
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
                        'btn-primary text-base-100': cc2 === '4',
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
                        'btn-primary text-base-100': cc2 === '5',
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
                        'btn-primary text-base-100': cc3 === '1',
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
                        'btn-primary text-base-100': cc3 === '2',
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
                        'btn-primary text-base-100': cc3 === '3',
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
                        'btn-primary text-base-100': cc3 === '4',
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
    <div class="flex items-start justify-center mx-auto my-auto">
        <div x-show="step === 4" class="flex flex-col items-start justify-start max-w-3xl">

            <div class="flex items-start justify-start">
                <span class="mt-4 mb-2 text-lg font-semibold leading-none text-left"
                    x-text="sqd_language[language].sqd_instruction"></span>
            </div>

            {{-- SQD0 --}}
            <div x-show="has_sqd0" class="flex flex-col items-center w-full p-2 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd0" :class="{ 'text-error': sqd0_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-2 btn"
                        :class="{
                            'btn-primary text-base-100': sqd0 === '1',
                            'btn-default btn-outline': sqd0 !== '1',
                            '!btn-error': sqd0 === null && sqd0_hasError === true,
                        }"
                        @click="handle_sqd0_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-2 btn"
                        :class="{
                            'btn-primary text-base-100': sqd0 === '2',
                            'btn-default btn-outline': sqd0 !== '2',
                            '!btn-error': sqd0 === null && sqd0_hasError === true,
                        }"
                        @click="handle_sqd0_click('2')">
                        <div class="flex flex-col items-center justify-center ">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-2 btn"
                        :class="{
                            'btn-primary text-base-100': sqd0 === '3',
                            'btn-default btn-outline': sqd0 !== '3',
                            '!btn-error': sqd0 === null && sqd0_hasError === true,
                        }"
                        @click="handle_sqd0_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-2 btn"
                        :class="{
                            'btn-primary text-base-100': sqd0 === '4',
                            'btn-default btn-outline': sqd0 !== '4',
                            '!btn-error': sqd0 === null && sqd0_hasError === true,
                        }"
                        @click="handle_sqd0_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-2 btn"
                        :class="{
                            'btn-primary text-base-100': sqd0 === '5',
                            'btn-default btn-outline ': sqd0 !== '5',
                            '!btn-error': sqd0 === null && sqd0_hasError === true,
                        }"
                        @click="handle_sqd0_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-2 btn"
                        :class="{
                            'btn-primary text-base-100': sqd0 === '6',
                            'btn-default btn-outline': sqd0 !== '6',
                            '!btn-error': sqd0 === null && sqd0_hasError === true,
                        }"
                        @click="handle_sqd0_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD0 --}}

            {{-- SQD1 --}}
            <div x-show="has_sqd1"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd1" :class="{ 'text-error': sqd1_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd1 === '1',
                            'btn-default btn-outline': sqd1 !== '1',
                            '!btn-error': sqd1 === null && sqd1_hasError === true,
                        }"
                        @click="handle_sqd1_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd1 === '2',
                            'btn-default btn-outline': sqd1 !== '2',
                            '!btn-error': sqd1 === null && sqd1_hasError === true,
                        }"
                        @click="handle_sqd1_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd1 === '3',
                            'btn-default btn-outline': sqd1 !== '3',
                            '!btn-error': sqd1 === null && sqd1_hasError === true,
                        }"
                        @click="handle_sqd1_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd1 === '4',
                            'btn-default btn-outline': sqd1 !== '4',
                            '!btn-error': sqd1 === null && sqd1_hasError === true,
                        }"
                        @click="handle_sqd1_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd1 === '5',
                            'btn-default btn-outline ': sqd1 !== '5',
                            '!btn-error': sqd1 === null && sqd1_hasError === true,
                        }"
                        @click="handle_sqd1_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd1 === '6',
                            'btn-default btn-outline': sqd1 !== '6',
                            '!btn-error': sqd1 === null && sqd1_hasError === true,
                        }"
                        @click="handle_sqd1_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD1 --}}

            {{-- SQD2 --}}
            <div x-show="has_sqd2"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd2" :class="{ 'text-error': sqd2_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd2 === '1',
                            'btn-default btn-outline': sqd2 !== '1',
                            '!btn-error': sqd2 === null && sqd2_hasError === true,
                        }"
                        @click="handle_sqd2_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd2 === '2',
                            'btn-default btn-outline': sqd2 !== '2',
                            '!btn-error': sqd2 === null && sqd2_hasError === true,
                        }"
                        @click="handle_sqd2_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd2 === '3',
                            'btn-default btn-outline': sqd2 !== '3',
                            '!btn-error': sqd2 === null && sqd2_hasError === true,
                        }"
                        @click="handle_sqd2_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd2 === '4',
                            'btn-default btn-outline': sqd2 !== '4',
                            '!btn-error': sqd2 === null && sqd2_hasError === true,
                        }"
                        @click="handle_sqd2_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd2 === '5',
                            'btn-default btn-outline ': sqd2 !== '5',
                            '!btn-error': sqd2 === null && sqd2_hasError === true,
                        }"
                        @click="handle_sqd2_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd2 === '6',
                            'btn-default btn-outline': sqd2 !== '6',
                            '!btn-error': sqd2 === null && sqd2_hasError === true,
                        }"
                        @click="handle_sqd2_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD2 --}}

            {{-- SQD3 --}}
            <div x-show="has_sqd3"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd3" :class="{ 'text-error': sqd3_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd3 === '1',
                            'btn-default btn-outline': sqd3 !== '1',
                            '!btn-error': sqd3 === null && sqd3_hasError === true,
                        }"
                        @click="handle_sqd3_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd3 === '2',
                            'btn-default btn-outline': sqd3 !== '2',
                            '!btn-error': sqd3 === null && sqd3_hasError === true,
                        }"
                        @click="handle_sqd3_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd3 === '3',
                            'btn-default btn-outline': sqd3 !== '3',
                            '!btn-error': sqd3 === null && sqd3_hasError === true,
                        }"
                        @click="handle_sqd3_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd3 === '4',
                            'btn-default btn-outline': sqd3 !== '4',
                            '!btn-error': sqd3 === null && sqd3_hasError === true,
                        }"
                        @click="handle_sqd3_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd3 === '5',
                            'btn-default btn-outline ': sqd3 !== '5',
                            '!btn-error': sqd3 === null && sqd3_hasError === true,
                        }"
                        @click="handle_sqd3_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd3 === '6',
                            'btn-default btn-outline': sqd3 !== '6',
                            '!btn-error': sqd3 === null && sqd3_hasError === true,
                        }"
                        @click="handle_sqd3_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD3 --}}

            {{-- SQD4 --}}
            <div x-show="has_sqd4"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd4" :class="{ 'text-error': sqd4_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd4 === '1',
                            'btn-default btn-outline': sqd4 !== '1',
                            '!btn-error': sqd4 === null && sqd4_hasError === true,
                        }"
                        @click="handle_sqd4_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd4 === '2',
                            'btn-default btn-outline': sqd4 !== '2',
                            '!btn-error': sqd4 === null && sqd4_hasError === true,
                        }"
                        @click="handle_sqd4_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500 " />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd4 === '3',
                            'btn-default btn-outline': sqd4 !== '3',
                            '!btn-error': sqd4 === null && sqd4_hasError === true,
                        }"
                        @click="handle_sqd4_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd4 === '4',
                            'btn-default btn-outline': sqd4 !== '4',
                            '!btn-error': sqd4 === null && sqd4_hasError === true,
                        }"
                        @click="handle_sqd4_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd4 === '5',
                            'btn-default btn-outline ': sqd4 !== '5',
                            '!btn-error': sqd4 === null && sqd4_hasError === true,
                        }"
                        @click="handle_sqd4_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd4 === '6',
                            'btn-default btn-outline': sqd4 !== '6',
                            '!btn-error': sqd4 === null && sqd4_hasError === true,
                        }"
                        @click="handle_sqd4_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD4 --}}

            {{-- SQD5 --}}
            <div x-show="has_sqd5"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd5" :class="{ 'text-error': sqd5_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd5 === '1',
                            'btn-default btn-outline': sqd5 !== '1',
                            '!btn-error': sqd5 === null && sqd5_hasError === true,
                        }"
                        @click="handle_sqd5_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd5 === '2',
                            'btn-default btn-outline': sqd5 !== '2',
                            '!btn-error': sqd5 === null && sqd5_hasError === true,
                        }"
                        @click="handle_sqd5_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500 " />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd5 === '3',
                            'btn-default btn-outline': sqd5 !== '3',
                            '!btn-error': sqd5 === null && sqd5_hasError === true,
                        }"
                        @click="handle_sqd5_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd5 === '4',
                            'btn-default btn-outline': sqd5 !== '4',
                            '!btn-error': sqd5 === null && sqd5_hasError === true,
                        }"
                        @click="handle_sqd5_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd5 === '5',
                            'btn-default btn-outline ': sqd5 !== '5',
                            '!btn-error': sqd5 === null && sqd5_hasError === true,
                        }"
                        @click="handle_sqd5_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd5 === '6',
                            'btn-default btn-outline': sqd5 !== '6',
                            '!btn-error': sqd5 === null && sqd5_hasError === true,
                        }"
                        @click="handle_sqd5_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD5 --}}

            {{-- SQD6 --}}
            <div x-show="has_sqd6"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd6" :class="{ 'text-error': sqd6_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd6 === '1',
                            'btn-default btn-outline': sqd6 !== '1',
                            '!btn-error': sqd6 === null && sqd6_hasError === true,
                        }"
                        @click="handle_sqd6_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd6 === '2',
                            'btn-default btn-outline': sqd6 !== '2',
                            '!btn-error': sqd6 === null && sqd6_hasError === true,
                        }"
                        @click="handle_sqd6_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500 " />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd6 === '3',
                            'btn-default btn-outline': sqd6 !== '3',
                            '!btn-error': sqd6 === null && sqd6_hasError === true,
                        }"
                        @click="handle_sqd6_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd6 === '4',
                            'btn-default btn-outline': sqd6 !== '4',
                            '!btn-error': sqd6 === null && sqd6_hasError === true,
                        }"
                        @click="handle_sqd6_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd6 === '5',
                            'btn-default btn-outline ': sqd6 !== '5',
                            '!btn-error': sqd6 === null && sqd6_hasError === true,
                        }"
                        @click="handle_sqd6_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd6 === '6',
                            'btn-default btn-outline': sqd6 !== '6',
                            '!btn-error': sqd6 === null && sqd6_hasError === true,
                        }"
                        @click="handle_sqd6_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD6 --}}

            {{-- SQD7 --}}
            <div x-show="has_sqd7"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd7" :class="{ 'text-error': sqd7_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd7 === '1',
                            'btn-default btn-outline': sqd7 !== '1',
                            '!btn-error': sqd7 === null && sqd7_hasError === true,
                        }"
                        @click="handle_sqd7_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd7 === '2',
                            'btn-default btn-outline': sqd7 !== '2',
                            '!btn-error': sqd7 === null && sqd7_hasError === true,
                        }"
                        @click="handle_sqd7_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500 " />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd7 === '3',
                            'btn-default btn-outline': sqd7 !== '3',
                            '!btn-error': sqd7 === null && sqd7_hasError === true,
                        }"
                        @click="handle_sqd7_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd7 === '4',
                            'btn-default btn-outline': sqd7 !== '4',
                            '!btn-error': sqd7 === null && sqd7_hasError === true,
                        }"
                        @click="handle_sqd7_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd7 === '5',
                            'btn-default btn-outline ': sqd7 !== '5',
                            '!btn-error': sqd7 === null && sqd7_hasError === true,
                        }"
                        @click="handle_sqd7_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd7 === '6',
                            'btn-default btn-outline': sqd7 !== '6',
                            '!btn-error': sqd7 === null && sqd7_hasError === true,
                        }"
                        @click="handle_sqd7_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD7 --}}

            {{-- SQD8 --}}
            <div x-show="has_sqd8"
                class="flex flex-col items-center w-full p-1 mt-1 border-2 rounded-md border-primary">
                <div class="w-full p-2 bg-primary text-base-100">
                    <h1 class="p-0 m-0 font-semibold leading-none text-left text-small"
                        x-text="sqd_language[language].sqd8" :class="{ 'text-error': sqd8_hasError }"></h1>
                </div>

                <div class="flex flex-wrap justify-center gap-1 mt-1">
                    <!-- Button 1 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd8 === '1',
                            'btn-default btn-outline': sqd8 !== '1',
                            '!btn-error': sqd8 === null && sqd8_hasError === true,
                        }"
                        @click="handle_sqd8_click('1')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-angry class="w-7 h-7 !text-red-700" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_sd" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 2 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd8 === '2',
                            'btn-default btn-outline': sqd8 !== '2',
                            '!btn-error': sqd8 === null && sqd8_hasError === true,
                        }"
                        @click="handle_sqd8_click('2')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-frown class="w-7 h-7 !text-red-500 " />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_d" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>

                    <!-- Button 3 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd8 === '3',
                            'btn-default btn-outline': sqd8 !== '3',
                            '!btn-error': sqd8 === null && sqd8_hasError === true,
                        }"
                        @click="handle_sqd8_click('3')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-meh class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_n" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 4 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd8 === '4',
                            'btn-default btn-outline': sqd8 !== '4',
                            '!btn-error': sqd8 === null && sqd8_hasError === true,
                        }"
                        @click="handle_sqd8_click('4')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-smile-beam class="text-green-500 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center ">
                                <span x-text="sqd_language[language].label_a" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 5 -->
                    <button class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd8 === '5',
                            'btn-default btn-outline ': sqd8 !== '5',
                            '!btn-error': sqd8 === null && sqd8_hasError === true,
                        }"
                        @click="handle_sqd8_click('5')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center">
                                <x-far-grin-stars class="text-green-700 w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_sa" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                    <!-- Button 6 -->
                    <button x-show="allow_na" class="flex flex-col w-20 h-auto p-1 btn"
                        :class="{
                            'btn-primary text-base-100': sqd8 === '6',
                            'btn-default btn-outline': sqd8 !== '6',
                            '!btn-error': sqd8 === null && sqd8_hasError === true,
                        }"
                        @click="handle_sqd8_click('6')">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center mb-2">
                                <x-far-question-circle class="w-7 h-7" />
                            </div>
                            <div class="flex items-center justify-center">
                                <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            {{-- END SQD8 --}}

        </div>
    </div>
    {{-- END STEP4 --}}


    {{-- STEP 5 --}}
    <div class="flex flex-col items-center justify-center w-full">
        <div x-show="step === 5" class="flex flex-col items-center justify-center w-full max-w-3xl">
            <div class="mt-4 col">
                <h1 class="text-lg font-semibold">
                    {{ $sqd_language[$language]['suggestion'] }}</h1>
            </div>

            <div class="items-center w-full max-w-lg col">
                <textarea wire:model='suggestion' placeholder="Leave empty if not necessary." rows="3"
                    class="items-center w-full textarea textarea-bordered textarea-lg"></textarea>
            </div>

            <div class="items-center justify-center mt-4 text-center col">
                <h1 class="items-center justify-center text-lg font-semibold text-start">
                    {{ $sqd_language[$language]['email_address'] }}</h1>
            </div>

            <div class="w-full max-w-lg col">
                <input type="text" wire:model='email' placeholder="Email Address"
                    class="w-full input input-bordered input-lg" />
            </div>
        </div>
    </div>

    {{-- END STEP 5 --}}

    {{-- STEP 6 --}}
    <div class="flex items-center justify-center w-full">
        <div x-show="step === 6" class="flex flex-col w-full max-w-2xl p-4 shadow-md bg-base-200rounded-lg">
            <div class="flex items-center justify-center mb-4">
                <span class="text-lg font-semibold" x-text="sqd_language[language].summary_header"></span>
            </div>

            <x-summary-item label="" alpine_value='client_type' value="selected_client_type" />
            <x-summary-item label="" alpine_value='sex' value="selected_sex" />
            <x-summary-item label="" alpine_value='age' value="clientAge" />
            <x-summary-item label="" alpine_value='region' value="clientRegionText" />
            <x-summary-item label="" alpine_value='office_transacted' value="office_transacted_word" />
            <x-summary-item label="" alpine_value='service_availed' value="service_availed_word" />

            <x-summary-item label="Citizen's Charter 1 (CC1)" alpine_value="cc_awareness"
                value="cc1_selected_word" />

            <div x-show="cc1 !== '4'">

                <x-summary-item label="Citizen's Charter 2 (CC2)" alpine_value="cc_visibility"
                    value="cc2_selected_word" />
                <x-summary-item label="Citizen's Charter 3 (CC3)" alpine_value="cc_helpfulness"
                    value="cc3_selected_word" />
            </div>

            <x-summary-item-sqd x-show='has_sqd0' value="0" label="" alpine_value="sqd0" />
            <x-summary-item-sqd x-show='has_sqd1' value="1" label="" alpine_value="sqd1" />
            <x-summary-item-sqd x-show='has_sqd2' value="2" label="" alpine_value="sqd2" />
            <x-summary-item-sqd x-show='has_sqd3' value="3" label="" alpine_value="sqd3" />
            <x-summary-item-sqd x-show='has_sqd4' value="4" label="" alpine_value="sqd4" />
            <x-summary-item-sqd x-show='has_sqd5' value="5" label="" alpine_value="sqd5" />
            <x-summary-item-sqd x-show='has_sqd6' value="6" label="" alpine_value="sqd6" />
            <x-summary-item-sqd x-show='has_sqd7' value="7" label="" alpine_value="sqd7" />
            <x-summary-item-sqd x-show='has_sqd8' value="8" label="" alpine_value="sqd8" />


            <x-summary-item label="" alpine_value="suggestion" value="suggestion" />
            <x-summary-item label="Email or Contact" alpine_value="email_address" value="email" />

        </div>
    </div>

    {{-- END STEP 6 --}}


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
            <x-mary-button class="btn btn-primary" x-show="step < 6" @click="handleNextClick()">
                <span x-text="sqd_language[language].next"></span>
            </x-mary-button>
            <x-mary-button icon='o-check' spinner class="btn btn-success" x-show="step === 6"
                wire:click='save_feedback'>
                <span wire:loading.remove>Save</span>
                <span wire:loading>Saving</span>
            </x-mary-button>
        </div>
        <div class="flex mt-5">
            <x-mary-theme-toggle darkTheme="{{ env('DARK_THEME', 'forest') }}"
                lightTheme="{{ env('LIGHT_THEME', 'cupcake') }}" class="btn btn-circle btn-ghost" />
        </div>
    </div>
</div>

{{-- CODE VERY SHORT --}}
