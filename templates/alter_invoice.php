<?php foreach ($editable_formatted_names as $key => $value): ?>
    <label><?php echo "$editable_formatted_names[$key]: "; ?></label>
    <br>
    <?php if (in_array($editable_field_names[$key], $required_fields)): ?>
        <?php if ($editable_field_names[$key] == "customer_id"): ?>
        <select name="customer_id" class="form-control" id="item-name-select" placeholder="Enter item name">
            <option disabled selected value> --- Select Customer --- </option>
            <?php foreach ($customer_names as $key => $value): ?>
            <option value="<?php echo $customer_ids[$key][0]; ?>"><?php echo $customer_names[$key][0]; ?></option>
            <?php endforeach; ?>
        </select>
        <?php elseif ($editable_field_names[$key] == "title"): ?>
        <input value="INV<?php echo $next_ID; ?>" class="form-control" required
            id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
            name="<?php echo $editable_field_names[$key]; ?>" />
        <?php elseif ($editable_field_names[$key] == "status"): ?>
        <select name="<?php echo $editable_field_names[$key]; ?>" class="form-control" id="status-select">
            <option disabled selected value> --- Select Invoice Status --- </option>
            <option value="Pending">Pending</option>
            <option value="Complete">Complete</option>
            <option value="Overdue">Overdue</option>
        </select>
        <?php elseif ($raw_types[$key] == "varchar(5)"): ?>
            <select name="<?php echo $editable_field_names[$key]; ?>" class="form-control" required
            id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>">
            <option disabled selected value> --- Select true / false --- </option>
            <option value="true">True</option>
            <option value="false">False</option>
            </select>
        <?php elseif ($raw_types[$key] == "date"): ?>
            <input type="date" class="form-control" required
                id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>"
                name="<?php echo $editable_field_names[$key]; ?>">
        <?php elseif ($editable_field_names[$key] == "VAT" || $editable_field_names[$key] == "net_value"): ?>
        <input class="form-control" required
            id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
            name="<?php echo $editable_field_names[$key]; ?>">
        <?php else: ?>
        <input class="form-control" required
            id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
            name="<?php echo $editable_field_names[$key]; ?>">
        <?php endif; ?>
    <?php else: ?>
    <input class="form-control"
        id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
        name="<?php echo $editable_field_names[$key]; ?>">
    <?php endif; ?>
<?php endforeach; ?>