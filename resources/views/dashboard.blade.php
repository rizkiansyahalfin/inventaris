@php
    $user = auth()->user();
    if ($user->hasRole('admin')) {
        $view = 'dashboard.admin';
    } elseif ($user->hasRole('petugas')) {
        $view = 'dashboard.staff';
    } else {
        $view = 'dashboard.user';
    }
@endphp

@include($view)
