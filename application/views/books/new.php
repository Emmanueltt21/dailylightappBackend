<section class="content">
    <div class="container-fluid">
        <div class="block-header">
          <h2>Add New Book</h2>
        </div>
    <!-- Page content-->
    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">

                        <div class="body">
                          <div class="card-inner">
                          <form id="upload-form">
                            <div class="addon-line">

                                <div class="form-line">
                                  <label>Book Category</label>
                                  <select class="form-control" id="cat_id" required="" autofocus="">
                                    <option value="">select Book category</option>
                                    <?php foreach ($categories as $res) { ?>
                                      <option value="<?php echo $res->id; ?>"><?php echo $res->cat_name; ?></option>
                                    <?php  } ?>
                                  </select>
                                </div>
                            </div>
                            <div class="addon-line" style="margin-top:20px;  display:none;">

                                <div class="form-line">
                                  <label>Book SubCategory</label>
                                  <select class="form-control" id="subcategories">
                                    <option value="0">Select Audio SubCategory</option>
                                    <?php foreach ($subcategories as $res) { ?>
                                      <option value="<?php echo $res->id; ?>"><?php echo $res->name; ?></option>
                                    <?php  } ?>
                                  </select>
                                </div>
                            </div>
                            <div class="input-group addon-line" style="margin-top:20px;">
                                <label>Book Title (Users can search for this book using this title)</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="b_title" placeholder="Book Title" required="" autofocus="">
                                </div>
                            </div>
                             
                             <div class="input-group addon-line" style="margin-top:30px;">
                                  <label>Book Description (Users can search for this Book using this description)</label>
                                <div class="form-line">
                                    <!--<textarea class="editor"  id ="b_desc" name="b_desc" placeholder="Book Description" required="" autofocus=""></textarea> -->
                                  <textarea type="text" class="form-control" id="b_desc" placeholder="Book Description" required="" autofocus=""><?php echo $book->b_desc;  ?></textarea>

                                </div>
                            </div>
                            
                             <div class="input-group addon-line" style="margin-top:20px;">
                                <label>Book price </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="amount" placeholder="Book price (In USD  example :   50 )" required="" autofocus="">
                                </div>
                            </div>

                            <div class="addon-line" style="margin-top:20px;">
                                <div class="form-line">
                                  <label>Book Type</label>
                                    <select class="form-control" id="audio_type" required="" autofocus="">
                                        <option value="0" selected>Upload Book File</option>
                                       <!-- <option value="1" >Provide Audio Link</option>on>-->
                                    </select>
                                </div>
                            </div>
                            <div class="input-group addon-line" style="margin-top:20px;">
                                <label>BookStore Link (Amazon, or External Store)</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="book_url" placeholder="Book Store Link" required="" autofocus="">
                                </div>
                            </div>

                            <div id="upload_div">
                              <div class="input-group addon-line" style="margin-top:20px;">
                                  <label>Book CoverPhoto (Please resize your image before uploading)</label>
                                  <div class="form-line">
                                      <input id="thumbnail" type="file" data-allowed-file-extensions="jpeg jpg png JPEG PNG" class="dropify2" required>
                                  </div>
                              </div>

                              <div class="input-group addon-line" style="margin-top:20px;">
                                     <label>pdf File (Please resize your file before uploading)</label>
                                  <div class="form-line">
                                      <input id="pdf-file" type="file" name="pdf" data-allowed-file-extensions="pdf" class="dropify" required>
                                  </div>

                              </div>
                            </div>

                            <div id="link_div" style="margin-top:20px; display:none;">
                              <div class="input-group addon-line" style="margin-top:20px;">
                                  <label>CoverPhoto Link</label>
                                  <div class="form-line">
                                      <input type="url" class="form-control" id="thumbnail_link" placeholder="Coverphoto Link" autofocus="">
                                  </div>
                              </div>

                              <div class="input-group addon-line" style="margin-top:20px;">
                                     <label>book Link</label>

                                  <div class="form-line">
                                      <input type="url" class="form-control" id="media_link" placeholder="Book Link" autofocus="">
                                  </div>

                              </div>
                            </div>

                  <!--            <div class="input-group addon-line" style="margin-top:20px;">
                                <label>Audio Duration (format 00:00)</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="duration" name="duration" placeholder="Audio duration" required="" autofocus="">
                                </div>
                            </div> -->

                              <div class="addon-line" style="margin-top:20px; display:none;">

                                  <div class="form-line">
                                    <label>Allow users stream this Book for free?</label>
                                      <select class="form-control" id="is_free" required="" autofocus="">
                                          <option value="0">NO</option>
                                          <!--   <option value="1">YES</option> -->
                                      </select>
                                  </div>
                              </div>

                              <div class="addon-line" style="margin-top:20px;">

                                  <div class="form-line">
                                    <label>Allow users download this Book  to their device?</label>
                                      <select class="form-control" id="can_download" required="" autofocus="">
                                          <option value="0">NO</option>
                                          <!--  <option value="1">YES</option>-->
                                      </select>
                                  </div>
                              </div>

                              <div class="addon-line" style="margin-top:20px; display:none;">

                                  <div class="form-line">
                                    <label>Allow users preview a few seconds of this audio?</label>
                                      <select class="form-control" id="can_preview" required="" autofocus="">
                                          <option value="0">YES</option>
                                          <option value="1">NO</option>
                                      </select>
                                  </div>
                              </div>

                         <!--      <div class="input-group addon-line" style="margin-top:20px; display:none;">
                                  <label>How many seconds preview is allowed for this audio? (If user cant preview this Book, leave default value)</label>
                                  <div class="form-line">
                                      <input type="number" value="60" class="form-control" id="preview_duration" placeholder="Preview Duration in seconds" required="" autofocus="">
                                  </div>
                              </div>-->

                              <div class="addon-line" style="margin-top:20px;">

                                  <div class="form-line">
                                    <label>Send a broadcast to notify users of this Book?</label>
                                      <select class="form-control" id="notify" required="" autofocus="">
                                          <option value="true">YES</option>
                                        <!--    <option value="false">NO</option>-->
                                      </select>
                                  </div>
                              </div>

                            <div class="box-footer text-center" style="margin-top:20px;">
                               <button id="submit" onclick="uploadNewBook(event)" class="btn btn-primary waves-effect" type="submit">UPLOAD NEW BOOK</button>
                               <ol class="breadcrumb align-center" id="loader" style="display:none;">
                                 <li><span style="font-size:18px; color:grey; font-style:italic;" id="publish_hint">Processing Request, Please Wait..</span>
                                   <br>
                                   <div class="preloader pl-size-xs">
                                       <div class="spinner-layer pl-teal">
                                           <div class="circle-clipper left">
                                               <div class="circle"></div>
                                           </div>
                                           <div class="circle-clipper right">
                                               <div class="circle"></div>
                                           </div>
                                       </div>
                                   </div>
                                 </li>
                               </ol>
                            </div>

                          </form>
                        </div>
                      </div>
                    </div>
                </div>
    </div>
</section>
