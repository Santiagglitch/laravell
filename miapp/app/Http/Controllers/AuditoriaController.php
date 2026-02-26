<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use App\Services\ProductosService;

class AuditoriaController extends Controller
{
    private ProductosService $productosService;

    public function __construct(ProductosService $productosService)
    {
        $this->productosService = $productosService;
    }

    public function index(Request $request)
    {
        $query = Auditoria::query();

        // =========================
        // FILTROS
        // =========================
        if ($request->tabla) {
            $query->where('Tabla_Afectada', $request->tabla);
        }

        if ($request->op) {
            $query->whereRaw("UPPER(TRIM(Operacion)) = ?", [strtoupper(trim($request->op))]);
        }

        if ($request->desde) {
            $query->whereDate('Fecha', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->whereDate('Fecha', '<=', $request->hasta);
        }

        // ✅ PAGINACIÓN (5 por página)
        $auditorias = $query
            ->orderByDesc('ID_Auditoria')
            ->paginate(5)
            ->withQueryString();

        // =========================
        // MAPAS (desde tu API Spring)
        // =========================
        $catalogos = $this->productosService->obtenerCatalogos();

        $mapCategorias = $catalogos['categorias'] ?? [];
        $mapEstados    = $catalogos['estados'] ?? [];
        $mapGamas      = $catalogos['gamas'] ?? [];

        $mapProductos = $this->obtenerMapaProductos();

        // =========================
        // TRANSFORMAR AUDITORÍAS
        // =========================
        $auditorias->getCollection()->transform(function ($a) use ($mapCategorias, $mapEstados, $mapGamas, $mapProductos) {

            $a->Tabla_Afectada = str_replace('_', ' ', (string)$a->Tabla_Afectada);

            $a->Datos_Antes   = $this->formatAuditText($a->Datos_Antes, $mapCategorias, $mapEstados, $mapGamas, $mapProductos);
            $a->Datos_Despues = $this->formatAuditText($a->Datos_Despues, $mapCategorias, $mapEstados, $mapGamas, $mapProductos);

            return $a;
        });

        $tablas = Auditoria::select('Tabla_Afectada')
            ->distinct()
            ->pluck('Tabla_Afectada');

        $stats = [
            'hoy'    => Auditoria::whereRaw('DATE(Fecha) = CURDATE()')->count(),
            'insert' => Auditoria::whereRaw("UPPER(TRIM(Operacion)) = 'INSERT'")->count(),
            'update' => Auditoria::whereRaw("UPPER(TRIM(Operacion)) = 'UPDATE'")->count(),
            'delete' => Auditoria::whereRaw("UPPER(TRIM(Operacion)) = 'DELETE'")->count(),
        ];

        return view('auditoria.index', compact('auditorias', 'tablas', 'stats'));
    }

    private function obtenerMapaProductos(): array
    {
        $map = [];

        $productos = $this->productosService->obtenerProductos();

        if (!is_array($productos)) return $map;

        foreach ($productos as $p) {
            if (!is_array($p)) continue;

            $id = isset($p['ID_Producto']) ? (string)$p['ID_Producto'] : '';
            $nom = isset($p['Nombre_Producto']) ? (string)$p['Nombre_Producto'] : '';

            if ($id !== '' && $nom !== '') {
                $map[$id] = $nom;
            }
        }

        return $map;
    }

    private function formatAuditText($text, array $mapCategorias, array $mapEstados, array $mapGamas, array $mapProductos): string
    {
        if (!$text || trim($text) === '' || trim($text) === '-') {
            return '—';
        }

        $items = array_filter(array_map('trim', explode(',', $text)));
        if (count($items) === 0) return '—';

        $html = '';

        foreach ($items as $item) {
            $parts = explode('=', $item, 2);

            if (count($parts) !== 2) {
                $html .= "<div class='field-line'>" . e($this->prettyText($item)) . "</div>";
                continue;
            }

            $rawKey = trim($parts[0]);
            $rawVal = trim($parts[1]);

            $label = $this->prettyLabel($rawKey);
            $value = $this->resolveValue($rawKey, $rawVal, $mapCategorias, $mapEstados, $mapGamas, $mapProductos);

            $html .= "<div class='field-line'><span class='field-label'>" . e($label) . ":</span> " . e($value) . "</div>";
        }

        return $html ?: '—';
    }

    private function prettyLabel(string $key): string
    {
        $map = [
            'ID_Categoria' => 'Categoría',
            'ID_Estado'    => 'Estado',
            'ID_Gama'      => 'Gama',
            'ID_Producto'  => 'Producto',
        ];

        if (isset($map[$key])) return $map[$key];

        return $this->prettyText($key);
    }

    private function resolveValue(string $key, string $value, array $mapCategorias, array $mapEstados, array $mapGamas, array $mapProductos): string
    {
        $v = trim($value);

        switch ($key) {
            case 'ID_Categoria':
                return $mapCategorias[$v] ?? $v;

            case 'ID_Estado':
                return $mapEstados[$v] ?? $v;

            case 'ID_Gama':
                return $mapGamas[$v] ?? $v;

            case 'ID_Producto':
                return $mapProductos[$v] ?? $v;

            default:
                return $v;
        }
    }

    private function prettyText(string $text): string
    {
        $text = str_replace('_', ' ', trim($text));
        $text = strtolower($text);
        return ucwords($text);
    }
}