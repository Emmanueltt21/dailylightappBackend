<section class="content">
    <!-- Page content-->
    <div class="container-fluid">
        <div class="block-header">
            <h2>Edit News</h2>
        </div>


            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">

                        <div class="body">
                          <div class="card-inner">
                          <form method="POST" action="<?php echo base_url(); ?>editNewsData" enctype="multipart/form-data" style="margin-top:30px;">
                            <input type="hidden" class="form-control" name="id"  value="<?php echo $news->id; ?>">
                           <!--   <div class="input-group addon-line" style="margin-top:20px;">
                                  <label>News Date</label>
                                <div class="form-line">
                                    <input type="date" class="form-control" name="date" placeholder="News Date" required="" autofocus="" value="<?php echo $news->date; ?>">
                                </div>
                            </div>-->
                            
                              <div class="input-group addon-line" style="margin-top:20px;">
                                 <label>News Writer/Author</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="author" placeholder="News Writer/Author" required="" autofocus="" value="<?php echo $news->author; ?>">
                                </div>
                            </div>

                          <div class="input-group addon-line">
                                <label>News CoverPhoto (Leave empty to use app default image)</label>
                                <div class="form-line">
                                    <input data-default-file="<?php echo $news->thumbnail; ?>" type="file" name="thumbnail" data-allowed-file-extensions="png jpg jpeg PNG" class="thumbs_dropify" >
                                </div>
                            </div>
                            <div class="input-group addon-line" style="margin-top:20px;">
                                 <label>News Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="title" placeholder="Devotional Title" required="" autofocus="" value="<?php echo $news->title; ?>">
                                </div>
                            </div>

                            <div class="input-group addon-line" style="margin-top:30px;">
                                  <label>News Content</label>
                                <div class="form-line">
                                  <textarea class="editor" name="content"><?php echo $news->content; ?></textarea>
                                </div>
                            </div>
                            
                            
                            
                             <div class="input-group addon-line" style="margin-top:20px;">
                                 <label>French Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="french_title" placeholder="French Title" required="" autofocus="" value="<?php echo $news->french_title; ?>">
                                </div>
                            </div>
                             <div class="input-group addon-line" style="margin-top:30px;">
                                  <label>French Content</label>
                                <div class="form-line">
                                  <textarea class="editor" name="french_content"><?php echo $news->french_content; ?></textarea>
                                </div>
                            </div>
                            
                            
                            
                             <div class="input-group addon-line" style="margin-top:20px;">
                                 <label>German Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="german_title" placeholder="German Title" required="" autofocus="" value="<?php echo $news->german_title; ?>">
                                </div>
                            </div>
                            
                             <div class="input-group addon-line" style="margin-top:30px;">
                                  <label>German Content</label>
                                <div class="form-line">
                                  <textarea class="editor" name="german_content"><?php echo $news->german_content; ?></textarea>
                                </div>
                            </div>




                            </div>


                             <?php $this->load->helper('form'); ?>
                             <div class="row">
                                 <div class="col-md-12">
                                     <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                                 </div>
                             </div>
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

                            <div class="box-footer text-center">
                               <button class="btn btn-primary waves-effect" type="submit">Edit News</button>
                            </div>

                          </form>
                        </div>
                      </div>
                    </div>
                </div>
    </div>
</section>
