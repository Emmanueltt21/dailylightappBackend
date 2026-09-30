<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <h2><i class="material-icons">favorite</i> Respond to Prayer Request</h2>
            <small>Provide a thoughtful and caring response to this prayer request</small>
        </div>

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header bg-blue-grey">
                        <h2><i class="material-icons">info</i> Prayer Request Details</h2>
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="<?php echo base_url(); ?>getPrayer_requestweb" class="btn btn-xs btn-default waves-effect">
                                    <i class="material-icons">arrow_back</i> Back to List
                                </a>
                            </li>
                        </ul>
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
                                <i class="material-icons">error</i> <?php echo $error; ?>
                            </div>
                        <?php }
                        $success = $this->session->flashdata('success');
                        if($success)
                        {
                            ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="material-icons">check_circle</i> <?php echo $success; ?>
                            </div>
                        <?php } ?>

                        <!-- Prayer Request Information Card -->
                        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #2196F3;">
                            <div class="body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-light-blue hover-expand-effect">
                                            <div class="icon">
                                                <i class="material-icons">title</i>
                                            </div>
                                            <div class="content">
                                                <div class="text">Subject</div>
                                                <div class="number"><?php echo htmlspecialchars($prayer_request->title); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-light-green hover-expand-effect">
                                            <div class="icon">
                                                <i class="material-icons">person</i>
                                            </div>
                                            <div class="content">
                                                <div class="text">Author</div>
                                                <div class="number"><?php echo htmlspecialchars($prayer_request->author); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-orange hover-expand-effect">
                                            <div class="icon">
                                                <i class="material-icons">email</i>
                                            </div>
                                            <div class="content">
                                                <div class="text">Email</div>
                                                <div class="number" style="font-size: 14px;"><?php echo htmlspecialchars($prayer_request->uti); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-purple hover-expand-effect">
                                            <div class="icon">
                                                <i class="material-icons">date_range</i>
                                            </div>
                                            <div class="content">
                                                <div class="text">Request Date</div>
                                                <div class="number" style="font-size: 14px;"><?php echo $prayer_request->dou; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card" style="border-left: 4px solid #4CAF50;">
                                            <div class="header bg-light-green">
                                                <h2><i class="material-icons">message</i> Prayer Request Content</h2>
                                            </div>
                                            <div class="body" style="background-color: #f9f9f9; padding: 20px; border-radius: 5px;">
                                                <p style="font-size: 16px; line-height: 1.6; color: #333; margin: 0;">
                                                    <?php echo nl2br(htmlspecialchars($prayer_request->content)); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if(!empty($prayer_request->response)): ?>
                        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #FF9800;">
                            <div class="header bg-orange">
                                <h2><i class="material-icons">reply</i> Current Response</h2>
                            </div>
                            <div class="body" style="background-color: #fff3e0; padding: 20px; border-radius: 5px;">
                                <p style="font-size: 16px; line-height: 1.6; color: #333; margin-bottom: 15px;">
                                    <?php echo nl2br(htmlspecialchars($prayer_request->response)); ?>
                                </p>
                                <div class="alert alert-info" style="margin: 0;">
                                    <i class="material-icons">person</i> <strong>Responded by:</strong> <?php echo $prayer_request->utimo; ?> 
                                    <i class="material-icons" style="margin-left: 15px;">schedule</i> <strong>on:</strong> <?php echo $prayer_request->dmo; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Response Form -->
                        <div class="card" style="border-left: 4px solid #4CAF50;">
                            <div class="header bg-light-green">
                                <h2><i class="material-icons"><?php echo !empty($prayer_request->response) ? 'edit' : 'add_comment'; ?></i> 
                                    <?php echo !empty($prayer_request->response) ? 'Update Response' : 'Add Response'; ?>
                                </h2>
                            </div>
                            <div class="body">
                                <form action="<?php echo base_url(); ?>updatePrayerResponse" method="post">
                                    <input type="hidden" name="id" value="<?php echo $prayer_request->id; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <i class="material-icons">message</i> 
                                                    <strong><?php echo !empty($prayer_request->response) ? 'Update Your Response:' : 'Your Response:'; ?></strong>
                                                </label>
                                                <div class="form-line focused">
                                                    <textarea name="response" class="form-control" rows="8" 
                                                              placeholder="Enter your thoughtful and caring response to this prayer request. Remember that your words can bring comfort and hope to someone in need..." 
                                                              required style="font-size: 16px; line-height: 1.6;"><?php echo !empty($prayer_request->response) ? htmlspecialchars($prayer_request->response) : ''; ?></textarea>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="material-icons" style="font-size: 14px;">info</i> 
                                                    Take your time to provide a meaningful response that shows care and understanding.
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12" style="text-align: center; padding-top: 20px;">
                                            <button type="submit" class="btn btn-lg btn-success waves-effect" style="margin-right: 10px;">
                                                <i class="material-icons">send</i>
                                                <?php echo !empty($prayer_request->response) ? 'Update Response' : 'Send Response'; ?>
                                            </button>
                                            <a href="<?php echo base_url(); ?>getPrayer_requestweb" class="btn btn-lg btn-default waves-effect">
                                                <i class="material-icons">arrow_back</i>
                                                Back to List
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>