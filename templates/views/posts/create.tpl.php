<!doctype html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body class="p-2">
    <div class="container">
   
    <?php if (isset($_SESSION['errors'])): ?>
    <div class="alert alert-danger">
        <?php foreach ($_SESSION['errors'] as $field => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p><?= $message ?></p>
            <?php endforeach ?>
        <?php endforeach ?>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif ?>

         <div class="row">
            <div class="12">

            <form method="post" action="/posts/store">
                <h1>Name</h1>
                <input type="text" name="name" class="form-control">

                <h1>Description</h1>
                <textarea name="description" class="form-control"></textarea>

                <button type="submit" class="btn btn-success mt-3">Send</button>
            </form>
            </div>
         </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>