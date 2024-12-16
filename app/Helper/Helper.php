<?php

use App\Models\Pengguna;

if (!function_exists('SuperAdmin')) {
    function SuperAdmin()
    {
        $user = Pengguna::where('user_id', auth()->user()->user_id)->with('Departement')->first();
        // dd($user);

        if (in_array('21', $user->Departement->pluck('id')->toArray())) {
            return true;
        }
        return false;
    }
}
