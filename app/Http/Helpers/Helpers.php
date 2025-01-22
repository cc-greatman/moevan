<?php

use Carbon\Carbon;


function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function randPl($min, $max){
    while(true) {
        if( time() % 5 == 0 ) { // get time in seconds and check if it is multiple of 5
            echo rand($min, $max);
            break; // break the loop
        } else {
            sleep(1);
        }
    }
    header("Refresh:5");
}


function showDate($date, $format = 'Y/m/d')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false){
    $separator = '';
    if($separate){
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if($exceptZeros){
    $exp = explode('.', $printAmount);
        if($exp[1]*1 == 0){
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}
