<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test extends AdminController
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $now = new Datetime();
    var_dump($now->format('U'));
  }

  private function generate_period($period, $param1 = null)
  {
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
  }
}
