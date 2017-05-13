<?php

function display_datetime($date)
{
    return $date->format('d/m/y H:i');
}

function display_date($date)
{
    return $date->format('d/m/y');
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
    return 'R$ ' . toMoney($amount);
}