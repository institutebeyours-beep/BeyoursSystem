export const manualContent = {
    // ========================================== //
    // SECCIONES GENERALES (todos los usuarios)
    // ========================================== //
    general: {
        title: '📚 Manual de Usuario',
        sections: [
            {
                id: 'introduccion',
                title: 'Introducción',
                icon: '📖',
                roles: ['all'],
                content: `
                    <h2>¿Qué es este sistema?</h2>
                    <p>El Sistema de Configuración de Calificaciones permite a los coordinadores académicos y docentes definir la estructura de evaluación de cada curso.</p>
                    <h3>Características principales:</h3>
                    <ul>
                        <li>✅ Gestión de tipos de componente</li>
                        <li>✅ Configuración de calificaciones por curso</li>
                        <li>✅ Clonación de configuraciones</li>
                        <li>✅ Validación automática de porcentajes</li>
                    </ul>
                `
            },
            {
                id: 'acceso',
                title: 'Acceso al Sistema',
                icon: '🔐',
                roles: ['all'],
                content: `
                    <h2>¿Cómo acceder?</h2>
                    <ol>
                        <li>Ve a la URL de la aplicación</li>
                        <li>Ingresa tu email y contraseña</li>
                        <li>Haz clic en "Iniciar Sesión"</li>
                    </ol>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mt-4">
                        <p><strong>⚠️ Nota:</strong> Si tienes 2FA activado, deberás ingresar el código de verificación.</p>
                    </div>
                `
            }
        ]
    },

    // ========================================== //
    // SECCIONES PARA ROL ACADÉMICO
    // ========================================== //
    academic: {
        title: '📋 Módulo Académico',
        sections: [
            {
                id: 'tipos-componente',
                title: 'Tipos de Componente',
                icon: '🏷️',
                roles: ['academico', 'admin', 'super-admin'],
                content: `
                    <h2>¿Qué son los Tipos de Componente?</h2>
                    <p>Los tipos de componente son categorías que permiten clasificar las evaluaciones.</p>
                    <h3>Tipos disponibles por defecto:</h3>
                    <ul>
                        <li>📝 Parcial</li>
                        <li>📊 Examen Final</li>
                        <li>📋 Asistencia</li>
                        <li>🚀 Proyecto</li>
                        <li>📚 Tarea</li>
                        <li>🧪 Quiz</li>
                    </ul>
                    <h3>¿Cómo crear un nuevo tipo?</h3>
                    <ol>
                        <li>Ve a "Tipos de Componente" en el menú lateral</li>
                        <li>Haz clic en "➕ Nuevo Tipo"</li>
                        <li>Completa el formulario</li>
                        <li>Haz clic en "Guardar"</li>
                    </ol>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mt-4">
                        <p><strong>💡 Tip:</strong> Puedes elegir entre múltiples iconos y colores para personalizar tus tipos.</p>
                    </div>
                `
            },
            {
                id: 'configuracion',
                title: 'Configurar Calificaciones',
                icon: '⚙️',
                roles: ['academico', 'admin', 'super-admin'],
                content: `
                    <h2>Configuración de Calificaciones por Curso</h2>
                    <h3>Paso 1: Seleccionar curso</h3>
                    <p>En el menú lateral, ve a "Configurar Notas" y selecciona el curso deseado.</p>
                    <h3>Paso 2: Crear configuración (si no existe)</h3>
                    <p>Si el curso no tiene configuración, haz clic en "Crear Configuración".</p>
                    <h3>Paso 3: Agregar componentes</h3>
                    <p>Haz clic en "➕ Agregar" y completa el formulario del componente.</p>
                    <h3>Paso 4: Validar porcentajes</h3>
                    <p>La suma de todos los porcentajes debe ser 100%. La barra de progreso te ayudará a visualizarlo.</p>
                    <h3>Paso 5: Guardar</h3>
                    <p>Haz clic en "💾 Guardar Configuración".</p>
                `
            },
            {
                id: 'clonacion',
                title: 'Clonación de Configuraciones',
                icon: '📋',
                roles: ['academico', 'admin', 'super-admin'],
                content: `
                    <h2>Clonar Configuración</h2>
                    <p>La clonación permite copiar la configuración de un curso a otro.</p>
                    <h3>¿Cómo clonar?</h3>
                    <ol>
                        <li>Ve a la configuración del curso origen</li>
                        <li>Haz clic en "📋 Clonar"</li>
                        <li>Selecciona el curso destino</li>
                        <li>Si el curso destino ya tiene configuración, marca "Reemplazar"</li>
                        <li>Haz clic en "Clonar"</li>
                    </ol>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg mt-4">
                        <p><strong>✅ Beneficio:</strong> Ahorra tiempo reutilizando configuraciones entre cursos similares.</p>
                    </div>
                `
            }
        ]
    },

    // ========================================== //
    // SECCIONES PARA ADMIN
    // ========================================== //
    admin: {
        title: '🔧 Módulo Administrativo',
        sections: [
            {
                id: 'gestion-tipos',
                title: 'Gestión Avanzada de Tipos',
                icon: '🔧',
                roles: ['admin', 'super-admin'],
                content: `
                    <h2>Gestión Avanzada de Tipos de Componente</h2>
                    <p>Como administrador, puedes:</p>
                    <ul>
                        <li>✅ Crear, editar y eliminar tipos</li>
                        <li>✅ Asignar colores e iconos personalizados</li>
                        <li>✅ Activar/desactivar tipos</li>
                        <li>✅ Ver qué tipos están en uso</li>
                    </ul>
                    <h3>¿Cómo gestionar tipos?</h3>
                    <ol>
                        <li>Ve a "Tipos de Componente" en el menú lateral</li>
                        <li>Usa los botones ✏️ y 🗑️ para editar o eliminar</li>
                        <li>Usa el checkbox "Activo" para activar/desactivar</li>
                    </ol>
                `
            }
        ]
    },

    // ========================================== //
    // SECCIÓN DE RESOLUCIÓN DE PROBLEMAS
    // ========================================== //
    troubleshooting: {
        title: '🔧 Resolución de Problemas',
        sections: [
            {
                id: 'problemas-comunes',
                title: 'Problemas Comunes',
                icon: '❓',
                roles: ['all'],
                content: `
                    <h2>Problemas y Soluciones</h2>
                    
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg mt-4 border border-red-200 dark:border-red-800">
                        <h3>❌ Error: "El porcentaje debe ser 100%"</h3>
                        <p><strong>Solución:</strong> Revisa la suma de todos los porcentajes y ajusta los valores hasta que sumen 100%.</p>
                    </div>
                    
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg mt-4 border border-red-200 dark:border-red-800">
                        <h3>❌ Error: "No se puede eliminar el tipo porque tiene componentes"</h3>
                        <p><strong>Solución:</strong> Primero elimina o cambia el tipo de los componentes asociados, luego intenta eliminar el tipo.</p>
                    </div>
                    
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg mt-4 border border-red-200 dark:border-red-800">
                        <h3>❌ Error: "El curso destino ya tiene una configuración"</h3>
                        <p><strong>Solución:</strong> Usa la opción "Reemplazar" para sobrescribir la configuración existente.</p>
                    </div>
                `
            }
        ]
    }
};