<?php

use Illuminate\Support\Str;

if (! function_exists('greet')) {
    function greet($name) {
        return "Hello, " . ucfirst($name) . "!";
    }
}

if (! function_exists('short_uuid')) {
    function short_uuid() {
        return Str::uuid()->toString();
    }
}
if (! function_exists('my_money_format')) {
    function my_money_format($amt, $country='US') {
        if($country == 'IN'){
            return '₹'.number_format($amt, 2,'.',',');

        }elseif($country == 'US'){
            return '$'.number_format($amt, 2,'.',',');
        }else{
            return '$'.number_format($amt, 2,'.',',');
        }
        
    }
}

