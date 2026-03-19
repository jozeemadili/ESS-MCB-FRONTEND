<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('branch-submit')->html();
} elseif ($_instance->childHasBeenRendered('3zLFMqq')) {
    $componentId = $_instance->getRenderedChildComponentId('3zLFMqq');
    $componentTag = $_instance->getRenderedChildComponentTagName('3zLFMqq');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('3zLFMqq');
} else {
    $response = \Livewire\Livewire::mount('branch-submit');
    $html = $response->html();
    $_instance->logRenderedChild('3zLFMqq', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?><?php /**PATH /var/www/html/laravel/resources/views/admin/intermediaries/branchesutumish.blade.php ENDPATH**/ ?>