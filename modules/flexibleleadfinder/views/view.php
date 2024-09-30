<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <?php echo $this->load->view('partials/search-detail') ?>
</div>
<?php init_tail(); ?>
<script>
    'use strict';
    
    $(document).ready(function () {
        // console.log('ready')
        function reloadPage(){
            let url = `<?php echo flexibleleadfinder_admin_url('view/' . $search['id']); ?>`;

            $.get(url, {},
                function (response, textStatus, jqXHR) {
                    if(response.success){
                        $('#wrapper').html(response.html)
                    }else{
                        alert_float('danger', response.message)
                    }
                },
                "json"
            );
        }

        $(document).on('click', '.flexlf-delete-contact', function(e){
            e.preventDefault();

            if (confirm_delete()) {
                let id = $(this).data('id');
                let url = `<?php echo flexibleleadfinder_admin_url('delete_contact'); ?>/${id}`;
    
                $.post(url, {},
                    function (response, textStatus, jqXHR) {
                        if(response.success){
                            alert_float('success', response.message)
                            reloadPage()
                        }else{
                            alert_float('danger', response.message)
                        }
                    },
                    "json"
                );
            }

            return false;
        })

        $(document).on('click', '.flexlf-sync-contact', function (e) {
            e.preventDefault();

            let id = $(this).data('id');
            let url = `<?php echo flexibleleadfinder_admin_url('sync_contact'); ?>/${id}`;

            $.post(url, {},
                function (response, textStatus, jqXHR) {
                    if (response.success) {
                        alert_float('success', response.message)
                        reloadPage()
                    } else {
                        alert_float('danger', response.message)
                    }
                },
                "json"
            );
        })
        
        $(document).on('click', '.flexlf-sync-all', function (e) {
            e.preventDefault();

            let id = $(this).data('id');
            let url = `<?php echo flexibleleadfinder_admin_url('sync_all'); ?>/${id}`;

            $.post(url, {},
                function (response, textStatus, jqXHR) {
                    if (response.success) {
                        alert_float('success', response.message)
                        reloadPage()
                    } else {
                        alert_float('danger', response.message)
                    }
                },
                "json"
            );
        })
    });
</script>
</body>

</html>