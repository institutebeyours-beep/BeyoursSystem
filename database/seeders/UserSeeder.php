<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================== //
        // 1. CREAR ROLES                            //
        // ========================================== //
        $this->createRoles();

        // ========================================== //
        // 2. CREAR USUARIOS ADMINISTRATIVOS         //
        // ========================================== //

        // 👑 SUPER-ADMIN
        $superAdmin = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Super Administrador',
            'lastname' => null,
            'second_lastname' => null,
            'birth_date' => null,
            'address' => null,
            'profile_image' => null,
            'email' => 'superadmin@beyours.com',
            'phone' => null,
            'cellphone' => null,
            'email_verified_at' => now(),
            'password' => bcrypt('beyours123'),
            'is_active' => true,
            'last_login_at' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        // 👨‍💼 ADMIN
        $admin = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Administrador',
            'lastname' => null,
            'second_lastname' => null,
            'birth_date' => null,
            'address' => null,
            'profile_image' => null,
            'email' => 'admin@beyours.com',
            'phone' => null,
            'cellphone' => null,
            'email_verified_at' => now(),
            'password' => bcrypt('beyours123'),
            'is_active' => true,
            'last_login_at' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $admin->assignRole('admin');

        // 📊 MANAGER
        $manager = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Gerente',
            'lastname' => null,
            'second_lastname' => null,
            'birth_date' => null,
            'address' => null,
            'profile_image' => null,
            'email' => 'manager@beyours.com',
            'phone' => null,
            'cellphone' => null,
            'email_verified_at' => now(),
            'password' => bcrypt('beyours123'),
            'is_active' => true,
            'last_login_at' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $manager->assignRole('manager');

        // 📚 ACADÉMICO
        $academico = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Académico',
            'lastname' => 'Docente',
            'second_lastname' => 'Universidad',
            'birth_date' => '1980-01-15',
            'address' => 'Calle Academia 123, Ciudad Universitaria',
            'profile_image' => null,
            'email' => 'academico@beyours.com',
            'phone' => '555-1234',
            'cellphone' => '555-5678',
            'email_verified_at' => now(),
            'password' => bcrypt('beyours123'),
            'is_active' => true,
            'last_login_at' => now(),
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $academico->assignRole('academico');

        // 💰 CASHIER
        $cashier = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Cajero',
            'lastname' => 'Financiero',
            'second_lastname' => 'Pagos',
            'birth_date' => '1985-05-20',
            'address' => 'Calle Comercio 456, Centro',
            'profile_image' => null,
            'email' => 'cashier@beyours.com',
            'phone' => '555-9012',
            'cellphone' => '555-3456',
            'email_verified_at' => now(),
            'password' => bcrypt('beyours123'),
            'is_active' => true,
            'last_login_at' => now(),
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cashier->assignRole('cashier');

        // 📋 ENROLLER
        $enroller = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Inscriptor',
            'lastname' => 'Administrativo',
            'second_lastname' => 'Matrículas',
            'birth_date' => '1982-08-10',
            'address' => 'Calle Registro 789, Universidad',
            'profile_image' => null,
            'email' => 'enroller@beyours.com',
            'phone' => '555-7890',
            'cellphone' => '555-2345',
            'email_verified_at' => now(),
            'password' => bcrypt('beyours123'),
            'is_active' => true,
            'last_login_at' => now(),
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $enroller->assignRole('enroller');

        // ========================================== //
        // 3. CREAR ESTUDIANTES (con acceso)        //
        // ========================================== //

        // 👨‍🎓 Estudiante 1: Juan Pérez
        $studentUser1 = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Juan Pérez',
            'lastname' => 'Pérez',
            'second_lastname' => 'González',
            'birth_date' => '2000-05-15',
            'address' => 'Calle Estudiantes 123, Ciudad Universitaria',
            'profile_image' => null,
            'email' => 'juan.perez@estudiante.com',
            'phone' => '555-1111',
            'cellphone' => '555-2222',
            'email_verified_at' => now(),
            'password' => bcrypt('estudiante123'),
            'is_active' => true,
            'last_login_at' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentUser1->assignRole('student');

        // Crear registro en students
        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'code' => 'EST-0001',
            'enrollment_date' => now(),
            'phone' => '555-1111',
            'address' => 'Calle Estudiantes 123, Ciudad Universitaria',
            'birth_date' => '2000-05-15',
            'guardian_name' => 'María González',
            'guardian_phone' => '555-3333',
            'status' => 'active',
            'created_by' => $superAdmin->id,
        ]);

        // 👨‍🎓 Estudiante 2: María Rodríguez
        $studentUser2 = User::create([
            'uuid' => Str::uuid(),
            'name' => 'María Rodríguez',
            'lastname' => 'Rodríguez',
            'second_lastname' => 'López',
            'birth_date' => '2001-08-20',
            'address' => 'Calle Universitaria 456, Ciudad',
            'profile_image' => null,
            'email' => 'maria.rodriguez@estudiante.com',
            'phone' => '555-4444',
            'cellphone' => '555-5555',
            'email_verified_at' => now(),
            'password' => bcrypt('estudiante123'),
            'is_active' => true,
            'last_login_at' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentUser2->assignRole('student');

        // Crear registro en students
        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'code' => 'EST-0002',
            'enrollment_date' => now(),
            'phone' => '555-4444',
            'address' => 'Calle Universitaria 456, Ciudad',
            'birth_date' => '2001-08-20',
            'guardian_name' => 'Carlos Rodríguez',
            'guardian_phone' => '555-6666',
            'status' => 'active',
            'created_by' => $superAdmin->id,
        ]);

        // 👨‍🎓 Estudiante 3: Carlos Gómez
        $studentUser3 = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Carlos Gómez',
            'lastname' => 'Gómez',
            'second_lastname' => 'Martínez',
            'birth_date' => '1999-12-10',
            'address' => 'Calle Estudios 789, Universidad',
            'profile_image' => null,
            'email' => 'carlos.gomez@estudiante.com',
            'phone' => '555-7777',
            'cellphone' => '555-8888',
            'email_verified_at' => now(),
            'password' => bcrypt('estudiante123'),
            'is_active' => true,
            'last_login_at' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_temp_token' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentUser3->assignRole('student');

        // Crear registro en students
        $student3 = Student::create([
            'user_id' => $studentUser3->id,
            'code' => 'EST-0003',
            'enrollment_date' => now(),
            'phone' => '555-7777',
            'address' => 'Calle Estudios 789, Universidad',
            'birth_date' => '1999-12-10',
            'guardian_name' => 'Ana Martínez',
            'guardian_phone' => '555-9999',
            'status' => 'active',
            'created_by' => $superAdmin->id,
        ]);

        // ========================================== //
        // 4. MENSAJE FINAL                         //
        // ========================================== //
        $this->command->info('✅ Usuarios creados exitosamente:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('   👑 superadmin@beyours.com  →  super-admin');
        $this->command->info('   👨‍💼 admin@beyours.com       →  admin');
        $this->command->info('   📊 manager@beyours.com      →  manager');
        $this->command->info('   📚 academico@beyours.com    →  academico');
        $this->command->info('   💰 cashier@beyours.com      →  cashier');
        $this->command->info('   📋 enroller@beyours.com     →  enroller');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('   👨‍🎓 juan.perez@estudiante.com   →  student');
        $this->command->info('   👨‍🎓 maria.rodriguez@estudiante.com →  student');
        $this->command->info('   👨‍🎓 carlos.gomez@estudiante.com  →  student');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('   🔑 Contraseña para admin: beyours123');
        $this->command->info('   🔑 Contraseña para estudiantes: estudiante123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * Crear roles si no existen
     */
    private function createRoles()
    {
        $roles = [
            'super-admin',
            'admin', 
            'manager',
            'academico',
            'cashier',
            'enroller',
            'student',
            'user'
        ];
        
        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web'
            ]);
        }
        
        $this->command->info('✅ Roles verificados/creados');
    }
}