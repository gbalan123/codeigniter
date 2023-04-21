<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal PDF Upload</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/public/css/bootstrap.css">
    <!-- TDS-366 bootstrap icon cdn -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css'>    
</head>
<body>
    <div class="container">
        <!-- Message -->
        <div class="row p20">
            <?php if(Null !== session()->get("success")): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo session("success"); ?>
                </div>
            <?php endif; ?>
        
            <?php if(Null !== session()->get("error")): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo session("error"); ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Upload -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <form action="<?=site_url('InternalPDF/InternalPDFUpload');?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="name">Upload PDF :</label> 
                                        <input type="file" accept="application/pdf,text/html" class="form-control input-lg" name="cefr_ability" id="cefr_ability"  required />
                                    </div>
                                    <div class="pd_20">
                                        <button  name="cefr_ability_submit" class="btn btn-primary"><?php echo lang('app.language_admin_upload'); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- List -->
        <div class="row">
            <?php
            foreach($list_files as $list_file)
            {
                echo "<h5>$list_file</h5><br>";
            }
            ?>
        </div>
    </div>
</body>
</html>