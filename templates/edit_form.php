<?php
    $rows = get_table_contents($conn, $table_name, $filter);
    $table_info = get_table_info($conn, $table_name);
    $formatted_names = $table_info[0];
    $field_names = $table_info[1];
    $editable_formatted_names = $table_info[2];
    $editable_field_names = $table_info[3];
    $required_fields = $table_info[4];
    $raw_types = get_raw_types_form($conn, $table_name);
    $id_modifier = "";
    if (isset($alt) && $alt == true) {
        $id_modifier = "alt-";
    }
?>

<form class="popup-form-content animate" action="dbh/manage_data.php" method="post">
    <input type="hidden" id="edit-form-identity" name="id" value="">
    <input type="hidden" name="table_name" value="<?php echo($table_name);?>">
    <div class="popup-form-container" id="editForm">
        <p id="edit_error"></p>
        <br>
        <?php foreach($editable_formatted_names as $key => $value): ?>
            <?php if (in_array($editable_field_names[$key], $required_fields)): ?>
                <label for="<?php echo $editable_field_names[$key]; ?>">*<?php echo "$editable_formatted_names[$key]: "; ?></label>
                <br>
                <?php if ($editable_field_names[$key] == "customer_id"): ?>
                <select required name="customer_id" class="form-control" id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>">
                    <option disabled selected value> --- Select Customer --- </option>
                    <?php foreach ($customer_names as $customer_key => $value): ?>
                        <option value="<?php echo $customer_ids[$customer_key][0]; ?>"><?php echo $customer_names[$customer_key][0]; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php elseif ($editable_field_names[$key] == "supplier_id"): ?>
                    <select required name="supplier_id" class="form-control" id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>">
                        <option disabled selected value> --- Select Supplier --- </option>
                        <?php foreach ($supplier_names as $key => $value): ?>
                            <option value="<?php echo $supplier_ids[$key][0]; ?>"><?php echo $supplier_names[$key][0]; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($editable_field_names[$key] == "invoice_id"): ?>
                    <select required name="invoice_id" class="form-control" id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>">
                        <option disabled selected value> --- Select Invoice --- </option>
                        <?php foreach ($invoice_titles as $key => $value): ?>
                        <option value="<?php echo $invoice_ids[$key][0]; ?>"><?php echo $invoice_titles[$key][0]; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($editable_field_names[$key] == "item_id"): ?>
                    <select name="item_id" class="form-control" id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>">
                        <option disabled selected value> --- Select Item --- </option>
                        <?php foreach($item_names as $item_key => $value): ?>
                            <option value="<?php echo($item_ids[$item_key][0]); ?>"><?php echo($item_names[$item_key][0]); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php elseif ($editable_field_names[$key] == "warehouse_id"): ?>
                    <select required name="warehouse_id" class="form-control" id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>">
                        <option disabled selected value> --- Select Warehouse --- </option>
                        <?php foreach($warehouse_names as $key => $value): ?>
                            <option value="<?php echo $warehouse_ids[$key][0]; ?>"><?php echo $warehouse_names[$key][0]; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($raw_types[$key] == "date"): ?>
                    <input style="font-family:Source Code Pro, FontAwesome" type="text" class="form-control form-datepicker" required
                        id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key])); ?>_edit"
                        name="<?php echo $editable_field_names[$key]; ?>" autocomplete="off" placeholder="&#xf073;">
                <?php elseif (str_contains($raw_types[$key], "enum")): ?>
                    <select name="<?php echo $editable_field_names[$key]; ?>" class="form-control" required id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key])); ?>_edit">
                        <option disabled selected value> --- Select <?php echo $editable_formatted_names[$key]; ?> --- </option>
                        <?php foreach (explode(',',substr($raw_types[$key], 5, -1)) as $option): ?>
                            <option value="<?php echo str_replace("'", '', $option); ?>"><?php echo str_replace("'", '', $option); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input class="form-control"
                    id="<?php echo strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>"
                    type="text" name="<?php echo $editable_field_names[$key];?>" value="" required>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($editable_field_names[$key] != "image_file_name"): ?>
                    <label for="<?php echo $editable_field_names[$key]; ?>"><?php echo "$editable_formatted_names[$key]: "; ?></label>
                    <br>
                <?php endif; ?>
                <?php if ($raw_types[$key] == "date"): ?>
                    <input style="font-family:Source Code Pro, FontAwesome" type="text" class="form-control form-datepicker"
                        id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key])); ?>_edit"
                        name="<?php echo $editable_field_names[$key]; ?>" autocomplete="off" placeholder="&#xf073;">
                <?php elseif (str_contains($raw_types[$key], "enum")): ?>
                    <select name="<?php echo $editable_field_names[$key]; ?>" class="form-control" id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key])); ?>_edit">
                        <option disabled selected value> --- Select <?php echo $editable_formatted_names[$key]; ?> --- </option>
                        <?php foreach (explode(',',substr($raw_types[$key], 5, -1)) as $option): ?>
                            <option value="<?php echo str_replace("'", '', $option); ?>"><?php echo str_replace("'", '', $option); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($editable_field_names[$key] == "image_file_name"): ?>
                    <label class="custom-file-upload">
                        <input type="file" value="" class="form-control" required
                        id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key])); ?>_edit"
                        name="<?php echo $editable_field_names[$key]; ?>" /> 
                        <i class="fa fa-cloud-upload"></i> Upload Image
                    </label>
                    <br>
                <?php elseif ($editable_field_names[$key] == "offer_id"): ?>
                    <select name="offer_id" class="form-control" id="<?php echo $id_modifier; ?>offer-name-select">
                        <option disabled selected value> --- Select Offer Name --- </option>
                        <?php foreach($offer_names as $key => $value): ?>
                            <option value="<?php echo $offer_ids[$key][0]; ?>"><?php echo $offer_names[$key][0]; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($editable_field_names[$key] == "invoice_id"): ?>
                    <select name="invoice_id" class="form-control" id="<?php echo $id_modifier.strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>">
                        <option disabled selected value> --- Select Invoice --- </option>
                        <?php foreach ($invoice_titles as $key => $value): ?>
                        <option value="<?php echo $invoice_ids[$key][0]; ?>"><?php echo $invoice_titles[$key][0]; ?></option>
                        <?php endforeach; ?>
                    </select>    
                <?php else: ?>
                    <input class="form-control"
                    id="<?php echo strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>"
                    type="text" name="<?php echo $editable_field_names[$key];?>" value="">
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="buttons">
        </div>
    </div>
    <div class="popup-form-container popup-form-container-footer">
        <p onclick=hideForm(this);>Cancel</p>
        <button name="action" value="append" type="submit" style="float: right">
            <p>Save</p>
        </button>
    </div>
</form>