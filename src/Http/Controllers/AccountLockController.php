<?php

namespace Wijourdil\LaravelAccountLock\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Wijourdil\LaravelAccountLock\AccountLock;

class AccountLockController extends BaseController
{
    public function __construct(private AccountLock $service)
    {
    }

    public function lock(Request $request): View
    {
        /** @var array<string,mixed> $data */
        $data = Crypt::decrypt($request->get('data'));

        // todo vérifier que le compte que j'essaie de bloquer existe ?

        // todo est-ce que je devrais aussi bloquer les autres providers pour le même User ?
        //  par ex si j'ai un provider api et un provider users

        // =>>>>> todo 404 si model not found
        $table = $data['table'];
        $id = $data['id'];

        if (empty(DB::table($table)->find($id))) { // todo utiliser la méthode privée du service ?
            throw new ModelNotFoundException();
        }

        $this->service->lock($table, $id);

        Auth::logoutCurrentDevice();

        return view('account-lock::account-locked');

        // todo si je viens sur une URL expirée, afficher une erreur bien en forme :
        //  => https://laravel.com/docs/8.x/urls#responding-to-invalid-signed-routes

        // todo retourner sur une page "votre compte vient d'être bloqué"

        //        dd(
        //            $request->all(),
        //            $data,
        //            \Illuminate\Support\Facades\Auth::createUserProvider($data['provider'])
        //                ->retrieveById($data['authenticable_id'])->toArray()
        //        );
    }
}
