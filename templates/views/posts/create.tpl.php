<!doctype html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($title ?? 'Create Post'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="p-2">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <form method="POST" action="posts/store" enctype="multipart/form-data">
            <?php echo csrf(); ?>
            <h1>Name</h1>
            <input type="text" name="name" class="form-control" required />
            <h1>Description</h1>
            <textarea name="description" class="form-control" required></textarea>
            <button type="submit" class="btn btn-success mt-3">Submit</button>
          </form>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
