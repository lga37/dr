<?php 


function timeToSeconds(string $time): int
{
    $arr = explode(':', $time);
    if (count($arr) === 3) {
        return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
    }
    return $arr[0] * 60 + $arr[1];
}

function colorLog($msg, $type = 'i')
{
    switch ($type) {
        case 'e': //error
            echo "\033[31m $msg \033[0m\n";
            break;
        case 's': //success
            echo "\033[32m $msg \033[0m\n";
            break;
        case 'w': //warning
            echo "\033[33m $msg \033[0m\n";
            break;
        case 'i': //info
            echo "\033[36m $msg \033[0m\n";
            break;
        default:
            break;
    }
}

function isolaTrechoHtml($dom, $die = false)
{
    echo "\n\n\n";
    echo colorLog($dom->saveHTML(), 'e');
    echo "\n\n\n";
    if ($die) {
        die;
    }
}
function squish($value)
{
    return preg_replace('~(\s|\x{3164}|\x{1160})+~u', ' ', preg_replace('~^[\s\x{FEFF}]+|[\s\x{FEFF}]+$~u', '', $value));
}

function getKeyCanaisUnicos($valor)
{
    $canais_unicos = getCanaisUnicos();
    foreach ($canais_unicos as $nome => $c) {
        if ($c == $valor) {
            return $nome;
        }
    }
    return false;
}


function formataString($str)
{
    if (empty($str) || is_null($str)) {
        echo "\n string vazia - formataString";
        return;
    }
    $str = trim($str);

    #echo "<hr>$str<hr>";
    #$str = mb_convert_encoding($str, "Windows-1252", "UTF-8");
    $str = preg_replace('/(\s{2,}|\n|-{2,})/', " ", $str);
    #$str = urldecode($str);
    $str = stripAccents($str);
    $str = limpaStr2BD($str);
    return $str;
}

    ############### ta com erro nas maiusculas
    function stripAccents($string)
    {
        if (is_null($string) || empty($string)) {
            return;
        }

        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }
    
function limpaStr2BD($str)
{
    ######## intervalos permitidos : da white list
    #32 - 38 = espaco - &
    #40 - 90 = ( - Z
    #97 - 122 = a - z

    $permitidos1 = range(32, 38);
    $permitidos2 = range(40, 93); #inclui mais 3 91[ 92 \ 93]
    $permitidos3 = [95]; #underline _
    $permitidos4 = range(97, 122);
    $permit = array_merge($permitidos1, $permitidos2, $permitidos3, $permitidos4);

    $str_nova = "";

    for ($i = 0; $i < strlen($str); $i++) {
        if (in_array(ord($str[$i]), $permit)) {
            $str_nova .= $str[$i];
        }
    }

    return $str_nova;
}


function limpaStrAlfaNum($str)
{
    ######## intervalos permitidos : da white list
    #32       = espaco 
    #48 - 57  = 0 - 9
    #65 - 90 = A - Z
    #97 - 122 = a - z

    $permitidos1 = [32];
    $permitidos2 = range(48, 57);
    $permitidos3 = range(65, 90);
    $permitidos4 = range(97, 122);
    $permit = array_merge($permitidos1, $permitidos2, $permitidos3, $permitidos4);

    $str_nova = "";

    for ($i = 0; $i < strlen($str); $i++) {
        if (in_array(ord($str[$i]), $permit)) {
            $str_nova .= $str[$i];
        }
    }

    return $str_nova;
}

function parseNumLikes(string $likes)
{
    #var_dump($likes);

    $likes = strtolower(trim($likes));
    if (preg_match('/([\d|,|\.]+)\s?(k|K|M|mil|mi|m|bi|B)?/', $likes, $m)) {
        #print_r($m);
        $num = $m[1];
        $num = str_replace(',', '.', $num);
        #$txt = $m[2]??'sem'; deu pau aqui
    } elseif (preg_match('/[\d\.,]/', $likes, $m)) { #no caso de so nums ou 23.344 ou 23,344
        $novo_likes = preg_replace('/[^\d]/', "", $likes);
        return $novo_likes;
    } else {
        echo "\n\n ************************** Atencao checar isso em parseNumLikes : $likes";
    }
    $x = 1;
    if (str_contains($likes, 'mil') || str_contains($likes, 'k')) {
        $x = 1000;
    } elseif (str_contains($likes, 'mi') || str_contains($likes, 'm')) {
        $x = 1000000;
    }

    $res = $num * $x;
    #echo "\n\nparseNumLikes likes:$likes , res:$res ; x:$x";
    #echo $res;die;
    return $res;
}

function parseDataUploadVideo(string $dt_video): string|bool
{
    #echo $dt_video;
    if (preg_match('/([\d]{1,2})?\s?de\s?(.+)\s?de\s?([\d]{4})?/s', $dt_video, $m)) {
        $dia = $m[1];
        $mes = $m[2];
        $ano = $m[3];
    } elseif (preg_match('/([a-zA-Z]{3,})\s?([\d]{1,2}),(\s+)?([\d]{4})/', $dt_video, $m)) {
        #Sep 24, 2013
        $dia = $m[2];
        $mes = $m[1];
        $ano = $m[4];
    } else {
        echo "\n\n<br>parseDataUploadVideo passou batido : $dt_video<hr>";
        return false;
    }

    $mes = retornaMes($mes);
    $nova_dt = sprintf("%d/%d/%d", $dia, $mes, $ano);
    #echo $nova_dt;
    return $nova_dt;
}


function getCanaisUnicos()
{
    $canais_unicos = [
        'MUSIC' => '/channel/UC-9-kyTW8ZkZNDHQJ6FgpwQ',
        'SPORTS' => '/channel/UCEgdi0XIXXZ-qJOFPf4JSKw',
        'GAMING' => '/gaming',
        'MOVIES' => '/channel/UClgRkhTL3_hImCAmdLfDE4g',
        'NEWS' => '/channel/UCYfdidRxbB8Qhf0Nx7ioOYw',
        'LIVE' => '/channel/UC4R8DWoMoI7CAwX8_LjQHig',
        'LEARNING' => '/channel/UCtFRv9O2AHqOZjjynzrv-xg',
        'SPOTLIGHT' => '/channel/UCUN9lhwfMJRxMVuet7Shg0w',
        '360' => '/channel/UCzuqhhs6NWbgTzMuM09WKDQ',
    ];
    return $canais_unicos;
}
function retornaMes(string $mes)
{
    $mes = substr($mes, 0, 3);
    $mes = strtolower($mes);

    switch (trim($mes, ".")) {
        case 'jan':
            $mes = 1;
            break;
        case 'fev':
        case 'feb':
            $mes = 2;
            break;
        case 'mar':
            $mes = 3;
            break;
        case 'abr':
        case 'apr':
            $mes = 4;
            break;
        case 'mai':
        case 'may':
            $mes = 5;
            break;
        case 'jun':
            $mes = 6;
            break;
        case 'jul':
            $mes = 7;
            break;
        case 'ago':
        case 'aug':
            $mes = 8;
            break;
        case 'set':
        case 'sep':
            $mes = 9;
            break;
        case 'out':
        case 'oct':
            $mes = 10;
            break;
        case 'nov':
            $mes = 11;
            break;
        case 'dez':
        case 'dec':
            $mes = 12;
            break;
    }
    return $mes;
}