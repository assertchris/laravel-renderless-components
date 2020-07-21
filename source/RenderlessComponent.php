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

        $file = $this->createViewFile();

        return view($this->view(), [
            'render' => require_once $file,
        ] + $this->viewParams());
    }

    private function createViewFile(): string
    {
        $children = trim($this->children);
        $renderParams = $this->renderParams();

        $folder = $this->createComponentsFolder();
        $file = $this->join($folder, sha1($this->view()) . '.php');

        if (!file_exists($file) || app()->environment('local')) {
            file_put_contents($file, trim(
<<<FN
<?php

return function ({$renderParams}) use (\$component) {
    if (true):
        ?>
            {$children}
        <?php
    endif;
};
FN
            ));
        }

        return $file;
    }

    private function createComponentsFolder(): string
    {
        $folder = storage_path($this->join('framework', 'views', 'components'));

        if (!file_exists($folder)) {
            mkdir($folder);
        }

        return $folder;
    }

    private function join(...$parts): string
    {
        return join(DIRECTORY_SEPARATOR, $parts);
    }
}
