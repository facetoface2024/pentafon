<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class SistemaQrController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->get();

        return Inertia::render('Dashboard', [
            'clientes' => $clientes->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'apellido_paterno' => $cliente->apellido_paterno,
                    'apellido_materno' => $cliente->apellido_materno,
                    'correo' => $cliente->correo,
                    'nombre_completo' => $cliente->nombre_completo,
                    'qr_path' => $cliente->qr_path,
                    'qr_token' => $cliente->qr_token,
                    'qr_activo' => $cliente->qr_activo,
                    'qr_url' => $cliente->qr_url,
                    'created_at' => $cliente->created_at->format('d/m/Y H:i'),
                ];
            })
        ]);
    }

    public function descargarPlantilla()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Nombre');
        $sheet->setCellValue('B1', 'Apellido Paterno');
        $sheet->setCellValue('C1', 'Apellido Materno');
        $sheet->setCellValue('D1', 'Correo');

        // Estilos para encabezados
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        // Autoajustar columnas
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'plantilla_clientes_' . date('Y-m-d_H-i-s') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);

        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function subirExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('archivo');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Omitir la primera fila (encabezados)
            array_shift($rows);

            $clientesCreados = 0;
            $errores = [];

            foreach ($rows as $index => $row) {
                // Validar que la fila no esté vacía
                if (empty(array_filter($row))) {
                    continue;
                }

                $nombre = trim($row[0] ?? '');
                $apellidoPaterno = trim($row[1] ?? '');
                $apellidoMaterno = trim($row[2] ?? '');
                $correo = trim($row[3] ?? '');

                // Validaciones básicas - solo nombre y correo son obligatorios
                if (empty($nombre) || empty($correo)) {
                    $errores[] = "Fila " . ($index + 2) . ": Nombre y correo son obligatorios";
                    continue;
                }

                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $errores[] = "Fila " . ($index + 2) . ": Correo inválido: {$correo}";
                    continue;
                }

                // Verificar si el correo ya existe
                if (Cliente::where('correo', $correo)->exists()) {
                    $errores[] = "Fila " . ($index + 2) . ": El correo {$correo} ya existe";
                    continue;
                }

                                                try {
                    $cliente = Cliente::create([
                        'nombre' => $nombre,
                        'apellido_paterno' => $apellidoPaterno ?: null,
                        'apellido_materno' => $apellidoMaterno ?: null,
                        'correo' => $correo,
                    ]);

                    // Preparar ruta para QR (se generará en el frontend)
                    $correoLimpio = str_replace(['@', '.', ' '], ['_', '_', '_'], $cliente->correo);
                    $qrPath = 'qr_codes/' . $correoLimpio . '.png';
                    $cliente->update(['qr_path' => $qrPath]);

                    $clientesCreados++;
                } catch (\Exception $e) {
                    $errores[] = "Fila " . ($index + 2) . ": Error al crear cliente: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Se importaron {$clientesCreados} clientes correctamente. Generando códigos QR...",
                'errores' => $errores,
                'clientes_creados' => $clientesCreados,
                'generar_qr_automatico' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 422);
        }
    }

    public function generarQr($id)
    {
        $cliente = Cliente::findOrFail($id);

        try {
            // Solo marcamos que el QR está listo
            // El QR se generará en el frontend usando JavaScript
            $correoLimpio = str_replace(['@', '.', ' '], ['_', '_', '_'], $cliente->correo);
            $qrPath = 'qr_codes/' . $correoLimpio . '.png';

            // Actualizar cliente con la ruta donde se guardará el QR
            $cliente->update([
                'qr_path' => $qrPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Código QR preparado exitosamente',
                'qr_url' => $cliente->qr_url,
                'cliente' => [
                    'id' => $cliente->id,
                    'qr_url' => $cliente->qr_url,
                    'qr_path' => $qrPath,
                    'nombre_completo' => $cliente->nombre_completo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al preparar QR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saludoCliente($token)
    {
        $cliente = Cliente::where('qr_token', $token)
                         ->where('qr_activo', true)
                         ->first();

        if (!$cliente) {
            abort(404, 'Cliente no encontrado o QR inactivo');
        }

        return Inertia::render('Cliente/Saludo', [
            'cliente' => [
                'nombre_completo' => $cliente->nombre_completo,
                'nombre' => $cliente->nombre,
                'apellido_paterno' => $cliente->apellido_paterno,
                'apellido_materno' => $cliente->apellido_materno,
                'correo' => $cliente->correo,
            ]
        ]);
    }

    public function scanner()
    {
        return Inertia::render('Cliente/Scanner');
    }

    public function toggleQrEstado($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update([
            'qr_activo' => !$cliente->qr_activo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del QR actualizado',
            'qr_activo' => $cliente->qr_activo
        ]);
    }

    public function eliminarCliente($id)
    {
        $cliente = Cliente::findOrFail($id);

        // Eliminar archivo QR si existe
        if ($cliente->qr_path && Storage::disk('public')->exists($cliente->qr_path)) {
            Storage::disk('public')->delete($cliente->qr_path);
        }

        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado correctamente'
        ]);
    }

        public function guardarQr(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'qr_content' => 'required|string',
            'qr_path' => 'required|string'
        ]);

        try {
            $cliente = Cliente::findOrFail($request->cliente_id);

            // Crear directorio si no existe
            $directorioQr = dirname($request->qr_path);
            if (!Storage::disk('public')->exists($directorioQr)) {
                Storage::disk('public')->makeDirectory($directorioQr);
            }

                        // Limpiar el base64 (remover espacios y caracteres no válidos)
            $cleanBase64 = preg_replace('/[^A-Za-z0-9+\/=]/', '', $request->qr_content);

            // Guardar el PNG en storage (decodificar base64)
            $pngData = base64_decode($cleanBase64);

            // Verificar que la decodificación fue exitosa
            if ($pngData === false || strlen($pngData) < 100) {
                Log::error('Error decodificando base64 QR', [
                    'cliente_id' => $request->cliente_id,
                    'qr_path' => $request->qr_path,
                    'base64_length' => strlen($request->qr_content),
                    'decoded_length' => $pngData ? strlen($pngData) : 0
                ]);
                throw new \Exception('Error al decodificar el contenido base64 del QR');
            }

            Storage::disk('public')->put($request->qr_path, $pngData);

            // Verificar que el archivo se guardó correctamente
            if (!Storage::disk('public')->exists($request->qr_path)) {
                throw new \Exception('El archivo QR no se pudo guardar correctamente');
            }

            return response()->json([
                'success' => true,
                'message' => 'QR guardado exitosamente',
                'qr_path' => Storage::url($request->qr_path),
                'file_exists' => Storage::disk('public')->exists($request->qr_path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar QR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportarClientes()
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = ['ID', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'Correo', 'QR Activo', 'Fecha Creación'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . '1', $header);
        }

        // Datos
        foreach ($clientes as $index => $cliente) {
            $row = $index + 2;
            $sheet->setCellValue('A' . $row, $cliente->id);
            $sheet->setCellValue('B' . $row, $cliente->nombre);
            $sheet->setCellValue('C' . $row, $cliente->apellido_paterno);
            $sheet->setCellValue('D' . $row, $cliente->apellido_materno);
            $sheet->setCellValue('E' . $row, $cliente->correo);
            $sheet->setCellValue('F' . $row, $cliente->qr_activo ? 'Activo' : 'Inactivo');
            $sheet->setCellValue('G' . $row, $cliente->created_at->format('d/m/Y H:i'));
        }

        // Estilos
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        // Autoajustar columnas
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'clientes_' . date('Y-m-d_H-i-s') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);

        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

            public function generarTodosQr()
    {
        $clientes = Cliente::where('qr_activo', true)->get();

        return response()->json([
            'success' => true,
            'clientes' => $clientes->map(function ($cliente) {
                $tieneArchivoFisico = $cliente->qr_path && Storage::disk('public')->exists($cliente->qr_path);
                return [
                    'id' => $cliente->id,
                    'correo' => $cliente->correo,
                    'qr_url' => $cliente->qr_url,
                    'qr_path' => $cliente->qr_path,
                    'tiene_archivo_fisico' => $tieneArchivoFisico
                ];
            })
        ]);
    }

    public function clientesConQr()
    {
        $clientesActivos = Cliente::where('qr_activo', true)->get();
        $clientesConQr = $clientesActivos->filter(function ($cliente) {
            return $cliente->qr_path && Storage::disk('public')->exists($cliente->qr_path);
        });

        $clientesSinQr = $clientesActivos->count() - $clientesConQr->count();

        return response()->json([
            'success' => true,
            'clientes' => $clientesConQr->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre_completo' => $cliente->nombre_completo,
                    'correo' => $cliente->correo,
                    'qr_path' => $cliente->qr_path,
                    'created_at' => $cliente->created_at->format('d/m/Y H:i'),
                    'created_at_timestamp' => $cliente->created_at->timestamp,
                ];
            })->values(),
            'clientes_sin_qr' => $clientesSinQr
        ]);
    }

    public function descargarQrSeleccionados(Request $request)
    {
        $request->validate([
            'clientes_ids' => 'required|array|min:1',
            'clientes_ids.*' => 'exists:clientes,id'
        ]);

        $clientes = Cliente::whereIn('id', $request->clientes_ids)
                          ->where('qr_activo', true)
                          ->get();

        if ($clientes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron clientes válidos'
            ], 400);
        }

        try {
            // Crear archivo ZIP temporal
            $zipPath = tempnam(sys_get_temp_dir(), 'qr_codes_seleccionados') . '.zip';
            $zip = new ZipArchive();

            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo crear el archivo ZIP'
                ], 500);
            }

            $agregados = 0;
            $clientesSinQr = [];

            foreach ($clientes as $cliente) {
                // Verificar si el cliente tiene QR guardado
                if ($cliente->qr_path && Storage::disk('public')->exists($cliente->qr_path)) {
                    // Usar el QR existente
                    $qrContent = Storage::disk('public')->get($cliente->qr_path);

                    // Nombre del archivo usando el correo
                    $nombreArchivo = $cliente->correo . '.png';

                    // Agregar al ZIP
                    $zip->addFromString($nombreArchivo, $qrContent);
                    $agregados++;
                } else {
                    // Agregar a la lista de clientes sin QR
                    $clientesSinQr[] = $cliente->correo;
                }
            }

            $zip->close();

            // Si no se agregó ningún QR, devolver error
            if ($agregados === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los clientes seleccionados no tienen códigos QR generados.',
                    'clientes_sin_qr' => $clientesSinQr
                ], 400);
            }

            $filename = 'codigos_qr_seleccionados_' . date('Y-m-d_H-i-s') . '.zip';

            return response()->download($zipPath, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar ZIP: ' . $e->getMessage()
            ], 500);
        }
    }

    public function descargarTodosQr()
    {
        $clientes = Cliente::where('qr_activo', true)->get();

        if ($clientes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay clientes activos para descargar'
            ], 400);
        }

        try {
            // Crear archivo ZIP temporal
            $zipPath = tempnam(sys_get_temp_dir(), 'qr_codes') . '.zip';
            $zip = new ZipArchive();

            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo crear el archivo ZIP'
                ], 500);
            }

            $agregados = 0;
            $clientesSinQr = [];

            foreach ($clientes as $cliente) {
                // Verificar si el cliente tiene QR guardado
                if ($cliente->qr_path && Storage::disk('public')->exists($cliente->qr_path)) {
                    // Usar el QR existente
                    $qrContent = Storage::disk('public')->get($cliente->qr_path);

                                    // Nombre del archivo usando el correo
                $nombreArchivo = $cliente->correo . '.png';

                    // Agregar al ZIP
                    $zip->addFromString($nombreArchivo, $qrContent);
                    $agregados++;
                } else {
                    // Agregar a la lista de clientes sin QR
                    $clientesSinQr[] = $cliente->correo;
                }
            }

            $zip->close();

            // Si no se agregó ningún QR, devolver error
            if ($agregados === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron códigos QR generados. Por favor, asegúrate de que los clientes tengan QR.',
                    'clientes_sin_qr' => $clientesSinQr
                ], 400);
            }

            $filename = 'codigos_qr_' . date('Y-m-d_H-i-s') . '.zip';

            return response()->download($zipPath, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar ZIP: ' . $e->getMessage()
            ], 500);
        }
    }



}
