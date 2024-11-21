<div class="p-6">
    Inserir Manualmente {{ $canal->nome }} <br><br>

    <form method="POST" class="flex items-center mb-3" wire:submit='add'>
        <x-input-label for="busca" class="mr-1" value="dd/mm/yyyy" />
        <x-text-input id="busca" class="mt-1 w-8" />

        <x-primary-button class="ms-3">
            {{ __('Add') }}
        </x-primary-button>
    </form>


    @php 
    $ano = $canal->dt->format('Y') ?? 2020;
    @endphp
    
    
    @for ($a=$ano;$a <= 2024; $a++)
        <a href="https://web.archive.org/web/{{ $a }}*/https://www.youtube.com/channel/{{ $canal->youtube_id }}" target="_blank">webarx {{ $a }}</a><br>
    @endfor

   

</div>
