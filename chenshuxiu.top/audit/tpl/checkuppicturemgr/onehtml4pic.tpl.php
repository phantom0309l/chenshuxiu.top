<div>
    <canvas id="canvas"></canvas>
    <img id="imgsrc" class="cdft" style="width: 100%;" src="<?= $checkuppicture->picture->getSrc(); ?>">
</div>

<script type="text/javascript">
    $('img').okzoom({
        width: 200,
        height: 200,
        round: true,
        background: "#fff",
        backgroundRepeat: "repeat",
        shadow: "0 0 5px #000",
        border: "1px solid black"
    });
</script>
