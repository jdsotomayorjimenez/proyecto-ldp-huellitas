<?php

namespace Database\Seeders;

use App\Models\Raza;
use Illuminate\Database\Seeder;

class RazaDescripcionSeeder extends Seeder
{
    /**
     * Renombra "Sin raza definida" a "Mestizo" y aplica descripciones reales por raza.
     */
    public function run(): void
    {
        // 1) "Sin raza definida" -> "Mestizo" (Ave, Conejo, Hámster; Perro y Gato ya lo tienen).
        Raza::where('nombre', 'Sin raza definida')->update(['nombre' => 'Mestizo']);

        // 2) Descripciones por tipo|raza. (No se toca "Conejo Enano Holandés".)
        $descripciones = [
            'Perro|Mestizo' => 'Perro fruto de la mezcla de varias razas; suele ser sano, longevo y muy adaptable, con un carácter equilibrado y un aspecto único en cada ejemplar.',
            'Perro|Labrador' => 'Perro mediano-grande, sociable e inteligente; noble, enérgico y muy fácil de entrenar, es un compañero ideal para familias y niños.',
            'Perro|Poodle' => 'Perro elegante de pelo rizado e hipoalergénico; muy inteligente y activo, aprende rápido y existe en tamaños estándar, mediano y toy.',
            'Perro|Pomerania' => 'Perro miniatura tipo spitz, de pelaje abundante y aspecto de peluche; despierto, alegre y muy apegado a su familia.',
            'Perro|Yorkshire Terrier' => 'Perro de bolsillo con pelo largo y sedoso; valiente, enérgico y cariñoso, perfecto para quienes viven en espacios pequeños.',
            'Perro|Golden Retriever' => 'Perro grande, amable y paciente; muy leal e inteligente, excelente con niños y uno de los más fáciles de educar.',
            'Perro|Bulldog Francés' => 'Perro pequeño y musculoso, de orejas de murciélago; tranquilo, cariñoso y buen compañero de interior que no necesita mucho ejercicio.',
            'Perro|Corgi' => 'Perro de patas cortas y cuerpo alargado originario de Gales; inteligente, activo y muy sociable, fue criado para pastorear ganado.',
            'Perro|Husky' => 'Perro nórdico de gran resistencia y ojos llamativos; enérgico, independiente y sociable, necesita ejercicio diario y espacio.',
            'Perro|Chihuahua' => 'La raza de perro más pequeña del mundo; valiente, leal y muy apegado a su dueño, ideal para la vida en interiores.',
            'Perro|Schnauzer' => 'Perro de barba y cejas características; alerta, inteligente y leal, buen guardián y compañero familiar en sus tres tamaños.',
            'Perro|Pastor Alemán' => 'Perro grande, fuerte e inteligente; leal y protector, muy valorado como perro de trabajo, guía y compañía.',

            'Gato|Mestizo' => 'Gato común europeo sin pedigrí; resistente, adaptable y de carácter variado, suele ser muy cariñoso y un compañero excelente.',
            'Gato|Siamés' => 'Gato esbelto de ojos azules y pelaje con puntos de color; muy vocal, sociable e intensamente apegado a las personas.',
            'Gato|Persa' => 'Gato de pelo largo y cara aplanada; tranquilo, cariñoso y casero, necesita cepillado frecuente para cuidar su manto.',
            'Gato|Maine Coon' => 'Una de las razas de gato más grandes; de pelaje denso y carácter dócil y sociable, apodado el "gigante gentil".',

            'Conejo|Mestizo' => 'Conejo sin línea definida; sano, sociable y adaptable, de tamaño, color y pelaje variables según su mezcla.',
            'Conejo|Holland Lop' => 'Conejo enano de orejas caídas originario de los Países Bajos; pequeño, dócil y muy cariñoso, ideal como mascota de compañía.',
            'Conejo|Cabeza de León' => 'Conejo pequeño con una melena de pelo largo alrededor de la cabeza; juguetón, sociable y de aspecto muy llamativo.',

            'Ave|Mestizo' => 'Ave de compañía sin raza específica; alegre y sociable, se adapta bien a vivir en una jaula amplia o pajarera con otras aves.',
            'Ave|Periquito Australiano' => 'Pequeño loro muy popular, colorido y sociable; con paciencia puede aprender a imitar sonidos y algunas palabras.',
            'Ave|Canario' => 'Ave pequeña apreciada por su canto melodioso y su plumaje vivo; tranquila, resistente y fácil de cuidar.',
            'Ave|Cacatúa Ninfa' => 'Ave de cresta eréctil y mejillas anaranjadas; cariñosa, sociable y capaz de silbar melodías, muy apegada a su cuidador.',

            'Hámster|Mestizo' => 'Hámster sin línea definida; pequeño, activo y de hábitos nocturnos, es ideal como primera mascota por su fácil cuidado.',
            'Hámster|Sirio' => 'Hámster dorado, el más grande y común; solitario pero dócil y fácil de manejar, perfecto para quienes empiezan.',
            'Hámster|Roborovski' => 'El hámster más pequeño y veloz; muy activo y sociable entre los suyos, ideal para observar más que para manipular.',
        ];

        foreach (Raza::with('tipoMascota')->get() as $raza) {
            $clave = $raza->tipoMascota->nombre.'|'.$raza->nombre;

            if (isset($descripciones[$clave])) {
                $raza->update(['descripcion' => $descripciones[$clave]]);
            }
        }
    }
}
