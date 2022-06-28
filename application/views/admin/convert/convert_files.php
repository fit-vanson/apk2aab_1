<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo $title; ?></h3>
    </div><!-- /.box-header -->

    <div class="box-body">
        <div class="row">
            <!-- include message block -->
            <div class="col-sm-12">
                <?php $this->load->view('admin/includes/_messages'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <?php $this->load->view('admin/convert/_filter_convert_requests'); ?>
                        <thead>
                        <tr role="row">
                            <th><?php echo trans('#'); ?></th>
                            <th><?php echo "Name"; ?></th>
                            <th><?php echo trans('status'); ?></th>
                            <th><?php echo 'Apk'; ?></th>
                            <th><?php echo 'Aab'; ?></th>
                            <th><?php echo 'Aab-apk'; ?></th>
                            <th><?php echo trans('updated'); ?></th>
                            <th><?php echo trans('date'); ?></th>
                            <th class="max-width-120"><?php echo trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($convert_files as $item): ?>
                            <tr>
                                <td>#<?php echo $item->id; ?></td>
                                <td>
                                    <?php $user = get_user($item->buyer_id);

                                    if (!empty($user)):?>

                                        <a href="<?php echo generate_profile_url($user->slug); ?>" target="_blank" class="link-black">
                                            <strong class="font-600"><?= html_escape($user->username) . ' - '. html_escape($user->phone_number); ?></strong>
                                            <p><strong><?= $user->email; ?></strong></p>
                                        </a>
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

                                        <div class="table-seller-bid">
                                            <p><strong><?php echo ($item->file_apk); ?></strong></p>
                                        </div>

                                </td>
                                <td>
                                    <?php if ($item->status != 'new_convert_request' && $item->file_aab != null ): ?>
                                        <div class="table-seller-bid">
                                            <p><strong><?php echo ($item->file_aab); ?></strong></p>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item->status != 'new_convert_request' && $item->file_aab_apk != null ): ?>
                                        <div class="table-seller-bid">
                                            <p><strong><?php echo ($item->file_aab_apk); ?></strong></p>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item->status != 'new_convert_request' && $item->price_offered != 0): ?>
                                        <div class="table-seller-bid">
                                            <p><strong><?php echo price_formatted($item->price_offered, $item->price_currency); ?></strong></p>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatted_date($item->created_at); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?php echo trans('select_option'); ?>
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu options-dropdown">
                                            <?php if ($item->status == 'new_convert_request'): ?>
                                                <li>
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSubmitQuote<?php echo $item->id; ?>"><i class="fa fa-plus option-icon"></i><?= trans("submit_a_quote"); ?></a>
                                                </li>
                                            <?php elseif ($item->status == 'pending_quote'): ?>
                                                <li>
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSubmitQuote<?php echo $item->id; ?>"><i class="fa fa-edit option-icon"></i><?= trans("update_quote"); ?></a>
                                                </li>
                                            <?php elseif ($item->status == 'rejected_quote'): ?>
                                                <li>
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSubmitQuote<?php echo $item->id; ?>"><i class="fa fa-refresh option-icon"></i><?= trans("submit_a_new_quote"); ?></a>
                                                </li>
                                            <?php elseif ($item->status == 'completed'): ?>
                                                <li>
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSubmitQuote<?php echo $item->id; ?>"><i class="fa fa-plus option-icon"></i><?= trans("submit"); ?></a>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <a href="javascript:void(0)" onclick="delete_quote_request(<?php echo $item->id; ?>,'<?php echo trans("confirm_quote_request"); ?>');"><i class="fa fa-trash option-icon"></i><?php echo trans('delete'); ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>
                    </table>

                    <?php if (empty($convert_files)): ?>
                        <p class="text-center">
                            <?php echo trans("no_records_found"); ?>
                        </p>
                    <?php endif; ?>
                    <div class="col-sm-12 table-ft">
                        <div class="row">
                            <div class="pull-right">
                                <?php echo $this->pagination->create_links(); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div><!-- /.box-body -->
</div>

<!-- Modal -->
<?php if (!empty($convert_files)):
    foreach ($convert_files as $convert_file):
//        $quote_product = get_product($quote_request->product_id);
    ?>
        <div class="modal fade" id="modalSubmitQuote<?php echo $convert_file->id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-custom">
                    <!-- form start -->
                    <?php echo form_open_multipart('submit-convert-post'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo trans("submit_a_convert"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true"><i class="icon-close"></i> </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" class="form-control" value="<?php echo $convert_file->id; ?>">


                        <?php if($convert_file->status != 'completed' ) :?>


                            <div class="form-group">
                                <label class="control-label"><?php echo 'File Aab'; ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>

                                    <input type="file" class="form-control form-input" name="file"   <?php echo ($this->rtl == true) ? 'dir="rtl"' : ''; ?> required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php echo trans('price'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><?= $this->default_currency->symbol; ?></span>
                                    <input type="hidden" name="currency" value="<?php echo $this->payment_settings->default_currency; ?>">
                                    <input type="text" name="price" aria-describedby="basic-addon1" class="form-control form-input price-input validate-price-input" data-item-id="<?php echo $convert_file->id; ?>" data-product-quantity="<?php echo 1; ?>"
                                           placeholder="<?php echo $this->input_initial_price; ?>" onpaste="return false;" maxlength="32" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <p class="calculated-price">
                                    <strong><?php echo trans("unit_price"); ?> (<?= $this->default_currency->symbol; ?>):&nbsp;&nbsp;
                                        <span id="unit_price_<?php echo $convert_file->id; ?>" class="earned-price">
                                        <?php echo number_format(0, 2, '.', ''); ?>
                                    </span>
                                    </strong><br>

                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if($convert_file->status == 'completed' ) :?>

                        <div class="form-group">
                            <label class="control-label"><?php echo 'File Aab -  Apk'; ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                <input type="file" class="form-control form-input" name="fileConvert"   <?php echo ($this->rtl == true) ? 'dir="rtl"' : ''; ?> required>
                            </div>
                        </div>

                        <?php endif; ?>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-md btn-default" data-dismiss="modal"><?php echo trans("close"); ?></button>
                        <button type="submit" class="btn btn-md btn-success"><?php echo trans("submit"); ?></button>
                    </div>
                    <?php echo form_close(); ?><!-- form end -->
                </div>
            </div>
        </div>
    <?php endforeach;
endif; ?>




<script>
    //calculate product earned value
    var thousands_separator = '<?php echo $this->thousands_separator; ?>';
    var commission_rate = '<?php echo $this->general_settings->commission_rate; ?>';
    $(document).on("input keyup paste change", ".price-input", function () {
        var input_val = $(this).val();
        var data_item_id = $(this).attr('data-item-id');
        var data_product_quantity = $(this).attr('data-product-quantity');
        input_val = input_val.replace(',', '.');
        var price = parseFloat(input_val);
        commission_rate = parseInt(commission_rate);
        //calculate earned price
        if (!Number.isNaN(price)) {
            var earned_price = price - ((price * commission_rate) / 100);
            earned_price = earned_price.toFixed(2);
            if (thousands_separator == ',') {
                earned_price = earned_price.replace('.', ',');
            }
        } else {
            earned_price = '0' + thousands_separator + '00';
        }

        //calculate unit price
        if (!Number.isNaN(price)) {
            var unit_price = price / data_product_quantity;
            unit_price = unit_price.toFixed(2);
            if (thousands_separator == ',') {
                unit_price = unit_price.replace('.', ',');
            }
        } else {
            unit_price = '0' + thousands_separator + '00';
        }

        $("#earned_price_" + data_item_id).html(earned_price);
        $("#unit_price_" + data_item_id).html(unit_price);
    });

    $(document).on("click", ".btn_submit_quote", function () {
        $('.modal-title').text("<?php echo trans("submit_a_quote"); ?>");
    });
    $(document).on("click", ".btn_update_quote", function () {
        $('.modal-title').text("<?php echo trans("update_quote"); ?>");
    });
</script>
