<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {        
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120
        ]);
    }

    public function register(Request $request){
        
        $validator = Validator::make($request->all(), [
            'tipodocumentoid' => 'required',
            'numerodocumento' => 'required',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'username' => 'required|string|min:6',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'acceso' => 'required',
            'rol' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        /*$user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));*/
        $user = new User();
        $user->tipodocumentoid = $request->tipodocumentoid;
        $user->numerodocumento = $request->numerodocumento;
        $user->nombres = $request->nombres;
        $user->apellidos = $request->apellidos;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->acceso = $request->acceso;
        $user->rol = $request->rol;

        $user->empresa_id = $request->has('empresa_id') ? $request->empresa_id : null;

        $user->save();

        return response()->json($user, 201);
    }

    public function index(Request $request) {
        try{
            $usuarios = User::all();

            return response()->json(
                [
                    'data' => $usuarios,
                    'status' => 200,
                    'message' => 'Usuarios obtenidos correctamente'
                ]
            );
        }catch(Exception $ex){
            return response()->json(
                [
                    'data' => [],
                    'status' => 401,
                    'error' => 'Error al ejecutar la operación'
                ]
            );
        }
    }

    public function update($id, Request $request) {
        $user = User::find($id);
        $validator = Validator::make($request->all(), [
            'tipodocumentoid' => 'required',
            'numerodocumento' => 'required',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'username' => 'required|string|min:6',
            'email' => 'required|string|email',
            'acceso' => 'required',
            'rol' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user->tipodocumentoid = $request->tipodocumentoid;
        $user->numerodocumento = $request->numerodocumento;
        $user->nombres = $request->nombres;
        $user->apellidos = $request->apellidos;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->acceso = $request->acceso;
        $user->rol = $request->rol;

        $user->update();

        return response()->json($user, 201);
    }
}
