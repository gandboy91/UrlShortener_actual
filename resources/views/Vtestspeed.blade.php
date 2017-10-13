<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>
	<link rel="stylesheet" href="{{ asset('css/cache.css') }}" type="text/css" />
	
</head>
<body >
<div id="wrapper">
	<div class="container">
		
	
	<h1 class="page-title" style="display: inline-block;">Акриловые ванны 		</h1>
	<div class="catalog-items-block">
		<?php
		foreach ($vanni as $v)
		{?>
			<div class = "catalog-elem" style="min-height:450px;border:2px solid #2980B9">
				<p>id: <?=$v['id']?></p>
				<p><?=$v['name']?></p>
				<p><?=$v['cname']?></p>
				<?php if ($v['d']!=0 && $v['sh']!=0 && $v['v']!=0) {?><p>Габариты: <?=$v['d']."x".$v['sh']."x".$v['v']?></p> <?php }?>
				<p>image:</p>
				<p style="font-size: 10px;"><?=$v['imgname']?></p>
				<p style="font-size: 7px;"><?=$v['slug']?></p>
				<p><?=$v['price']?></p>
				<?php if (isset($v['amount']) and !empty($v['amount'])){
					foreach ($v['amount'] as $am)
					{
						echo "<p style='font-size: 10px;'>на складе ".$am['sklad']." ".$am['amount']." шт.</p>";
					}
				} else {echo "<p>нет информации о наличии на складе</p>";}?>

			</div>
  <?php }?>
	</div>
	</div>
</div>
</body>
</html>
