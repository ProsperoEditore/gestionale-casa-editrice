<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLibroOrdineTable extends Migration
{
    public function up()
    {
        Schema::table('libro_ordine', function (Blueprint $table) {
            // Rinominare 'prezzo' in 'prezzo_copertina' (se necessario)
            if (Schema::hasColumn('libro_ordine', 'prezzo')) {
                $table->renameColumn('prezzo', 'prezzo_copertina');
            } else {
                $table->decimal('prezzo_copertina', 10, 2)->after('quantita');
            }

            // Aggiungere 'valore_scontato' se non esiste
            if (!Schema::hasColumn('libro_ordine', 'valore_scontato')) {
                $table->decimal('valore_scontato', 10, 2)->nullable()->after('sconto');
            }
        });
    }

    public function down()
    {
        Schema::table('libro_ordine', function (Blueprint $table) {
            // Ripristinare il nome della colonna solo se era presente in origine
            if (Schema::hasColumn('libro_ordine', 'prezzo_copertina')) {
                $table->renameColumn('prezzo_copertina', 'prezzo');
            }

            // Eliminare 'valore_scontato' se presente
            if (Schema::hasColumn('libro_ordine', 'valore_scontato')) {
                $table->dropColumn('valore_scontato');
            }
        });
    }
}
