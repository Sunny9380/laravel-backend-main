<?php

namespace App\View\Components\email;

use App\Models\Configuration;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{

    public $logo;

    public function __construct($logo)
    {
        $this->logo = $logo;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
       return view('components.email.header');
    }
}
