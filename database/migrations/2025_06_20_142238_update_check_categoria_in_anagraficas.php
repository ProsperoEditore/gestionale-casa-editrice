use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rimuove la vecchia constraint
        DB::statement("ALTER TABLE anagraficas DROP CONSTRAINT IF EXISTS anagraficas_categoria_check");

        // Aggiunge la nuova constraint con 'scuola'
        DB::statement("
            ALTER TABLE anagraficas
            ADD CONSTRAINT anagraficas_categoria_check
            CHECK (
                categoria IN (
                    'magazzino editore',
                    'sito',
                    'libreria c.e.',
                    'libreria cliente',
                    'privato',
                    'associazione',
                    'grossista',
                    'distributore',
                    'fiere',
                    'festival',
                    'altro',
                    'biblioteca',
                    'università',
                    'scuola'
                )
            )
        ");
    }

    public function down(): void
    {
        // Ripristina la constraint originale (senza 'scuola')
        DB::statement("ALTER TABLE anagraficas DROP CONSTRAINT IF EXISTS anagraficas_categoria_check");

        DB::statement("
            ALTER TABLE anagraficas
            ADD CONSTRAINT anagraficas_categoria_check
            CHECK (
                categoria IN (
                    'magazzino editore',
                    'sito',
                    'libreria c.e.',
                    'libreria cliente',
                    'privato',
                    'associazione',
                    'grossista',
                    'distributore',
                    'fiere',
                    'festival',
                    'altro',
                    'biblioteca',
                    'università'
                )
            )
        ");
    }
};
