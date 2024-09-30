<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Top 10 Selling Products
  Description: Top 10 Selling Products
  
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
      TBItemable.description AS description,
      (SUM(TBItemable.qty)) AS quantity_sold,
      SUM(TBItemable.rate * TBItemable.qty) AS rate,
      AVG(TBItemable.rate * TBItemable.qty) AS avg_price
    FROM 
      " . db_prefix() . "itemable TBItemable
          JOIN 
      " . db_prefix() . "invoices TBInvoices ON TBInvoices.id = TBItemable.rel_id
    WHERE TBItemable.rel_type = 'invoice' AND TBInvoices.status = 2 
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= " 
      AND TBInvoices.date >= '" . $widget_req_from . "' AND TBInvoices.date <= '" . $widget_req_to . "' 
    ";
  }
  $sql .= "
    GROUP BY description
    ORDER BY rate DESC
    LIMIT 0 , 10
  ";
  
  return $this->db->query($sql )->result_array();
};

$widget_data = $fn_get_data();
?>
<div class="widget widget-top-10-selling-products widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="">
    <div class="panel_s">
      <div class="panel-body">
        <div class="widget-dragger"></div>

        <h4 class="pull-left mtop5"><?php echo _l('top_10_selling_products'); ?></h4>
        <div class="clearfix"></div>
        <div class="row mtop5">
          <hr class="hr-panel-heading-dashboard">
        </div>

        <table class="table dataTable no-footer dtr-inline">
          <thead>
            <th><?= _l('name') ?></th>
            <th><?= _l('avg_price') ?></th>
            <th><?= _l('sold') ?></th>
            <th><?= _l('revenue') ?></th>
          </thead>
          <tbody>
            <?php if (count($widget_data) > 0) { ?>
              <?php foreach ($widget_data as $row_data) { ?>
                <tr>
                  <td><?= $row_data['description'] ?></td>
                  <td class="priceable"><?= $row_data['avg_price'] ?></td>
                  <td class="quantity-num"><?= $row_data['quantity_sold'] ?></td>
                  <td class="priceable"><?= $row_data['rate'] ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="4"><?= _l('not_found') ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>