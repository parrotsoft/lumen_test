<?php
/** @var \Laravel\Lumen\Routing\Router $router */

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

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'directorio'], function() use ($router) {
    
    $router->get('/listar/{usuario_id}', function($usuario_id) {
        $result = app('db')->select("select * from contactos where usuario_id = $usuario_id");
        return response()->json($result);
    });

    $router->post('/', function(Request $request) {
        $nombres = $request->get('nombres');
        $apellidos = $request->get('apellidos');
        $correo = $request->get('correo');
        $celular = $request->get('celular');
        $usuario_id = $request->get('usuario_id');
    
        $result = app('db')->insert("insert into contactos(nombres, apellidos, correo, celular, usuario_id) values ('$nombres', '$apellidos', '$correo', '$celular', $usuario_id)");
        return response()->json(['mensaje' => 'Contacto creado']);
    });

    $router->put('/{id}', function(Request $request, $id) {
        $nombres = $request->get('nombres');
        $apellidos = $request->get('apellidos');
        $correo = $request->get('correo');
        $celular = $request->get('celular');
    
        $result = app('db')->update("update contactos set nombres='$nombres', apellidos='$apellidos', correo='$correo', celular='$celular' where id=$id");
        return response()->json(['mensaje' => 'Contacto actualizado']);
    });

    $router->get('/{id}', function($id) {
        $result = collect(app('db')->select("select * from contactos where id=$id"));
        return response()->json($result->first());
    });

    $router->delete('/{id}', function($id) {
        app('db')->delete("delete from contactos where id=$id");
        return response()->json(['mensaje' => 'Contacto eliminado']);
    });
});


$router->post('/login', function(Request $request) {
    try {
        $usuario = $request->get('usuario');
        $clave = $request->get('clave');
        $result = collect(app('db')->select("select * from usuarios where usuario = '$usuario' "))->first();
        if (app('hash')->check($clave, $result->clave)) {
            return response()->json([
                'id' => $result->id,
                'usuario' => $result->usuario
            ]);
        } else {
            return response()->json(['mensaje' => 'Datos errados...']);
        }
    }catch(\Exception $e) {
        return return response()->json($e);
    }
});

$router->post('/usuario', function(Request $request) {
    $usuario = $request->get('usuario');
    $clave = app('hash')->make($request->get('clave'));
    $result = app('db')->insert("insert into usuarios(usuario, clave) values ('$usuario', '$clave')");
    return response()->json(['mensaje' => 'Usuario creado']);
});