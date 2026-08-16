<?php
require_once __DIR__ . '/../src/bootstrap.php';
use CT275\Labs\Contact;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $avatarPath = null;
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

    $contact = new Contact($PDO);
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
    $subtitle = 'Add your contacts here.';
    include_once __DIR__ . '/../src/partials/heading.php';
    ?>

        <div class="row">
            <div class="col-12">

                <form action="/add.php" method="POST" enctype="multipart/form-data">

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name"
                            class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" maxlen="255"
                            id="name" placeholder="Enter Name"
                            value="<?= isset($_POST['name']) ? html_escape($_POST['name']) : '' ?>" />

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
                            id="phone" placeholder="Enter Phone"
                            value="<?= isset($_POST['phone']) ? html_escape($_POST['phone']) : '' ?>" />

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
                            placeholder="Enter notes (maximum character limit: 255)"><?= isset($_POST['notes']) ? html_escape($_POST['notes']) : '' ?></textarea>

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
                        <img id="avatar-preview" src="#" alt="Preview" class="mt-2 rounded-circle"
                            style="display:none; width: 80px; height: 80px; object-fit: cover;">
                    </div>

                    <!-- Submit -->
                    <button type="submit" name="submit" class="btn btn-primary">Add Contact</button>
                </form>

            </div>
        </div>

    </div>

    <?php include_once __DIR__ . '/../src/partials/footer.php' ?>
    <script>
    function previewImage(input) {
        const preview = document.getElementById('avatar-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>

</html>