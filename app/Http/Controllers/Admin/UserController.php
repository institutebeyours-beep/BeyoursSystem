<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Actions\LogUserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Listar usuarios con filtros
     */
    public function index(Request $request)
    {
        try {
            \Log::info('=== UserController@index FILTROS ===');
            \Log::info('Search:', [$request->search]);
            \Log::info('Role:', [$request->role]);
            \Log::info('Is_active:', [$request->is_active]);
            
            $query = User::with('roles');

            // 🔹 FILTRO POR NOMBRE O EMAIL
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('lastname', 'LIKE', "%{$search}%")
                      ->orWhere('second_lastname', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('cellphone', 'LIKE', "%{$search}%");
                });
                \Log::info('Aplicando filtro search: ' . $search);
            }

            // 🔹 FILTRO POR ROL
            if ($request->has('role') && !empty($request->role)) {
                $roleName = $request->role;
                $query->whereHas('roles', function($q) use ($roleName) {
                    $q->where('name', $roleName);
                });
                \Log::info('Aplicando filtro role: ' . $roleName);
            }

            // 🔹 FILTRO POR ESTADO
            if ($request->has('is_active') && $request->is_active !== '' && $request->is_active !== null) {
                $query->where('is_active', $request->is_active);
                \Log::info('Aplicando filtro is_active: ' . $request->is_active);
            }

            // 🔹 OBTENER USUARIOS FILTRADOS
            $users = $query->orderBy('created_at', 'desc')->get();
            
            \Log::info('Usuarios encontrados (FILTRADOS): ' . $users->count());

            // 🔹 OBTENER ROLES
            $roles = Role::all();

            // 🔹 FORMATEAR RESPUESTA
            return response()->json([
                'users' => [
                    'data' => $users->map(function($user) {
                        return [
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
                            'profile_image' => $user->profile_image,
                            'is_active' => (bool) $user->is_active,
                            'last_login_at' => $user->last_login_at,
                            'created_at' => $user->created_at,
                            'updated_at' => $user->updated_at,
                            // ✅ AHORA SOLO UN ARRAY DE STRINGS
                            'roles' => $user->roles->pluck('name')->toArray(),
                        ];
                    }),
                ],
                'roles' => $roles,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en UserController@index: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Obtener un usuario específico
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        
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
                'profile_image' => $user->profile_image,
                'is_active' => $user->is_active,
                // ✅ AHORA SOLO UN ARRAY DE STRINGS
                'roles' => $user->getRoleNames()->toArray(),
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
            ]
        ]);
    }

    /**
     * Crear nuevo usuario
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'second_lastname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar que no se pueda crear super-admin si no es super-admin
        if ($request->role === 'super-admin' && !auth()->user()->hasRole('super-admin')) {
            return response()->json([
                'message' => 'No tienes permisos para crear un super-admin'
            ], 403);
        }

        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'lastname' => $request->lastname,
            'second_lastname' => $request->second_lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'cellphone' => $request->cellphone,
            'birth_date' => $request->birth_date,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'is_active' => $request->is_active ?? true,
        ]);

        $user->assignRole($request->role);

        LogUserAction::adminAction(auth()->user(), 'user_created', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $request->role
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
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
                'profile_image' => $user->profile_image,
                'is_active' => $user->is_active,
                'roles' => $user->getRoleNames()->toArray(),
                'created_at' => $user->created_at,
            ],
        ], 201);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // No permitir editar a super-admin si no es super-admin
        if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
            return response()->json([
                'message' => 'No tienes permisos para editar un super-admin'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'second_lastname' => 'nullable|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar cambio de rol a super-admin
        if ($request->has('role') && $request->role === 'super-admin' && !auth()->user()->hasRole('super-admin')) {
            return response()->json([
                'message' => 'No tienes permisos para asignar el rol super-admin'
            ], 403);
        }

        $oldData = [
            'name' => $user->name,
            'lastname' => $user->lastname,
            'second_lastname' => $user->second_lastname,
            'email' => $user->email,
            'phone' => $user->phone,
            'cellphone' => $user->cellphone,
            'birth_date' => $user->birth_date,
            'address' => $user->address,
            'is_active' => $user->is_active,
            'role' => $user->getRoleNames()->first(),
        ];

        // Actualizar campos
        $fields = ['name', 'lastname', 'second_lastname', 'email', 'phone', 'cellphone', 'birth_date', 'address'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $user->$field = $request->$field;
            }
        }

        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('is_active')) {
            $user->is_active = $request->is_active;
        }

        $user->save();

        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        LogUserAction::adminAction(auth()->user(), 'user_updated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'old' => $oldData,
            'new' => [
                'name' => $user->name,
                'lastname' => $user->lastname,
                'second_lastname' => $user->second_lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'cellphone' => $user->cellphone,
                'birth_date' => $user->birth_date,
                'address' => $user->address,
                'is_active' => $user->is_active,
                'role' => $user->getRoleNames()->first(),
            ]
        ]);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
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
                'profile_image' => $user->profile_image,
                'is_active' => $user->is_active,
                'roles' => $user->getRoleNames()->toArray(),
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
            ],
        ]);
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // No permitir eliminar a super-admin si no es super-admin
        if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar un super-admin'
            ], 403);
        }

        // No permitir eliminar a sí mismo
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'No puedes eliminar tu propia cuenta'
            ], 403);
        }

        $email = $user->email;
        $user->delete();

        LogUserAction::adminAction(auth()->user(), 'user_deleted', [
            'user_id' => $id,
            'email' => $email
        ]);

        return response()->json([
            'message' => 'Usuario eliminado exitosamente',
        ]);
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function changePassword(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // No permitir cambiar contraseña a super-admin si no es super-admin
            if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
                return response()->json([
                    'message' => 'No tienes permisos para cambiar la contraseña de un super-admin'
                ], 403);
            }

            // 🔹 VALIDACIÓN
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'message' => 'Contraseña actualizada exitosamente',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en changePassword: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}