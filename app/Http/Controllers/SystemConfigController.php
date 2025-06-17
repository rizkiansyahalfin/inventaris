<?php

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class SystemConfigController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $configs = SystemConfig::orderBy('key')->paginate(10);
        return view('system-configs.index', compact('configs'));
    }

    public function create()
    {
        return view('system-configs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_configs',
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        SystemConfig::create($validated);

        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi sistem berhasil ditambahkan.');
    }

    public function edit(SystemConfig $systemConfig)
    {
        return view('system-configs.edit', compact('systemConfig'));
    }

    public function update(Request $request, SystemConfig $systemConfig)
    {
        $validated = $request->validate([
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $systemConfig->update($validated);

        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }

    public function destroy(SystemConfig $systemConfig)
    {
        $systemConfig->delete();

        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi sistem berhasil dihapus.');
    }
} 