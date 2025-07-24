<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProgressSteps extends Component
{
    public $currentStep, $progress, $steps;

    public function __construct($currentStep = 1, $progress = '0%', $steps = null)
    {
        $this->currentStep = $currentStep;
        $this->progress = $progress;
        $this->steps = $steps ?? [
            ['label' => 'Select Rooms'],
            ['label' => 'Payment'],
            ['label' => 'Completed'],
        ];
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.progress-steps');
    }
}
