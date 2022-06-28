<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Wrapper -->
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo lang_base_url(); ?>"><?php echo trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $title; ?></li>
                    </ol>
                </nav>

                <h1 class="page-title"><?php echo $title; ?></h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="row-custom">
                    <div class="profile-tab-content">
                        <!-- include message block -->
                        <?php $this->load->view('partials/_messages'); ?>
                        <div class="table-responsive">
                            <table class="table table-quote_requests table-striped">
                                <thead>
                                <tr>
                                    <th scope="col"><?php echo '#'; ?></th>
                                    <th scope="col"><?php echo 'File APK'; ?></th>
                                    <th scope="col"><?php echo 'Convert Demo'; ?></th>
                                    <th scope="col"><?php echo trans("status"); ?></th>
                                    <th scope="col"><?php echo trans("sellers_bid"); ?></th>
                                    <th scope="col"><?php echo trans("updated"); ?></th>
                                    <th scope="col"><?php echo trans("options"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($convert_files_requests)): ?>
                                    <?php foreach ($convert_files_requests as $item): ?>


                                        <tr>
                                            <td>#<?php echo $item->id; ?></td>
                                            <td>
                                                <p><strong><?php echo ($item->file_apk); ?></strong></p>
                                            </td>
                                            <td>
                                                <?php if ( $item->status != 'rejected_quote' && $item->file_aab != null): ?>
                                                    <?php echo form_open('file_controller/download_convert_demo_file', ['id' => 'form_download_demo_file']); ?>
                                                    <input type="hidden" name="id" value="<?php echo $item->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-info color-white pull-right m-r-5" onclick="$('#form_download_demo_file').submit();">
                                                        <i class="icon-download-solid"></i>&nbsp;&nbsp;<?php echo trans("download"); ?>
                                                    </button>
                                                    <?php echo form_close();?>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if ($item->status == "new_convert_request"): ?>
                                                    <label class="label label-success"><?= trans($item->status); ?></label>
                                                <?php elseif ($item->status == "pending_quote"): ?>
                                                    <label class="label label-warning"><?= trans($item->status); ?></label>
                                                <?php elseif ($item->status == "pending_payment"): ?>
                                                    <label class="label label-info"><?= trans($item->status); ?></label>
                                                <?php elseif ($item->status == "rejected_quote"): ?>
                                                    <label class="label label-danger"><?= trans($item->status); ?></label>
                                                <?php elseif ($item->status == "closed"): ?>
                                                    <label class="label label-default"><?= trans($item->status); ?></label>
                                                <?php elseif ($item->status == "completed"): ?>
                                                    <label class="label label-primary"><?= trans($item->status); ?></label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($item->status != 'new_quote_request' && $item->price_offered != 0): ?>
                                                    <div class="table-seller-bid">
                                                        <p><strong><?= price_formatted($item->price_offered, $this->selected_currency->code); ?></strong></p>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo time_ago($item->updated_at); ?></td>
                                            <td>
                                                <?php if ($item->status == 'pending_quote'): ?>
                                                    <?php echo form_open('accept-convert-post'); ?>
                                                    <input type="hidden" name="id" class="form-control" value="<?php echo $item->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-info btn-table-option"><?php echo trans("accept_quote"); ?></button>
                                                    <?php echo form_close(); ?>

                                                    <?php echo form_open('reject-convert-post'); ?>
                                                    <input type="hidden" name="id" class="form-control" value="<?php echo $item->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-secondary btn-table-option"><?php echo trans("reject_quote"); ?></button>
                                                    <?php echo form_close(); ?>

                                                <?php elseif ($item->status == 'pending_payment'): ?>
                                                    <?php echo form_open('add-to-cart-file'); ?>
                                                    <input type="hidden" name="id" class="form-control" value="<?php echo $item->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-info btn-table-option"><i class="icon-cart"></i>&nbsp;<?php echo trans("add_to_cart"); ?></button>
                                                    <?php echo form_close(); ?>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-danger btn-table-option btn-delete-quote" onclick="delete_quote_request(<?php echo $item->id; ?>,'<?php echo trans("confirm_quote_request"); ?>');"><?php echo trans("delete_quote"); ?></button>
                                            </td>
                                        </tr>


                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($convert_files_requests)): ?>
                            <p class="text-center">
                                <?php echo trans("no_records_found"); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($convert_files_requests)): ?>
                    <div class="number-of-entries">
                        <span><?= trans("number_of_entries"); ?>:</span>&nbsp;&nbsp;<strong><?= $num_rows; ?></strong>
                    </div>
                <?php endif; ?>
                <div class="table-pagination">
                    <?php echo $this->pagination->create_links(); ?>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Wrapper End-->

