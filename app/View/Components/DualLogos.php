<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DualLogos extends Component
{
    public $leftLogo;
    public $rightLogo;
    public $leftSize;
    public $rightSize;
    public $containerClass;

    public function __construct(
        $leftLogo = 'img/gobierno.jpg',
        $rightLogo = 'img/udevipo.png',
        $leftSize = 'h-30', // Tamaño por defecto ajustado
        $rightSize = 'h-30', // Tamaño por defecto ajustado
        $containerClass = '' // Clases adicionales para el contenedor
    ) {
        $this->leftLogo = $leftLogo;
        $this->rightLogo = $rightLogo;
        $this->leftSize = $leftSize;
        $this->rightSize = $rightSize;
        $this->containerClass = $containerClass;
    }

    public function render()
    {
        return view('components.dual-logos');
    }
}
