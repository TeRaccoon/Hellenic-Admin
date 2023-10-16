<?php
    if (!isset($_SESSION['table'])) {
        header("Location: ./login.php");
    }
    $table_name = $_SESSION['table'];
    $filter = $_SESSION['filter'];
    $conn = mysqli_connect("localhost", "root", "password", "hellenic");
    
    $rows = get_table_contents($conn, $table_name, $filter);
    $table_info = get_table_info($conn, $table_name);
    if ($table_info != null) {
        $formatted_names = $table_info[0];
        $field_names = $table_info[1];
        $editable_formatted_names = $table_info[2];
        $editable_field_names = $table_info[3];
        $types = get_raw_types_table($conn, $table_name);
        $assoc_data = pull_assoc($conn, $table_name);
    }
?>
<?php if ($table_info != null): ?>
    <table class="data-table" id="<?php echo($table_name); ?>">
        <tr class="table-heading">
            <th onclick="selectAll()">Select</th>
            <?php foreach($formatted_names as $key => $value): ?>
                <?php if ($key == 0): ?>
                    <th onclick="sortTable(this, this.parentNode.parentNode.parentNode, <?php echo $key; ?>)"><?php echo $formatted_names[$key]; ?><p class="material-icons">arrow_upward</p></th>
                <?php elseif ($field_names[$key] == "created_at"): ?>    
                    <th data-value="edit-exclude" onclick="sortTable(this, this.parentNode.parentNode.parentNode, <?php echo $key; ?>)"><?php echo $formatted_names[$key]; ?></th>
                <?php else: ?>
                    <th onclick="sortTable(this, this.parentNode.parentNode.parentNode, <?php echo $key; ?>)"><?php echo $formatted_names[$key]; ?></th>
                <?php endif; ?>
            <?php endforeach; ?>
            <th></th>
        </tr>
        <?php if ($rows != null): ?>
            <?php foreach($rows as $key => $row): ?>
                <tr>
                    <td class="checkbox-container" onclick="select(this.parentNode, false)"><input type="checkbox" id="select-<?php echo $key; ?>" name="row-<?php echo $key; ?>"></td>
                    <?php foreach($field_names as $field_key => $field_name): ?>
                        <?php if ($field_key == 0): ?>
                            <td data-value="<?php echo $rows[$key][$field_name]; ?>" onclick="select(this.parentNode, false)"><?php echo $rows[$key][$field_names[$field_key]]; ?></td>
                        <?php else: ?>
                            <?php if ($types[$field_key] == "float" || $types[$field_key] == "decimal(10,2)"): ?>
                                <td data-value="<?php echo $rows[$key][$field_name]; ?>" onclick="select(this.parentNode, false)"><?php echo "£".number_format($rows[$key][$field_name], 2); ?></td>                                
                            <?php elseif ($types[$field_key] == "double"): ?>
                                <td data-value="<?php echo $rows[$key][$field_name]; ?>" onclick="select(this.parentNode, false)"><?php echo $rows[$key][$field_name]."%"; ?></td>
                            <?php elseif ($types[$field_key] == "date"): ?>
                                <td data-value="<?php echo $rows[$key][$field_name]; ?>" onclick="select(this.parentNode, false)"><?php echo $rows[$key][$field_name] == null ? '' : date('d-m-Y', strtotime($rows[$key][$field_name])); ?></td>
                            <?php elseif ($assoc_data != null && array_key_exists($field_name, $assoc_data) && array_key_exists($rows[$key][$field_name], $assoc_data[$field_name])): ?>
                                <td data-value="<?php echo $rows[$key][$field_name]; ?>" onclick="select(this.parentNode, false)"><?php echo $assoc_data[$field_name][$rows[$key][$field_name]]; ?></td>
                            <?php elseif ($field_name == "image_file_name"): ?>
                                <td data-value="<?php echo $rows[$key][$field_name]; ?>" onclick="select(this.parentNode, false)"><img src="../uploads/<?php echo $rows[$key][$field_name] ?>" alt="*Image not found*"></td>
                            <?php elseif ($types[$field_key] == "enum('Yes','No')"): ?>
                                <?php if ($rows[$key][$field_name] == 'Yes'): ?>
                                    <td data-value="<?php echo $rows[$key][$field_name]; ?>" class="checkbox-container"><input onclick="toggleValue(this, <?php echo($key); ?>, this.parentNode.parentNode.parentNode)" value="<?php echo $rows[$key][$field_name]; ?>" checked="true" type="checkbox" name="row-<?php echo $key; ?>"></td>
                                <?php else: ?>
                                    <td data-value="<?php echo $rows[$key][$field_name]; ?>" class="checkbox-container"><input onclick="toggleValue(this, <?php echo($key); ?>, this.parentNode.parentNode.parentNode)" value="<?php echo $rows[$key][$field_name]; ?>" type="checkbox" name="row-<?php echo $key; ?>"></td>
                                <?php endif; ?>
                            <?php else: ?>
                                <td data-value="<?php echo $rows[$key][$field_name]; ?>" onclick="select(this.parentNode, false)"><?php echo $rows[$key][$field_name]; ?></td>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td class="edit-column" onclick="displayEditForm(<?php echo($key); ?>, this.parentNode.parentNode.parentNode)"><i class="inline-icon material-icons">edit</i></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
<?php endif; ?>
