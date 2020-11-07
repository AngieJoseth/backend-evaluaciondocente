<?php

namespace Database\Seeders;

use App\Models\Authentication\Module;
use App\Models\Authentication\Permission;
use App\Models\Authentication\Role;
use App\Models\Authentication\Route;
use App\Models\Authentication\Shortcut;
use App\Models\Authentication\System;
use App\Models\Authentication\User;
use App\Models\Ignug\Authority;
use App\Models\Ignug\AuthorityType;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\Institution;
use App\Models\Ignug\Setting;
use App\Models\Ignug\Teacher;
use Illuminate\Database\Seeder;
use App\Models\Ignug\State;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        // Estados de los registris para todas las tablas, para poder hacer borrados logicos
        State::factory()->create([
            'code' => '1',
            'name' => 'ACTIVE',
        ]);
        State::factory()->create([
            'code' => '2',
            'name' => 'INACTIVE',

        ]);
        State::factory()->create([
            'code' => '3',
            'name' => 'DELETED',
        ]);
        State::factory()->create([
            'code' => '4',
            'name' => 'LOCKED',
        ]);
        $status = Catalogue::factory()->create([
            'code' => Catalogue::STATUS_AVAILABLE,
            'name' => 'AVAILABLE',
            'type' => Catalogue::TYPE_STATUS,
        ]);
        // Sistemas de prueba
        $systemIgnug = System::factory()->create([
            'code' => System::IGNUG,
            'name' => 'IGNUG',
            'status_id' => $status->id,
        ]);
        $systemSerd = System::factory()->create([
            'code' => System::SERD,
            'name' => 'SERD',
            'status_id' => $status->id,
        ]);

        // Roles para el sistema IGNUG
        $roleIgnugAdmin = Role::factory()->create([
            'code' => Role::ROLE_IGNUG_ADMIN,
            'name' => 'ADMINISTRADOR',
            'uri' => Role::URI_DASHBOARD,
            'system_id' => $systemIgnug->id
        ]);
        $roleIgnugAgent = Role::factory()->create([
            'code' => Role::ROLE_IGNUG_AGENT,
            'name' => 'REPRESENTANTE LEGAL',
            'uri' => Role::URI_DASHBOARD,
            'system_id' => $systemIgnug->id
        ]);
        $roleIgnugStudent = Role::factory()->create([
            'code' => Role::ROLE_IGNUG_STUDENT,
            'name' => 'ESTUDIANTE',
            'uri' => Role::URI_DASHBOARD,
            'system_id' => $systemIgnug->id
        ]);
        $roleIgnugTeacher = Role::factory()->create([
            'code' => Role::ROLE_IGNUG_TEACHER,
            'name' => 'PROFESOR',
            'uri' => Role::URI_DASHBOARD,
            'system_id' => $systemIgnug->id
        ]);

        // Roles para el sistema SERD
        $roleSerdAdmin = Role::factory()->create([
            'code' => Role::ROLE_SERD_ADMIN,
            'name' => 'ADMIN',
            'uri' => Role::URI_DASHBOARD,
            'system_id' => $systemSerd->id
        ]);
        $roleSerdTeacher = Role::factory()->create([
            'code' => Role::ROLE_SERD_TEACHER,
            'name' => 'TEACHER',
            'uri' => Role::URI_DASHBOARD,
            'system_id' => $systemSerd->id
        ]);

        // Usuarios con sus respectivos roles para el sistema IGNUG
        $userAdmin = User::factory()->create(['username' => '1234567890', 'email' => 'cesar.tamayo0204@gmail.com']);
        $userAgent = User::factory()->create(['username' => '1234567891', 'email' => 'cesar.tamayo02@outlook.com']);
        $userStudent = User::factory()->create(['username' => '1234567892', 'email' => 'andrew.tamayo@uecavanis.edu.ec']);
        $userTeacher = User::factory()->create(['username' => '1234567893', 'email' => 'ctamayo@yavirac.edu.ec']);
        $userAdmin->roles()->attach($roleIgnugAdmin->id);
        $userAgent->roles()->attach($roleIgnugAgent->id);
        $userStudent->roles()->attach($roleIgnugStudent->id);
        $userTeacher->roles()->attach($roleIgnugTeacher->id);
        User::factory()->times(1000)->create();

        // asignacion de avatar
        $userAdmin->images()->create([
            'code' => $userAdmin->username,
            'name' => 'AVATAR',
            'uri' => 'avatars/' . $userAdmin->username . '.png',
            'extension' => 'png',
            'type' => User::TYPE_AVATARS,
            'state_id' => 1
        ]);

        // Los mismos usuarios pero con roles en el sistema SERD
        $userAdmin->roles()->attach($roleSerdAdmin->id);
        $userTeacher->roles()->attach($roleSerdTeacher->id);

        // Modulos para el sistema IGNUG
        $moduleGrades = Module::factory()->create([
            'code' => Module::IGNUG_GRADES,
            'name' => 'CALIFICACIONES',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleAttendances = Module::factory()->create([
            'code' => Module::IGNUG_ATTENDANCES,
            'name' => 'ASITENCIAS',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleHomeworks = Module::factory()->create([
            'code' => Module::IGNUG_HOMEWORKS,
            'name' => 'TAREAS',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleClass = Module::factory()->create([
            'code' => Module::IGNUG_CLASS,
            'name' => 'CLASE',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleSchudle = Module::factory()->create([
            'code' => Module::IGNUG_SCHUDLE,
            'name' => 'HORARIO',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $modulePortfolio = Module::factory()->create([
            'code' => Module::IGNUG_PORTFOLIO,
            'name' => 'PORTAFOLIO',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleIncidents = Module::factory()->create([
            'code' => Module::IGNUG_INCIDENTS,
            'name' => 'INCIDENTES',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleCalendar = Module::factory()->create([
            'code' => Module::IGNUG_CALENDAR,
            'name' => 'CALENDARIO',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleReports = Module::factory()->create([
            'code' => Module::IGNUG_REPORTS,
            'name' => 'REPORTES',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleEmail = Module::factory()->create([
            'code' => Module::IGNUG_EMAIL,
            'name' => 'CORREO ELECTRONICO',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleStudents = Module::factory()->create([
            'code' => Module::IGNUG_STUDENTS,
            'name' => 'ESTUDIANTES',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleTeachers = Module::factory()->create([
            'code' => Module::IGNUG_TEACHERS,
            'name' => 'PROFESORES',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleProfile = Module::factory()->create([
            'code' => Module::IGNUG_PROFILE,
            'name' => 'PERFIL',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);
        $moduleBilling = Module::factory()->create([
            'code' => Module::IGNUG_BILLING,
            'name' => 'FACTURACION',
            'system_id' => $systemIgnug->id,
            'status_id' => $status->id,
        ]);

        $menu = Catalogue::factory()->create([
            'code' => Route::MENU,
            'name' => 'MENU',
            'type' => Route::TYPE_MENUS
        ]);

        $megaMenu = Catalogue::factory()->create([
            'code' => Route::MEGA_MENU,
            'name' => 'MEGA MENU',
            'type' => Route::TYPE_MENUS
        ]);

        // Creacion de rutas para MENU para 7 modulos
        $routeGrade = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_GRADES,
            'module_id' => $moduleGrades->id,
            'type_id' => $menu->id,
            'label' => 'CALIFICACIONES',
            'order' => 1,
        ]);
        $routeHomework = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_HOMEWORKS,
            'module_id' => $moduleHomeworks->id,
            'type_id' => $menu->id,
            'label' => 'TAREAS',
            'order' => 2,
        ]);
        $routeClass = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_CLASS,
            'module_id' => $moduleClass->id,
            'type_id' => $menu->id,
            'label' => 'CLASES',
            'order' => 3,
        ]);
        $routePortfolio = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_PORTFOLIO,
            'module_id' => $modulePortfolio->id,
            'type_id' => $menu->id,
            'label' => 'PORTAFOLIO',
            'order' => 4,
        ]);
        $routeAttendance = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_ATTENDANCES,
            'module_id' => $moduleAttendances->id,
            'type_id' => $menu->id,
            'label' => 'ASISTENCIAS',
            'order' => 5,
        ]);
        $routeIncident = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_INCIDENTS,
            'module_id' => $moduleIncidents->id,
            'type_id' => $menu->id,
            'label' => 'INCIDENTES',
            'order' => 6,
        ]);
        $routeCalendar = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_CALENDAR,
            'module_id' => $moduleCalendar->id,
            'type_id' => $menu->id,
            'label' => 'CALENDARIO ACADEMICO',
            'order' => 7,
        ]);

        // Creacion de rutas para MEGA_MENU para 7 modulos

        $routeSchudle = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_SCHUDLE,
            'module_id' => $moduleSchudle->id,
            'type_id' => $menu->id,
            'label' => 'HORARIO',
            'order' => 8,
        ]);
        $routeTeacher = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_TEACHERS,
            'module_id' => $moduleTeachers->id,
            'type_id' => $menu->id,
            'label' => 'PROFESORES',
            'order' => 9,
        ]);
        $routeReport = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_REPORTS,
            'module_id' => $moduleReports->id,
            'type_id' => $megaMenu->id,
            'label' => 'REPORTES',
            'order' => 10,
        ]);
        $routeEmail = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_EMAIL,
            'module_id' => $moduleEmail->id,
            'type_id' => $menu->id,
            'label' => 'CORREO ELECTRONICO',
            'order' => 11,
        ]);
        $routeStudent = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_STUDENTS,
            'module_id' => $moduleStudents->id,
            'type_id' => $menu->id,
            'label' => 'ESTUDIANTES',
            'order' => 12,
        ]);
        $routeProfile = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_PROFILE,
            'module_id' => $moduleProfile->id,
            'type_id' => $menu->id,
            'label' => 'PERFIL',
            'order' => 13,
        ]);
        $routeBilling = Route::factory()->create([
            'uri' => Route::URI_MODULE_IGNUG_BILLING,
            'module_id' => $moduleBilling->id,
            'type_id' => $megaMenu->id,
            'label' => 'FACTURACION',
            'order' => 14,
        ]);

        // Asignacion de permisos a los diferentes roles
        $roles = Role::all();
        foreach ($roles as $role) {
            // Rutas para MENU
            $permission = Permission::factory()->create([
                'route_id' => $routeGrade->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeAttendance->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeHomework->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeClass->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeCalendar->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeIncident->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routePortfolio->id,
            ]);
            $permission->roles()->attach($role->id);
            // Rutas para MEGA_MENU
            $permission = Permission::factory()->create([
                'route_id' => $routeSchudle->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeTeacher->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeReport->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeEmail->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeStudent->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeProfile->id,
            ]);
            $permission->roles()->attach($role->id);
            $permission = Permission::factory()->create([
                'route_id' => $routeBilling->id,
            ]);
            $permission->roles()->attach($role->id);
        }

        // Institutos prueba
        $institution1 = Institution::factory()->create(['name' => 'SEBASTIAN DE BENALCAZAR', 'logo' => 'institutions/1.png']);
        $institution2 = Institution::factory()->create(['name' => 'QUITUMBE', 'logo' => 'institutions/2.png']);
        $institution3 = Institution::factory()->create(['name' => 'SAN FRANCISCO DE QUITO', 'logo' => 'institutions/3.png']);
        $institution4 = Institution::factory()->create(['name' => 'ANTONIO JOSE DE SUCRE', 'logo' => 'institutions/4.png']);
        $institution5 = Institution::factory()->create(['name' => 'BICENTENARIO', 'logo' => 'institutions/5.png']);
        $institution6 = Institution::factory()->create(['name' => 'CALDERON', 'logo' => 'institutions/6.png']);
        $institution7 = Institution::factory()->create(['name' => 'EUGENIO ESPEJO', 'logo' => 'institutions/7.png']);
        $institution8 = Institution::factory()->create(['name' => 'FERNANDEZ MADRID', 'logo' => 'institutions/8.png']);
        $institution9 = Institution::factory()->create(['name' => 'JULIO ENRIQUE MORENO', 'logo' => 'institutions/9.png']);
        $institution10 = Institution::factory()->create(['name' => 'OSWALDO LOMBEYDA', 'logo' => 'institutions/10.png']);

        // Asignacion de usuarios a institutos
        $userAdmin->institutions()->attach($institution1);
        $userAdmin->institutions()->attach($institution2);
        $userAdmin->institutions()->attach($institution3);
        $userAdmin->institutions()->attach($institution4);
        $userAdmin->institutions()->attach($institution5);
        $userAdmin->institutions()->attach($institution6);
        $userAdmin->institutions()->attach($institution7);
        $userAdmin->institutions()->attach($institution8);
        $userAdmin->institutions()->attach($institution9);
        $userAdmin->institutions()->attach($institution10);
        $userTeacher->institutions()->attach($institution1);
        $userTeacher->institutions()->attach($institution2);
        $userStudent->institutions()->attach($institution1);
        $userAgent->institutions()->attach($institution1);

        $i = 1;
        foreach (Route::all() as $route) {
            $shortcut = Shortcut::factory()->create([
                'user_id' => $userAdmin->id,
                'role_id' => $roleIgnugAdmin,
                'route_id' => $route->id,
            ]);
            $shortcut->images()->create([
                'code' => $shortcut->id,
                'name' => 'ADMIN',
                'uri' => 'shortcuts/shortcut' . $i . '.png',
                'extension' => 'png',
                'type' => Shortcut::TYPE,
                'state_id' => 1
            ]);
            $i++;
        }
        $i = 1;
        foreach (Route::all() as $route) {
            $shortcut = Shortcut::factory()->create([
                'user_id' => $userAgent->id,
                'role_id' => $roleIgnugAgent,
                'route_id' => $route->id,
            ]);
            $shortcut->images()->create([
                'code' => $shortcut->id,
                'name' => 'AGENT',
                'uri' => 'shortcuts/shortcut' . $i . '.png',
                'extension' => 'png',
                'type' => Shortcut::TYPE,
                'state_id' => 1
            ]);
            $i++;
        }
        $i = 1;
        foreach (Route::all() as $route) {
            $shortcut = Shortcut::factory()->create([
                'user_id' => $userStudent->id,
                'role_id' => $roleIgnugStudent,
                'route_id' => $route->id,
            ]);
            $shortcut->images()->create([
                'code' => $shortcut->id,
                'name' => 'STUDENT',
                'uri' => 'shortcuts/shortcut' . $i . '.png',
                'extension' => 'png',
                'type' => Shortcut::TYPE,
                'state_id' => 1
            ]);
            $i++;
        }
        $i = 1;
        foreach (Route::all() as $route) {
            $shortcut = Shortcut::factory()->create([
                'user_id' => $userTeacher->id,
                'role_id' => $roleIgnugTeacher,
                'route_id' => $route->id,
            ]);
            $shortcut->images()->create([
                'code' => $shortcut->id,
                'name' => 'TEACHER',
                'uri' => 'shortcuts/shortcut' . $i . '.png',
                'extension' => 'png',
                'type' => Shortcut::TYPE,
                'state_id' => 1
            ]);
            $i++;
        }
        Setting::factory()->create([
            'code' => 'logo',
            'name' => 'logo',
            'value' => '/settings/',
        ]);
    }
}
/*
            drop schema if exists authentication cascade;
            drop schema if exists attendance cascade;
            drop schema if exists ignug cascade;
            drop schema if exists job_board cascade;
            drop schema if exists web cascade;
            drop schema if exists teacher_eval cascade;
            drop schema if exists community cascade;
            drop schema if exists cecy cascade;

            create schema authentication;
            create schema attendance;
            create schema ignug;
            create schema job_board;
            create schema web;
            create schema teacher_eval;
            create schema community;
            create schema cecy;
 */
