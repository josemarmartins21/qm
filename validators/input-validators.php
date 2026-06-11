<?php

function hasNumber(string $name)
{
    for ($i=0; $i < 9; $i++) { 
        if (str_contains($name, (string)$i)) {
            return true;
        }
    }
    return false;
}