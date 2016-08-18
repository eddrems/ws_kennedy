<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});


$app->get('/api/Matriculas', function () use ($app) {

    $matriculas = \Illuminate\Support\Facades\DB::table('enroll_matricula')
        ->join('rrhh_estudiante', 'enroll_matricula.estudiante_id', '=', 'rrhh_estudiante.estudiante_id')
        ->join('acad_grupo', 'enroll_matricula.grupo_id', '=', 'acad_grupo.grupo_id')
        ->join('rrhh_persona', 'rrhh_estudiante.persona_id', '=', 'rrhh_persona.persona_id')
        ->where('enroll_matricula.campo_migracion_externos', 0)
        ->orderBy(\DB::raw('CONCAT(rrhh_persona.apellidos, " ", rrhh_persona.nombres)'))
        ->select(
            'enroll_matricula.matricula_id',
            'enroll_matricula.estudiante_id',
            'rrhh_estudiante.facturacion_ci_ruc',
            'rrhh_estudiante.facturacion_nombre',
            'rrhh_estudiante.facturacion_telefono',
            'rrhh_estudiante.facturacion_direccion',
            'rrhh_persona.apellidos',
            'rrhh_persona.nombres',
            'acad_grupo.denominacion as grupo',
            'acad_grupo.codigo_externos',
            'acad_grupo.periodo_id'
        )
        ->get();

    $res_array = array();

    foreach ($matriculas as $matricula)
    {
        array_push($res_array, [
            'CodigoAlumno' =>  $matricula->estudiante_id,
            'CodigoMatricula' =>  $matricula->matricula_id,
            'Representante_TipoIdentificacion' =>  strlen($matricula->facturacion_ci_ruc) == 10 ? 1 : strlen($matricula->facturacion_ci_ruc) < 10 ? 3 : 2,
            'Representante_Identificacion' =>  $matricula->facturacion_ci_ruc,
            'Representante_Nombres' =>  $matricula->facturacion_nombre,
            'Representante_Apellidos' =>  '',
            'Representante_Telefono_Domicilio' =>  $matricula->facturacion_telefono,
            'Representante_Telefono_Trabajo' =>  null,
            'Representante_Telefono_Movil' =>  null,
            'Representante_Direccion' =>  $matricula->facturacion_direccion,
            'Alumno_Nombres' =>  $matricula->apellidos . ' ' . $matricula->nombres,
            'Curso_Denominacion' =>  $matricula->grupo,
            'Curso_Codigo' =>  $matricula->codigo_externos,
            'IDPeriodo_Academico' =>  $matricula->periodo_id,
            'Migrado' =>  false
        ]);

    }


    return $res_array;


});


$app->get('/api/Matriculas/{id}', function ($id) use ($app) {

    $matriculas = \Illuminate\Support\Facades\DB::table('enroll_matricula')
        ->join('rrhh_estudiante', 'enroll_matricula.estudiante_id', '=', 'rrhh_estudiante.estudiante_id')
        ->join('acad_grupo', 'enroll_matricula.grupo_id', '=', 'acad_grupo.grupo_id')
        ->join('rrhh_persona', 'rrhh_estudiante.persona_id', '=', 'rrhh_persona.persona_id')
        ->where('enroll_matricula.matricula_id', $id)
        ->orderBy(\DB::raw('CONCAT(rrhh_persona.apellidos, " ", rrhh_persona.nombres)'))
        ->select(
            'enroll_matricula.matricula_id',
            'enroll_matricula.estudiante_id',
            'rrhh_estudiante.facturacion_ci_ruc',
            'rrhh_estudiante.facturacion_nombre',
            'rrhh_estudiante.facturacion_telefono',
            'rrhh_estudiante.facturacion_direccion',
            'rrhh_persona.apellidos',
            'rrhh_persona.nombres',
            'acad_grupo.denominacion as grupo',
            'acad_grupo.codigo_externos',
            'acad_grupo.periodo_id'
        )
        ->get();

    $res_array = array();

    foreach ($matriculas as $matricula)
    {
        array_push($res_array, [
            'CodigoAlumno' =>  $matricula->estudiante_id,
            'CodigoMatricula' =>  $matricula->matricula_id,
            'Representante_TipoIdentificacion' =>  strlen($matricula->facturacion_ci_ruc) == 10 ? 1 : strlen($matricula->facturacion_ci_ruc) < 10 ? 3 : 2,
            'Representante_Identificacion' =>  $matricula->facturacion_ci_ruc,
            'Representante_Nombres' =>  $matricula->facturacion_nombre,
            'Representante_Apellidos' =>  '',
            'Representante_Telefono_Domicilio' =>  $matricula->facturacion_telefono,
            'Representante_Telefono_Trabajo' =>  null,
            'Representante_Telefono_Movil' =>  null,
            'Representante_Direccion' =>  $matricula->facturacion_direccion,
            'Alumno_Nombres' =>  $matricula->apellidos . ' ' . $matricula->nombres,
            'Curso_Denominacion' =>  $matricula->grupo,
            'Curso_Codigo' =>  $matricula->codigo_externos,
            'IDPeriodo_Academico' =>  $matricula->periodo_id,
            'Migrado' =>  false
        ]);

    }


    return $res_array[0];


});


$app->post('/api/Matriculas', function (Illuminate\Http\Request $request) use ($app) {

    \Illuminate\Support\Facades\DB::table('enroll_matricula')
        ->where('matricula_id', $request->input('id'))
        ->update(['campo_migracion_externos' => 1]);

    return response()->json([
        'resultado' => 'Ok'
    ], 200);
    

});
