<?php

namespace App\Enum;

enum Status: string
{
    case EN_COURS = 'enCours';
    case TERMINE = 'termine';
    case EN_ATTENTE = 'enAttente';
}