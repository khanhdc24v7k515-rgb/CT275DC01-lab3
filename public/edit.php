<?php
require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/classes/Contact.php';

use CT275\Labs\Contact;

$contact = new Contact($PDO);

$id = isset($_REQUEST['id']) ?
    filter_var($_REQUEST['id'], FILTER_VALIDATE_INT) : false;

if (!$id || !($contact->find($id))) {
    redirect('/');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $avatarPath = $contact->avatar; // Giữ nguyên ảnh cũ mặc định

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $extension;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $filename)) {
            $avatarPath = '/uploads/' . $filename;
        }
    }
    $contactData = [
        'name' => $_POST['name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'notes' => $_POST['notes'] ?? '',
        'avatar' => $avatarPath
    ];

    $errors = $contact->validate($contactData);
    if (empty($errors)) {
        $contact->fill($contactData);
        $contact->save() && redirect('/');
    }
}

include_once __DIR__ . '/../src/partials/header.php';
?>

<body>
    <?php include_once __DIR__ . '/../src/partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">

        <?php
    $subtitle = 'Update your contacts here.';
    include_once __DIR__ . '/../src/partials/heading.php';
    ?>

        <div class="row">
            <div class="col-12">

                <form method="post" class="col-md-6 offset-md-3">

                    <input type="hidden" name="id" value="<?= $contact->id ?>">

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name"
                            class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" maxlen="255"
                            id="name" placeholder="Enter Name" value="<?= html_escape($contact->name) ?>" />

                        <?php if (isset($errors['name'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['name'] ?></strong>
                        </span>
                        <?php endif ?>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" name="phone"
                            class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>" maxlen="255"
                            id="phone" placeholder="Enter Phone" value="<?= html_escape($contact->phone) ?>" />

                        <?php if (isset($errors['phone'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['phone'] ?></strong>
                        </span>
                        <?php endif ?>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes </label>
                        <textarea name="notes" id="notes"
                            class="form-control<?= isset($errors['notes']) ? ' is-invalid' : '' ?>"
                            placeholder="Enter notes (maximum character limit: 255)"><?= html_escape($contact->notes) ?></textarea>

                        <?php if (isset($errors['notes'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['notes'] ?></strong>
                        </span>
                        <?php endif ?>
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*"
                            onchange="previewImage(this)">
                        <img id="avatar-preview"
                            src="<?= $contact->avatar ? html_escape($contact->avatar) : '/uploads/default.png' ?>"
                            alt="Avatar" class="mt-2 rounded-circle"
                            style="width: 80px; height: 80px; object-fit: cover;">
                    </div>

                    <!-- Submit -->
                    <button type="submit" name="submit" class="btn btn-primary">Update Contact</button>
                </form>

            </div>
        </div>

    </div>

    <?php include_once __DIR__ . '/../src/partials/footer.php' ?>
</body>

</html>