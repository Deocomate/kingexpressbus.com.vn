<?php

namespace App\View\Components\Client;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    public ?object $webProfile;

    public function __construct($webProfile = null)
    {
        $this->webProfile = $webProfile;
    }

    public function render(): View|Closure|string
    {
        return view('components.client.footer');
    }
}
