<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Package;
use App\Models\Role;

class AdminController extends Controller
{
    public function updateSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return response()->json(['status' => 'success']);
    }

    public function createPackage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'driver' => 'required|string',
            'price' => 'required|numeric',
            'cpu' => 'required|integer',
            'ram' => 'required|integer',
            'disk' => 'required|integer',
            'data' => 'nullable|array'
        ]);

        $package = Package::create($data);
        return response()->json(['status' => 'success', 'package_id' => $package->id]);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'slug' => 'required|string|unique:roles,slug',
            'permissions' => 'required|array'
        ]);

        $role = Role::create($request->only('name', 'slug'));
        $role->permissions()->sync($request->permissions);

        return response()->json(['status' => 'success']);
    }
}