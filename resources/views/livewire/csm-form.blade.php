<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\Office;
// use SQD
use App\Models\Sqd\sqd;

new #[Layout('components.layouts.form')] #[Title('CSM')] class extends Component {
    public int $step = 1;
    public string $language = 'english';

    public function mount(Office $office_id)
    {
    }

    public function with(): array
    {
        $sqds = Sqd::where(['is_onsite' => 1])->get();
        $sqd_language = [];
        foreach ($sqds as $sqd) {
            $sqd_language[$sqd->language] = $sqd;
        }
        return [
            'sqd_language' => $sqd_language,
        ];
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

    public function test()
    {
        dd($this->language);
    }
}; ?>


<div x-data="{ step: @entangle('step'), language: @entangle('language'), sqd_language: @js($sqd_language) }">


    <div class="flex items-center justify-center w-full">
        <x-mary-steps wire:model="step" steps-color="step-primary">
            <x-mary-step step="1" text="A" />
            <x-mary-step step="2" text="B" />
            <x-mary-step step="3" text="C" data-content="✓" step-classes="!step-success" />
        </x-mary-steps>
    </div>

    {{-- STEP 1 --}}
    <div x-show="step === 1" class="flex flex-col items-center justify-start">
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
        <div class="flex">
            <template x-if="sqd_language[language]">
                <span x-text="sqd_language[language].header"></span>
            </template>
        </div>
    </div>

    <hr class="my-5" />

    <div class="flex items-center justify-center gap-1">
        {{-- <x-mary-button label="<< Prev" @click="step > 1 ? step-- : step" />
        <x-mary-button label="Next >>" @click="step < 3 ? step++ : step" /> --}}
        <x-mary-button @click="step > 1 ? step-- : step">
            <span x-text="sqd_language[language].previous"></span>
        </x-mary-button>
        <x-mary-button @click="step < 3 ? step++ : step">
            <span x-text="sqd_language[language].next"></span>
        </x-mary-button>
    </div>

    <hr class="my-5" />



    <button wire:click='test'>TEST</button>
</div>
