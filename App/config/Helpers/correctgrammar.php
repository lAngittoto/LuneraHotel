<?php

function correctGrammar($count) {
    if($count == 1) {
        return "Up to 1 person";
    }else {
         return "Up to {$count} people";
    }
}