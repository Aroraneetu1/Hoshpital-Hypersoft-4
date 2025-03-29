<section id="unseen">

    <?php include('rooms_div.php'); ?>

    <div class="form">
        <form action="" class="cmxform form-horizontal form-example" method="post">

            <div class="row">
                <div class="col-md-4">
                    <label>Room Name</label>
                    <input type="text" class="form-control" name="room_name" value="<?= old("room_name"); ?>"> 
                    <span class="error"><?= isset($validation) ? $validation->getError('room_name') : ''; ?></span>
                </div>

                <div class="col-md-4">
                    <label>Room Number</label>
                    <input type="text" class="form-control" name="room_number" value="<?= old("room_number"); ?>"> 
                    <span class="error"><?= isset($validation) ? $validation->getError('room_number') : ''; ?></span>
                </div>

                <div class="col-md-4">
                    <label>Category</label>
                    <select class="form-control" name="room_category">
                        <option value="">Select one</option>
                        <?php if (!empty($roomc)) { foreach ($roomc as $value) { ?>
                            <option value="<?= $value->id; ?>" <?= (old('room_category') == $value->id) ? 'selected' : ''; ?>>
                                <?= $value->name; ?>
                            </option>
                        <?php } } ?>
                    </select>
                    <span class="error"><?= isset($validation) ? $validation->getError('room_category') : ''; ?></span>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <label>Description</label>
                    <input type="text" class="form-control" name="description" value="<?= old("description"); ?>"> 
                    <span class="error"><?= isset($validation) ? $validation->getError('description') : ''; ?></span>
                </div>

                <div class="col-md-4">
                    <label>Room Rate</label>
                    <input type="text" class="form-control" name="room_rate" value="<?= old("room_rate"); ?>"> 
                    <span class="error"><?= isset($validation) ? $validation->getError('room_rate') : ''; ?></span>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                        Add New Room
                    </button>
                </div>
            </div>
        </form>
    </div>

</section>
