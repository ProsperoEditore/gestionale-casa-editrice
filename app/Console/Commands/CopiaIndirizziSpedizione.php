<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Anagrafica;

class CopiaIndirizziSpedizione extends Command
{
    protected $signature = 'anagrafiche:aggiorna-indirizzi-spedizione';
    protected $description = 'Copia indirizzo di fatturazione come indirizzo di spedizione dove questo è mancante';

    public function handle()
    {
        $anagrafiche = Anagrafica::whereNull('indirizzo_spedizione')
            ->orWhere('indirizzo_spedizione', '')
            ->get();

        $count = 0;

        foreach ($anagrafiche as $anagrafica) {
            $anagrafica->indirizzo_spedizione = $anagrafica->indirizzo_fatturazione;
            $anagrafica->save();
            $count++;
        }

        $this->info("Aggiornate $count anagrafiche.");
        return 0;
    }
}
