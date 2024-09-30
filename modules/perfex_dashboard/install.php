<?php

if (!$CI->db->table_exists(db_prefix() . 'perfex_dashboard_widget_categories')) {
  $CI->db->query("
    CREATE TABLE " . db_prefix() . "perfex_dashboard_widget_categories (
    id                    BIGINT(20) NOT NULL,
    name                  VARCHAR(191) DEFAULT NULL,
    note                  TEXT DEFAULT NULL,
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_widget_categories
    ADD PRIMARY KEY (id);
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_widget_categories
    MODIFY id BIGINT(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  ");

  $CI->db->query("
    INSERT INTO " . db_prefix() . "perfex_dashboard_widget_categories (`id`, `name`, `note`) VALUES
      (1, 'Logged staff', 'Widgets for logged staff'),
      (2, 'Finance', 'Stuff relate to Finance'),
      (3, 'Customers', 'Stuff relate to customers'),
      (4, 'Staff', 'Stuff relate to staff'),
      (5, 'Projects', 'Stuff relate to projects'),
      (6, 'Leads', 'Stuff relate to leads'),
      (7, 'Support', 'Stuff relate to support')
      ;
  ");
}


if (!$CI->db->table_exists(db_prefix() . 'perfex_dashboard_list')) {
  $CI->db->query("
    CREATE TABLE " . db_prefix() . "perfex_dashboard_list (
    id                    BIGINT(20) NOT NULL,
    name                  VARCHAR(191) DEFAULT NULL,
    dashboard_widgets     TEXT DEFAULT NULL,
    note                  TEXT DEFAULT NULL,
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_list
    ADD PRIMARY KEY (id);
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_list
    MODIFY id BIGINT(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1
  ");
}

if (!$CI->db->table_exists(db_prefix() . 'perfex_dashboard_users')) {
  $CI->db->query("
    CREATE TABLE " . db_prefix() . "perfex_dashboard_users (
    id                    BIGINT(20) NOT NULL,
    user_id               BIGINT(20) NOT NULL,
    dashboard_id          BIGINT(20) NOT NULL,
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_users
    ADD PRIMARY KEY (id);
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_users
    MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1
  ");

  // Default dashboard
  $CI->db->query("
    INSERT INTO " . db_prefix() . "perfex_dashboard_list (`id`, `name`, `dashboard_widgets`, `note`, `updated_at`, `created_at`) VALUES
    (1, 'CRM Dashboard', 'a:6:{s:16:\"top-left-first-4\";a:1:{i:0;s:2:\"17\";}s:15:\"top-left-last-4\";a:1:{i:0;s:2:\"18\";}s:17:\"top-right-first-4\";a:1:{i:0;s:2:\"15\";}s:16:\"top-right-last-4\";a:1:{i:0;s:2:\"14\";}s:13:\"middle-left-6\";a:3:{i:0;s:2:\"19\";i:1;s:2:\"23\";i:2;s:1:\"9\";}s:14:\"middle-right-6\";a:4:{i:0;s:2:\"20\";i:1;s:2:\"22\";i:2;s:2:\"10\";i:3;s:2:\"21\";}}', 'CRM Dashboard', '2021-10-01 19:04:55', '2021-10-01 19:04:55'),
    (2, 'Sale Dashboard', 'a:6:{s:16:\"top-left-first-4\";a:1:{i:0;s:2:\"35\";}s:15:\"top-left-last-4\";a:1:{i:0;s:2:\"36\";}s:17:\"top-right-first-4\";a:1:{i:0;s:2:\"37\";}s:16:\"top-right-last-4\";a:1:{i:0;s:2:\"14\";}s:13:\"middle-left-6\";a:4:{i:0;s:2:\"38\";i:1;s:1:\"8\";i:2;s:1:\"7\";i:3;s:2:\"11\";}s:14:\"middle-right-6\";a:3:{i:0;s:2:\"39\";i:1;s:1:\"5\";i:2;s:2:\"10\";}}', 'Sale Dashboard', '2021-10-05 02:36:23', '2021-10-05 02:36:23'),
    (3, 'Staff Dashboard', 'a:4:{s:16:\"top-left-first-4\";a:1:{i:0;s:2:\"16\";}s:6:\"top-12\";a:1:{i:0;s:2:\"32\";}s:13:\"middle-left-6\";a:2:{i:0;s:2:\"33\";i:1;s:2:\"11\";}s:14:\"middle-right-6\";a:2:{i:0;s:2:\"34\";i:1;s:2:\"20\";}}', 'Staff Dashboard', '2021-10-05 10:26:28', '2021-10-05 10:26:28'),
    (4, 'Projects Dashboard', 'a:7:{s:16:\"top-left-first-4\";a:1:{i:0;s:2:\"24\";}s:15:\"top-left-last-4\";a:1:{i:0;s:2:\"25\";}s:17:\"top-right-first-4\";a:1:{i:0;s:2:\"26\";}s:16:\"top-right-last-4\";a:1:{i:0;s:2:\"27\";}s:13:\"middle-left-6\";a:2:{i:0;s:2:\"28\";i:1;s:2:\"30\";}s:14:\"middle-right-6\";a:2:{i:0;s:2:\"29\";i:1;s:2:\"31\";}s:6:\"left-8\";a:2:{i:0;s:2:\"12\";i:1;s:2:\"13\";}}', 'Projects Dashboard', '2021-10-05 10:33:28', '2021-10-05 10:33:28');
  ");
}

if (!$CI->db->table_exists(db_prefix() . 'perfex_dashboard_widgets')) {
  $CI->db->query("
    CREATE TABLE " . db_prefix() . "perfex_dashboard_widgets (
    id                    BIGINT(20) NOT NULL,
    name                  VARCHAR(191) DEFAULT NULL,
    category              BIGINT(20) DEFAULT NULL,
    widget_name           VARCHAR(191) DEFAULT NULL,
    default_container     VARCHAR(191) DEFAULT NULL,
    note                  TEXT DEFAULT NULL,
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_widgets
    ADD PRIMARY KEY (id);
  ");

  $CI->db->query("
    ALTER TABLE " . db_prefix() . "perfex_dashboard_widgets
    MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1
  ");

  // Default widgets
  $CI->db->query("
    INSERT INTO " . db_prefix() . "perfex_dashboard_widgets (`name`, `category`, `widget_name`, `default_container`, `note`) VALUES
    ('User data', 1, 'widget-techfago_logged_staff_user_data', 'top-12', 'Display information (task,projects,reminders,tickets...) for the current login user'),
    ('Calendar', 1, 'widget-techfago_logged_staff_calendar', 'top-12', 'Display calendar for the current login user'),
    ('Todos', 1, 'widget-techfago_logged_staff_todos', 'top-12', 'Display todos list for the current login user'),
    ('Projects Activity', 1, 'widget-techfago_logged_staff_projects_activity', 'top-12', 'Display projects activity for the current login user'),
    ('Top 10 Selling Products', 2, 'widget-techfago_finance_top_10_selling_products', 'top-12', 'List of top 10 selling products'),
    ('Total Revenue', 2, 'widget-techfago_finance_total_revenue', 'top-12', 'Display Revenue'),
    ('Revenue top 10 with city', 2, 'widget-techfago_finance_revenue_top_10_with_city', 'top-12', 'List of top 10 revenue with city'),
    ('Revenue top 10 with country', 2, 'widget-techfago_finance_revenue_top_10_with_country', 'top-12', 'List of top 10 revenue with country'),
    ('Top 10 new customers', 3, 'widget-techfago_customers_top_10_new_customers', 'top-12', 'List of top 10 new customers'),
    ('Top 10 customer payment', 3, 'widget-techfago_customers_top_10_customer_payment', 'top-12', 'List top 10 customers payment'),
    ('Top 20 staff income', 4, 'widget-techfago_staff_top_20_staff_income', 'top-12', 'Top 20 staff income'),
    ('Projects to deadline', 5, 'widget-techfago_projects_deadline_projects', 'top-12', 'All projects to deadline'),
    ('Tasks to deadline', 5, 'widget-techfago_projects_deadline_tasks', 'top-12', 'All tasks to deadline'),
    ('Total Orders', 2, 'widget-techfago_finance_total_orders', 'top-12', 'Total Orders'),
    ('Total Customers', 3, 'widget-techfago_customers_total_customers', 'top-12', 'Total Customers'),
    ('Total Staff', 4, 'widget-techfago_staff_total_staff', 'top-12', 'Total Staff'),
    ('Total leads', 6, 'widget-techfago_leads_total_leads', 'top-12', 'Total leads'),
    ('Total converted leads', 6, 'widget-techfago_leads_total_converted_leads', 'top-12', 'Total converted leads'),
    ('Leads and converted leads by sources', 6, 'widget-techfago_leads_leads_and_converted_leads_by_sources', 'top-12', 'Leads and converted leads by sources'),
    ('Leads and converted leads by staff', 6, 'widget-techfago_leads_leads_and_converted_leads_by_staff', 'top-12', 'Leads and converted leads by staff'),
    ('Lead by countries', 6, 'widget-techfago_leads_lead_by_countries', 'top-12', 'Lead by countries'),
    ('Lead by sources', 6, 'widget-techfago_leads_lead_by_sources', 'top-12', 'Lead by sources'),
    ('Lead by tags', 6, 'widget-techfago_leads_lead_by_tags', 'top-12', 'Lead by tags'),
    ('Total projects', 5, 'widget-techfago_projects_total_projects', 'top-12', 'Total projects'),
    ('Total overdue projects', 5, 'widget-techfago_projects_total_overdue_projects', 'top-12', 'Total overdue projects'),
    ('Total tasks', 5, 'widget-techfago_projects_total_tasks', 'top-12', 'Total tasks'),
    ('Total overdue tasks', 5, 'widget-techfago_projects_total_overdue_tasks', 'top-12', 'Total overdue tasks'),
    ('Total completed projects', 5, 'widget-techfago_projects_total_completed_projects', 'top-12', 'Total completed projects'),
    ('Total completed tasks', 5, 'widget-techfago_projects_total_completed_tasks', 'top-12', 'Total completed tasks'),
    ('Logged hours by projects', 5, 'widget-techfago_projects_logged_hours_by_projects', 'top-12', 'Logged hours by projects'),
    ('Logged hours by staff', 5, 'widget-techfago_projects_logged_hours_by_staff', 'top-12', 'Logged hours by staff'),
    ('Staff with statuses', 4, 'widget-techfago_staff_staff_with_statuses', 'top-12', 'Staff with statuses'),
    ('Staff with roles', 4, 'widget-techfago_staff_staff_with_roles', 'top-12', 'Staff with roles'),
    ('Staff with departments', 4, 'widget-techfago_staff_staff_with_departments', 'top-12', 'Staff with departments'),
    ('Total income', 2, 'widget-techfago_finance_total_income', 'top-12', 'Total income'),
    ('Total expense', 2, 'widget-techfago_finance_total_expense', 'top-12', 'Total expense'),
    ('Total profit', 2, 'widget-techfago_finance_total_profit', 'top-12', 'Total profit'),
    ('Expense with categories', 2, 'widget-techfago_finance_expense_with_categories', 'top-12', 'Expense with categories'),
    ('Monthly income and expense', 2, 'widget-techfago_finance_monthly_income_and_expense', 'top-12', 'Monthly income and expense')
    ;
  ");
}



