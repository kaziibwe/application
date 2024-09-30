<div id="<?php echo $filters_wrapper_id ?? 'tasksFilters'; ?>" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
    <app-filters 
        id="<?php echo $tasks_table->id(); ?>" 
        view="<?php echo $tasks_table->viewName(); ?>"
        :saved-filters="<?php echo $tasks_table->filtersJs(); ?>"
        :available-rules="<?php echo $tasks_table->rulesJs(); ?>">
    </app-filters>
</div>
<script>
    if(typeof(vNewApp) == 'function'){
        vNewApp('#tasksFilters')
    }
</script>