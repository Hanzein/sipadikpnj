<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="author" content="Tim SIPADIKPNJ">
  <title>SIPADIKPNJ</title>
  <meta content="Sistem Pendataan Akademik dan Non Akademik PNJ" name="description">
  <meta content="Lebih mudah,cepat,dan transparan" name="keywords">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  
  <!-- Vendor CSS Files -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

  <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>

<body>
  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <h1 class="logo"><a href="http://127.0.0.1:8000">SIPADIK<span>PNJ</span></a></h1>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="#hero">Beranda</a></li>
          <li><a class="nav-link scrollto" href="#about">Tentang</a></li>
          <li><a class="nav-link scrollto" href="#services">Fitur</a></li>
          <!-- <li><a class="nav-link scrollto" href="#testimonials">Testimoni</a></li>
          <li><a class="nav-link scrollto" href="#contact">Kontak</a></li> -->
          <li>
    <a class="btn btn-primary text-white px-3 py-1" href="{{ url('/user/login') }}">
      Login
    </a>
  </li>
</ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>
  <!-- End Header -->

<!-- ======= Hero Section ======= -->
<section id="hero" class="d-flex align-items-center bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      
      <!-- Kiri: Teks -->
      <div class="col-lg-6 mb-4 mb-lg-0" data-aos="zoom-out" data-aos-delay="100">
        <h1 class="display-5 fw-bold mb-3">
          Solusi Pendataan Prestasi Mudah dan Cepat dengan <span>SIPADIKPNJ</span>
        </h1>
        <h2 class="fs-5 mb-4 text">
          Kami adalah tim yang mengembangkan sistem untuk dipakai oleh bidang kemahasiswaan PNJ dan juga mahasiswa PNJ.
        </h2>
        <a href="#about" class="btn-get-started scrollto btn-lg px-8 py-8">Mulai Sekarang</a>
      </div>

      <!-- Kanan: Gambar -->
      <div class="col-lg-6 d-flex justify-content-center" data-aos="fade-left" data-aos-delay="200">
        <img src="{{ asset('img/about/foto_gedungdirektorat.jpeg') }}" class="img-fluid rounded shadow" alt="Gambar Hero" style="max-height: 400px; object-fit: cover;">
      </div>

    </div>
  </div>
</section>
<!-- End Hero -->


  <main id="main">
    <!-- ======= About Section ======= -->
    <section id="about" class="about section-bg">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Tentang</h2>
          <h3>Apa itu <span>SIPADIK PNJ?</span></h3>
          <p>SIPADIK PNJ adalah Sistem Pendataan Prestasi Akademik dan Non Akademik yang ditujukan untuk mahasiswa berprestasi di Politeknik Negeri Jakarta.</p>
        </div>

        <div class="row">
          <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
          <img src="{{ asset('img/about/tugupnj.jpg') }}" class="img-fluid" alt="About Us Image">
          </div>
          <div class="col-lg-6 pt-4 pt-lg-0 content d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="100">
            <h3>Apa saja keuntungan memakai SIPADIK PNJ?</h3>
            <p class="fst-italic">
              Sebagai mahasiswa yang sedang skripsian ini, kami menawarkan:
            </p>
            <ul>
              <li>
                <i class="bi bi-check-circle"></i>
                <div>Pendataan lebih cepat dan mudah</div>
              </li>
              <li>
                <i class="bi bi-check-circle"></i>
                <div>Mahasiswa dapat memantau status pengajuan Bantuan UKT</div>
              </li>
              <li>
                <i class="bi bi-check-circle"></i>
                <div>Memudahkan admin dalam memvalidasi prestasi mahasiswa</div>
              </li>
              <li>
                <i class="bi bi-check-circle"></i>
                <div>Ada filtering dan sorting untuk data prestasi</div>
              </li>
            </ul>
            <p>
              Kami berkomitmen untuk memberikan solusi inovatif untuk mengatasi masalah admin dalam mendata prestasi mahasiswa. Dengan adanya status untuk pengajuan Bantuan UKT, harapannya dapat mempermudah mahassiwa dalam tracking pengajuan bantuan UKTnya.
            </p>
          </div>
        </div>
      </div>
    </section>
    <!-- End About Section -->

    <!-- ======= Services Section ======= -->
    <section id="services" class="services">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Fitur</h2>
          <h3>Jelajahi <span>Fitur Kami</span></h3>
          <p>Kami menawarkan berbagai fitur untuk kenyamanan user dan admin.</p>
        </div>

        <div class="row">
          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
            <div class="icon-box">
              <div class="icon"><i class="bx bxs-bar-chart-alt-2"></i></div>
              <h4><a href="">Deteksi Sertifikat</a></h4>
              <p>Mendeteksi Sertifikat menggunakan model OCR dan YOLO</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="200">
            <div class="icon-box">
              <div class="icon"><i class="bx bx-file"></i></div>
              <h4><a href="">Deteksi Surat Tugas</a></h4>
              <p>Mendeteksi Surat Tugas menggunakan model OCR dan YOLO</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in" data-aos-delay="300">
            <div class="icon-box">
              <div class="icon"><i class="bx bx-world"></i></div>
              <h4><a href="">Automation Search</a></h4>
              <p>Automation Search untuk Mencari lomba yang diikuti mahasiswa di google secara otomatis</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="icon-box">
              <div class="icon"><i class="bx bx-slideshow"></i></div>
              <h4><a href="">Lebih Transparan</a></h4>
              <p>Dengan adanya status yang bisa dipantau mahasiswa</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="icon-box">
              <div class="icon"><i class="bx bx-group"></i></div>
              <h4><a href="">Data Terstruktur</a></h4>
              <p>Semua data mahasiswa tersimpan dengan rapi di dalam sistem</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="300">
            <div class="icon-box">
              <div class="icon"><i class="bx bx-arch"></i></div>
              <h4><a href="">Lebih Mudah</a></h4>
              <p>Melakukan Pendataan dengan lebih mudah dan cepat</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Services Section -->

    <!-- ======= Testimonials Section ======= -->
    <!-- <section id="testimonials" class="testimonials">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2 style="color: #008697;">Testimoni</h2>
          <h3 style="color: white;">Apa yang <span style="color: #008697;">User Kami</span> Katakan</h3>
          <p style="color: white;">Berbagai testimoni dari klien yang telah bekerjasama dengan kami.</p>
        </div>

        <div class="row">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="testimonial-item">
            <img src="{{ asset('img/person/person-m-7.webp') }}" class="testimonial-img" alt="Testimonial 1">
              <h3>Akbar</h3>
              <h4>D4 TIK, Mahasiswa PNJ</h4>
              <p>
                <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                Website ini sangat membantu
                <i class="bx bxs-quote-alt-right quote-icon-right"></i>
              </p>
            </div>
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="testimonial-item">
              <img src="{{ asset('img/person/person-f-9.webp') }}" class="testimonial-img" alt="Testimonial 2">
              <h3>Ratna</h3>
              <h4>D4 Akuntansi, Mahasiswa PNJ</h4>
              <p>
                <i class="bx bxs-quote-alt-left quote-icon-left"></i>
               Membantu dalam memantau status Pengajuan Bantuan UKT
                <i class="bx bxs-quote-alt-right quote-icon-right"></i>
              </p>
            </div>
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="testimonial-item mt-4">
              <img src="{{ asset('img/person/person-m-12.webp') }}" class="testimonial-img" alt="Testimonial 3">
              <h3>Hendra</h3>
              <h4>D3 Elektro, Mahasiswa PNJ</h4>
              <p>
                <i class="bx bxs-quote-alt-left quote-icon-left"></i>
              Website ini sangat membantu dan ga ribet
                <i class="bx bxs-quote-alt-right quote-icon-right"></i>
              </p>
            </div>
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="testimonial-item mt-4">
              <img src="{{ asset('img/person/person-f-5.webp') }}" class="testimonial-img" alt="Testimonial 4">
              <h3>Diana Putri</h3>
              <h4>D4 Desain Grafis, Mahasiswa PNJ</h4>
              <p>
                <i class="bx bxs-quote-alt-left quote-icon-left"></i>
             Bagus sekali website ini
                <i class="bx bxs-quote-alt-right quote-icon-right"></i>
              </p>
            </div>
          </div>
        </div>
      </div>
    </section> -->
    <!-- End Testimonials Section -->

    <!-- ======= Contact Section ======= -->
    <!-- <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Kontak</h2>
          <h3><span>Hubungi Kami</span></h3>
          <p>Hubungi kami untuk informasi lebih lanjut jika ada masalah.</p>
        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="100">
          <div class="col-lg-6">
            <div class="info-box mb-4">
              <i class="bx bx-map"></i>
              <h3>Alamat Kami</h3>
              <p>Universitas Indonesia, Jl. Prof. DR. G.A. Siwabessy, Kukusan, Kecamatan Beji, Kota Depok, Jawa Barat 16425</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="info-box mb-4">
              <i class="bx bx-envelope"></i>
              <h3>Email Kami</h3>
              <p>akademik@pnj.ac.id</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="info-box mb-4">
              <i class="bx bx-phone-call"></i>
              <h3>Telepon Kami</h3>
              <p>021-7270044 / 0821-1214-4860</p>
            </div>
          </div>
        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="100">
          <div class="col-lg-6">
            <iframe class="mb-4 mb-lg-0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.179775048593!2d106.82109567505054!3d-6.370776193619396!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ec1cabb59bdf%3A0x28b4f84e4677f329!2sPoliteknik%20Negeri%20Jakarta!5e0!3m2!1sid!2sid!4v1747562726538!5m2!1sid!2sid" frameborder="0" style="border:0; width: 100%; height: 384px;" allowfullscreen></iframe>
          </div>

          <div class="col-lg-6">
            <form class="php-email-form">
              <div class="row">
                <div class="col form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Nama Anda" required>
                </div>
                <div class="col form-group">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Email Anda" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subjek" required>
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" rows="5" placeholder="Pesan" required></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Memuat</div>
                <div class="error-message"></div>
                <div class="sent-message">Pesan Anda telah terkirim. Terima kasih!</div>
              </div>
              <div class="text-center"><button type="submit">Kirim Pesan</button></div>
            </form>
          </div>
        </div>
      </div> -->
  </main>
  <!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-6 footer-contact">
            <h3>SIPADIK<span>PNJ</span></h3>
            <p>
              Jl. Prof. DR. G.A. Siwabessy <br>
              Kota Depok, Jawa Barat 16425<br>
              Indonesia <br><br>
              <strong>Phone:</strong> 021-7270044 / 0821-1214-4860<br>
              <strong>Email:</strong> akademik@pnj.ac.id<br>
            </p>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Tautan Berguna</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#hero">Beranda</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#about">Tentang Kami</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#services">Fitur</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Syarat Layanan</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Kebijakan Privasi</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Layanan Kami</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Deteksi Sertifikat</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Deteksi Surat Tugas</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Automation Search</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Lebih Transparan</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Data Terstruktur</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Media Sosial Kami</h4>
            <p>Ikuti kami di media sosial untuk mendapatkan update terbaru dan informasi bermanfaat</p>
            <div class="social-links mt-3">
              <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
              <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
              <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
              <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container py-4">
      <div class="copyright">
        &copy; Copyright <strong><span>SIPADIKPNJ</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        Designed by <a href="#">mahasiswaskripsian</a>
      </div>
    </div>
  </footer>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>

</body>
</html>