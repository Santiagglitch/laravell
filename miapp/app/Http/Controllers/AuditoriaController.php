<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::query();

        // Filtros
        if ($request->tabla) {
            $query->where('Tabla_Afectada', $request->tabla);
        }

        if ($request->op) {
            $query->where('Operacion', $request->op);
        }

        if ($request->desde) {
            $query->whereDate('Fecha', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->whereDate('Fecha', '<=', $request->hasta);
        }

        $auditorias = $query
            ->orderByDesc('ID_Auditoria')
            ->paginate(15);

        $tablas = Auditoria::select('Tabla_Afectada')
            ->distinct()
            ->pluck('Tabla_Afectada');

        // KPIs corregidos
        $stats = [
            'hoy' => Auditoria::whereRaw('DATE(Fecha) = CURDATE()')->count(),
            'insert' => Auditoria::whereRaw("UPPER(TRIM(Operacion)) = 'INSERT'")->count(),
            'update' => Auditoria::whereRaw("UPPER(TRIM(Operacion)) = 'UPDATE'")->count(),
            'delete' => Auditoria::whereRaw("UPPER(TRIM(Operacion)) = 'DELETE'")->count(),
        ];

        return view('auditoria.index', compact('auditorias', 'tablas', 'stats'));
    }
}
