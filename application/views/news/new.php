<section class="content">
    <!-- Page content-->
    <div class="container-fluid">
        <div class="block-header">
            <h2>Add News</h2>
        </div>

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body">
                        <div class="card-inner">
                            <form id="newNewsForm" method="POST" action="<?php echo base_url(); ?>saveNewNews"
                                enctype="multipart/form-data" style="margin-top:30px;">

                                <!--   <div class="input-group addon-line" style="margin-top:20px;">
                                  <label>News Date</label>
                                <div class="form-line">
                                    <input type="date" class="form-control" name="date" placeholder="News Date" required="" autofocus="">
                                </div>
                            </div>-->

                                <div class="input-group addon-line" style="margin-top:20px;">
                                    <label>News Writer/Author</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="author"
                                            placeholder="News Writer/Author" required="" autofocus=""
                                            value="<?php echo $author; ?>">
                                    </div>
                                </div>

                                <div class="input-group addon-line">
                                    <label>News CoverPhoto (Leave empty to use app default image)</label>
                                    <div class="form-line">
                                        <input type="file" name="thumbnail"
                                            data-allowed-file-extensions="png jpg jpeg PNG" class="thumbs_dropify">
                                    </div>
                                </div>

                                <div class="input-group addon-line" style="margin-top:20px;">
                                    <label>News Title</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="title" placeholder="News Title"
                                            required="" autofocus="">
                                    </div>
                                </div>

                                <div class="input-group addon-line" style="margin-top:30px;">
                                    <label>News Content</label>
                                    <div class="form-line">
                                        <textarea class="editor" name="content">Add News Content Here</textarea>
                                    </div>
                                </div>

                                <?php $this->load->helper('form'); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                                    </div>
                                </div>
                                <?php
                                $error = $this->session->flashdata('error');
                                if ($error) { ?>
                                    <div class="alert alert-danger alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">×</button>
                                        <?php echo $error; ?>
                                    </div>
                                <?php }
                                $success = $this->session->flashdata('success');
                                if ($success) { ?>
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">×</button>
                                        <?php echo $success; ?>
                                    </div>
                                <?php } ?>

                                <div class="box-footer text-center">
                                    <button id="saveNewsBtn" class="btn btn-primary waves-effect"
                                        type="submit">SAVE</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Translation Progress Overlay ===== -->
<div id="newsTranslationOverlay"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.78); z-index:99999; align-items:center; justify-content:center; flex-direction:column;">
    <div
        style="background:#fff; border-radius:14px; padding:40px 50px; max-width:480px; width:90%; text-align:center; box-shadow:0 24px 64px rgba(0,0,0,0.45);">

        <!-- Spinner -->
        <div style="margin-bottom:22px;">
            <div
                style="width:64px; height:64px; border:6px solid #e0e0e0; border-top:6px solid #3F51B5; border-radius:50%; margin:0 auto; animation:newsSpin 0.9s linear infinite;">
            </div>
        </div>

        <h3 style="margin:0 0 6px; font-size:20px; color:#212121; font-weight:700;">Saving News Article</h3>
        <p id="newsStatusText" style="color:#666; font-size:14px; margin:0 0 22px;">Preparing content...</p>

        <!-- Step list -->
        <div style="text-align:left; background:#f5f5f5; border-radius:10px; padding:14px 18px;">
            <div class="news-step" id="news-step1"
                style="display:flex; align-items:center; gap:10px; padding:7px 0; color:#bbb; font-size:13px;">
                <span class="news-icon">⏳</span><span>Saving news article to database</span>
            </div>
            <div class="news-step" id="news-step2"
                style="display:flex; align-items:center; gap:10px; padding:7px 0; color:#bbb; font-size:13px;">
                <span class="news-icon">⏳</span><span>Translating to French &amp; German</span>
            </div>
            <div class="news-step" id="news-step3"
                style="display:flex; align-items:center; gap:10px; padding:7px 0; color:#bbb; font-size:13px;">
                <span class="news-icon">⏳</span><span>Translating to Italian, Spanish &amp; Hindi</span>
            </div>
            <div class="news-step" id="news-step4"
                style="display:flex; align-items:center; gap:10px; padding:7px 0; color:#bbb; font-size:13px;">
                <span class="news-icon">⏳</span><span>Translating to Russian, Portuguese &amp; Mandarin</span>
            </div>
            <div class="news-step" id="news-step5"
                style="display:flex; align-items:center; gap:10px; padding:7px 0; color:#bbb; font-size:13px;">
                <span class="news-icon">⏳</span><span>Finalizing &amp; saving all translations</span>
            </div>
        </div>

        <p style="color:#bbb; font-size:12px; margin-top:18px; margin-bottom:0;">Please wait — do not close or refresh
            this page.</p>
    </div>
</div>

<style>
    @keyframes newsSpin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .news-step.active {
        color: #3F51B5 !important;
        font-weight: 600;
    }

    .news-step.done {
        color: #4CAF50 !important;
    }
</style>

<script>
    document.getElementById('newNewsForm').addEventListener('submit', function () {
        // Show overlay
        var overlay = document.getElementById('newsTranslationOverlay');
        overlay.style.display = 'flex';

        // Disable button to prevent double submit
        var btn = document.getElementById('saveNewsBtn');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        var steps = [
            { id: 'news-step1', delay: 200, text: 'Saving news article ...' },
            { id: 'news-step2', delay: 1800, text: 'Translating to French & German...' },
            { id: 'news-step3', delay: 5500, text: 'Translating to Italian, Spanish & Hindi...' },
            { id: 'news-step4', delay: 9500, text: 'Translating to Russian, Portuguese & Mandarin...' },
            { id: 'news-step5', delay: 14000, text: 'Finalizing & saving all translations...' },
        ];

        steps.forEach(function (step, index) {
            setTimeout(function () {
                // Mark previous step as done
                if (index > 0) {
                    var prev = document.getElementById(steps[index - 1].id);
                    prev.classList.remove('active');
                    prev.classList.add('done');
                    prev.querySelector('.news-icon').textContent = '✅';
                }
                // Activate current step
                var el = document.getElementById(step.id);
                el.classList.add('active');
                el.querySelector('.news-icon').textContent = '🔄';
                document.getElementById('newsStatusText').textContent = step.text;
            }, step.delay);
        });
    });
</script>