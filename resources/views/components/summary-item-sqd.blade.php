@props(['sqd', 'sqd_language', 'language'])

<div class="grid items-center w-full grid-cols-2 gap-6 p-1 leading-none border-y-2">
    <div class="leading-none">
        <span class="inline text-sm leading-none">
            {{ $sqd_language[$language]['sqd' . $sqd] }}
        </span>
    </div>
    <div class="leading-none ">

        <div class="flex items-center justify-start text-red-700" x-show="sqd{{ $sqd }}==='1'">
            <x-far-angry class="mr-1 w-7 h-7" />
            <span x-text="sqd_language[language].label_sd"></span>
        </div>

        <div class="flex items-center justify-start text-red-500" x-show="sqd{{ $sqd }}==='2'">
            <x-far-frown class="mr-1 w-7 h-7" />
            <span x-text="sqd_language[language].label_d"></span>
        </div>
        <div class="flex items-center justify-start" x-show="sqd{{ $sqd }}==='3'">
            <x-far-meh class="mr-1 w-7 h-7" />
            <span x-text="sqd_language[language].label_n"></span>
        </div>
        <div class="flex items-center justify-start text-green-500" x-show="sqd{{ $sqd }}==='4'">
            <x-far-smile-beam class="mr-1 w-7 h-7" />
            <span x-text="sqd_language[language].label_a"></span>
        </div>
        <div class="flex items-center justify-start text-green-700" x-show="sqd{{ $sqd }}==='5'">
            <x-far-grin-stars class="mr-1 w-7 h-7" />
            <span x-text="sqd_language[language].label_sa"></span>

        </div>
        <div class="flex items-center justify-start " x-show="sqd{{ $sqd }}==='6'">
            <x-far-question-circle class="mr-1 w-7 h-7" />
            <span x-text="sqd_language[language].label_na"></span>

        </div>

    </div>
</div>
