<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $menu;

    public function __construct()
    {
        $this->menu = [
            ['name' => 'Inicio', 'route' => 'dashboard', 'icon' => 'bi-house'],
            ['name' => 'Enviadas', 'route' => 'enviadas', 'icon' => 'bi-send'],
            ['name' => 'Recibidas', 'route' => 'recibidas', 'icon' => 'bi-inbox'],
            ['name' => 'Reportes', 'route' => 'recibidas', 'icon' => 'bi-inbox'],
            ['name' => 'Documentos', 'route' => 'documentos', 'icon' => 'bi-file-earmark'],
        ];
    }

    public function render()
    {
        return view('components.sidebar');
    }
}
