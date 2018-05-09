<?php config('title', 'Welcome to BANG!'); ?>

<h1>BANG<span class="blue">!</span></h1>
<span class="zoomIn animated delay-15">
    Inilah awal mula aplikasimu terbentuk
</span>

<!-- block::styles -->
<link rel="stylesheet" href="<?= base_url('assets/css/style.min.css') ?>">

<!-- block::scripts -->
<script>
var h1 = document.getElementsByTagName('h1');
if (h1.length) { 
    h1[0].setAttribute('class', 'tada animated'); 
}
</script>
