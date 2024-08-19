<?php
                        namespace App\Livewire\Forms\Sqd;
                        use App\Models\Sqd\Sqd;
                        use Illuminate\Support\Facades\Auth;
                        use Livewire\Attributes\Validate;
                        use Livewire\Form;
                        
                        class SqdForm extends Form
                        {

                        public ?Sqd $sqd;

                            #[Validate('required|numeric')]

public $office_id;
#[Validate('required|min:3')]

public $language;
#[Validate('required|numeric')]

public $is_onsite;
#[Validate('required')]

public $header;
#[Validate('required|min:3')]

public $client_type;
#[Validate('required|min:3')]

public $citizen;
#[Validate('required|min:3')]

public $business;
#[Validate('required|min:3')]

public $government;
#[Validate('required|min:3')]

public $date;
#[Validate('required|min:3')]

public $sex;
#[Validate('required|min:3')]

public $male;
#[Validate('required|min:3')]

public $female;
#[Validate('required|min:3')]

public $age;
#[Validate('required|min:3')]

public $region;
#[Validate('required')]

public $sqd0;
#[Validate('required')]

public $sqd1;
#[Validate('required')]

public $sqd2;
#[Validate('required')]

public $sqd3;
#[Validate('required')]

public $sqd4;
#[Validate('required')]

public $sqd5;
#[Validate('required')]

public $sqd6;
#[Validate('required')]

public $sqd7;
#[Validate('required')]

public $sqd8;
#[Validate('required')]

public $cc1;
#[Validate('required')]

public $cc1_1;
#[Validate('required')]

public $cc1_2;
#[Validate('required')]

public $cc1_3;
#[Validate('required')]

public $cc1_4;
#[Validate('required')]

public $cc2;
#[Validate('required')]

public $cc2_1;
#[Validate('required')]

public $cc2_2;
#[Validate('required')]

public $cc2_3;
#[Validate('required')]

public $cc2_4;
#[Validate('required')]

public $cc2_5;
#[Validate('required')]

public $cc3;
#[Validate('required')]

public $cc3_1;
#[Validate('required')]

public $cc3_2;
#[Validate('required')]

public $cc3_3;
#[Validate('required')]

public $cc3_4;
#[Validate('required')]

public $suggestion;
#[Validate('required|min:3')]

public $email_address;


                            public function setSqd($sqd)
                            {
                                $this->sqd = $sqd;
                                $this->office_id = $sqd->office_id;
$this->language = $sqd->language;
$this->is_onsite = $sqd->is_onsite;
$this->header = $sqd->header;
$this->client_type = $sqd->client_type;
$this->citizen = $sqd->citizen;
$this->business = $sqd->business;
$this->government = $sqd->government;
$this->date = $sqd->date;
$this->sex = $sqd->sex;
$this->male = $sqd->male;
$this->female = $sqd->female;
$this->age = $sqd->age;
$this->region = $sqd->region;
$this->sqd0 = $sqd->sqd0;
$this->sqd1 = $sqd->sqd1;
$this->sqd2 = $sqd->sqd2;
$this->sqd3 = $sqd->sqd3;
$this->sqd4 = $sqd->sqd4;
$this->sqd5 = $sqd->sqd5;
$this->sqd6 = $sqd->sqd6;
$this->sqd7 = $sqd->sqd7;
$this->sqd8 = $sqd->sqd8;
$this->cc1 = $sqd->cc1;
$this->cc1_1 = $sqd->cc1_1;
$this->cc1_2 = $sqd->cc1_2;
$this->cc1_3 = $sqd->cc1_3;
$this->cc1_4 = $sqd->cc1_4;
$this->cc2 = $sqd->cc2;
$this->cc2_1 = $sqd->cc2_1;
$this->cc2_2 = $sqd->cc2_2;
$this->cc2_3 = $sqd->cc2_3;
$this->cc2_4 = $sqd->cc2_4;
$this->cc2_5 = $sqd->cc2_5;
$this->cc3 = $sqd->cc3;
$this->cc3_1 = $sqd->cc3_1;
$this->cc3_2 = $sqd->cc3_2;
$this->cc3_3 = $sqd->cc3_3;
$this->cc3_4 = $sqd->cc3_4;
$this->suggestion = $sqd->suggestion;
$this->email_address = $sqd->email_address;

                            }

                            public function store()
                            {
                                $this->validate();

                                $data = ['office_id' => $this->office_id,
'language' => $this->language,
'is_onsite' => $this->is_onsite,
'header' => $this->header,
'client_type' => $this->client_type,
'citizen' => $this->citizen,
'business' => $this->business,
'government' => $this->government,
'date' => $this->date,
'sex' => $this->sex,
'male' => $this->male,
'female' => $this->female,
'age' => $this->age,
'region' => $this->region,
'sqd0' => $this->sqd0,
'sqd1' => $this->sqd1,
'sqd2' => $this->sqd2,
'sqd3' => $this->sqd3,
'sqd4' => $this->sqd4,
'sqd5' => $this->sqd5,
'sqd6' => $this->sqd6,
'sqd7' => $this->sqd7,
'sqd8' => $this->sqd8,
'cc1' => $this->cc1,
'cc1_1' => $this->cc1_1,
'cc1_2' => $this->cc1_2,
'cc1_3' => $this->cc1_3,
'cc1_4' => $this->cc1_4,
'cc2' => $this->cc2,
'cc2_1' => $this->cc2_1,
'cc2_2' => $this->cc2_2,
'cc2_3' => $this->cc2_3,
'cc2_4' => $this->cc2_4,
'cc2_5' => $this->cc2_5,
'cc3' => $this->cc3,
'cc3_1' => $this->cc3_1,
'cc3_2' => $this->cc3_2,
'cc3_3' => $this->cc3_3,
'cc3_4' => $this->cc3_4,
'suggestion' => $this->suggestion,
'email_address' => $this->email_address,
'created_by' => Auth::id(),
'updated_by' => Auth::id(),
];
                                

                                Sqd::create($data);
                            }

                            public function update()
                            {
                                $this->validate();

                                $data = ['office_id' => $this->office_id,
'language' => $this->language,
'is_onsite' => $this->is_onsite,
'header' => $this->header,
'client_type' => $this->client_type,
'citizen' => $this->citizen,
'business' => $this->business,
'government' => $this->government,
'date' => $this->date,
'sex' => $this->sex,
'male' => $this->male,
'female' => $this->female,
'age' => $this->age,
'region' => $this->region,
'sqd0' => $this->sqd0,
'sqd1' => $this->sqd1,
'sqd2' => $this->sqd2,
'sqd3' => $this->sqd3,
'sqd4' => $this->sqd4,
'sqd5' => $this->sqd5,
'sqd6' => $this->sqd6,
'sqd7' => $this->sqd7,
'sqd8' => $this->sqd8,
'cc1' => $this->cc1,
'cc1_1' => $this->cc1_1,
'cc1_2' => $this->cc1_2,
'cc1_3' => $this->cc1_3,
'cc1_4' => $this->cc1_4,
'cc2' => $this->cc2,
'cc2_1' => $this->cc2_1,
'cc2_2' => $this->cc2_2,
'cc2_3' => $this->cc2_3,
'cc2_4' => $this->cc2_4,
'cc2_5' => $this->cc2_5,
'cc3' => $this->cc3,
'cc3_1' => $this->cc3_1,
'cc3_2' => $this->cc3_2,
'cc3_3' => $this->cc3_3,
'cc3_4' => $this->cc3_4,
'suggestion' => $this->suggestion,
'email_address' => $this->email_address,
'updated_by' => Auth::id(),
];

                                 

                                $this->sqd->update($data);
                            }

                           
                        }
                        