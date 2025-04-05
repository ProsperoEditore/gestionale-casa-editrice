<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Impostazioni generali di DomPDF
    |--------------------------------------------------------------------------
    */

    'show_warnings' => false,

    'orientation' => 'portrait',

    'defines' => [

        /**
         * La risoluzione predefinita del PDF generato (in dpi).
         */
        'DOMPDF_DPI' => 96,

        /**
         * Imposta la carta predefinita (A4, letter, legal, ecc.).
         */
        'DOMPDF_DEFAULT_PAPER_SIZE' => 'a4',

        /**
         * Imposta l'orientamento predefinito ('portrait' o 'landscape').
         */
        'DOMPDF_DEFAULT_PAPER_ORIENTATION' => 'portrait',

        /**
         * Abilita PHP nel template (essenziale per numerazione pagine).
         */
        'DOMPDF_ENABLE_PHP' => true,

        /**
         * Consenti accesso ai file locali (necessario per immagini da filesystem locale).
         */
        'DOMPDF_ENABLE_REMOTE' => true,

        /**
         * Consenti accesso ai file locali.
         */
        'DOMPDF_ENABLE_LOCAL_FILE_ACCESS' => true,

        /**
         * Margini predefiniti (top, right, bottom, left) in punti.
         */
        'DOMPDF_MARGIN_TOP' => 20,
        'DOMPDF_MARGIN_RIGHT' => 20,
        'DOMPDF_MARGIN_BOTTOM' => 20,
        'DOMPDF_MARGIN_LEFT' => 20,

        /**
         * Abilita debug DOMPDF.
         */
        'DOMPDF_DEBUG' => false,

        /**
         * Carica automaticamente i font disponibili (essenziale per DejaVu).
         */
        'DOMPDF_ENABLE_AUTOLOAD' => true,

        /**
         * Caratteri predefiniti.
         */
        'DOMPDF_DEFAULT_FONT' => 'Helvetica',

        /**
         * Altezza minima di una riga.
         */
        'DOMPDF_DEFAULT_LINE_HEIGHT' => 1.2,

        /**
         * Imposta la cartella per il caching dei font e immagini.
         */
        'DOMPDF_FONT_CACHE' => storage_path('fonts/'),

        /**
         * Impostazioni per abilitare il caricamento di CSS esterni.
         */
        'DOMPDF_ENABLE_CSS_FLOAT' => true,
        'DOMPDF_ENABLE_HTML5PARSER' => true,
    ],
];
