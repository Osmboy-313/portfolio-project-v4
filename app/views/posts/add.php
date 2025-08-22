
<?php

$errorClass = fn($field) => isset($errors[$field]) ? 'error' : '';
$fieldError = fn($field) => $errors[$field] ?? '';
$fillFields = fn($field) => $oldValues[$field] ?? '';

$statusClass = key($status);
$statusMsg = $statusClass ? $status[$statusClass] : '' ;

?>



<div class="title"></div>

<div class="main-content add-edit-post">

    
    <form action="?c=post&a=add" method='POST' enctype="multipart/form-data" >
        
        <div class="alert <?= !empty($statusClass) ? htmlspecialchars($statusClass) : 'de-active'?>">
            <p> <?= htmlspecialchars($statusMsg) ?> </p>
            <i class='bx bx-x alert__close'></i> 
        </div>
        
        <div class="title"><span>Create Post</span></div>

        <div class="input-box <?= $errorClass('title')?>">
            <label for="Post Name">Post Title</label>
            <input type="text" name="title" value="<?= $fillFields('title') ?>" placeholder="Enter the Title of the Post">

            <?php if( $msg = $fieldError('title') ) :?>
            <span class="error-box"> <?= $msg?> </span>
            <?php endif ?>

        </div>

        <div class="input-box <?= $errorClass('tags')?>">
            <label for="">Post Tags</label>
            <input type="text" id="tag-input" placeholder="Enter the tags of the Post">
            <input type="text" name="tags" id="hidden-tag-input" class="hidden-tag-input" value="<?= $fillFields('tags') ?>" hidden>

            <?php if( $msg = $fieldError('tags') ) :?>
            <span class="error-box"> <?= $msg ?> </span>
            <?php endif ?>

            <div class="tags" id="tags"></div>

        </div>

        <div class="input-box <?= $errorClass('description')?>">
            <label for="">Post Description</label>
            <textarea name="description" class="news-decription-field"><?= $fillFields('description') ?></textarea>

            <?php if( $msg = $fieldError('description') ) :?>
            <span class="error-box"> <?= $msg ?> </span>
            <?php endif ?>

        </div>

        <div class="input-box <?= $errorClass('category') ?>">

            <label for="">Post Category</label>

            <div class="select-wrapper">
                <select name="category" id="" value="<?= $fillFields('category') ?>" >
                    <option value="" selected disabled>Select an option</option>

                    <?php foreach ($categories as $category): ?>

                        <?php $selected = $category['id'] == $fillFields('category')  ? 'selected' : '' ?>

                        <option value="<?= $category['id'] ?>" <?= $selected ?>><?= $category['category_name'] ?> </option>

                    <?php endforeach ?>

                </select>
                <i class='bx bx-chevron-down select-tag-arrow'></i>
            </div>

            <?php if( $msg = $fieldError('category') ) :?>
            <span class="error-box"> <?= $msg ?> </span>
            <?php endif ?>

        </div>

        <div class="input-box <?= $errorClass('image') ?>">

            <label for="Post Picture">Post Picture</label>

            <div class="custom-file-upload" id="custom-file-upload">
                <input type="file" name="image" class="file-upload-input" id="file-upload-input" hidden>
                <button type="button" class="file-upload-btn" id="file-upload-btn">Browse ...</button>
                <span class="file-upload-msg" id="file-upload-msg">
                    <?= !empty($fillFields('image_O_Name')) ? $fillFields('image_O_Name') : 'No File Selected'?>
                </span>
            </div>

            <?php if ($fillFields('didImgUpload')): ?>
                <input type="text" name="existing-temp-file" value="1" hidden>
            <?php endif; ?>


            <?php if( $msg = $fieldError('image') ) :?>
            <span class="error-box"> <?= $msg?> </span>
            <?php endif ?>

        </div>

        <input type="submit" name="add-post" class="btn btn--primary btn--add-edit-post" value="Create Post">

    </form>

</div>