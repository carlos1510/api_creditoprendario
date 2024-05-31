<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

date_default_timezone_set('America/Lima');

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        //$credentials = request(['username', 'password']);
        $credentials = $request->only('username', 'password');

        try{
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid credentials'], 401);
            }
    
            //return $this->respondWithToken($token);
        }catch(JWTException $ex){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //return response()->json($token);

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
        return response()->json([
            'ok' => true,
            'status' => 200,
        ], 200);
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
            'ok' => true,
            'status' => 200,
            'data' => array('token' => $token, 'token_type' => 'bearer', 'expires_in' => config('jwt.ttl'), 'user' => auth()->user())
        ],200);
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
                ],200
            );
        }catch(JWTException $ex){
            return response()->json(
                [
                    'data' => [],
                    'status' => 401,
                    'error' => 'Error al ejecutar la operación'
                ], 401
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

    public function getUsersByEmpresa(Request $request) {
        $usuarios = User::where('empresa_id',1)
        ->where('acceso',1)
        ->get();

        return response()->json(
            [
                'data' => $usuarios,
                'status' => 200,
                'ok' => true
            ]
        );
    }

    public function getDatosUsersByDoc($tipodocumento, $numerodocumento) {
        $data = array('tipodocumento' => $tipodocumento, 'numerodocumento' => $numerodocumento, 'nombres' => '', 'apellidos' => '');
        try{
            $user = User::where('tipodocumentoid', $tipodocumento)
                ->where('numerodocumento', $numerodocumento)
                ->first();

            if(!isset($user->id)){
                if($tipodocumento == 1){
                    //DNI
                    //consultaremos a la api de padron persona
                    $token = 'apis-token-866.a7kD7Q9DNmGj1NG1uYFqp1PxnGB8zpjd';

                    $client = new Client(['base_uri' => 'https://api.apis.net.pe', 'verify' => false]);
                    $parameters = [
                        'http_errors' => false,
                        'connect_timeout' => 5,
                        'headers' => [
                            'Authorization' => 'Bearer '.$token,
                            'Referer' => 'https://apis.net.pe/api-consulta-dni',
                            'User-Agent' => 'laravel/guzzle',
                            'Accept' => 'application/json',
                        ],
                        'query' => ['numero' => $numerodocumento]
                    ];
                    $res = $client->request('GET', '/v1/dni', $parameters);
                    $resultado = json_decode($res->getBody()->getContents(), true);
                    $data = array('tipodocumento' => $tipodocumento, 'numerodocumento' => $numerodocumento, 'nombres' => $resultado['nombres'], 'apellidos' => $resultado['apellidoPaterno'].' '.$resultado['apellidoMaterno']);
                }
            }

            return response()->json([
                'data' => isset($user->id)?$user:$data, 
                'status' => 201,
                'ok' => true
            ]);
        }catch(Exception $ex){
            return response()->json([
                'data' => $data, 
                'status' => 201,
                'ok' => true
            ]);
        }
    }

}
