<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\TcontenidoController;
use App\Http\Controllers\ContenidoController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\PlanificacionController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\CursoEstudianteController;
use App\Http\Controllers\CodigoLibrosController;
use App\Http\Controllers\RespuestaController;
use App\Http\Controllers\ProvinciasController;
use App\Http\Controllers\LibrosController;
use App\Http\Controllers\CodigosController;
use App\Http\Controllers\VerificacionController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\PeriodosController;
use App\Http\Controllers\PeriodosInstitucionController;
use App\Http\Controllers\TemaController;
use App\Http\Controllers\UnidadController;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/infouser', [AuthController::class, 'infouser'])->middleware('auth:sanctum');
Route::apiResource('area',AreaController::class);
Route::apiResource('asignatura',AsignaturaController::class);
//api para traer los libros
Route::get('traerLibros',[AsignaturaController::class,'traerLibros']);
Route::post('guardarAsignaturasDocente',[AsignaturaController::class,'guardarAsignaturasDocente']);
Route::get('AsignaturasDocente',[AsignaturaController::class,'AsignaturasDocente']);
//para eliminar la asignaturas del docente
Route::get('e_asignaturasDocentes',[AsignaturaController::class,'e_asignaturasDocentes']);
Route::apiResource('tcontenido',TcontenidoController::class);
Route::apiResource('infocontenido',ContenidoController::class);
Route::apiResource('institucion',InstitucionController::class);
Route::apiResource('usuario',UsuarioController::class);
Route::apiResource('grupo',GrupoController::class);
Route::apiResource('estudiante',EstudianteController::class);
Route::apiResource('libro',LibroController::class);

//===============APIS ACTIVIDAD==========================
Route::apiResource('actividad',ActividadController::class);
Route::get('actividadEliminar',[ActividadController::class,'actividadEliminar']);
Route::get('actividades_libros_unidad/{id}',[ActividadController::class,'actividades_libros_unidad']);
Route::post('guardarActividadCurso',[ActividadController::class,'guardarActividadCurso']);
Route::get('ActividadesAsignadas',[ActividadController::class,'ActividadesAsignadas']);
Route::get('actividadEstudiantePendiente',[ActividadController::class,'actividadEstudiantePendiente']);
Route::get('ActividadEstudianteRealizada',[ActividadController::class,'ActividadEstudianteRealizada']);
Route::post('editarCalificacionActividad',[ActividadController::class,'editarCalificacionActividad']);
Route::post('eliminarActividadEstudiante',[ActividadController::class,'eliminarActividadEstudiante']);

//============FIN DE APIS DE ACTIVIDAD

//===============APIS PLANIFICACION========================================

Route::apiResource('planificacion',PlanificacionController::class);
Route::post('eliminarPlanificacion',[PlanificacionController::class,'eliminarPlanificacion']);
//============FIN DE APIS DE PLANIFICACION==============================================

//===============APIS CURSOS========================================
Route::apiResource('curso',CursoController::class);
Route::get('cursosAdmin',[CursoController::class,'cursosAdmin']);
Route::get('cursosArchivados',[CursoController::class,'cursosArchivados']);

//============FIN DE APIS DE CURSOS==============================================

//===============APIS UNIDADES========================================
Route::resource('unidad',UnidadController::class);
Route::get('unidadesX_Libro/{id}', [UnidadController::class,'unidadesX_Libro']);
Route::get('buscar_unidad_libro',[UnidadController::class,'buscar_unidad_libro']);
Route::get('eliminar_unidad_libro',[UnidadController::class,'eliminar_unidad_libro']);

//============FIN DE APIS DE UNIDADES==============================================

//===============APIS TEMAS========================================
Route::resource('tema',TemaController::class);
Route::post('eliminar_tema',[TemaController::class,'eliminar_tema']);
Route::post('temasUnidad', [ActividadController::class,'temasUnidad']);

//============FIN DE APIS DE TEMAS==============================================

Route::apiResource('archivo',ArchivoController::class);
Route::post('dragArchivo',[ArchivoController::class,'dragArchivo']);
Route::apiResource('tarea',TareaController::class);
Route::apiResource('cursoEstudiante',CursoEstudianteController::class);
Route::resource('codigoLibro',CodigoLibrosController::class);
Route::apiResource('respuesta',RespuestaController::class);
Route::get('alumnos',[CursoEstudianteController::class,'alumnos']);
//=========APIS TAREAS=============================================================


Route::post('editarCalificacionTarea',[TareaController::class,'editarCalificacionTarea']);
Route::get('tareaEstudiantePendiente',[TareaController::class,'tareaEstudiantePendiente']);
Route::get('tareaEstudianteRealizada',[TareaController::class, 'tareaEstudianteRealizada']);
Route::post('eliminarTareaEstudiante',[TareaController::class,'eliminarTareaEstudiante']);
//===========FIN APIS DE TAREAS=============================================

Route::get('password',[UsuarioController::class,'password']);
Route::get('codigo',[CursoController::class , 'codigo']);
Route::get('provincias',[ProvinciasController::class, 'provincias']);
//apis para milton 
Route::resource('/libros', LibrosController::class);
//api para traer los libros
Route::post('generarLibros',[LibrosController::class,'generarLibros']);
//api para traer los codigos
Route::resource('codigos', CodigosController::class);

//api para mostrar por id los codigos
Route::get('codigolist/{codigo}',[CodigosController::class, 'listarcodigo']);
//api para guardar el codigo del docente
Route::post('savecodigo',[CodigosController::class,'savecodigo']);
//============APIS DE VERIFICACIONES===========================================
//api para ver los codigos de verificacion del docente
Route::post('vercodigos',[CodigosController::class,'vercodigos']);
//api para ver los codigos de las verificaciones de un contrato
Route::get('verificacionContrato/{contrato}',[CodigosController::class,'verificacionContrato']);
Route::get('verificacionContrato/{contrato}/{numero}',[ContratoController::class,'contratoVerificacion']);
Route::get('informacionContrato',[CodigosController::class,'informacionContrato']);
//api para ver el administrador dentro de la lista de codigos
Route::post('veradmin',[VerificacionController::class,'verAdmin']);
//api para traer los docentes
Route::get('traerDocentes',[VerificacionController::class,'traerDocentes']);
//api para cambiar el estado al codigo -> api de milton
Route::post('changeEstadoCode',[VerificacionController::class,'changeEstadoCode']);
Route::get('changeEstadoCode/{codigo}',[VerificacionController::class,'estadoCodigo']);
//===========FIN DE APIS DE LAS VERIFICACIONES===============================
//api para mostrar las temporadas
Route::resource('temporadas',TemporadaController::class);
//api para mostrar los contratos
Route::resource('contratos',ContratoController::class);
Route::get('contratosXPeriodo',[ContratoController::class,'contratosXPeriodo']);
Route::post('contrato-desactivar',[ContratoController::class,'contratodesactivar']);
//Eliminar el contrato de BD
Route::post('contratoEliminarBd',[ContratoController::class,'contratoEliminarBd']);
//para para el listado de los contratos del docente
Route::get('contratodocente',[ContratoController::class,'contratodocente']);
//para filtrar los docentes por institucion
Route::get('traerprofesor',[ContratoController::class,'traerprofesor']);
//para guardar o actualizar el contrato
Route::post('contrato-guardar',[ContratoController::class,'contratoGuardar']);
//api para generar listado de  verificacion del docente
Route::post('verificacionlistado',[ContratoController::class,'verificacionlistado']);
//api para generar informacion de  verificacion del contrato
Route::get('verificacionesInfo',[ContratoController::class,'verificacionesInfo']);
//api para generar la verificacion para el docente
Route::post('contratosgenerar',[ContratoController::class,'generarContrato']);
//api para la verificacion de los docentes
Route::resource('verificarcodigos',VerificacionController::class);
//api para import codigos a la verificacion
Route::post('codigosImportar',[VerificacionController::class,'codigosImportar']);

//menu administrador
Route::get('listaMenu',[MenuController::class,'listaMenu' ]);
Route::get('eliminaMenu/{id}',[MenuController::class,'eliminaMenu']);
//periodos
Route::apiResource('periodos',PeriodosController::class);
Route::get('eliminaPeriodo/{id}',[PeriodosController::class,'eliminaPeriodo']);
Route::post('periodo/desactivar',[PeriodosController::class,'desactivar']);
Route::post('periodo/activar',[PeriodosController::class,'activar']);
Route::get('institucionTraerPeriodo',[PeriodosController::class,'institucionTraerPeriodo']);
//periodos has institucion
Route::apiResource('periodosInstitucion',PeriodosInstitucionController::class);
Route::post('verifica_periodo',[PeriodosInstitucionController::class,'verificaPeriodoInstitucion']);
Route::get('periodosXInstitucion/{id}',[PeriodosInstitucionController::class,'periodosXInstitucion']);
Route::get('eliminarPeriodosInstitucion/{id}',[PeriodosInstitucionController::class,'eliminarPeriodosXInstitucion']);
    
Route::apiResource('menu', MenuController::class);
Route::get('/get_menu', [MenuController::class, 'get_menu']);
Route::post('/add_editMenu', [MenuController::class, 'add_editMenu']);
Route::apiResource('roles', RolesController::class);
Route::apiResource('user', UserController::class);

Route::get('libroDocente',[LibroController::class, 'docente']);
Route::get('listaAdministrador',[UsuarioController::class, 'admins']);
Route::get('buscaCodigo/{id}',[CodigosController::class, 'findCode']);
//APIS CONFIGURACION
Route::get('statusPeriodo',[MenuController::class,'statusPeriodo']);
//api para  todos los codigos por contrato
Route::get('showCodigos/{contrato}',[ContratoController::class,'showCodigos']);


