<?php


function addString($value, string $toAdd): string {
    if (!str_contains($value, $toAdd)) {
        return trim($toAdd . " ". $value);
    }
    return $value;
}