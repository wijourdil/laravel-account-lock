<?php

namespace Wijourdil\LaravelAccountLock\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Wijourdil\LaravelAccountLock\LaravelAccountLock;

class LaravelAccountLockController extends BaseController
{
    public function __construct(private LaravelAccountLock $service)
    {
    }

    public function lock(Request $request): View
    {
        /** @var array<string,mixed> $data */
        $data = Crypt::decrypt($request->get('data'));

        $table = $data['table'];
        $id = $data['id'];

        if (empty(DB::table($table)->find($id))) {
            throw new ModelNotFoundException;
        }

        $this->service->lock($table, $id);

        return view('account-lock::account-locked');
    }
}
