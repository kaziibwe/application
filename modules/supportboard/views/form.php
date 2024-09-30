<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">   
                   <div class="form-group form-sb">
                        <label class="bold"><?php echo _l('Support Board Plugin URL') ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('Ex. https://www.your-site.com/supportboard') ?>"></i></label>
                        <br><input name="sb_perfex_url" id="sb_perfex_url" class="form-control" type="text" value="<?php echo get_option('sb_url'); ?>">
                        <p><?php echo _l('Enter the URL of Support Board.') ?></p>
                        <br>
                        <label class="bold"><?php echo _l('Support Board Button Name') ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('Leave empty to disable') ?>"></i></label>
                        <br><input name="sb_perfex_button" id="sb_perfex_button" class="form-control" type="text" value="<?php echo get_option('sb_button') ?>">
                        <p><?php echo _l('Enter the name of the menu link that will appear in the left sidebar. The link will open the Support Board admin area.') ?></p>
                        <br>
                        <label class="bold"><?php echo _l('Support Board Admin Area') ?> </label>
                        <br>
                        <?php $sb_admin_type = get_option('sb_admin_type') ?>
                        <select name="sb_admin_type" id="sb_admin_type" class="form-control">
                           <option value="new-window" <?php if ($sb_admin_type == 'new-window') echo 'selected="selected"' ?>><?php echo _l('New window') ?></option>
                           <option value="inside" <?php if ($sb_admin_type == 'inside') echo 'selected="selected"' ?>><?php echo _l('Inside Perfex') ?></option>
                        </select>
                        <p><?php echo _l('Set where to open the Support Board admin panel.') ?></p>
                        <br>
                        <label class="bold"><?php echo _l('Support Board Path') ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('Ex. /var/www/html/supportboard') ?>"></i></label>
                        <br><input name="sb_perfex_path" id="sb_perfex_path" class="form-control" type="text" value="<?php echo get_option('sb_perfex_path') ?>">
                        <p><?php echo _l('Required if the Support Board admin panel is open inside Perfex. Get it from Support Board > Settings > Miscellaneous > Support Board Path.') ?></p>
                        <br>
                        <label class="bold"><?php echo _l('Tickets Area Button Name') ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('Leave empty to disable') ?>"></i></label>
                        <br><input name="sb_perfex_button_tickets" id="sb_perfex_button_tickets" class="form-control" type="text" value="<?php echo get_option('sb_button_tickets'); ?>">
                        <p><?php echo _l('Enter the name of the tickets menu that will appear in the client area. The link will open the Support Board Tickets area. This feature requires the Tickets App (https://board.support/tickets).') ?></p>
                       <br>
                       <label class="bold"><?php echo _l('Disable auto-loading') ?></label>
                       <br><input name="sb_disable_chat" id="sb_disable_chat" class="form-control" type="checkbox" style="width: 20px; box-shadow: none" <?php if (get_option('sb_disable_chat') == 'true') echo 'checked'; ?>>
                       <p><?php echo _l('Disable the automatic loading of the chat in the customers\' area. Activate this option if the chat is manually included via script.') ?></p>
                    </div>
                   <br>
                   <a id="sb-save" href="#" class="btn btn-info"><?php echo _l('Save Changes') ?></a>
                   <br><br>
                   <p><?php echo _l('Rating our module <a href="https://codecanyon.net/downloads">here</a> will help us continue developing it!') ?></p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        $(document).ready(function () {
            $('#sb-save').on('click', function (e) {
                $.post(admin_url + 'supportboard/save', {
                     sb_url: $('#sb_perfex_url').val(),
                     sb_button: $('#sb_perfex_button').val(),
                     sb_button_tickets: $('#sb_perfex_button_tickets').val(),
                     sb_admin_type: $('#sb_admin_type').val(),
                     sb_perfex_path: $('#sb_perfex_path').val(),
                     sb_disable_chat: $('#sb_disable_chat').is(':checked') ? 'true' : 'false'
                }).done(function (response) {
                     let code = '<div id="sb_alert" class="float-alert animated fadeInRight col-xs-10 col-sm-3 alert alert-' + (response == 'success' ? 'success' : 'warning') + '"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="fa fa-bell-o" data-notify="icon"></span><span class="alert-title">' + (response == 'success' ? '<?php echo _l('Settings saved') ?>' : '<?php echo _l('Invalid URL. Enter the Support Board URL.') ?>') + '</span></div>';
                     $('body').append(code);
                });
                e.preventDefault();
                return false;
            }); 
        });
   });
</script>
<style>
   .form-sb input + p,.form-sb select + p {
        color: #4e75ad;
        margin-top: 5px;
        opacity: .6;
    }
   .form-sb input + p:hover,.form-sb select + p:hover {
        opacity: 1;
    }
</style>
</body>
</html>