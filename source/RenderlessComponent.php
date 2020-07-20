<?php

namespace RenderlessComponents;

use Illuminate\View\Component;

abstract class RenderlessComponent extends Component
{
    abstract public function view(): string;
    abstract public function viewParams(): array;
    abstract public function renderParams(): string;

    public string $children;

    public function render()
    {
        $component = $this;
        $children = trim($this->children);

        $viewParams = $this->viewParams();
        $renderParams = $this->renderParams();

        $fn = <<<FN
            return function ({$renderParams}) use (\$component) {
                if (true):
                    ?>
                        {$children}
                    <?php
                endif;
            };
        FN;

        $fn = eval($fn);

        return view($this->view(), [
            'render' => $fn,
        ] + $viewParams);
    }
}
