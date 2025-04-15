<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <title>Bootstrap demo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="p-2">
  <div class="container">

    
    <div class="row">
      <div class="12">

        <form method="post" action="/posts/store">
          <input type="hidden" name="_token" value="d6b0f1530655302a2411b3d3dfa34e8722f29ad9d8093b0baefd5339fff30ca2" />

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