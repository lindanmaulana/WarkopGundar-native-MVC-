<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buku</title>
</head>

<body>
  <div>
    <ul>
      <?php foreach ($dataBuku as $data): ?>
        <li>Judul : <?= $data['judul'] ?></li>
        <li>Pengarang : <?= $data['pengarang'] ?></li>
        <li>Penerbit : <?= $data['penerbit'] ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</body>

</html>