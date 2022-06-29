<?php

namespace AcMarche\Icar\Utils;

class ErrorHelper
{
    public static function text(int $codeError): string
    {
        return match ($codeError) {
            0 => "Pas d'erreur",
            1 => "Erreur générale",
            2 => "Indexation de la base de données en cours",
            3 => " Problème de paramètre d'appel",
            4 => "Problème de critères de recherche",
            default => ""
        };
    }
}