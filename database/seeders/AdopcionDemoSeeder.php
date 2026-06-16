<?php

namespace Database\Seeders;

use App\Models\Adopcion;
use App\Models\CitaAdopcion;
use App\Models\CumplimientoRequisito;
use App\Models\Mascota;
use App\Models\RequisitoAdopcion;
use App\Models\RespuestaSolicitud;
use App\Models\Role;
use App\Models\SolicitudAdopcion;
use App\Models\TipoMascota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AdopcionDemoSeeder extends Seeder
{
    /** Tope: nada ocurre después de "hoy" (14 jun 2026). */
    private Carbon $tope;

    private int $contadorUsuario = 0;

    private int $contadorActa = 0;

    private Role $rolAdoptante;

    private User $admin;

    /** @var array<int, string> */
    private array $nombres = [
        'María Fernanda Vélez', 'Carlos Andrés Mendoza', 'Lucía Paredes Cox', 'Jorge Luis Cedeño',
        'Andrea Carolina Suárez', 'Diego Armando Reyes', 'Valentina Morales León', 'Sebastián Castro Ruiz',
        'Camila Andrade Ponce', 'Mateo Salazar Vera', 'Daniela Espinoza Bravo', 'Joaquín Tapia Mora',
        'Gabriela Rosales Núñez', 'Esteban Villacís Lara', 'Paula Jiménez Ortega', 'Ricardo Naranjo Coello',
        'Fernanda Acosta Pino', 'Tomás Guerrero Solís', 'Isabella Romero Cruz', 'Martín Aguirre Plaza',
        'Sofía Carrión Maldonado', 'Alejandro Benítez Loor', 'Renata Vinueza Haro', 'Nicolás Padilla Game',
        'Antonella Zambrano Rizzo', 'Bruno Cabrera Muñoz', 'Emilia Toledo Franco', 'Ignacio Robles Calle',
        'Victoria Mosquera Vaca', 'Samuel Idrovo Quinde', 'Florencia Ávila Donoso', 'Adrián Carpio Saá',
    ];

    /** @var array<int, string> */
    private array $motivos = [
        'Quiero darle un hogar lleno de amor y compañía.',
        'Mi familia está lista para recibir un nuevo integrante.',
        'Busco una mascota que acompañe a mis hijos a crecer.',
        'Tengo el tiempo y el espacio para cuidarlo como merece.',
        'Siempre quise adoptar en vez de comprar.',
        'Deseo brindarle una segunda oportunidad a un animalito.',
        'Vivo solo y quiero una compañía fiel y cariñosa.',
        'Mi mascota anterior falleció y quiero volver a tener una.',
    ];

    /** @var array<int, string> */
    private array $experiencias = [
        'He tenido perros toda mi vida.',
        'Crecí rodeada de gatos en casa de mis padres.',
        'Es mi primera mascota, pero me he informado bien.',
        'He cuidado mascotas de amigos y familiares.',
        'Trabajé como voluntario en un refugio.',
        null,
    ];

    /** @var array<int, string> Mensajes de aprobación (cortos, 4-5 líneas). */
    private array $respuestasAprobadas = [
        'El adoptante cumple con el perfil requerido y mostró buena disposición para un cuidado responsable. Continúa con la cita agendada para verificar los requisitos y conocer a tu nueva mascota.',
        'Solicitud aprobada: el hogar es adecuado y el compromiso es genuino. El siguiente paso es asistir a la cita programada para revisar los requisitos y coordinar la entrega.',
        'Aprobada por el comité de adopciones; las condiciones del hogar cumplen los criterios del refugio. Te esperamos en la cita para completar la verificación y dar el siguiente paso.',
    ];

    /** @var array<int, string> Mensajes de rechazo genéricos (cortos, 4-5 líneas). */
    private array $respuestasRechazadas = [
        'Tras revisar la solicitud, el refugio determinó que el solicitante no cumple por ahora con los requisitos necesarios para esta mascota. Agradecemos el interés y puede volver a postularse más adelante.',
        'El hogar propuesto no reúne en este momento las condiciones adecuadas para el cuidado de la mascota. Priorizamos el bienestar animal, por lo que la solicitud no pudo aprobarse en esta ocasión.',
        'El solicitante no cumple actualmente con los criterios definidos por el refugio para esta adopción. Invitamos a actualizar sus datos o a elegir otra mascota acorde a sus posibilidades.',
    ];

    public function run(): void
    {
        if (Adopcion::exists()) {
            $this->command?->warn('AdopcionDemoSeeder: ya existen adopciones; se omite para no duplicar.');

            return;
        }

        $this->tope = Carbon::create(2026, 6, 14, 18, 0);
        $this->rolAdoptante = Role::where('nombre', 'Adoptante')->firstOrFail();
        $this->admin = User::whereHas('rol', fn ($q) => $q->where('nombre', 'Administrador'))->firstOrFail();

        // Las mascotas deben haberse registrado ANTES de que lleguen los adoptantes:
        // se distribuyen a lo largo de mayo 2026 (antes del flujo de adopción).
        $this->registrarMascotasEnMayo();

        // 1) Adoptar ~20% de las mascotas disponibles de CADA tipo (flujo completo).
        $totalAdoptadas = 0;
        $sobrantes = collect();

        foreach (TipoMascota::orderBy('nombre')->get() as $tipo) {
            $disponibles = Mascota::with('raza')
                ->whereHas('raza', fn ($q) => $q->where('tipo_mascota_id', $tipo->id))
                ->where('estado', 'disponible')
                ->inRandomOrder()
                ->get();

            $cuantas = (int) round($disponibles->count() * 0.20);

            foreach ($disponibles->take($cuantas) as $mascota) {
                $this->adopcionCompleta($mascota);
                $totalAdoptadas++;
            }

            $sobrantes = $sobrantes->merge($disponibles->slice($cuantas));
        }

        // 2) Solicitudes en otros estados sobre mascotas que siguen disponibles.
        $sobrantes = $sobrantes->shuffle()->values();
        $i = 0;
        $plan = [
            ['aprobada_en_proceso', 3],
            ['pendiente', 5],
            ['rechazada', 4],
            ['cancelada', 3],
        ];

        foreach ($plan as [$estado, $cantidad]) {
            for ($n = 0; $n < $cantidad && $i < $sobrantes->count(); $n++, $i++) {
                $this->solicitudEnEstado($sobrantes[$i], $estado);
            }
        }

        $this->command?->info("AdopcionDemoSeeder: {$totalAdoptadas} adopciones completas + solicitudes en varios estados.");
    }

    /** Reparte el registro de las mascotas a lo largo de mayo 2026 (antes del flujo). */
    private function registrarMascotasEnMayo(): void
    {
        foreach (Mascota::all() as $mascota) {
            $registro = Carbon::create(2026, 5, 1)->addDays(rand(0, 27))->setTime(rand(8, 18), rand(0, 59));
            $mascota->forceFill(['created_at' => $registro, 'updated_at' => $registro])->saveQuietly();
        }
    }

    /** Flujo completo: solicitud → respuesta(aprobada) → cita(completada) → cumplimientos → adopción. */
    private function adopcionCompleta(Mascota $mascota): void
    {
        [$user, $registro] = $this->nuevoAdoptante();

        $fSolicitud = $this->cap($registro->copy()->addDays(rand(1, 3))->setTime(rand(9, 20), rand(0, 59)));
        $solicitud = $this->crearSolicitud($user, $mascota, 'aprobada', $fSolicitud);

        $fRespuesta = $this->cap($fSolicitud->copy()->addDays(rand(1, 2)));
        $this->crearRespuesta($solicitud, 'aprobada', $this->respuestasAprobadas[array_rand($this->respuestasAprobadas)], $fRespuesta);

        $fCita = $this->cap($fRespuesta->copy()->addDays(rand(1, 3)));
        $this->crearCita($solicitud, 'completada', $fCita);

        $this->crearCumplimientos($solicitud, $mascota, true, $fCita);

        $fAdopcion = $this->cap($fCita->copy()->addDays(rand(0, 2)));
        $this->contadorActa++;
        $adopcion = Adopcion::create([
            'solicitud_adopcion_id' => $solicitud->id,
            'fecha_adopcion' => $fAdopcion->toDateString(),
            'numero_acta' => sprintf('ACTA-2026-%04d', $this->contadorActa),
            'observaciones' => 'Entrega realizada con éxito. Mascota en buen estado y adoptante informado.',
        ]);
        $this->sello($adopcion, $fAdopcion);

        $mascota->update(['estado' => 'adoptada']);
    }

    private function solicitudEnEstado(Mascota $mascota, string $estado): void
    {
        [$user, $registro] = $this->nuevoAdoptante();
        $fSolicitud = $this->cap($registro->copy()->addDays(rand(1, 4))->setTime(rand(9, 20), rand(0, 59)));

        if ($estado === 'pendiente') {
            $this->crearSolicitud($user, $mascota, 'pendiente', $fSolicitud);
            $mascota->update(['estado' => 'en_proceso']);

            return;
        }

        if ($estado === 'cancelada') {
            $this->crearSolicitud($user, $mascota, 'cancelada', $fSolicitud);
            // El adoptante se retractó: la mascota vuelve a estar disponible.

            return;
        }

        if ($estado === 'rechazada') {
            $solicitud = $this->crearSolicitud($user, $mascota, 'rechazada', $fSolicitud);
            $fRespuesta = $this->cap($fSolicitud->copy()->addDays(rand(1, 3)));
            $this->crearRespuesta($solicitud, 'rechazada', $this->respuestasRechazadas[array_rand($this->respuestasRechazadas)], $fRespuesta);

            return;
        }

        // aprobada_en_proceso: aprobada con cita programada y requisitos aún por cumplir.
        $solicitud = $this->crearSolicitud($user, $mascota, 'aprobada', $fSolicitud);
        $fRespuesta = $this->cap($fSolicitud->copy()->addDays(rand(1, 2)));
        $this->crearRespuesta($solicitud, 'aprobada', $this->respuestasAprobadas[array_rand($this->respuestasAprobadas)], $fRespuesta);
        $fCita = $this->cap($fRespuesta->copy()->addDays(rand(1, 4)));
        $this->crearCita($solicitud, 'programada', $fCita);
        // Requisitos confirmados al aprobar (criterio para aprobar).
        $this->crearCumplimientos($solicitud, $mascota, true, $fCita);
        $mascota->update(['estado' => 'en_proceso']);
    }

    private function crearSolicitud(User $user, Mascota $mascota, string $estado, Carbon $fecha): SolicitudAdopcion
    {
        $solicitud = SolicitudAdopcion::create([
            'user_id' => $user->id,
            'mascota_id' => $mascota->id,
            'motivo' => $this->motivos[array_rand($this->motivos)],
            'experiencia' => $this->experiencias[array_rand($this->experiencias)],
            'tipo_vivienda' => ['casa', 'departamento', 'finca', 'otro'][array_rand(['casa', 'departamento', 'finca', 'otro'])],
            'vivienda_propia' => (bool) rand(0, 1),
            'tiene_mascotas' => (bool) rand(0, 1),
            'estado' => $estado,
            'fecha_solicitud' => $fecha,
        ]);
        $this->sello($solicitud, $fecha);

        return $solicitud;
    }

    private function crearRespuesta(SolicitudAdopcion $solicitud, string $resultado, string $texto, Carbon $fecha): void
    {
        $r = RespuestaSolicitud::create([
            'solicitud_adopcion_id' => $solicitud->id,
            'administrador_id' => $this->admin->id,
            'resultado' => $resultado,
            'respuesta' => $texto,
            'fecha_respuesta' => $fecha->toDateString(),
        ]);
        $this->sello($r, $fecha);
    }

    private function crearCita(SolicitudAdopcion $solicitud, string $estado, Carbon $fecha): void
    {
        $cita = CitaAdopcion::create([
            'solicitud_adopcion_id' => $solicitud->id,
            'fecha' => $fecha->toDateString(),
            'hora' => sprintf('%02d:%02d:00', rand(9, 16), [0, 30][rand(0, 1)]),
            'lugar' => 'Refugio Huellitas, Av. de las Américas, Guayaquil',
            'indicaciones' => 'Traer cédula y comprobante de domicilio. Llegar 10 minutos antes.',
            'estado' => $estado,
        ]);
        $this->sello($cita, $fecha);
    }

    private function crearCumplimientos(SolicitudAdopcion $solicitud, Mascota $mascota, bool $todoCumplido, Carbon $fecha): void
    {
        $requisitos = RequisitoAdopcion::where('tipo_mascota_id', $mascota->raza->tipo_mascota_id)
            ->where('estado', 'activo')
            ->get();

        foreach ($requisitos as $requisito) {
            if ($todoCumplido) {
                // Algún requisito no obligatorio puede quedar como "no aplica".
                $estado = (! $requisito->obligatorio && rand(0, 3) === 0) ? 'no_aplica' : 'cumplido';
                $revision = $fecha;
            } else {
                // Aún en proceso: obligatorios pendientes, alguno ya cumplido.
                $estado = rand(0, 1) ? 'cumplido' : 'pendiente';
                $revision = $estado === 'pendiente' ? null : $fecha;
            }

            $c = CumplimientoRequisito::create([
                'solicitud_adopcion_id' => $solicitud->id,
                'requisito_adopcion_id' => $requisito->id,
                'estado' => $estado,
                'observacion' => $estado === 'no_aplica' ? 'No aplica para este caso.' : null,
                'fecha_revision' => $revision?->toDateString(),
            ]);
            $this->sello($c, $revision ?? $fecha);
        }
    }

    /** Crea un adoptante registrado entre el 1 y el 3 de junio (y "verificado"/activo). */
    private function nuevoAdoptante(): array
    {
        $i = $this->contadorUsuario++;
        $nombre = $this->nombres[$i % count($this->nombres)];
        $registro = Carbon::create(2026, 6, 1)->addDays($i % 3)->setTime(8 + ($i % 10), ($i * 13) % 60);

        $user = User::create([
            'role_id' => $this->rolAdoptante->id,
            'name' => $nombre,
            'email' => Str::slug($nombre, '.').'.'.($i + 1).'@correo.com',
            'cedula' => sprintf('13%08d', $i + 1),
            'fecha_nacimiento' => Carbon::create(rand(1980, 2004), rand(1, 12), rand(1, 28))->toDateString(),
            'password' => 'adoptante123',
            'telefono' => '09'.sprintf('%08d', 10000000 + $i),
            'direccion' => ['Urdesa', 'Sauces', 'Alborada', 'Ceibos', 'Samborondón', 'Kennedy'][array_rand(['Urdesa', 'Sauces', 'Alborada', 'Ceibos', 'Samborondón', 'Kennedy'])].', Guayaquil',
        ]);
        $user->forceFill([
            'email_verified_at' => $registro,
            'created_at' => $registro,
            'updated_at' => $registro,
        ])->saveQuietly();

        return [$user, $registro];
    }

    /** Limita una fecha al tope (hoy) para que nada ocurra en el futuro. */
    private function cap(Carbon $fecha): Carbon
    {
        return $fecha->greaterThan($this->tope) ? $this->tope->copy() : $fecha;
    }

    /** Alinea created_at/updated_at del registro con su fecha lógica. */
    private function sello($modelo, Carbon $fecha): void
    {
        $modelo->forceFill(['created_at' => $fecha, 'updated_at' => $fecha])->saveQuietly();
    }
}
