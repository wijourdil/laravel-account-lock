<?php

namespace Wijourdil\LaravelAccountLock\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Wijourdil\LaravelAccountLock\AccountLock;
use Wijourdil\LaravelAccountLock\Classes\Account;

class AccountLockController extends BaseController
{
    public function lock(Request $request, AccountLock $service): View
    {
        /** @var array<string,mixed> $data */
        $data = Crypt::decrypt($request->get('data'));

        $account = Account::fromArray($data);

        $user = DB::table($account->getTable())
            ->where($account->getIdentifierName(), '=', $account->getIdentifierValue())
            ->first();

        if (empty($user)) {
            throw new ModelNotFoundException();
        }

        $class = $account->getType();

        Model::unguard();
        $authenticatable = new $class((array)$user);
        Model::reguard();

        $service->lock($authenticatable);

        return view('account-lock::account-locked');
    }
}
