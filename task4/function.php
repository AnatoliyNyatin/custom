<?php

function sum($a, $b)
{
    $a = array_reverse(str_split($a));
    $b = array_reverse(str_split($b));

    $maxLength = max(count($a), count($b));

    $res = [];
    $c = 0;
    for ($i = 0; $i < $maxLength; $i++) {
        $c = $c + ($a[$i] ?? 0) + ($b[$i] ?? 0);
        $res[$i] = $c % 10;
        $c = intdiv($c, 10);
    }

    if ($c) {
        $maxLength++;
        $res[$maxLength] = $c;
    }

    return implode('', array_reverse($res));
}


echo sum('111111', '99');
