<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->app_css->add('perfex_dashboard_styles', PERFEX_DASHBOARD_ASSETS_PATH . '/css/perfex_dashboard_styles.css');
$this->app_scripts->add('perfex_dashboard_scripts', PERFEX_DASHBOARD_ASSETS_PATH . '/js/perfex_dashboard_scripts.js');
$this->app_scripts->add('perfex_dashboard_widget_scripts', PERFEX_DASHBOARD_ASSETS_PATH . '/js/widgets/widget.js');
$this->app_css->add('perfex_dashboard_widget_styles', PERFEX_DASHBOARD_ASSETS_PATH . '/css/widget.css');

$fn_generate_period = function ($period, $param1 = null) {
    $format = 'Y-m-d';
    $now = new Datetime();

    switch (strtoupper($period)) {
        case 'NOW':
            return [$now->format($format), $now->format($format)];
            break;

        case 'CURRENT_WEEK':
            $start_current_week = (clone $now)->sub(new DateInterval('P' . $now->format('w') . 'D'));
            $end_current_week = (clone $start_current_week)->add(new DateInterval('P6D'));
            return [$start_current_week->format($format), $end_current_week->format($format)];
            break;

        case 'PREVIOUS_WEEK':
            $end_previous_week = (clone $now)->sub(new DateInterval('P' . $now->format('w') . 'D'))->sub(new DateInterval('P1D'));
            $start_previous_week = (clone $end_previous_week)->sub((new DateInterval('P6D')));
            return [$start_previous_week->format($format), $end_previous_week->format($format)];
            break;

        case 'CURRENT_MONTH':
            $start_current_month = (clone $now)->setDate($now->format('Y'), $now->format('m'), 1);
            $end_current_month = (clone $start_current_month)->add(new DateInterval('P' . (intval($now->format('t')) - 1) . 'D'));
            return [$start_current_month->format($format), $end_current_month->format($format)];
            break;

        case 'PREVIOUS_MONTH':
            $end_previous_month = (clone $now)->setDate($now->format('Y'), $now->format('m'), 1)->sub(new DateInterval('P1D'));
            $start_previous_month = (clone $now)->setDate($end_previous_month->format('Y'), $end_previous_month->format('m'), 1);
            return [$start_previous_month->format($format), $end_previous_month->format($format)];
            break;

        case 'CURRENT_YEAR':
            $start_current_year = (clone $now)->setDate($now->format('Y'), 1, 1);
            $end_current_year = (clone $now)->setDate($now->format('Y'), 12, 31);
            return [$start_current_year->format($format), $end_current_year->format($format)];
            break;

        case 'PREVIOUS_YEAR':
            $start_previous_year = (clone $now)->setDate((intval($now->format('Y')) - 1), 1, 1);
            $end_previous_year = (clone $now)->setDate((intval($now->format('Y')) - 1), 12, 31);
            return [$start_previous_year->format($format), $end_previous_year->format($format)];
            break;

        case 'LAST_MONTHS':
            if (!isset($param1)) {
                return null;
            }
            return [date('Y-m-01', strtotime("-" . ($param1 - 1) . " MONTH")), date('Y-m-t')];
            break;

        default:
            return null;
            break;
    }
};

$tmp_current_month = $fn_generate_period('CURRENT_MONTH');
$tmp_previous_month = $fn_generate_period('PREVIOUS_MONTH');
$tmp_current_year = $fn_generate_period('CURRENT_YEAR');
$tmp_previous_year = $fn_generate_period('PREVIOUS_YEAR');
$tmp_last_3_months = $fn_generate_period('LAST_MONTHS', 3);
$tmp_last_6_months = $fn_generate_period('LAST_MONTHS', 6);
$tmp_last_12_months = $fn_generate_period('LAST_MONTHS', 12);
?>
    <?php init_head(); ?>
    <div id="wrapper">
        <div class="content perfex-dashboard-widget-list">
            <div class="row">
                <?php $this->load->view('admin/includes/alerts'); ?>
                <?php hooks()->do_action( 'before_start_render_dashboard_content' ); ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="get" action="<?php echo admin_url('perfex_dashboard/dashboards/my_dashboard'); ?>">
                        <div class="perfexdashboard-actions-wrap perfexdashboard-actions-wrap-between">
                            <div class="perfexdashboard-actions-section-left">
                                <select name="dashboard_id" class="form-control perfexdashboard-width-select">
                                    <?php foreach ($user_dashboards as $user_dashboard) { ?>
                                        <option value="<?= $user_dashboard['id'] ?>" <?= $user_dashboard['id'] == $active_dashboard_id ? 'selected' : '' ?>><?= $user_dashboard['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="perfexdashboard-actions-section-right">
                                <div class="perfexdashboard-period-select">
                                    <?php
                                    $tmp_req_from = DateTime::createFromFormat('Y-m-d', $this->input->get('period_from'));
                                    if ($tmp_req_from !== false) {
                                        $tmp_req_from = $tmp_req_from->format('Y-m-d');
                                    } else {
                                        $tmp_req_from = null;
                                    }

                                    $tmp_req_to = DateTime::createFromFormat('Y-m-d', $this->input->get('period_to'));
                                    if ($tmp_req_to !== false) {
                                        $tmp_req_to = $tmp_req_to->format('Y-m-d');
                                    } else {
                                        $tmp_req_to = null;
                                    }

                                    $tmp_req_period = '';
                                    if (isset($tmp_req_from) && isset($tmp_req_from)) {
                                        $tmp_req_period = $tmp_req_from . ';' . $tmp_req_to;
                                    }
                                    $tmp_selected = false;
                                    ?>
                                    <select class="selectpicker perfexdashboard-period-dropdown" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="" <?php if ($tmp_req_period === '') {
                                                                echo 'selected';
                                                                $tmp_selected = true;
                                                            } ?>><?php echo _l('report_sales_months_all_time'); ?></option>
                                        <option value="<?= implode(';', $tmp_current_month) ?>" <?php if ($tmp_req_period === implode(';', $tmp_current_month)) {
                                                                                                    echo 'selected';
                                                                                                    $tmp_selected = true;
                                                                                                } ?>><?php echo _l('this_month'); ?></option>
                                        <option value="<?= implode(';', $tmp_previous_month) ?>" <?php if ($tmp_req_period === implode(';', $tmp_previous_month)) {
                                                                                                        echo 'selected';
                                                                                                        $tmp_selected = true;
                                                                                                    } ?>><?php echo _l('last_month'); ?></option>
                                        <option value="<?= implode(';', $tmp_current_year) ?>" <?php if ($tmp_req_period === implode(';', $tmp_current_year)) {
                                                                                                    echo 'selected';
                                                                                                    $tmp_selected = true;
                                                                                                } ?>><?php echo _l('this_year'); ?></option>
                                        <option value="<?= implode(';', $tmp_previous_year) ?>" <?php if ($tmp_req_period === implode(';', $tmp_previous_year)) {
                                                                                                    echo 'selected';
                                                                                                    $tmp_selected = true;
                                                                                                } ?>><?php echo _l('last_year'); ?></option>
                                        <option value="<?= implode(';', $tmp_last_3_months) ?>" <?php if ($tmp_req_period === implode(';', $tmp_last_3_months)) {
                                                                                                    echo 'selected';
                                                                                                    $tmp_selected = true;
                                                                                                } ?> data-subtext="<?php echo _d($tmp_last_3_months[0]); ?> - <?php echo _d($tmp_last_3_months[1]); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                                        <option value="<?= implode(';', $tmp_last_6_months) ?>" <?php if ($tmp_req_period === implode(';', $tmp_last_6_months)) {
                                                                                                    echo 'selected';
                                                                                                    $tmp_selected = true;
                                                                                                } ?> data-subtext="<?php echo _d($tmp_last_6_months[0]); ?> - <?php echo _d($tmp_last_6_months[1]); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                                        <option value="<?= implode(';', $tmp_last_12_months) ?>" <?php if ($tmp_req_period === implode(';', $tmp_last_12_months)) {
                                                                                                        echo 'selected';
                                                                                                        $tmp_selected = true;
                                                                                                    } ?> data-subtext="<?php echo _d($tmp_last_12_months[0]); ?> - <?php echo _d($tmp_last_12_months[1]); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                                        <option value="custom" <?php if (!$tmp_selected) {
                                                                    echo 'selected';
                                                                } ?>><?php echo _l('period_datepicker'); ?></option>
                                    </select>
                                    <div class="perfexdashboard-date-range">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group date">
                                                    <input type="text" placeholder="<?= _l('from_date') ?>" value="<?= $tmp_req_from ?>" class="form-control datepicker" name="period_from">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar calendar-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="input-group date">
                                                    <input type="text" placeholder="<?= _l('to_date') ?>" value="<?= $tmp_req_to ?>" class="form-control datepicker" name="period_to">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar calendar-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary"><?= _l('search') ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($dashboard)) { ?>
                <?php if (count($dashboard['dashboard_widgets']) == 0) { ?>
                    <div class="row mtop30">
                        <div class="col-md-12">
                            <p><?= _l('not_found') ?></p>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row mtop30">
                        <div class="col-md-3" data-container="top-left-first-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-left-first-4'); ?>
                        </div>
                        <div class="col-md-3" data-container="top-left-last-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-left-last-4'); ?>
                        </div>
                        <div class="col-md-3" data-container="top-right-first-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-right-first-4'); ?>
                        </div>
                        <div class="col-md-3" data-container="top-right-last-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-right-last-4'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" data-container="top-12">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-12'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" data-container="middle-left-6">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'middle-left-6'); ?>
                        </div>
                        <div class="col-md-6" data-container="middle-right-6">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'middle-right-6'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8" data-container="left-8">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'left-8'); ?>
                        </div>
                        <div class="col-md-4" data-container="right-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'right-4'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4" data-container="bottom-left-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'bottom-left-4'); ?>
                        </div>
                        <div class="col-md-4" data-container="bottom-middle-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'bottom-middle-4'); ?>
                        </div>
                        <div class="col-md-4" data-container="bottom-right-4">
                            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'bottom-right-4'); ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <script>
        const APP_PATH = "/my_dashboard";
    </script>
    <?php init_tail(); ?>
    </body>

    </html>