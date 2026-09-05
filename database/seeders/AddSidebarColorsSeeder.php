<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class AddSidebarColorsSeeder extends Seeder
{
    public function run(): void
    {
        // Agregar color del sidebar
        Setting::setValue('sidebar_color', '#1a202c', 'string', 'appearance', 'Color del sidebar', 'Color de fondo del menú lateral', true);
        
        // Agregar color del texto del sidebar
        Setting::setValue('sidebar_text_color', '#ffffff', 'string', 'appearance', 'Color del texto del sidebar', 'Color del texto en el menú lateral', true);
        
        // Agregar nombre de la aplicación (si no existe)
        Setting::setValue('app_name', 'Beyours', 'string', 'appearance', 'Nombre de la aplicación', 'Nombre que aparece en el sistema', true);
        
        $this->command->info('✅ Configuraciones de colores del sidebar agregadas');
    }
}