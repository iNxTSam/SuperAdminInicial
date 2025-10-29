<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    // Dashboard principal
    public function dashboard()
    {
        $stats = [
            'total_usuarios' => DB::table('usuarios')->count(),
            'total_vehiculos' => DB::table('vehiculos')->count(),
            'bahias_ocupadas' => DB::table('bahias')->where('ocupada', true)->count(),
            'total_bahias' => DB::table('bahias')->count(),
            'contratos_activos' => DB::table('contratos')
                ->join('estados_contrato', 'contratos.estado_id', '=', 'estados_contrato.id')
                ->where('estados_contrato.nombre', 'activo')
                ->count(),
            'ingresos_mes' => DB::table('recibos')
                ->whereMonth('created_at', now()->month)
                ->sum('valor')
        ];

        $ocupacion_por_tipo = DB::table('bahias')
            ->join('tipos_vehiculo', 'bahias.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select(
                'tipos_vehiculo.nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN ocupada = 1 THEN 1 ELSE 0 END) as ocupadas')
            )
            ->groupBy('tipos_vehiculo.id', 'tipos_vehiculo.nombre')
            ->get();

        return view('superadmin.dashboard', compact('stats', 'ocupacion_por_tipo'));
    }

    // GESTIÓN DE TARIFAS
    public function tarifas()
    {
        $tarifas = DB::table('tarifas')
            ->join('tipos_vehiculo', 'tarifas.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('tarifas.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->orderBy('tarifas.created_at', 'desc')
            ->get();

        $tipos_vehiculo = DB::table('tipos_vehiculo')->get();

        return view('superadmin.tarifas.index', compact('tarifas', 'tipos_vehiculo'));
    }

    public function storeTarifa(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|string|max:20',
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'valor' => 'required|numeric|min:0'
        ]);

        DB::table('tarifas')->insert([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'valor' => $request->valor,
            'activa' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('superadmin.tarifas')->with('success', 'Tarifa creada exitosamente');
    }

    public function updateTarifa(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|string|max:20',
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'valor' => 'required|numeric|min:0',
            'activa' => 'nullable|boolean'
        ]);

        DB::table('tarifas')->where('id', $id)->update([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'valor' => $request->valor,
            'activa' => $request->boolean('activa'),
            'updated_at' => now()
        ]);

        return redirect()->route('superadmin.tarifas')->with('success', 'Tarifa actualizada exitosamente');
    }


    // GESTIÓN DE BAHÍAS
    public function bahias()
    {
        $bahias = DB::table('bahias')
            ->join('tipos_vehiculo', 'bahias.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('bahias.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->orderBy('bahias.numero')
            ->get();

        $tipos_vehiculo = DB::table('tipos_vehiculo')->get();

        return view('superadmin.bahias.index', compact('bahias', 'tipos_vehiculo'));
    }

    public function storeBahia(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:bahias,numero',
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'capacidad_maxima' => 'required|integer|min:1',
            'ubicacion' => 'nullable|string|max:100'
        ]);

        DB::table('bahias')->insert([
            'numero' => $request->numero,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'capacidad_maxima' => $request->capacidad_maxima,
            'ubicacion' => $request->ubicacion,
            'ocupada' => false,
            'activa' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('superadmin.bahias')->with('success', 'Bahía creada exitosamente');
    }

    public function updateBahia(Request $request, $id)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:bahias,numero,' . $id,
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'capacidad_maxima' => 'required|integer|min:1',
            'ubicacion' => 'nullable|string|max:100',
            'activa' => 'boolean'
        ]);

        DB::table('bahias')->where('id', $id)->update([
            'numero' => $request->numero,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'capacidad_maxima' => $request->capacidad_maxima,
            'ubicacion' => $request->ubicacion,
            'activa' => $request->has('activa'),
            'updated_at' => now()
        ]);

        return redirect()->route('superadmin.bahias')->with('success', 'Bahía actualizada exitosamente');
    }

    public function deleteBahia(Request $request){

    }

    // REPORTES
    public function reportes()
    {
        return view('superadmin.reportes.index');
    }

    public function generarReporte(Request $request)
    {
        $request->validate([
            'tipo_reporte' => 'required|in:ocupacion,ingresos,contratos,usuarios',
            'formato' => 'required|in:csv,pdf',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
        ]);

        $data = $this->obtenerDatosReporte($request->tipo_reporte, $request->fecha_inicio, $request->fecha_fin);

        if ($request->formato === 'csv') {
            return $this->exportarCSV($data, $request->tipo_reporte);
        } else {
            return $this->exportarPDF($data, $request->tipo_reporte);
        }
    }

    private function obtenerDatosReporte($tipo, $fecha_inicio = null, $fecha_fin = null)
    {
        switch ($tipo) {
            case 'ocupacion':
                return DB::table('bahias')
                    ->join('tipos_vehiculo', 'bahias.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
                    ->leftJoin('tickets', function ($join) {
                        $join->on('bahias.id', '=', 'tickets.bahia_id')
                             ->where('tickets.estado', '=', 'activo');
                    })
                    ->leftJoin('vehiculos', 'tickets.vehiculo_id', '=', 'vehiculos.id')
                    ->select(
                        'bahias.numero',
                        'bahias.ubicacion',
                        'tipos_vehiculo.nombre as tipo_vehiculo',
                        'bahias.ocupada',
                        'vehiculos.placa'
                    )
                    ->get();

            case 'ingresos':
                $query = DB::table('recibos')
                    ->join('contratos', 'recibos.contrato_id', '=', 'contratos.id')
                    ->join('vehiculos', 'contratos.vehiculo_id', '=', 'vehiculos.id')
                    ->select(
                        'recibos.numero',
                        'recibos.fecha_emision',
                        'recibos.valor',
                        'vehiculos.placa',
                        'recibos.estado'
                    );

                if ($fecha_inicio) {
                    $query->whereDate('recibos.fecha_emision', '>=', $fecha_inicio);
                }
                if ($fecha_fin) {
                    $query->whereDate('recibos.fecha_emision', '<=', $fecha_fin);
                }

                return $query->get();

            case 'contratos':
                return DB::table('contratos')
                    ->join('vehiculos', 'contratos.vehiculo_id', '=', 'vehiculos.id')
                    ->join('usuarios', 'vehiculos.propietario_id', '=', 'usuarios.id')
                    ->join('tarifas', 'contratos.tarifa_id', '=', 'tarifas.id')
                    ->join('estados_contrato', 'contratos.estado_id', '=', 'estados_contrato.id')
                    ->select(
                        'vehiculos.placa',
                        'usuarios.nombre as propietario',
                        'contratos.fecha_inicio',
                        'contratos.fecha_fin',
                        'tarifas.nombre as tarifa',
                        'estados_contrato.nombre as estado',
                        'contratos.valor_total'
                    )
                    ->get();

            case 'usuarios':
                return DB::table('usuarios')
                    ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
                    ->select(
                        'usuarios.nombre',
                        'usuarios.cedula',
                        'usuarios.email',
                        'roles.nombre as rol',
                        'usuarios.activo',
                        'usuarios.created_at'
                    )
                    ->get();

            default:
                return collect();
        }
    }

    private function exportarCSV($data, $tipo_reporte)
    {
        $filename = "reporte_{$tipo_reporte}_" . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data, $tipo_reporte) {
            $file = fopen('php://output', 'w');

            // Encabezados según el tipo de reporte
            switch ($tipo_reporte) {
                case 'ocupacion':
                    fputcsv($file, ['Bahía', 'Ubicación', 'Tipo Vehículo', 'Ocupada', 'Placa']);
                    break;
                case 'ingresos':
                    fputcsv($file, ['Número Recibo', 'Fecha', 'Valor', 'Placa', 'Estado']);
                    break;
                case 'contratos':
                    fputcsv($file, ['Placa', 'Propietario', 'Fecha Inicio', 'Fecha Fin', 'Tarifa', 'Estado', 'Valor Total']);
                    break;
                case 'usuarios':
                    fputcsv($file, ['Nombre', 'Cédula', 'Email', 'Rol', 'Activo', 'Fecha Registro']);
                    break;
            }

            foreach ($data as $row) {
                fputcsv($file, (array) $row);
            }

            fclose($file);
        };

        // Registrar reporte generado
        DB::table('reportes')->insert([
            'nombre' => "Reporte de {$tipo_reporte}",
            'tipo' => $tipo_reporte,
            'formato' => 'CSV',
            'generado_por' => 1, 
            'created_at' => now()
        ]);

        return response()->stream($callback, 200, $headers);
    }

    private function exportarPDF($data, $tipo_reporte)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('superadmin.reportes.pdf', [
            'data' => $data,
            'tipo' => $tipo_reporte
        ]);

        $filename = "reporte_{$tipo_reporte}_" . date('Y-m-d_H-i-s') . '.pdf';

        // Registrar el reporte en la BD
        DB::table('reportes')->insert([
            'nombre' => "Reporte de {$tipo_reporte}",
            'tipo' => $tipo_reporte,
            'formato' => 'PDF',
            'generado_por' => DB::table('usuarios')->where('rol_id', 1)->value('id'),
            'created_at' => now()
        ]);

        return $pdf->download($filename);
    }


    // CONFIGURACIÓN GLOBAL
    public function configuracion()
    {
        $configuraciones = [
            'capacidad_total' => DB::table('bahias')->sum('capacidad_maxima'),
            'tipos_vehiculo' => DB::table('tipos_vehiculo')->get(),
            'tarifas_activas' => DB::table('tarifas')->where('activa', true)->count(),
            'usuarios_por_rol' => DB::table('usuarios')
                ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->select('roles.nombre', DB::raw('COUNT(*) as total'))
                ->groupBy('roles.id', 'roles.nombre')
                ->get()
        ];

        return view('superadmin.configuracion.index', compact('configuraciones'));
    }

    public function storeTipoVehiculo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipos_vehiculo,nombre',
            'descripcion' => 'nullable|string|max:255'
        ]);

        DB::table('tipos_vehiculo')->insert([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'created_at' => now()
        ]);

        return redirect()->route('superadmin.configuracion')->with('success', 'Tipo de vehículo creado exitosamente');
    }
}
