<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Top 20 staff income
  Description: Top 20 staff income
  
*/
?>

<?php
$fn_get_data = function () {
  $widget_req_from = DateTime::createFromFormat('Y-m-d', $this->input->get('period_from'));
  if ($widget_req_from !== false) {
    $widget_req_from = $widget_req_from->format('Y-m-d');
  } else {
    $widget_req_from = null;
  }

  $widget_req_to = DateTime::createFromFormat('Y-m-d', $this->input->get('period_to'));
  if ($widget_req_to !== false) {
    $widget_req_to = $widget_req_to->format('Y-m-d');
  } else {
    $widget_req_to = null;
  }

  $sql = "
    SELECT
      CONCAT(TBLStaff.firstname, ' ', TBLStaff.lastname) AS fullname, 
      TBLStaff.staffid as staffid,
      IFNULL(SUM(TBLPayments.amount), 0) AS totalincome
    FROM " . db_prefix() . "invoices TBLInvoices
    INNER JOIN " . db_prefix() . "staff TBLStaff ON TBLInvoices.sale_agent = TBLStaff.staffid
    INNER JOIN " . db_prefix() . "invoicepaymentrecords TBLPayments ON TBLInvoices.id = TBLPayments.invoiceid
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      WHERE TBLPayments.date >= '" . $widget_req_from . "' AND TBLPayments.date <= '" . $widget_req_to . "'
    ";
  }
  $sql .= "
    GROUP BY TBLStaff.staffid
    ORDER BY totalincome DESC
    LIMIT 0 , 20
  ";

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-top-20-staff-income widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="">
    <div class="panel_s user-data">
      <div class="panel-body perfex-dashboard-panel">
        <div class="widget-dragger"></div>

        <h4 class="pull-left mtop5"><?php echo _l('top_20_staff_income'); ?></h4>
        <div class="clearfix"></div>
        <div class="row mtop5">
          <hr class="hr-panel-heading-dashboard">
        </div>

        <table class="table dataTable no-footer dtr-inline">
          <thead>
            <th><?= _l('staff') ?></th>
            <th><?= _l('income') ?></th>
          </thead>
          <tbody>
            <?php if (count($widget_data) > 0) { ?>
              <?php foreach ($widget_data as $row_data) { ?>
                <tr>
                  <td><a href="<?= admin_url('staff/member/' . $row_data['staffid']) ?>"><?= $row_data['fullname'] ?></a></td>
                  <td class="priceable"><?= $row_data['totalincome'] ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="2"><?= _l('not_found') ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>