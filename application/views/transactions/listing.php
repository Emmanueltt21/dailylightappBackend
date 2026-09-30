
<section class="content">
    <!-- Page content-->
    <div class="container-fluid">
        <div class="block-header">
            <h2>Transaction Lists</h2>
          
        </div>


            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">

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
                            <table id="transactions_table" class="table table-responsive table-bordered table-striped table-hover exportable">
                                <thead>
                                <tr>
                                  <th>Id</th>
                                  <th>Full Name</th>
                                  <th>Transaction code </th>
                                  <th>Option </th>
                                  <th>pack Name </th>
                                  <th>Book Id </th>
                                  <th>Email </th>
                                  <th>Status </th>
                                  <th>Message </th>
                                  <th>Country </th>
                                  <th>Town </th>
                                  <th>Internal Id </th>
                                  <th>Date </th>
                                </tr>
                                </thead>
                              <tbody>
                                 
                                </tbody>
                            </table>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
</section>
