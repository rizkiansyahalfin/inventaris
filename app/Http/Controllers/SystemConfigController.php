<?php

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use Illuminate\Http\Request;

class SystemConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $configs = SystemConfig::orderBy('config_key')->paginate(20);
        return view('system-configs.index', compact('configs'));
    }

    public function create()
    {
        return view('system-configs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'config_key' => 'required|string|max:50|unique:system_configs',
            'config_value' => 'required|string',
        ]);

        SystemConfig::create($validated);

        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi berhasil ditambahkan.');
    }

    public function edit(SystemConfig $systemConfig)
    {
        return view('system-configs.edit', compact('systemConfig'));
    }

    public function update(Request $request, SystemConfig $systemConfig)
    {
        $validated = $request->validate([
            'config_value' => 'required|string',
        ]);

        $systemConfig->update($validated);

        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi berhasil diperbarui.');
    }

    public function destroy(SystemConfig $systemConfig)
    {
        $systemConfig->delete();

        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi berhasil dihapus.');
    }
} 