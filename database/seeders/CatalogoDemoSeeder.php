<?php

namespace Database\Seeders;

use App\Models\ImagenMascota;
use App\Models\Mascota;
use App\Models\Raza;
use App\Models\TipoMascota;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CatalogoDemoSeeder extends Seeder
{
    private const IMAGE_DIRECTORY = 'img/mascotas/catalogo-2026/';

    public function run(): void
    {
        $this->crearRazasMenoresSiNoExisten();
        $razas = $this->mapaDeRazas();
        $mascotas = $this->mascotas();

        if (count($mascotas) !== 79) {
            throw new RuntimeException('El catálogo debe contener exactamente 79 mascotas.');
        }

        DB::transaction(function () use ($mascotas, $razas): void {
            $this->limpiarCatalogoAnterior();

            foreach ($mascotas as $datos) {
                $claveRaza = $datos['tipo'].'|'.$datos['raza'];
                $raza = $razas->get($claveRaza);

                if (! $raza) {
                    throw new RuntimeException("La raza permitida {$claveRaza} no está registrada.");
                }

                $ruta = self::IMAGE_DIRECTORY.$datos['imagen'];

                if (! is_file(public_path($ruta))) {
                    throw new RuntimeException("Falta la imagen real requerida: {$ruta}");
                }

                $mascota = Mascota::create([
                    'raza_id' => $raza->id,
                    'nombre' => $datos['nombre'],
                    'fecha_nacimiento' => $datos['fecha_nacimiento'],
                    'genero' => $datos['genero'],
                    'tamanio' => $datos['tamanio'],
                    'descripcion' => $datos['descripcion'],
                    'estado' => 'disponible',
                ]);

                ImagenMascota::create([
                    'mascota_id' => $mascota->id,
                    'ruta' => $ruta,
                    'es_principal' => true,
                ]);
            }
        });
    }

    private function crearRazasMenoresSiNoExisten(): void
    {
        $razasPorTipo = [
            'Conejo' => ['Holland Lop', 'Cabeza de León'],
            'Hámster' => ['Sirio', 'Roborovski'],
        ];

        foreach ($razasPorTipo as $tipoNombre => $nombres) {
            $tipo = TipoMascota::where('nombre', $tipoNombre)->firstOrFail();

            if ($tipo->razas()->exists()) {
                continue;
            }

            foreach ($nombres as $nombre) {
                Raza::create([
                    'tipo_mascota_id' => $tipo->id,
                    'nombre' => $nombre,
                    'descripcion' => "Raza común de {$tipoNombre}.",
                ]);
            }
        }
    }

    private function mapaDeRazas()
    {
        return Raza::with('tipoMascota')
            ->get()
            ->keyBy(fn (Raza $raza) => $raza->tipoMascota->nombre.'|'.$raza->nombre);
    }

    private function limpiarCatalogoAnterior(): void
    {
        DB::table('adopciones')->delete();
        DB::table('citas_adopcion')->delete();
        DB::table('cumplimientos_requisitos')->delete();
        DB::table('respuestas_solicitud')->delete();
        DB::table('solicitudes_adopcion')->delete();
        ImagenMascota::query()->delete();
        Mascota::query()->delete();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function mascotas(): array
    {
        return [
            // 20 cachorros entregados en capturas.zip.
            $this->animal('Bingo', 'Perro', 'Pomerania', '2026-04-14', 'macho', 'pequeno', 'perro-bingo-pomerania-2m.png', 'Cachorro alegre, sociable y acostumbrado al contacto con personas.'),
            $this->animal('Zucky', 'Perro', 'Pomerania', '2026-03-14', 'hembra', 'pequeno', 'perro-zucky-pomerania-3m.png', 'Cachorra curiosa, activa y muy cariñosa.'),
            $this->animal('Tuti', 'Perro', 'Yorkshire Terrier', '2026-04-14', 'hembra', 'pequeno', 'perro-tuti-yorkshire-2m.png', 'Pequeña, atenta y tranquila cuando está en brazos.'),
            $this->animal('Milo', 'Perro', 'Yorkshire Terrier', '2026-04-14', 'macho', 'pequeno', 'perro-milo-yorkshire-2m.png', 'Cachorro juguetón que disfruta explorar espacios seguros.'),
            $this->animal('Cooper', 'Perro', 'Golden Retriever', '2026-04-14', 'macho', 'mediano', 'perro-cooper-golden-2m.png', 'Dócil, juguetón y con buen carácter para convivir en familia.'),
            $this->animal('Luna', 'Perro', 'Golden Retriever', '2026-02-14', 'hembra', 'mediano', 'perro-luna-golden-4m.png', 'Cachorra noble, activa y receptiva al entrenamiento.'),
            $this->animal('Leo', 'Perro', 'Golden Retriever', '2026-02-14', 'macho', 'mediano', 'perro-leo-golden-4m.png', 'Cariñoso, confiado y con energía para juegos diarios.'),
            $this->animal('Max', 'Perro', 'Labrador', '2026-04-14', 'macho', 'mediano', 'perro-max-labrador-2m.png', 'Cachorro amistoso, curioso y motivado por la comida.'),
            $this->animal('Finn', 'Perro', 'Labrador', '2026-03-14', 'macho', 'mediano', 'perro-finn-labrador-3m.png', 'Sociable, inteligente y listo para aprender rutinas básicas.'),
            $this->animal('Jesse', 'Perro', 'Bulldog Francés', '2026-04-14', 'hembra', 'pequeno', 'perro-jesse-bulldog-2m.png', 'Cachorra afectuosa y calmada, ideal para compañía dentro de casa.'),
            $this->animal('Molly', 'Perro', 'Bulldog Francés', '2026-04-14', 'hembra', 'pequeno', 'perro-molly-bulldog-2m.png', 'Dulce, observadora y muy apegada a las personas.'),
            $this->animal('Levi', 'Perro', 'Corgi', '2026-04-14', 'macho', 'pequeno', 'perro-levi-corgi-2m.png', 'Activo, simpático y siempre dispuesto a jugar.'),
            $this->animal('Zoe', 'Perro', 'Corgi', '2026-04-14', 'hembra', 'pequeno', 'perro-zoe-corgi-2m.png', 'Cachorra vivaz, confiada y de temperamento amistoso.'),
            $this->animal('Apollo', 'Perro', 'Husky', '2026-04-14', 'macho', 'mediano', 'perro-apollo-husky-2m.png', 'Enérgico, expresivo y adecuado para una familia activa.'),
            $this->animal('Athena', 'Perro', 'Husky', '2026-04-14', 'hembra', 'mediano', 'perro-athena-husky-2m.png', 'Cachorra inteligente que necesita actividad y acompañamiento.'),
            $this->animal('Rosie', 'Perro', 'Chihuahua', '2026-04-14', 'hembra', 'pequeno', 'perro-rosie-chihuahua-2m.png', 'Pequeña, alerta y cariñosa cuando gana confianza.'),
            $this->animal('Charlie', 'Perro', 'Chihuahua', '2026-05-14', 'macho', 'pequeno', 'perro-charlie-chihuahua-1m.png', 'El más joven del grupo; requiere un hogar paciente y protegido.'),
            $this->animal('Mónica', 'Perro', 'Schnauzer', '2026-04-14', 'hembra', 'pequeno', 'perro-monica-schnauzer-2m.png', 'Lista, atenta y con buena disposición para aprender.'),
            $this->animal('Leia', 'Perro', 'Pastor Alemán', '2026-03-14', 'hembra', 'mediano', 'perro-leia-pastor-3m.png', 'Noble, segura y con gran potencial para entrenamiento.'),
            $this->animal('Storm', 'Perro', 'Pastor Alemán', '2026-03-14', 'macho', 'mediano', 'perro-storm-pastor-3m.png', 'Cachorro activo, leal y muy atento a su entorno.'),

            // 10 perros mayores.
            $this->animal('Coco', 'Perro', 'Pomerania', '2017-05-10', 'hembra', 'pequeno', 'perro-mayor-coco-pomerania.jpg', 'Perrita mayor tranquila que disfruta las caminatas cortas y las siestas.'),
            $this->animal('Toby', 'Perro', 'Yorkshire Terrier', '2015-03-22', 'macho', 'pequeno', 'perro-mayor-toby-yorkshire.jpg', 'Compañero sereno, casero y acostumbrado a rutinas calmadas.'),
            $this->animal('Bruno', 'Perro', 'Golden Retriever', '2016-07-15', 'macho', 'grande', 'perro-mayor-bruno-golden.jpg', 'Perro noble y paciente que busca un hogar cómodo para su vejez.'),
            $this->animal('Maya', 'Perro', 'Labrador', '2014-09-05', 'hembra', 'grande', 'perro-mayor-maya-labrador.jpg', 'Adulta mayor equilibrada, leal y muy cercana a las personas.'),
            $this->animal('Otto', 'Perro', 'Bulldog Francés', '2017-01-30', 'macho', 'pequeno', 'perro-mayor-otto-bulldog.jpg', 'Tranquilo, afectuoso y feliz con paseos suaves.'),
            $this->animal('Nina', 'Perro', 'Corgi', '2016-11-12', 'hembra', 'pequeno', 'perro-mayor-nina-corgi.jpg', 'Perrita madura, dócil y muy buena compañera de hogar.'),
            $this->animal('Thor', 'Perro', 'Husky', '2015-04-18', 'macho', 'grande', 'perro-mayor-thor-husky.jpg', 'Veterano activo para su edad, disfruta paseos moderados y compañía.'),
            $this->animal('Pepa', 'Perro', 'Chihuahua', '2014-08-25', 'hembra', 'pequeno', 'perro-mayor-pepa-chihuahua.jpg', 'Abuelita pequeña, sensible y feliz en espacios tranquilos.'),
            $this->animal('Hugo', 'Perro', 'Schnauzer', '2016-06-08', 'macho', 'mediano', 'perro-mayor-hugo-schnauzer.jpg', 'Caballero de barba canosa, educado y de temperamento estable.'),
            $this->animal('Rocky', 'Perro', 'Pastor Alemán', '2015-02-14', 'macho', 'grande', 'perro-mayor-rocky-pastor.jpg', 'Perro mayor leal, protector y agradecido con quienes lo cuidan.'),

            // 10 gatos pequeños.
            $this->animal('Nala', 'Gato', 'Siamés', '2026-04-14', 'hembra', 'pequeno', 'gato-bebe-nala-siames.jpg', 'Gatita de dos meses, curiosa y acostumbrada al contacto humano.'),
            $this->animal('Kira', 'Gato', 'Siamés', '2026-03-14', 'hembra', 'pequeno', 'gato-bebe-kira-siames.jpg', 'Juguetona, conversadora y muy atenta a todo lo que ocurre.'),
            $this->animal('Moka', 'Gato', 'Siamés', '2026-01-14', 'macho', 'pequeno', 'gato-bebe-moka-siames.jpg', 'Gatito activo, sociable y amante de los lugares cálidos.'),
            $this->animal('Perla', 'Gato', 'Persa', '2026-05-14', 'hembra', 'pequeno', 'gato-bebe-perla-persa.jpg', 'Muy pequeña, delicada y tranquila; necesita cepillado frecuente.'),
            $this->animal('Nube', 'Gato', 'Persa', '2026-04-28', 'macho', 'pequeno', 'gato-bebe-nube-persa.jpg', 'Gatito suave, calmado y feliz durmiendo cerca de su familia.'),
            $this->animal('Alba', 'Gato', 'Persa', '2025-12-14', 'hembra', 'pequeno', 'gato-bebe-alba-persa.jpg', 'Joven, dócil y acostumbrada a vivir dentro de casa.'),
            $this->animal('Félix', 'Gato', 'Maine Coon', '2025-10-14', 'macho', 'mediano', 'gato-bebe-felix-maine.jpg', 'Gatito grande para su edad, sociable y de carácter gentil.'),
            $this->animal('Lía', 'Gato', 'Maine Coon', '2025-11-14', 'hembra', 'mediano', 'gato-bebe-lia-maine.jpg', 'Curiosa, peluda y muy cariñosa con personas tranquilas.'),
            $this->animal('Dante', 'Gato', 'Maine Coon', '2026-02-14', 'macho', 'pequeno', 'gato-bebe-dante-maine.jpg', 'Joven juguetón que disfruta trepar y perseguir juguetes.'),
            $this->animal('Pipo', 'Gato', 'Mestizo', '2026-03-28', 'macho', 'pequeno', 'gato-bebe-pipo-mestizo.jpg', 'Gatito rescatado, despierto y agradecido con las caricias.'),

            // 10 gatos mayores.
            $this->animal('Simba', 'Gato', 'Siamés', '2014-05-10', 'macho', 'mediano', 'gato-mayor-simba-siames.jpg', 'Gato mayor conversador, tranquilo y muy apegado a su rutina.'),
            $this->animal('Cleo', 'Gato', 'Siamés', '2012-08-14', 'hembra', 'pequeno', 'gato-mayor-cleo-siames.jpg', 'Señora independiente que disfruta ambientes silenciosos.'),
            $this->animal('Salem', 'Gato', 'Siamés', '2016-01-20', 'macho', 'mediano', 'gato-mayor-salem-siames.jpg', 'Adulto mayor curioso, casero y amante de las siestas al sol.'),
            $this->animal('Garfield', 'Gato', 'Persa', '2013-09-12', 'macho', 'mediano', 'gato-mayor-garfield-persa.jpg', 'Gato veterano relajado que necesita cepillado y un sofá cómodo.'),
            $this->animal('Pelusa', 'Gato', 'Persa', '2015-03-30', 'hembra', 'pequeno', 'gato-mayor-pelusa-persa.jpg', 'Tranquila, dulce y acostumbrada a la vida en interiores.'),
            $this->animal('Lola', 'Gato', 'Persa', '2011-12-05', 'hembra', 'pequeno', 'gato-mayor-lola-persa.jpg', 'Abuelita de movimientos pausados que busca un hogar sin estrés.'),
            $this->animal('León', 'Gato', 'Maine Coon', '2014-07-22', 'macho', 'grande', 'gato-mayor-leon-maine.jpg', 'Gigante gentil, paciente y muy afectuoso para su edad.'),
            $this->animal('Máximo', 'Gato', 'Maine Coon', '2012-04-15', 'macho', 'grande', 'gato-mayor-maximo-maine.jpg', 'Gato mayor imponente, sereno y acostumbrado a la compañía.'),
            $this->animal('Romeo', 'Gato', 'Maine Coon', '2016-10-08', 'macho', 'grande', 'gato-mayor-romeo-maine.jpg', 'Maduro, sociable y feliz recibiendo atención tranquila.'),
            $this->animal('Tigre', 'Gato', 'Mestizo', '2010-06-25', 'macho', 'mediano', 'gato-mayor-tigre-mestizo.jpg', 'Abuelo atigrado, noble y muy agradecido con un lugar cálido.'),

            // 10 aves recién nacidas o en primeras semanas.
            $this->animal('Pía', 'Ave', 'Periquito Australiano', '2026-05-24', 'hembra', 'pequeno', 'ave-bebe-pia-periquito.jpg', 'Polluela en primeras semanas que requiere manejo cuidadoso y alimentación adecuada.'),
            $this->animal('Chispa', 'Ave', 'Periquito Australiano', '2026-05-17', 'macho', 'pequeno', 'ave-bebe-chispa-periquito.jpg', 'Periquito joven en crecimiento, activo y atento a los sonidos.'),
            $this->animal('Azul', 'Ave', 'Periquito Australiano', '2026-05-10', 'macho', 'pequeno', 'ave-bebe-azul-periquito.jpg', 'Polluelo emplumando, curioso y todavía dependiente de cuidados especiales.'),
            $this->animal('Pepa', 'Ave', 'Periquito Australiano', '2026-05-03', 'hembra', 'pequeno', 'ave-bebe-pepa-periquito.jpg', 'Periquita joven, delicada y en proceso de ganar independencia.'),
            $this->animal('Miel', 'Ave', 'Mestizo', '2026-05-31', 'hembra', 'pequeno', 'ave-bebe-miel-polluelo.jpg', 'Polluela recién nacida que necesita temperatura y alimentación controladas.'),
            $this->animal('Luz', 'Ave', 'Canario', '2026-05-28', 'macho', 'pequeno', 'ave-bebe-luz-canario.jpg', 'Polluelo de canario en nido, aún bajo cuidados intensivos.'),
            $this->animal('Kiko', 'Ave', 'Cacatúa Ninfa', '2026-05-14', 'macho', 'pequeno', 'ave-bebe-kiko-ninfa.jpg', 'Ninfa joven en etapa de emplume y socialización temprana.'),
            $this->animal('Lila', 'Ave', 'Cacatúa Ninfa', '2026-05-07', 'hembra', 'pequeno', 'ave-bebe-lila-ninfa.jpg', 'Polluela sociable que requiere experiencia con aves jóvenes.'),
            $this->animal('Coco', 'Ave', 'Cacatúa Ninfa', '2026-04-30', 'macho', 'pequeno', 'ave-bebe-coco-ninfa.jpg', 'Ninfa pequeña, despierta y en transición hacia alimento sólido.'),
            $this->animal('Nori', 'Ave', 'Cacatúa Ninfa', '2026-04-23', 'hembra', 'pequeno', 'ave-bebe-nori-ninfa.jpg', 'Ave joven, curiosa y acostumbrándose al manejo humano.'),

            // 5 aves mayores.
            $this->animal('Piolín', 'Ave', 'Periquito Australiano', '2017-05-10', 'macho', 'pequeno', 'ave-mayor-piolin-periquito.jpg', 'Periquito mayor tranquilo que disfruta compañía y rutinas estables.'),
            $this->animal('Sol', 'Ave', 'Canario', '2016-01-15', 'macho', 'pequeno', 'ave-mayor-sol-canario.jpg', 'Canario veterano de canto suave y temperamento calmado.'),
            $this->animal('Cora', 'Ave', 'Cacatúa Ninfa', '2010-09-30', 'hembra', 'pequeno', 'ave-mayor-cora-ninfa.jpg', 'Ninfa mayor sociable, acostumbrada a posarse cerca de las personas.'),
            $this->animal('Pepe', 'Ave', 'Cacatúa Ninfa', '2008-03-18', 'macho', 'pequeno', 'ave-mayor-pepe-ninfa.jpg', 'Ave veterana que prefiere un entorno calmado y predecible.'),
            $this->animal('Lula', 'Ave', 'Cacatúa Ninfa', '2011-12-12', 'hembra', 'pequeno', 'ave-mayor-lula-ninfa.jpg', 'Ninfa madura, dócil y muy apegada a sus cuidadores.'),

            // 4 conejos pequeños y 3 mayores.
            $this->animal('Mota', 'Conejo', 'Mestizo', '2026-04-14', 'hembra', 'pequeno', 'conejo-bebe-mota.jpg', 'Conejita pequeña, curiosa y acostumbrada a comer heno.'),
            $this->animal('Brisa', 'Conejo', 'Mestizo', '2026-03-14', 'hembra', 'pequeno', 'conejo-bebe-brisa.jpg', 'Joven tranquila que disfruta explorar en un espacio protegido.'),
            $this->animal('Tilo', 'Conejo', 'Mestizo', '2026-02-14', 'macho', 'pequeno', 'conejo-bebe-tilo.jpg', 'Conejito joven, activo y de pelaje suave que requiere cepillado.'),
            $this->animal('Miel', 'Conejo', 'Cabeza de León', '2026-01-14', 'macho', 'pequeno', 'conejo-bebe-miel-lionhead.jpg', 'Pequeño sociable, curioso y acostumbrado al contacto cuidadoso.'),
            $this->animal('Tambor', 'Conejo', 'Holland Lop', '2018-03-10', 'macho', 'pequeno', 'conejo-mayor-tambor-holland.jpg', 'Conejo mayor sereno que necesita piso cómodo y controles veterinarios.'),
            $this->animal('Copito', 'Conejo', 'Holland Lop', '2017-11-14', 'hembra', 'pequeno', 'conejo-mayor-copito-holland.jpg', 'Abuelita dócil, limpia y feliz con caricias suaves en la frente.'),
            $this->animal('Nieve', 'Conejo', 'Cabeza de León', '2019-08-05', 'hembra', 'pequeno', 'conejo-mayor-nieve-lionhead.jpg', 'Conejita madura, calmada y acostumbrada a una dieta rica en heno.'),

            // 4 hámsters pequeños y 3 mayores.
            $this->animal('Chispa', 'Hámster', 'Roborovski', '2026-05-14', 'hembra', 'pequeno', 'hamster-bebe-chispa-roborovski.jpg', 'Hámster muy joven, curiosa y activa durante la noche.'),
            $this->animal('Pepita', 'Hámster', 'Roborovski', '2026-04-28', 'macho', 'pequeno', 'hamster-bebe-pepita-roborovski.jpg', 'Pequeño explorador que necesita recinto individual y enriquecimiento.'),
            $this->animal('Mini', 'Hámster', 'Roborovski', '2026-05-21', 'hembra', 'pequeno', 'hamster-bebe-mini-roborovski.jpg', 'Hámster diminuta, rápida y todavía en etapa temprana de crecimiento.'),
            $this->animal('Kiko', 'Hámster', 'Roborovski', '2026-04-14', 'macho', 'pequeno', 'hamster-bebe-kiko-roborovski.jpg', 'Joven veloz y curioso que disfruta excavar y correr.'),
            $this->animal('Bolita', 'Hámster', 'Sirio', '2024-02-10', 'hembra', 'pequeno', 'hamster-mayor-bolita-sirio.jpg', 'Hámster mayor tranquila que requiere fácil acceso a comida y agua.'),
            $this->animal('Maní', 'Hámster', 'Sirio', '2023-06-14', 'macho', 'pequeno', 'hamster-mayor-mani-sirio.jpg', 'Abuelo nocturno de movimientos pausados y carácter dócil.'),
            $this->animal('Canela', 'Hámster', 'Roborovski', '2023-10-14', 'hembra', 'pequeno', 'hamster-mayor-canela-roborovski.jpg', 'Hámster veterana, pequeña y activa en periodos cortos.'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function animal(
        string $nombre,
        string $tipo,
        string $raza,
        string $fechaNacimiento,
        string $genero,
        string $tamanio,
        string $imagen,
        string $descripcion,
    ): array {
        return [
            'nombre' => $nombre,
            'tipo' => $tipo,
            'raza' => $raza,
            'fecha_nacimiento' => $fechaNacimiento,
            'genero' => $genero,
            'tamanio' => $tamanio,
            'imagen' => $imagen,
            'descripcion' => $descripcion,
        ];
    }
}
