<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('branch-submit')->html();
} elseif ($_instance->childHasBeenRendered('aFMLM6K')) {
    $componentId = $_instance->getRenderedChildComponentId('aFMLM6K');
    $componentTag = $_instance->getRenderedChildComponentTagName('aFMLM6K');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('aFMLM6K');
} else {
    $response = \Livewire\Livewire::mount('branch-submit');
    $html = $response->html();
    $_instance->logRenderedChild('aFMLM6K', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?><?php /**PATH /Users/josephatwilliammadili/Desktop/UPDATED PROJECTS/EXTERNAL/MCB ESS/FRONT END/ESS-MCB-FRONTEND/resources/views/admin/intermediaries/branchesutumish.blade.php ENDPATH**/ ?>