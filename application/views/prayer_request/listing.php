
<section class="content">
    <!-- Page content-->
    <div class="container-fluid">
        <div class="block-header">
            <h2><i class="material-icons">favorite</i> Prayer Requests and Testimony Management</h2>
        </div>

        <!-- Exportable Table -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Prayer Requests List</h2>
                    </div>
                    <div class="body">
                        <?php
                        $this->load->helper('form');
                        $error = $this->session->flashdata('error');
                        if($error)
                        {
                            ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $error; ?>
                            </div>
                        <?php }
                        $success = $this->session->flashdata('success');
                        if($success)
                        {
                            ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $success; ?>
                            </div>
                        <?php } ?>
                        
                        <div style="overflow-x:auto;">
                            <table id="categories-table" class="table table-responsive table-bordered table-striped table-hover exportable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>Author</th>
                                    <th>Content</th>
                                    <th>Response</th>
                                    <th>Admin</th>
                                    <th>Request Date</th>
                                    <th>Response Date</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    <?php
                                    $count=1;
                                    forEach($request as $record){
                                    ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $record->title; ?></td>
                                        <td><?php echo $record->author; ?></td>
                                        <td><?php echo $record->content; ?></td>
                                        <td><?php echo $record->response; ?></td>
                                        <td><?php echo $record->utimo; ?></td>
                                        <td><?php echo $record->dou; ?></td>
                                        <td><?php echo $record->dmo; ?></td>
                                        <td>
                                            <a href="<?php echo site_url().'respondPrayerRequest/'.$record->id; ?>" 
                                               type="button" 
                                               class="btn btn-success btn-sm waves-effect" 
                                               title="Respond to Prayer Request">
                                                <i class="material-icons">reply</i>
                                            </a>
                                            <button onclick="deletePrayerRequest(<?php echo $record->id; ?>)" 
                                                    type="button" 
                                                    class="btn btn-danger btn-sm waves-effect" 
                                                    title="Delete Prayer Request">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $count++;}
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function deletePrayerRequest(id) {
    if (confirm('Are you sure you want to delete this prayer request?')) {
        $.ajax({
            url: '<?php echo base_url(); ?>deletePrayerRequest',
            type: 'POST',
            data: {id: id},
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    alert('Prayer request deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting prayer request');
            }
        });
    }
}
</script>
