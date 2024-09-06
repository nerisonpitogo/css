<?php
                        namespace App\Livewire\Forms\LibRegion;
                        use App\Models\LibRegion\LibRegion;
                        use Illuminate\Support\Facades\Auth;
                        use Livewire\Attributes\Validate;
                        use Livewire\Form;
                        
                        class LibRegionForm extends Form
                        {

                        public ?LibRegion $libregion;

                            #[Validate('required|min:3')]

public $name;


                            public function setLibRegion($libregion)
                            {
                                $this->libregion = $libregion;
                                $this->name = $libregion->name;

                            }

                            public function store()
                            {
                                $this->validate();

                                $data = ['name' => $this->name,
'created_by' => Auth::id(),
'updated_by' => Auth::id(),
];
                                

                                LibRegion::create($data);
                            }

                            public function update()
                            {
                                $this->validate();

                                $data = ['name' => $this->name,
'updated_by' => Auth::id(),
];

                                 

                                $this->libregion->update($data);
                            }

                           
                        }
                        