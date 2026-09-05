<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    // Obtener todas las configuraciones (admin)
    public function index()
    {
        $settings = Setting::all()->groupBy('group')->map(function ($items) {
            return $items->mapWithKeys(function ($item) {
                return [$item->key => [
                    'value' => Setting::getValue($item->key),
                    'type' => $item->type,
                    'label' => $item->label,
                    'description' => $item->description,
                    'is_public' => $item->is_public,
                ]];
            });
        });

        return response()->json($settings);
    }

    // Obtener configuraciones públicas (todos los usuarios)
    public function public()
    {
        $settings = Setting::where('is_public', true)->get()->mapWithKeys(function ($item) {
            return [$item->key => [
                'value' => Setting::getValue($item->key),
                'type' => $item->type,
                'label' => $item->label,
                'description' => $item->description,
            ]];
        });

        return response()->json($settings);
    }

    // Actualizar una configuración (admin)
    public function update(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $value = $request->value;
        if ($setting->type === 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } elseif ($setting->type === 'number') {
            $value = (float) $value;
        }

        $setting->value = is_array($value) ? json_encode($value) : (string) $value;
        $setting->save();

        return response()->json([
            'message' => 'Configuración actualizada',
            'setting' => [
                'key' => $setting->key,
                'value' => Setting::getValue($setting->key),
            ],
        ]);
    }

    /**
     * ✅ ACTUALIZAR IMAGEN - VERSIÓN SIMPLIFICADA
     */
    public function updateImage(Request $request, $key)
    {
        try {
            $setting = Setting::where('key', $key)->where('type', 'image')->firstOrFail();

            $validator = Validator::make($request->all(), [
                'image' => 'required|file|max:2048', // 2MB máximo
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Validar extensiones permitidas
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico'];
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'message' => 'Formato no permitido',
                    'errors' => ['image' => ['Los formatos permitidos son: ' . implode(', ', $allowedExtensions)]]
                ], 422);
            }
            
            $imageData = file_get_contents($file->getRealPath());
            
            if ($key === 'favicon') {
                return $this->processFavicon($file, $setting);
            }
            
            return $this->processRegularImage($imageData, $extension, $setting, $key);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ PROCESAR FAVICON - UN SOLO TAMAÑO (32x32)
     */
    private function processFavicon($file, $setting)
    {
        try {
            // ✅ Usar PHP nativo (sin dependencias externas)
            $imageInfo = getimagesize($file->getRealPath());
            if (!$imageInfo) {
                throw new \Exception('No se pudo leer la imagen');
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // ✅ Crear imagen desde el archivo
            switch ($mimeType) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($file->getRealPath());
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($file->getRealPath());
                    imagealphablending($source, true);
                    imagesavealpha($source, true);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($file->getRealPath());
                    break;
                case 'image/webp':
                    $source = imagecreatefromwebp($file->getRealPath());
                    break;
                case 'image/x-icon':
                case 'image/vnd.microsoft.icon':
                    // Si es .ico, lo guardamos directamente
                    copy($file->getRealPath(), public_path('favicon.ico'));
                    $setting->value = '/favicon.ico';
                    $setting->save();
                    
                    return response()->json([
                        'message' => '✅ Favicon actualizado correctamente',
                        'image_path' => '/favicon.ico',
                    ]);
                default:
                    throw new \Exception('Formato no soportado: ' . $mimeType);
            }
            
            // ✅ Tamaño fijo: 32x32 (el más común)
            $targetSize = 32;
            
            // Crear imagen vacía con fondo transparente
            $destination = imagecreatetruecolor($targetSize, $targetSize);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 0, 0, 0, 127);
            imagefill($destination, 0, 0, $transparent);
            
            // Redimensionar manteniendo proporción
            $ratio = min($targetSize / $width, $targetSize / $height);
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);
            $x = ($targetSize - $newWidth) / 2;
            $y = ($targetSize - $newHeight) / 2;
            
            imagecopyresampled($destination, $source, $x, $y, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // ✅ Guardar como ICO (los navegadores aceptan PNG como .ico)
            $path = public_path('favicon.ico');
            imagepng($destination, $path);
            
            // ✅ Guardar también versión PNG por si acaso
            $pngPath = public_path('favicon.png');
            imagepng($destination, $pngPath);
            
            // ✅ Liberar memoria
            imagedestroy($destination);
            imagedestroy($source);
            
            // ✅ Actualizar BD
            $setting->value = '/favicon.ico';
            $setting->save();
            
            return response()->json([
                'message' => '✅ Favicon generado (32x32)',
                'image_path' => '/favicon.ico',
                'size' => $targetSize,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar el favicon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ PROCESAR IMAGEN REGULAR
     */
    private function processRegularImage($imageData, $extension, $setting, $key)
    {
        try {
            $imageName = $key . '_' . time() . '.' . $extension;
            $path = storage_path('app/public/settings');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            file_put_contents($path . '/' . $imageName, $imageData);
            
            // Eliminar imagen anterior
            if ($setting->value) {
                $oldImage = basename($setting->value);
                if ($oldImage && file_exists(storage_path('app/public/settings/' . $oldImage))) {
                    unlink(storage_path('app/public/settings/' . $oldImage));
                }
            }
            
            $setting->value = 'settings/' . $imageName;
            $setting->save();

            return response()->json([
                'message' => '✅ Imagen actualizada correctamente',
                'image_path' => 'settings/' . $imageName,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar la imagen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ ELIMINAR IMAGEN
     */
    public function removeImage($key)
    {
        $setting = Setting::where('key', $key)->where('type', 'image')->firstOrFail();

        if ($setting->value) {
            if ($key === 'favicon') {
                // Eliminar favicon.ico
                if (file_exists(public_path('favicon.ico'))) {
                    unlink(public_path('favicon.ico'));
                }
                if (file_exists(public_path('favicon.png'))) {
                    unlink(public_path('favicon.png'));
                }
            } else {
                // Para otras imágenes
                $oldImage = basename($setting->value);
                if ($oldImage && file_exists(storage_path('app/public/settings/' . $oldImage))) {
                    unlink(storage_path('app/public/settings/' . $oldImage));
                }
            }
            
            $setting->value = null;
            $setting->save();

            return response()->json([
                'message' => '✅ Imagen eliminada correctamente',
            ]);
        }

        return response()->json([
            'message' => 'No hay imagen para eliminar',
        ], 404);
    }
}