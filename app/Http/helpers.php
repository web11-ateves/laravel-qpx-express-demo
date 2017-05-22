<?php

function display_datetime($date)
{
    return $date->format('H:i d/m/y');
}

function display_date($date)
{
    return $date->format('d/m/Y');
}

function toAffirmative($expression)
{
    return $expression ? 'Sim' : '';
}

function toMoney($amount)
{
    return number_format($amount, 2, ',', '.');
}

function toReal($amount)
{
    if(isset($amount)) {
        return 'R$ ' . toMoney($amount);
    } else {
        return "-";
    }

}