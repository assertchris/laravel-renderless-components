<?php

namespace RenderlessComponents;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;

class RenderlessComponentsBladeCompiler extends BladeCompiler
{
    public static function compileClassComponentOpening(string $component, string $alias, string $data, string $hash)
    {
        return implode("\n", [
            '<?php if (isset($component)) { $__componentOriginal'.$hash.' = $component; } ?>',
            '<?php $component = $__env->getContainer()->make('.Str::finish($component, '::class').', '.($data ?: '[]').'); ?>',
            '<?php $component->withName('.$alias.'); ?>',
            '<?php if ($component->shouldRender()): ?>',
            '<?php $component->children = <<<\'CHILDREN\'',
        ]);
    }

    protected function compileEndComponent()
    {
        $hash = array_pop(static::$componentHashStack);

        return implode("\n", [
            PHP_EOL,
            'CHILDREN; ?>',
            '<?php $__env->startComponent($component->resolveView(), $component->data()); ?>',
            '<?php if (isset($__componentOriginal'.$hash.')): ?>',
            '<?php $component = $__componentOriginal'.$hash.'; ?>',
            '<?php unset($__componentOriginal'.$hash.'); ?>',
            '<?php endif; ?>',
            '<?php echo $__env->renderComponent(); ?>',
        ]);
    }
}
