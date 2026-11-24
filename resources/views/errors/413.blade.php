<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unggahan terlalu besar</title>
    <style>
        body{font-family:system-ui,sans-serif;max-width:620px;margin:12vh auto;padding:0 1.25rem;color:#243b3b}
        main{border:1px solid #d9e5e5;border-radius:1rem;padding:2rem}
        h1{font-size:1.6rem;margin:.4rem 0}
        p{line-height:1.6;color:#526565}
        a{display:inline-block;margin-top:.5rem;padding:.7rem 1rem;border-radius:.5rem;background:#176b68;color:white;text-decoration:none}
    </style>
</head>
<body>
<main>
    <div aria-hidden="true">Peringatan</div>
    <h1>Ukuran unggahan terlalu besar</h1>
    <p>Total data yang dikirim melebihi batas server. Kurangi ukuran berkas atau unggah lebih sedikit berkas sekaligus, lalu coba kembali.</p>
    <a href="{{ url()->previous() }}">Kembali ke halaman sebelumnya</a>
</main>
</body>
</html>
