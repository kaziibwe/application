<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('admin_init', function () {
    App_table::register(
        App_table::new('clients')->customfieldable('customers')->setPrimaryKeyName('userid')
    );

    App_table::register(
        App_table::new('expenses')->customfieldable('expenses')
    );

    App_table::register(
        App_table::new('project_expenses')
    );

    App_table::register(
        App_table::new('expenses_detailed_report')->customfieldable('expenses')
    );

    App_table::register(
        $contractsTable = App_table::new('contracts')->customfieldable('contracts')
    );

    App_table::register(
        App_table::new('project_contracts', 'contracts')
            ->relatedTo($contractsTable->id())
            ->setRules($contractsTable->rules())
    );

    App_table::register(
        App_table::new('credit_notes')->setDbTableName('creditnotes')->customfieldable('credit_note')
    );

    App_table::register(
        App_table::new('projects')->customfieldable('projects')
    );

    App_table::register(
        App_table::new('leads')->customfieldable('leads')
    );

    App_table::register(
        App_table::new('subscriptions')
    );

    App_table::register(
        App_table::new('tickets')->setPrimaryKeyName('ticketid')->customfieldable('tickets')
    );

    App_table::register(
        $estimatesTable = App_table::new('estimates')->customfieldable('estimate')
    );

    App_table::register(
        App_table::new('project_estimates', 'estimates')
            ->relatedTo($estimatesTable->id())
            ->setRules($estimatesTable->rules())
    );

    App_table::register($tasksTable = App_table::new('tasks')->customfieldable('tasks'));

    App_table::register(
        App_table::new('related_tasks', 'tasks_relations')
            ->relatedTo($tasksTable->id())
            ->setRules($tasksTable->rules())
    );

    App_table::register(
        $invoicesTable = App_table::new('invoices')->customfieldable('invoice')
    );

    App_table::register(
        App_table::new('project_invoices', 'invoices')
            ->relatedTo($invoicesTable->id())
            ->setRules($invoicesTable->rules())
    );

    App_table::register(
        $proposalsTable = App_table::new('proposals')->customfieldable('proposal')
    );

    App_table::register(
        App_table::new('project_proposals', 'proposals')
            ->relatedTo($proposalsTable->id())
            ->setRules($proposalsTable->rules())
    );
});
