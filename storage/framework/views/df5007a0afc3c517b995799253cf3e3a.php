<div
    <?php echo e($attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)); ?>

>
    <?php echo e($getChildComponentContainer()); ?>

</div>
<?php /**PATH C:\Users\renz2\final\hotel-service\vendor\filament\forms\src\/../resources/views/components/grid.blade.php ENDPATH**/ ?>