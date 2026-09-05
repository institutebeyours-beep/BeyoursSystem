<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Obtener el perfil del usuario autenticado
     */
    public function show()
    {
        $user = Auth::user()->load('roles');
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'second_lastname' => $user->second_lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'cellphone' => $user->cellphone,
                'birth_date' => $user->birth_date,
                'address' => $user->address,
                'profile_image' => $user->profile_image, // ✅ Path relativo
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'two_factor_secret' => $user->two_factor_secret,
                'roles' => $user->roles->pluck('name')->toArray(),
            ]
        ]);
    }

    /**
     * Actualizar el perfil del usuario
     */
    public function update(Request $request)
    {
        \Log::info('📝 ProfileController@update - Datos recibidos:', $request->all());

        $user = Auth::user();
        
        \Log::info('👤 Usuario autenticado:', ['id' => $user->id, 'email' => $user->email]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'second_lastname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            \Log::error('❌ Errores de validación:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->second_lastname = $request->second_lastname;
        $user->phone = $request->phone;
        $user->cellphone = $request->cellphone;
        $user->birth_date = $request->birth_date;
        $user->address = $request->address;
        $user->save();

        \Log::info('✅ Usuario actualizado:', $user->fresh()->toArray());

        $user->refresh();
        $user->load('roles');

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'second_lastname' => $user->second_lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'cellphone' => $user->cellphone,
                'birth_date' => $user->birth_date,
                'address' => $user->address,
                'profile_image' => $user->profile_image, // ✅ Path relativo
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'two_factor_secret' => $user->two_factor_secret,
                'roles' => $user->roles->pluck('name')->toArray(),
            ]
        ]);
    }

    /**
     * Subir imagen de perfil
     */
    public function uploadImage(Request $request)
    {
        \Log::info('📝 ProfileController@uploadImage - Subiendo imagen');

        $request->validate([
            'image' => 'required|string'
        ]);

        $user = Auth::user();

        // Decodificar imagen base64
        $imageData = $request->image;
        $imageData = preg_replace('/^data:image\/(jpeg|png|gif|webp);base64,/', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        
        // Generar nombre único
        $imageName = 'profile_' . $user->id . '_' . time() . '.png';
        
        // Crear directorio si no existe
        $path = storage_path('app/public/profiles');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        
        // Guardar la imagen
        file_put_contents($path . '/' . $imageName, base64_decode($imageData));
        
        // Eliminar imagen anterior si existe
        if ($user->profile_image) {
            // Extraer solo el nombre del archivo del path
            $oldImage = basename($user->profile_image);
            if ($oldImage && file_exists(storage_path('app/public/profiles/' . $oldImage))) {
                unlink(storage_path('app/public/profiles/' . $oldImage));
            }
        }
        
        // ✅ GUARDAR SOLO EL PATH RELATIVO (NO LA URL COMPLETA)
        $imagePath = 'profiles/' . $imageName;
        $user->profile_image = $imagePath;
        $user->save();

        \Log::info('✅ Imagen subida:', ['image_path' => $imagePath]);

        $user->refresh();
        $user->load('roles');

        return response()->json([
            'message' => 'Imagen subida correctamente',
            'image_path' => $imagePath, // ✅ Path relativo
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'second_lastname' => $user->second_lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'cellphone' => $user->cellphone,
                'birth_date' => $user->birth_date,
                'address' => $user->address,
                'profile_image' => $user->profile_image, // ✅ Path relativo
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'two_factor_secret' => $user->two_factor_secret,
                'roles' => $user->roles->pluck('name')->toArray(),
            ]
        ]);
    }

    /**
     * Eliminar imagen de perfil
     */
    public function removeImage()
    {
        \Log::info('📝 ProfileController@removeImage - Eliminando imagen');

        $user = Auth::user();

        if ($user->profile_image) {
            // Extraer solo el nombre del archivo
            $oldImage = basename($user->profile_image);
            if ($oldImage && file_exists(storage_path('app/public/profiles/' . $oldImage))) {
                unlink(storage_path('app/public/profiles/' . $oldImage));
            }
            
            $user->profile_image = null;
            $user->save();

            \Log::info('✅ Imagen eliminada para usuario:', ['user_id' => $user->id]);
            
            return response()->json([
                'message' => 'Imagen eliminada correctamente'
            ]);
        }

        return response()->json([
            'message' => 'No hay imagen para eliminar'
        ], 404);
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        \Log::info('📝 ProfileController@changePassword - Cambiando contraseña');

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        \Log::info('✅ Contraseña actualizada para usuario:', ['user_id' => $user->id]);

        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }
}