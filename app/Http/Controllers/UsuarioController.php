<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\User;

class UsuarioController extends Controller
{
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->all();

        $validacao = Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string'
        ]);

        if ($validacao->fails())
        return ['status' => false, 'validacao' => $validacao->errors()];

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $user->imagem = asset($user->imagem);
            $user->token = $user->createToken($user->email)->accessToken;
            return ['status' => true, 'usuario' => $user];
        } else {
            return response()->json([
                'status' => false, 'validacao' => [
                    ['Usuário não autenticado.'],
                    ['Usuário ou Senha não localizado, tente novamente!']
                ]
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function usuarios()
    {
        return $this->user->all();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validacao = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validacao->fails())
        return ['status' => false, 'validacao' => $validacao->errors()];
        $form = $request->only(['name', 'email', 'password']);
        $form['password'] = bcrypt($form['password']);
        $form['imagem'] = "/profiles/default.png";
        $user = $this->user->updateOrCreate($form);
        $user->token = $user->createToken($user->email)->accessToken;
        $user->imagem = asset($user->imagem);
        return ['status' => true, 'usuario' => $user];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->all();
        $validacao = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if ($validacao->fails())
        return ['status' => false, 'validacao' => $validacao->errors()];

        if (isset($data['password']))
        $user->password = bcrypt($data['password']);

        if (isset($data['imagem'])) {
            Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
                $explode = explode(',', $value);
                $allow = ['png', 'jpg', 'svg', 'jpeg'];
                $format = str_replace(
                    [
                        'data:image/',
                        ';',
                        'base64',
                    ],
                    [
                        '', '', '',
                    ],
                    $explode[0]
                );
                //check file format
                if (!in_array($format, $allow)) {
                    return false;
                }
                //check base64 format
                if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                    return false;
                }
                return true;
            });
            $validacao = Validator::make($data, [
                'imagem' => 'base64image',
            ], ['base64image' => 'Imagem Inválida']);
            if ($validacao->fails())
            return ['status' => false, 'validacao' => $validacao->errors()];
            $time = time();
            $folderFather = 'profiles';
            $pathImagem = $folderFather . DIRECTORY_SEPARATOR . 'profile_id_' . $user->id;
            $typeImage = substr($data['imagem'], 11, strpos($data['imagem'], ';') - 11);
            $urlImagem = $pathImagem . DIRECTORY_SEPARATOR . $time . '.' . $typeImage;
            $file = str_replace('data:image/' . $typeImage . ';base64', '', $data['imagem']);
            $imagem = base64_decode($file);
            if (!file_exists($folderFather)) {
                mkdir($folderFather, 0755);
            }
            if ($user->imagem) {
                $imageUser = str_replace(asset('/'), "", $user->imagem);
                if (file_exists($imageUser))
                unlink($imageUser);
            }
            if (!file_exists($pathImagem)) {
                mkdir($pathImagem, 0755);
            }
            file_put_contents($urlImagem, $imagem);
            $user->imagem = $urlImagem;
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();
        //$user->imagem = asset($user->imagem);
        $user->token = $user->createToken($user->email)->accessToken;
        return ['status' => true, 'usuario' => $user];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function teste(Request $request)
    {
        /* $cleanUser = $this->user->first();
        $copyUser = $cleanUser;
        $copyUser['password'] = bcrypt('84135831');
        $cleanUser->delete();
        $this->user->insert($copyUser->toArray()); */
        $user = $this->user->with('conteudos', 'comentarios', 'amigos', 'curtidas')->first();
        if (isset($user)) {
            $conteudo = $user->conteudos()->create([
                'titulo' => 'Conteúdo 1',
                'texto' => 'Texto Exemplo',
                'imagem' => 'algun item',
                'link' => url('algumlink'),
                'data' => Carbon::now(),
            ]);
            $amigo = $user->amigos()->sync($this->user->orderByDesc('id')->first()->id);
        }
        if (isset($conteudo)) {
            $cometario = $user->comentarios()->create([
                'conteudo_id' => $conteudo->id,
                'texto' => 'Teste de Comentario',
                'data' => Carbon::now()
            ]);
            $curtida = $user->curtidas()->toggle($conteudo->id);
        }


        return $user;
    }
}
