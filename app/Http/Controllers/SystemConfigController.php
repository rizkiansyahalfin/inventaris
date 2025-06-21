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
        $configs = SystemConfig::orderBy('key')->get();
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'system_config', 'Lihat daftar konfigurasi sistem (' . $configs->count() . ' konfigurasi)');
        
        return view('system-configs.index', compact('configs'));
    }

    public function create()
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'system_config', 'Akses halaman tambah konfigurasi sistem baru');
        
        return view('system-configs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:system_configs',
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $config = SystemConfig::create($request->all());
        
        // Log activity
        \App\Models\ActivityLog::log('create', 'system_config', 'Menambah konfigurasi sistem: ' . $config->key);
        
        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi berhasil ditambahkan');
    }

    public function show(SystemConfig $systemConfig)
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'system_config', 'Lihat detail konfigurasi sistem: ' . $systemConfig->key);
        
        return view('system-configs.show', compact('systemConfig'));
    }

    public function edit(SystemConfig $systemConfig)
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'system_config', 'Akses halaman edit konfigurasi sistem: ' . $systemConfig->key);
        
        return view('system-configs.edit', compact('systemConfig'));
    }

    public function update(Request $request, SystemConfig $systemConfig)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:system_configs,key,' . $systemConfig->id,
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $systemConfig->update($request->all());
        
        // Log activity
        \App\Models\ActivityLog::log('update', 'system_config', 'Mengedit konfigurasi sistem: ' . $systemConfig->key);
        
        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi berhasil diperbarui');
    }

    public function destroy(SystemConfig $systemConfig)
    {
        $configKey = $systemConfig->key;
        $systemConfig->delete();
        
        // Log activity
        \App\Models\ActivityLog::log('delete', 'system_config', 'Menghapus konfigurasi sistem: ' . $configKey);
        
        return redirect()->route('system-configs.index')
            ->with('success', 'Konfigurasi berhasil dihapus');
    }
} 