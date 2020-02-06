<?php

namespace App\Http\Controllers;

use App\Conteudo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ConteudoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $conteudos = Conteudo::with('user')->orderBy('data', 'desc')->paginate(5);
        $user = $request->user();
        foreach ($conteudos as $key => $conteudo) {
            $conteudo->total_curtidas = $conteudo->curtidas()->count();
            $curtiu = $user->curtidas()->find($conteudo->id);
            $conteudo->curtiu_conteudo = isset($curtiu) ? true : false;
        }
        return ['status' => true, 'conteudos' => $conteudos];
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
            'titulo' => 'required|string|max:255',
            'texto' => 'required|string',
            //'link' => 'sometimes',
            //'imagem' => 'sometimes'
        ]);
        if ($validacao->fails())
            return ['status' => false, 'validacao' => $validacao->errors()];

        $user = $request->user();
        $dataRequest = $request->all();
        $dataRequest['data'] = Carbon::now();
        $dataRequest['link'] = $dataRequest['link'] != null ? $dataRequest['link'] : '#';
        $conteudo = $user->conteudos()->create($dataRequest);
        $conteudo = Conteudo::with('user')->orderBy('data', 'desc')->paginate(5);
        if ($conteudo) {
            return ['status' => true, 'conteudos' => $conteudo];
        } else {
            return response()->json(['status' => false, 'validacao' => ['Falha ao salvar no Banco de Dados.']]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Conteudo  $conteudo
     * @return \Illuminate\Http\Response
     */
    public function show(Conteudo $conteudo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Conteudo  $conteudo
     * @return \Illuminate\Http\Response
     */
    public function edit(Conteudo $conteudo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Conteudo  $conteudo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $conteudo = Conteudo::find($id);
        if ($conteudo != null) {
            $user = $request->user();
            $user->curtidas()->toggle($conteudo->id);
            return ['status' => true, 'curtidas' => $conteudo->curtidas()->count(), 'lista' => $this->index($request)];
        } else {
            return ['status' => false, 'errors' => 'Este conteúdo não existe!'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Conteudo  $conteudo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conteudo $conteudo)
    {
        //
    }
}
