<div class="p-6">
    Anos para webarx   {{ $canal->nome }} <br><br>

    @for ($a=2020;$a <= 2024; $a++)
        <a href="https://web.archive.org/web/{{ $a }}*/https://www.youtube.com/channel/{{ $canal->youtube_id }}" target="_blank">webarx {{ $a }}</a><br>
    @endfor


</div>
