<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Academic\DashboardController;
use App\Http\Controllers\Academic\CourseController;
use App\Http\Controllers\Academic\StudentController;
use App\Http\Controllers\Academic\GradeController;
use App\Http\Controllers\Academic\AttendanceController;
use App\Http\Controllers\Academic\ReportController;
use App\Http\Controllers\Academic\GradeConfigurationController;
use App\Http\Controllers\Academic\ComponentTypeController;
use App\Http\Controllers\Academic\SubjectController;
use App\Http\Controllers\Academic\CareerController;
use App\Http\Controllers\Academic\TemplateController;

/*
|--------------------------------------------------------------------------
| Academic Module Routes
|--------------------------------------------------------------------------
|
| Estas rutas se cargan con el prefijo /academic y los middlewares
| de autenticación, roles y permisos ya aplicados desde api.php
|
*/

// ========================================== //
// 📊 DASHBOARD
// ========================================== //
Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
    ->middleware('permission:academic_dashboard_view')
    ->name('dashboard.stats');

// ========================================== //
// 🎓 CARRERAS
// ========================================== //
Route::prefix('careers')->name('careers.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])
        ->middleware('permission:academic_careers_view')->name('index');
    
    Route::get('/all', [CareerController::class, 'all'])
        ->middleware('permission:academic_careers_view')->name('all');
    
    Route::post('/', [CareerController::class, 'store'])
        ->middleware('permission:academic_careers_create')->name('store');
    
    Route::post('/create-from-template', [CareerController::class, 'createFromTemplate'])
        ->middleware('permission:academic_careers_create')->name('create-from-template');
    
    Route::get('/{id}', [CareerController::class, 'show'])
        ->middleware('permission:academic_careers_view')->name('show');
    
    Route::put('/{id}', [CareerController::class, 'update'])
        ->middleware('permission:academic_careers_edit')->name('update');
    
    Route::delete('/{id}', [CareerController::class, 'destroy'])
        ->middleware('permission:academic_careers_delete')->name('destroy');
});

// ========================================== //
// 📋 PLANTILLAS
// ========================================== //
Route::prefix('templates')->name('templates.')->group(function () {
    Route::get('/', [TemplateController::class, 'index'])
        ->middleware('permission:academic_templates_view')->name('index');
    
    Route::get('/all', [TemplateController::class, 'all'])
        ->middleware('permission:academic_templates_view')->name('all');
    
    Route::post('/', [TemplateController::class, 'store'])
        ->middleware('permission:academic_templates_create')->name('store');
    
    Route::get('/{id}', [TemplateController::class, 'show'])
        ->middleware('permission:academic_templates_view')->name('show');
    
    Route::get('/{id}/preview', [TemplateController::class, 'preview'])
        ->middleware('permission:academic_templates_view')->name('preview');
    
    Route::post('/{id}/clone', [TemplateController::class, 'clone'])
        ->middleware('permission:academic_templates_create')->name('clone');
    
    Route::put('/{id}', [TemplateController::class, 'update'])
        ->middleware('permission:academic_templates_edit')->name('update');
    
    Route::delete('/{id}', [TemplateController::class, 'destroy'])
        ->middleware('permission:academic_templates_delete')->name('destroy');
});

// ========================================== //
// 📚 CURSOS
// ========================================== //
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])
        ->middleware('permission:academic_courses_view')->name('index');
    
    Route::post('/', [CourseController::class, 'store'])
        ->middleware('permission:academic_courses_create')->name('store');
    
    Route::get('/{id}', [CourseController::class, 'show'])
        ->middleware('permission:academic_courses_view')->name('show');
    
    Route::put('/{id}', [CourseController::class, 'update'])
        ->middleware('permission:academic_courses_edit')->name('update');
    
    Route::delete('/{id}', [CourseController::class, 'destroy'])
        ->middleware('permission:academic_courses_delete')->name('destroy');
    
    Route::get('/{courseId}/subjects', [CourseController::class, 'getSubjects'])
        ->middleware('permission:academic_courses_view')->name('subjects');
});

// ASIGNAR/REMOVER ASIGNATURAS
Route::post('/courses/assign-subject', [CourseController::class, 'assignSubject'])
    ->middleware('permission:academic_courses_edit')
    ->name('courses.assign-subject');

Route::delete('/courses/{courseId}/subjects/{subjectId}', [CourseController::class, 'removeSubject'])
    ->middleware('permission:academic_courses_edit')
    ->name('courses.remove-subject');

// ========================================== //
// 📖 ASIGNATURAS
// ========================================== //
Route::prefix('subjects')->name('subjects.')->group(function () {
    Route::get('/', [SubjectController::class, 'index'])
        ->middleware('permission:academic_subjects_view')->name('index');
    
    Route::get('/all', [SubjectController::class, 'all'])
        ->middleware('permission:academic_subjects_view')->name('all');
    
    Route::post('/', [SubjectController::class, 'store'])
        ->middleware('permission:academic_subjects_create')->name('store');
    
    Route::get('/{id}', [SubjectController::class, 'show'])
        ->middleware('permission:academic_subjects_view')->name('show');
    
    Route::put('/{id}', [SubjectController::class, 'update'])
        ->middleware('permission:academic_subjects_edit')->name('update');
    
    Route::delete('/{id}', [SubjectController::class, 'destroy'])
        ->middleware('permission:academic_subjects_delete')->name('destroy');
});

// ========================================== //
// 👨‍🎓 ESTUDIANTES
// ========================================== //
Route::prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])
        ->middleware('permission:academic_students_view')->name('index');
    
    Route::post('/', [StudentController::class, 'store'])
        ->middleware('permission:academic_students_create')->name('store');
    
    Route::get('/{id}', [StudentController::class, 'show'])
        ->middleware('permission:academic_students_view')->name('show');
    
    Route::put('/{id}', [StudentController::class, 'update'])
        ->middleware('permission:academic_students_edit')->name('update');
    
    Route::delete('/{id}', [StudentController::class, 'destroy'])
        ->middleware('permission:academic_students_delete')->name('destroy');
});

// ========================================== //
// ⚙️ CONFIGURACIÓN DE CALIFICACIONES
// ========================================== //
Route::prefix('grades/configuration')->name('grades.configuration.')->group(function () {
    Route::get('/subject/{subjectId}', [GradeConfigurationController::class, 'showBySubject'])
        ->middleware('permission:academic_grades_view')->name('subject');
    
    Route::get('/course/{courseId}', [GradeConfigurationController::class, 'showByCourse'])
        ->middleware('permission:academic_grades_view')->name('course');
    
    Route::post('/', [GradeConfigurationController::class, 'store'])
        ->middleware('permission:academic_grades_manage')->name('store');
    
    Route::put('/{id}', [GradeConfigurationController::class, 'update'])
        ->middleware('permission:academic_grades_manage')->name('update');
    
    Route::delete('/{id}', [GradeConfigurationController::class, 'destroy'])
        ->middleware('permission:academic_grades_manage')->name('destroy');
    
    Route::post('/clone', [GradeConfigurationController::class, 'clone'])
        ->middleware('permission:academic_grades_manage')->name('clone');
});

// ========================================== //
// 📋 TIPOS DE COMPONENTE
// ========================================== //
Route::prefix('component-types')->name('component-types.')->group(function () {
    Route::get('/', [ComponentTypeController::class, 'index'])
        ->middleware('permission:academic_component_types_view')->name('index');
    
    Route::get('/all', [ComponentTypeController::class, 'all'])
        ->middleware('permission:academic_component_types_view')->name('all');
    
    Route::post('/', [ComponentTypeController::class, 'store'])
        ->middleware('permission:academic_component_types_manage')->name('store');
    
    Route::get('/{id}', [ComponentTypeController::class, 'show'])
        ->middleware('permission:academic_component_types_view')->name('show');
    
    Route::put('/{id}', [ComponentTypeController::class, 'update'])
        ->middleware('permission:academic_component_types_manage')->name('update');
    
    Route::delete('/{id}', [ComponentTypeController::class, 'destroy'])
        ->middleware('permission:academic_component_types_manage')->name('destroy');
});

// ========================================== //
// 📊 CALIFICACIONES
// ========================================== //
Route::prefix('grades')->name('grades.')->group(function () {
    Route::get('/', [GradeController::class, 'index'])
        ->middleware('permission:academic_grades_view')->name('index');
    
    Route::post('/', [GradeController::class, 'store'])
        ->middleware('permission:academic_grades_manage')->name('store');
    
    Route::get('/course/{courseId}', [GradeController::class, 'getCourseGrades'])
        ->middleware('permission:academic_grades_view')->name('course');
    
    Route::get('/student/{studentId}', [GradeController::class, 'getStudentGrades'])
        ->middleware('permission:academic_grades_view')->name('student');
    
    Route::get('/reports', [GradeController::class, 'reports'])
        ->middleware('permission:academic_reports_view')->name('reports');
});

// ========================================== //
// 📊 REPORTES ACADÉMICOS
// ========================================== //
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/courses', [ReportController::class, 'courses'])
        ->middleware('permission:academic_reports_view')->name('courses');
    
    Route::get('/students', [ReportController::class, 'students'])
        ->middleware('permission:academic_reports_view')->name('students');
    
    Route::get('/general', [ReportController::class, 'general'])
        ->middleware('permission:academic_reports_view')->name('general');
    
    Route::get('/careers', [ReportController::class, 'careers'])
        ->middleware('permission:academic_reports_view')->name('careers');
});