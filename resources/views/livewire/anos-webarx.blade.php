<div class="p-6">
    Anos para webarx   {{ $canal->nome }} <br><br>

    @php 
    $ano = $canal->dt->format('Y') ?? 2020;
    @endphp
    
    
    @for ($a=$ano;$a <= 2024; $a++)
        <a href="https://web.archive.org/web/{{ $a }}*/https://www.youtube.com/channel/{{ $canal->youtube_id }}" target="_blank">webarx {{ $a }}</a><br>
    @endfor


</div>
